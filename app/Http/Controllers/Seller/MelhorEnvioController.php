<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Models\OrderME;
use App\Mail\OrderShipping;
use App\Models\OrderProduct;

use Illuminate\Http\Request;
use App\Models\ApiIntegration;
use App\Models\ShippingCustomer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MelhorEnvioController extends Controller
{
    public $url_melhor_envio = 'https://melhorenvio.com.br/';
    // public $url_melhor_envio = 'https://sandbox.melhorenvio.com.br/';
    public $endpoint_melhor_envio = [
        'agencies'  => 'api/v2/me/shipment/agencies',
        'services'  => 'api/v2/me/shipment/services',
        'cart'      => 'api/v2/me/cart',
        'checkout'  => 'api/v2/me/shipment/checkout',
        'generate'  => 'api/v2/me/shipment/generate',
        'tracking'  => 'api/v2/me/shipment/tracking',
        'print'     => 'api/v2/me/shipment/print',
        'balance'   => 'api/v2/me/balance',
        'oauth'     => 'oauth/token',
        'me'        => 'api/v2/me',
    ];

    public function indexApi()
    {
        $url_melhor_envio = $this->url_melhor_envio;
        $apiME = ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'Melhor-Envio-Token')->first();
        $saldo = null;
        if($apiME) $saldo = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'GET', ['balance'], '');
        if(!isset($saldo->balance)) $saldo = null;

        $orderME = OrderME::where('seller_id', auth('seller')->user()->id)->get();
        return view('seller.fretes.melhorEnvio', get_defined_vars());
    }

    public function callbackCode(Request $request)
    {
        $dados = [
            'grant_type' => 'authorization_code',
            'client_id' => ENV('CLIENT_ID'),
            'client_secret' => ENV('CLIENT_SECRET'),
            'redirect_uri' => route('callbackCode'),
            'code' => $request->code,
        ];
        $oauth = Http::withHeaders([
            'Accept' => 'application/json',
            'User-Agent' => 'Biguaçu (ellernetpar@gmail.com)'
        ])->asForm()->post($this->url_melhor_envio.$this->endpoint_melhor_envio['oauth'],$dados)->object();
        \Log::info(collect($oauth));

        $apime = [
            'user_id'       => auth()->guard('seller')->user()->id,
            'api_name'      => 'Melhor-Envio-Token',
            'token'         => 'Bearer '.$oauth->access_token,
            'refresh_token' => $oauth->refresh_token,
            'expires_in'    => date('Y-m-d H:i:s', strtotime('+ 2592000 Seconds')),
        ];

        $apiME = ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'Melhor-Envio-Token');
        if($apiME->count() > 0){
            $apiME->update($apime);
        }else{
            ApiIntegration::create($apime);
        }
        // \Log::info('estou entrando, uhules');
        // echo "<script>window.onload = function(){window.close()}</script>";
        return redirect()->route('seller.melhor_envio')->with('success', 'Parabens, sua conta foi vinculada com sucesso!');
    }

    // Desactivate ------
    public function storeApi(Request $request)
    {
        $api_melhor_envios['seller_id'] = auth()->guard('seller')->user()->id;
        $api_melhor_envios['app_name'] = $request->app_name;
        $api_melhor_envios['token'] = $request->token;
        $api_melhor_envios['document'] = str_replace(['.','-','/'],'', $request->cnpj_cpf);
        $api_melhor_envios['email'] = $request->email;
        $api_melhor_envios['phone'] = str_replace(['(',')',' ','-'],'', $request->phone);
        $api_melhor_envios['status'] = $request->status == 'on' ? 'true' : 'false';
        $api_melhor_envios['zip_code'] = $request->post_code;
        $api_melhor_envios['address'] = $request->address;
        $api_melhor_envios['number'] = $request->number;
        $api_melhor_envios['address2'] = $request->address2;
        $api_melhor_envios['country_id'] = $request->state;
        $api_melhor_envios['city'] = $request->city;
        $api_melhor_envios['complement'] = $request->complement;
        $api_melhor_envios['state_register'] = $request->state_register;

        if($request->id){
            ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'Melhor-Envio-Token')->update($api_melhor_envios);
        }else{
            ApiIntegration::create($api_melhor_envios);
        }

        return redirect()->back();
    }
    // Desactivate ------

    public function dataApi($id)
    {
        $apiME = ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'Melhor-Envio-Token')->first();
        $orderME = OrderME::find($id);
        $agencies = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'GET', ['agencies', '?company='.$orderME->company_id.'&country=BR&state='.auth()->guard('seller')->user()->store->state.'&city='.auth()->guard('seller')->user()->store->state->city], '');
        $service = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'GET', ['services', '/'.$orderME->service_id], '');

        return response()->json([
            'agencies' => $agencies,
            'service' => $service,
        ]);
    }

    public function storePurchaseApi(Request $request)
    {
        $orderME = OrderME::find($request->id);
        if($request->agency_id){
            $orderME->update(['agency_id' => $request->agency_id]);
        }

        $apiME = ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'Melhor-Envio-Token')->first();
        $order = Order::where('order_number', $orderME->order_number)->first();
        $shippingCustomer = ShippingCustomer::where('order_number', $orderME->order_number)->first();
        $order_products = OrderProduct::where('order_number', $orderME->order_number)->get();
        $products = [];
        $total_value = 0;
        foreach($order_products as $order_product) {
            $total_value += ($order_product->product_price * $order_product->quantity);
            $products[] = [
                'name' => $order_product->product_name,
                'quantity' => $order_product->quantity,
                'unitary_value' => (float)$order_product->product_sales_unit
            ];
        }

        $volumes = [];
        foreach ($orderME->package as $value){
            $volumes[] = [
                'height' => $value['dimensions']['height'],
                'width' => $value['dimensions']['width'],
                'length' => $value['dimensions']['length'],
                'weight' => $value['weight'],
            ];
        }

        $me_data = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'GET', ['me'], []);

        $dados = [
            'service' => $orderME->service_id,
            'angency' => $orderME->agency_id,
            'from' => [
                'name' => auth()->guard('seller')->user()->name,
                'phone' => $me_data->phone->phone,
                'email' => auth()->guard('seller')->user()->email,
                (strlen($me_data->document) > 11 ? 'company_document' : 'document') => $me_data->document,
                'state_register' => '0',
                'address' => $me_data->address->address,
                'complement' => $me_data->address->complement,
                'number' => $me_data->address->number,
                'district' => $me_data->address->district,
                'city' => $me_data->address->city->city,
                'country_id' => 'BR',
                'postal_code' => $me_data->address->postal_code,
                'note' => 'Sem Observação',
            ],
            'to' => [
                'name' => $order->user_name,
                'phone' => $shippingCustomer->phone2,
                'email' => $order->user_email,
                (strlen($order->user_cnpj_cpf > 11) ? 'company_document' : 'document') => $order->user_cnpj_cpf,
                'state_register' => null,
                'address' => $shippingCustomer->address,
                'complement' => $shippingCustomer->complement,
                'number' => $shippingCustomer->number,
                'district' => $shippingCustomer->address2,
                'city' => $shippingCustomer->city,
                'state_abbr' => $shippingCustomer->state,
                'country_id' => 'BR',
                'postal_code' => $shippingCustomer->zip_code,
                'note' => 'Sem Observação',
            ],
            'products' => $products,
            'volumes' => $volumes,
            'options' => [
                'insurance_value' => $total_value,
                'receipt' => false,
                'own_hand' => false,
                'reverse' => false,
                'non_commercial' => (empty($request->nfe_key) ? true : false),
                'invoice' => [
                    'key' => empty($request->nfe_key) ? '' : $request->nfe_key
                ]
            ]
        ];

        $cartME = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', ['cart'], $dados);
        \Log::info(['melhorEnvioController' => 'dados', 'dados' => json_encode($dados)]);
        \Log::info(['melhorEnvioController' => 'cartME', 'cartME' => json_encode($cartME)]);

        if(isset($cartME->id)){
            $orderME->update(['order_id' => $cartME->id]);
        }else{
            \Log::info('Log do melhor envio');
            \Log::info(json_encode($cartME));
            return redirect()->route('seller.melhor_envio')->with('error', 'Erro na criação do carrinho!');
        }

        $dados = [
            'orders' => [$cartME->id]
        ];

        $purchaseME = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', ['checkout'], $dados);
        \Log::info(['melhorEnvioController' => 'dados2', 'dados2' => json_encode($dados)]);
        \Log::info(['melhorEnvioController' => 'purchaseME', 'purchaseME' => json_encode($purchaseME)]);

        return redirect()->route('seller.melhor_envio')->with('success', 'Frete comprado com sucesso!');
    }

    public function etiquetaApi($id)
    {
        $apiME = ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'Melhor-Envio-Token')->first();
        $orderME = OrderME::find($id);

        $dados = [
            'orders' => [$orderME->order_id]
        ];

        $dados_verificar_1 = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', ['generate'], $dados);
        \Log::info(['melhorEnvioController' => 'dados_verificar_1_dados', 'dados_verificar_1_dados' => json_encode($dados)]);
        \Log::info(['melhorEnvioController' => 'dados_verificar_1', 'dados_verificar_1' => json_encode($dados_verificar_1)]);

        $tracking = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', ['tracking'], $dados);
        \Log::info(['melhorEnvioController' => 'tracking_dados', 'tracking_dados' => json_encode($dados)]);
        \Log::info(['melhorEnvioController' => 'tracking', 'tracking' => json_encode($tracking)]);
        // $tracking = collect($tracking)->keys()->all()[0];
        $tracking = collect($tracking)[$orderME->order_id];

        $impressao = $this->conect_melhor_envio($apiME->token, 'RaeaBiguaçusy (ellernetpar@gmail.com)', 'POST', ['print'], $dados);
        \Log::info(['melhorEnvioController' => 'impressao_dados', 'impressao_dados' => json_encode($dados)]);
        \Log::info(['melhorEnvioController' => 'impressao', 'impressao' => json_encode($impressao)]);

        OrderME::find($id)->update(['code' => ($tracking->tracking ?? $tracking->melhorenvio_tracking)]);
        ShippingCustomer::where('order_number', $orderME->order_number)->update(['tracking_id' => ($tracking->tracking ?? $tracking->melhorenvio_tracking)]);

        Mail::to(Order::where('order_number', $orderME->order_number)->first()->user_email)->send(new OrderShipping(Order::where('order_number', $orderME->order_number)->first(),($tracking->tracking ?? $tracking->melhorenvio_tracking), 'melhor_envio'));

        return redirect()->to($impressao->url);
    }

    public function etiquetaImpApi($id)
    {
        $apiME = ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'Melhor-Envio-Token')->first();
        $orderME = OrderME::find($id);

        $dados = [
            'orders' => [$orderME->order_id]
        ];

        $impressao = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', ['print'], $dados);

        return redirect()->to($impressao->url);
    }

    public function conect_melhor_envio($token, $user_agent, $metodo, $endpoint, $dados)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url_melhor_envio.$this->endpoint_melhor_envio[$endpoint[0]].(isset($endpoint[1]) ? $endpoint[1] : ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $metodo,
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

    // Rastreio
    public function rastreio($codigo)
    {
        $orderME = OrderME::where('code', $codigo)->first();
        $apiME = ApiIntegration::where('user_id', $orderME->seller_id)->where('api_name', 'Melhor-Envio-Token')->first();

        $dados = [
            'orders' => [$orderME->order_id]
        ];

        $tracking = $this->conect_melhor_envio($apiME->token, 'Biguaçu (ellernetpar@gmail.com)', 'POST', ['tracking'], $dados);
        \Log::info(json_encode($tracking));

        return response()->json($tracking->{$orderME->order_id});
    }
}