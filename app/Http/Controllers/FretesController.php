<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Seller;

use App\Models\Produto;
use App\Models\OwnTransport;
use Illuminate\Http\Request;
use App\Models\LocalRetirada;
use App\Models\ApiIntegration;
use App\Models\CustomerAddress;

class FretesController extends Controller
{
    public $endpoint_melhor_envio = [
        'calc' => 'me/shipment/calculate'
    ];

    public function freteCheckout(Request $request)
    {
        $cep = $this->consultaCep($request->zip_code);
        if(isset($cep->erro)) return response()->json(['error' => $cep],412);

        $session_cart = session()->get('session_cart');
        $sellers = collect($session_cart)->groupBy('attributes.seller_id');

        $fretes_sellers = $sellers->map(function($seller, $seller_id) use($cep, $request){
            $apiME = ApiIntegration::where('user_id', $seller_id)->where('api_name', 'Melhor-Envio-Token')->first();
            $produtos = collect();

            foreach($seller as $item){
                $produtos->push([
                    'id'                => $item['attributes']['var_id'] ?? $item['attributes']['product_id'],
                    'width'             => $item['attributes']['product_width'],
                    'height'            => $item['attributes']['product_height'],
                    'length'            => $item['attributes']['product_length'],
                    'weight'            => $item['attributes']['product_weight'],
                    'insurance_value'   => (float)$item['price'],
                    'quantity'          => (int)$item['quantity'],
                ]);
            }

            $locais_retirada = LocalRetirada::with(['localidade'])->where('seller_id', $seller_id)->get();

            $transporte_proprio = OwnTransport::where('seller_id', $seller_id)
            ->where('estado', $cep->uf ?? '')
            ->where(function ($query) use ($cep) {
                $query->where(function ($subQuery) use ($cep) {
                    $subQuery->where('cidade', $cep->localidade ?? '')
                        ->orWhere('cidade', 'todas as cidades');
                })->where(function ($subQuery) use ($cep) {
                    $subQuery->where('toda_cidade', 1)
                        ->orWhere('bairro', $cep->bairro ?? '');
                });
            })
            ->get();

            if($apiME){
                $dados = [
                    'from' => [
                        'postal_code' => str_replace('-', '', Seller::find($apiME->user_id)->store->post_code)
                    ],
                    'to' => [
                        'postal_code' => str_replace('-', '', $request->zip_code),
                    ],
                    'products' => $produtos
                ];

                $transportadoras = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', 'calc', $dados);
            }

            return [
                'seller_id' => $seller_id,
                'transportadoras' => ($apiME ? $transportadoras : []),
                'transporte_proprio' => $transporte_proprio->toArray(),
                'locais_retirada' => $locais_retirada->toArray(),
            ];
        });

        $fretes_sellers_html = [];
        foreach($fretes_sellers as $seller_id => $fretes){
            $fretes_sellers_html[$seller_id] = view('components.freteCheckout', get_defined_vars())->render();
        }

        return response()->json(['success' => true, 'html' => $fretes_sellers_html]);
    }

    public function conect_melhor_envio($token, $user_agent, $metodo, $endpoint, $dados)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://melhorenvio.com.br/api/v2/'.$this->endpoint_melhor_envio[$endpoint],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => ($metodo == 'POST' ? 'POST' : 'GET'),
            CURLOPT_POSTFIELDS => json_encode($dados),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: '.$token,
                'User-Agent: '.$user_agent
            ),
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        return $response;
    }

    public function consultaCep($cep){
        $cep = str_replace(['-'],'', $cep);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://opencep.com/v1/$cep");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, FALSE);

        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        return $response;
    }

    public function freteCheckoutPlano(Request $request)
    {
        
        if(isset($request->address_id)){
            $address = CustomerAddress::find($request->address_id);
        }else{
            $address = $this->consultaCep($request->cep);
            if(isset($cep->erro)) return response()->json(['error' => $cep],412);
        }

        $cart_session = session()->get('cart_session_plan');
        $total_entregas = getTotalEntrega($cart_session);

        $seller_id = $cart_session['attributes']['seller_id'];

        $store = Store::where('user_id', $seller_id)->first();
        $transporte_proprio = OwnTransport::where('seller_id', $seller_id)->where('estado', $address->uf ?? $address->state)->where(function($query) use($address) {
            return $query->orWhere('em_todas_cidades', 1)->orWhere('cidade', $address->localidade ?? $address->city);
        })->where(function($query) use ($address){
            return $query->orWhere('toda_cidade', 1)->orWhere('bairro', $address->bairro ?? $address->address2);
        });

        $apiME = ApiIntegration::where('user_id', $seller_id)->where('api_name', 'Melhor-Envio-Token')->first();
        $transportadoras = [];

        $perecivel = 0;
        if($apiME){
            $products[] = [
                'id' => $cart_session['id'],
                'width' => $cart_session['dimensoes_L'],
                'height' => $cart_session['dimensoes_A'],
                'length' => $cart_session['dimensoes_C'],
                'weight' => $cart_session['peso'],
                'insurance_value' => (float)$cart_session['plan_value'],
                'quantity' => 1,
            ];
            $dados = [
                'from' => [
                    'postal_code' => str_replace('-', '', Seller::find($apiME->user_id)->store->post_code)
                ],
                'to' => [
                    'postal_code' => str_replace('-', '', $address->cep ?? $address->post_code),
                ],
                'products' => $products
            ];

            $transportadoras = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', 'calc', $dados);
        }
        $fretes = [
            'addresses' => $address,
            'transportadoras' => ($perecivel == 1 ? [] : $transportadoras),
            'transporte_proprio' => $transporte_proprio->get(),
            'store' => $store,
            'cart_session' => $cart_session,
            'total_entregas' => $total_entregas,
        ];

        return response()->json($fretes);
    }

    public function freteCheckoutProduto(Request $request)
    {
        $cep = $this->consultaCep($request->cep_consulta);
        $produto = Produto::find($request->product_id);
        $apiME = ApiIntegration::where('user_id', $produto->seller_id)->where('api_name', 'Melhor-Envio-Token')->first();
        $transportadoras = collect();
        $produtos = collect();

        if(count($request->variacoes ?? []) > 0){
            $produtos->push([
                'id' => $request->variacoes['var_id'],
                'width' => (float)$request->variacoes['dimensoes_L'],
                'height' => (float)$request->variacoes['dimensoes_A'],
                'length' => (float)$request->variacoes['dimensoes_C'],
                'weight' => (float)$request->variacoes['peso'],
                'insurance_value' => (int)$request->quantidade*(float)$request->variacoes['preco'],
                'quantity' => (int)$request->quantidade,
            ]);
        }else{
            $produtos->push([
                'id' => $produto->id,
                'width' => (float)$produto->width,
                'height' => (float)$produto->height,
                'length' => (float)$produto->length,
                'weight' => (float)$produto->weight,
                'insurance_value' => (int)$request->quantidade*(float)$produto->preco,
                'quantity' => (int)$request->quantidade,
            ]);
        }

        $locais_retirada = LocalRetirada::with(['localidade'])->where('seller_id', $produto->seller_id)->get();

        $transporte_proprio = OwnTransport::where('seller_id', $produto->seller_id)
        ->where('estado', $cep->uf ?? '')
        ->where(function ($query) use ($cep) {
            $query->where(function ($subQuery) use ($cep) {
                $subQuery->where('cidade', $cep->localidade ?? '')
                    ->orWhere('cidade', 'todas as cidades');
            })->where(function ($subQuery) use ($cep) {
                $subQuery->where('toda_cidade', 1)
                    ->orWhere('bairro', $cep->bairro ?? '');
            });
        })
        ->get();

        if($apiME){
            $dados = [
                'from' => [
                    'postal_code' => str_replace('-', '', Seller::find($apiME->user_id)->store->post_code)
                ],
                'to' => [
                    'postal_code' => str_replace('-', '', $request->cep_consulta),
                ],
                'products' => $produtos
            ];

            $consulta_trans = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', 'calc', $dados);

            $transportadoras = $transportadoras->merge($consulta_trans);
        }
        
        $fretes_seller = [
            'transportadoras' => ($produto->perecivel == 1 ? [] : $transportadoras),
            'transporte_proprio' => $transporte_proprio,
            'locais_retirada' => $locais_retirada,
        ];

        return response()->json(['request' => $request->all(), 'fretes_seller' => $fretes_seller]);
    }
}
