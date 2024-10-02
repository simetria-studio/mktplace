<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\Orders;
use App\Models\User;
use App\Models\Order;
use App\Mail\OrderPlan;
use App\Models\OrderME;
use App\Models\Produto;
use App\Models\Seller;
use App\Mail\OrderPayment;

use App\Models\SignedPlan;

use App\Events\OrdemGerada;
use App\Models\AffiliatePs;
use Illuminate\Support\Str;
use App\Models\OrderProduct;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\SubSignedPlan;
use App\Models\CustomerAddress;
use App\Models\ShippingCustomer;
use App\Mail\OrderServicePayment;
use App\Models\Cart as CartModel;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductSaleAffiliate;
use Illuminate\Support\Facades\Mail;
use App\Payments\MetodosPagamento\Pix;
use Illuminate\Support\Facades\Session;
use App\Payments\MetodosPagamento\Boleto;
use App\Payments\MetodosPagamento\CartaoDeCredito;
use App\Payments\MetodosPagamento\MetodoPagamento;

class CheckoutController extends Controller
{
    public $parent_id = null;

    // Depois do carrinho, vem a escolha do endereço de entrega ou local de retirada
    public function indexModalidade()
    {
        $seo = json_decode(json_encode([
            'title' => 'Escolha de frete e local de retirada'
        ]));

        $session_cart = session()->get('session_cart');
        $coupons = session()->get('session_coupons');
        $cupon_calc = '{}';
        if($coupons){
            $cupon_calc = json_encode(calcCoupon());
        }

        if(empty($session_cart)) return redirect()->route('cart');

        if(auth('web')->check()) $addresses = CustomerAddress::where('user_id', auth()->user()->id)->get();

        $sellers = collect($session_cart)->groupBy('attributes.seller_id');

        return view('site.indexModalidade', get_defined_vars());
    }

    // Ele criar uma sessão dos fretes selecionados na modalidades apra ser recuperados depois
    public function createSessionModalidade(Request $request)
    {
        session()->put('session_modalidades', $request->fretes);
        session()->put('session_address', $request->address);

        return response()->json(['success' => true]);
    }

    public function checkout()
    {
        $seo = json_decode(json_encode([
            'title' => 'Checkout dos Produtos'
        ]));

        $session_cart           = session()->get('session_cart');
        $session_modalidades    = session()->get('session_modalidades');
        $session_address        = session()->get('session_address');
        $session_coupon         = session()->get('session_coupons');
        $coupon_calc            = calcCoupon();

        $transport_value = collect();
        $total_frete = collect($session_modalidades)->map(function($q, $k) use($transport_value){
            switch($q['tipo']){
                case 'TransportePropio':
                    $transport_value->put($k,$q['frete']['valor_entrega']);
                    return (float)$q['frete']['valor_entrega'];
                case 'Transportadora':
                    $transport_value->put($k,$q['frete']['custom_price']);
                    return (float)$q['frete']['custom_price'];
                case 'LocaisDeRetirada':
                    $transport_value->put($k,0);
                    return 0;
            }
        })->sum();

        $discount = 0;
        if (($coupon_calc['ftv'] ?? 'p') == 'free') {
            // Obtém o valor do frete e converte para float
            $frete = $transport_value[$coupon_calc['seller_id']];
        
            // Exibe o desconto equivalente ao valor do frete
            $discount = $frete;
        } elseif (($coupon_calc['ftv'] ?? 'p') == 'discount') {
            // Obtém o valor do frete e converte para float
            $frete = $transport_value[$coupon_calc['seller_id']];
        
            $discount2 = 0;
            if ($coupon_calc['ftc'] == 'porcentage') {
                // Calcula o desconto percentual sobre o frete
                $discount2 = ($frete * $coupon_calc['ftd'] / 100);
            } elseif ($coupon_calc['ftc'] == 'money') {
                // Usa o valor de desconto em dinheiro
                $discount2 = $coupon_calc['ftd'];
            }
        
            // Exibe o desconto
            $discount = $discount2;
        } else {
            // Exibe o valor de desconto fornecido diretamente
            $discount = $coupon_calc['dp'] ?? 0;
        }

        $total_checkout = collect($session_cart)->map(function($q){return $q['quantity']*$q['price'];})->sum()+$total_frete;
        $total_checkout = $total_checkout - $discount;

        return view('site.indexCheckout', get_defined_vars());
    }

    public function finalizar(Request $request)
    {
        $session_cart = session()->get('session_cart');
        $session_modalidades = session()->get('session_modalidades');
        $session_address = session()->get('session_address');
        $session_coupon         = session()->get('session_coupon');
        $coupon_calc            = calcCoupon();

        $transport_value = collect();
        $total_frete = collect($session_modalidades)->map(function($q, $k) use($transport_value){
            switch($q['tipo']){
                case 'TransportePropio':
                    $transport_value->put($k,$q['frete']['valor_entrega']);
                    return (float)$q['frete']['valor_entrega'];
                case 'Transportadora':
                    $transport_value->put($k,$q['frete']['custom_price']);
                    return (float)$q['frete']['custom_price'];
                case 'LocaisDeRetirada':
                    $transport_value->put($k,0);
                    return 0;
            }
        });

        $discount = 0;
        if (($coupon_calc['ftv'] ?? 'p') == 'free') {
            // Obtém o valor do frete e converte para float
            $frete = $transport_value[$coupon_calc['seller_id']];
        
            // Exibe o desconto equivalente ao valor do frete
            $discount = $frete;
        } elseif (($coupon_calc['ftv'] ?? 'p') == 'discount') {
            // Obtém o valor do frete e converte para float
            $frete = $transport_value[$coupon_calc['seller_id']];
        
            $discount2 = 0;
            if ($coupon_calc['ftc'] == 'porcentage') {
                // Calcula o desconto percentual sobre o frete
                $discount2 = ($frete * $coupon_calc['ftd'] / 100);
            } elseif ($coupon_calc['ftc'] == 'money') {
                // Usa o valor de desconto em dinheiro
                $discount2 = $coupon_calc['ftd'];
            }
        
            // Exibe o desconto
            $discount = $discount2;
        } else {
            // Exibe o valor de desconto fornecido diretamente
            $discount = $coupon_calc['dp'] ?? 0;
        }

        \Log::channel('asaas_send')->info([
            'user' => auth()->user()->id.'---'.auth()->user()->name,
            'info' => 'checkout-finalizar',
            'request' => $request->all(),
            'session_cart' => $session_cart,
            'session_modalidades' => $session_modalidades,
            'session_address' => $session_address
        ]);

        // ############## Atualiza ou cria o endereço do cliente ############## //
            $make_addresses['user_id']      = auth()->user()->id;
            $make_addresses['post_code']    = $session_address['zip_code'];
            $make_addresses['state']        = $session_address['state'];
            $make_addresses['city']         = $session_address['city'];
            $make_addresses['address2']     = $session_address['address2'];
            $make_addresses['address']      = $session_address['address'];
            $make_addresses['number']       = $session_address['number'];
            $make_addresses['complement']   = $session_address['complement'];
            // $make_addresses['phone1']       = $session_address['phone1'];

            CustomerAddress::updateOrCreate([
                'user_id' => auth()->user()->id,
                'post_code' => $session_address['zip_code'],
            ], $make_addresses);
        // ##################################################################### //

        $sellers = collect($session_cart)->groupBy('attributes.seller_id');

        $total_cart_geral = collect($session_cart)->map(function($q){
            return $q['price'] * $q['quantity'];
        })->sum();

        // ############ criando e atualizando os pedidos ############ //
            // Pedido Pai
            $order_number_pai = Order::max('order_number');
            $order_number_pai = str_pad(($order_number_pai + 1), 8, "0", STR_PAD_LEFT);

            // Criando o pedido Pai
            $this->parent_id = Order::create([
                'order_number' => $order_number_pai,
                'seller_id' => null,
                'user_id' => auth('web')->user()->id,
                'user_name' => auth('web')->user()->name,
                'user_email' => auth('web')->user()->email,
                'user_cnpj_cpf' => auth('web')->user()->cnpj_cpf,
                'birth_date' => auth('web')->user()->birth_date,
                'total_value' => ($total_cart_geral + $total_frete->sum()),
                'cost_freight' => $total_frete->sum(),
                'product_value' => $total_cart_geral,
                'discount' => $discount,
                'coupon_value' => null,
                'coupon' => $session_coupon ?? null,
                'payment_method' => $request->post('payment_method'),
                'pay' => 0
            ]);

            // Criando os pedidos dos vendedores
            foreach ($sellers as $seller_id => $seller) {
                $order_number = Order::max('order_number');
                $order_number = str_pad(($order_number + 1), 8, "0", STR_PAD_LEFT);

                $total_cart = collect($seller)->map(function($q){
                    return $q['price'] * $q['quantity'];
                })->sum();

                // Criando o pedido
                Order::create([
                    'order_number' => $order_number,
                    'parent_id' => $this->parent_id->id,
                    'seller_id' => $seller_id,
                    'user_id' => auth()->user()->id,
                    'user_name' => auth()->user()->name,
                    'user_email' => auth()->user()->email,
                    'user_cnpj_cpf' => auth()->user()->cnpj_cpf,
                    'birth_date' => auth()->user()->birth_date,
                    'total_value' => ($total_cart + $total_frete[$seller_id]),
                    'cost_freight' => $total_frete[$seller_id],
                    'product_value' => $total_cart,
                    'discount' => $coupon_calc['seller_id'] == $seller_id ? $discount : 0,
                    'coupon_value' => null,
                    'coupon' => collect($session_coupon)->where('seller_id', $seller_id)->first() || null,
                    'payment_method' => $request->post('method_payment'),
                    'pay' => 0,
                    'note' => ''
                ]);

                // Criando os produtos do pedido
                foreach ($seller as $content) {
                    $sequence_order = OrderProduct::where('order_number', $order_number)->max('sequence');
                    $sequence_order = ($sequence_order + 1);

                    // Esta em teste cuidaddo
                    $product_verif = Produto::find($content['attributes']['product_id']);
                    if($product_verif->stock_controller){
                        if(!empty($content['attributes']['atributo_valor'])){
                            $variations = VariationsProduto::with(['variations'=>function($query) use ($request) {
                                return $query->whereIn('attribute_id', $request->attributes_value);
                            }])->where('produto_id', $request->product_id)->whereHas('variations', function ($query) use ($request) {
                                return $query->whereIn('attribute_id', $request->attributes_value);
                            })->get()->map(function($query) use ($request) {
                                if($query->variations->count() == count($request->attributes_value)){
                                    return $query;
                                }
                            })->reject(function ($query) {
                                return empty($query);
                            })->first();
                            VariationsProduto::where('id', $variations->id)->update(['stock' => ($variations->stock - $content['quantity'])]);
                        }else{
                            Produto::where('id', $content['attributes']['product_id'])->update(['stock' => ($product_verif->stock - $content['quantity'])]);
                        }
                    }

                    $order_product = OrderProduct::create([
                        'order_number' => $order_number,
                        'sequence' => $sequence_order,
                        'seller_id' => $content['attributes']['seller_id'],
                        'product_id' => $content['attributes']['product_id'],
                        'product_code' => null,
                        'product_name' => $content['name'],
                        'product_price' => ($content['quantity'] * $content['price']),
                        'quantity' => $content['quantity'],
                        'product_weight' => $content['attributes']['product_weight'],
                        'product_height' => $content['attributes']['product_height'],
                        'product_width' => $content['attributes']['product_width'],
                        'product_length' => $content['attributes']['product_length'],
                        'product_sales_unit' => $content['price'],
                        'attributes' => $content['attributes']['selected_attribute'],
                        'discount' => 0,
                        'note' => null,
                    ]);
                }

                unset($make_addresses['post_code']);
                $make_addresses['order_number'] = $order_number;
                $make_addresses['zip_code'] = $session_address['zip_code'];
                $make_addresses['transport'] = collect($session_modalidades)->map(function($q){
                    if($q['tipo'] == 'TransportePropio') return 'Transportadora Própria';
                    if($q['tipo'] == 'Transportadora') return 'Melhor Envio';
                    if($q['tipo'] == 'LocaisDeRetirada') return 'Retirada em local selecionado';
                })[$seller_id];
                $make_addresses['price'] = $total_frete[$seller_id];
                $make_addresses['time'] = null;
                $make_addresses['general_data'] = $session_modalidades[$seller_id] ?? null;

                // Criando os dados da entrega
                $shipping_customer = ShippingCustomer::create($make_addresses);

                // $orderME = [];
                // if ($request->frete[$seller['seller_id']]['type'] == 'correios') {
                //     // Gerando o id do Melhor Envio
                //     $orderME['order_number'] = $order_number;
                //     $orderME['seller_id'] = $seller['seller_id'];
                //     $orderME['company_id'] = $request->frete[$seller['seller_id']]['company_id'];
                //     $orderME['service_id'] = $request->frete[$seller['seller_id']]['service_id'];
                //     $orderME['transport'] = $request->frete[$seller['seller_id']]['transport_name'];
                //     $orderME['package'] = json_decode(base64_decode($request->frete[$seller['seller_id']]['packages']), true);
                //     $orderME['price'] = $request->frete[$seller['seller_id']]['price'];
                //     $orderME['height'] = 0;
                //     $orderME['width'] = 0;
                //     $orderME['length'] = 0;
                //     $orderME['weight'] = 0;
                //     $orderME = OrderME::create($orderME);
                // }
            }
        // ########################################################## //

        $valor_biguacu = collect();
        $splits = collect();
        // splitsVendedores
        $sellers->map(function($seller, $seller_id) use($total_frete, $valor_biguacu, $splits){
            $session_coupon         = session()->get('session_coupons');
            $coupon_calc            = calcCoupon();

            $valor_frete = $total_frete[$seller_id];
            $wallet_id = Seller::find($seller_id)->wallet_id;

            $discount = 0;
            if(($coupon_calc['seller_id'] ?? '') == $seller_id && $session_coupon['fee'] == 'seller'){
                if (($coupon_calc['ftv'] ?? 'p') == 'free') {
                    $discount = $valor_frete;
                } elseif (($coupon_calc['ftv'] ?? 'p') == 'discount') {
                    $discount2 = 0;
                    if ($coupon_calc['ftc'] == 'porcentage') {
                        // Calcula o desconto percentual sobre o frete
                        $discount2 = ($valor_frete * $coupon_calc['ftd'] / 100);
                    } elseif ($coupon_calc['ftc'] == 'money') {
                        // Usa o valor de desconto em dinheiro
                        $discount2 = $coupon_calc['ftd'];
                    }
    
                    // Exibe o desconto
                    $discount = $discount2;
                } else {
                    // Exibe o valor de desconto fornecido diretamente
                    $discount = $coupon_calc['dp'] ?? 0;
                }
            }

            // $fixedValue = (($valorProduto * 15.0)/100);
            $sellerValue = collect($seller)->map(function($q){
                return $q['price'] * $q['quantity'];
            })->sum();
            $fixedValue = (($sellerValue * 15.0)/100);
            $valor_biguacu->push($fixedValue);

            $splits->push([
                'walletId' => $wallet_id,
                'fixedValue' => (float)number_format((($sellerValue-$fixedValue)+$valor_frete)-$discount, 2, '.', ''),
            ]);
        });

        // $splits->push([
        //     'walletId' => $this->wallet_id,
        //     'fixedValue' => $valor_biguacu->sum(),
        // ]);

        $cliente_asaas = $this->clienteAsaas();

        $make_payment = [
            'customer' => $cliente_asaas->id,
            'dueDate' => date('Y-m-d', strtotime('+5 day')),
            'value' => number_format(($total_cart_geral+$total_frete->sum())-$discount, 2, '.', ''),
            'description' => 'Vendas realizada no site da biguaçu',
            'externalReference' => $order_number_pai,
            'split' => $splits->toArray()
        ];

        switch($request->payment_method){
            case 'boleto':
                $make_payment['billingType'] = 'BOLETO';
            break;
            case 'cartao_credito':
                $make_payment['billingType'] = 'CREDIT_CARD';
                $make_payment['creditCard'] = [
                    'holderName' => $request->card_holder_name,
                    'number' => $request->card_number,
                    'expiryMonth' => $request->card_month,
                    'expiryYear' => $request->card_year,
                    'ccv' => $request->card_cvv,
                ];

                // $make_payment['installmentCount'] = $request->installments;

                $make_payment['creditCardHolderInfo'] = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'cpfCnpj' => $request->cnpj_cpf,
                    'postalCode' => $request->zip_code,
                    'addressNumber' => $request->address_number,
                    'phone' => $request->phone,
                ];
            break;
            case 'pix':
                $make_payment['billingType'] = 'PIX';
            break;
        }

        $payment = \Http::withHeaders([
            'access_token' => $this->access_token
        ])->post($this->url_asaas.'/payments', $make_payment)->object();

        \Log::channel('asaas_response')->info([
            'make_payment' => $make_payment,
            'payment' => $payment
        ]);

        if($payment->errors ?? null){
            return response()->json(['success' => false, 'message' => $payment->errors[0]->description]);
        }

        Order::find($this->parent_id->id)->update([
            'payment_id' => $payment->id
        ]);

        Order::where('parent_id', $this->parent_id->id)->update([
            'payment_id' => $payment->id
        ]);

        foreach (Order::where('parent_id', $this->parent_id->id)->get() as $order) {
            // Mail::to(auth()->user()->email)->send(new Orders($order->order_number, 'comprador'));
            // Mail::to('comercial@raeasy.com')->send(new Orders($order->order_number, 'biguacu'));
        }

        $session_cart_row_id = collect($session_cart)->map(function($q){
            return $q['row_id'];
        });

        // CartModel::where('user_id', auth()->user()->id)->whereIn('row_id', $session_cart_row_id)->delete();
        // session()->forget(['session_cart', 'session_modalidades', 'session_address', 'field_card', 'session_coupons]);

        return response()->json(['success' => true, 'redirect' => route('agradecimento', 'P-'.$this->parent_id->order_number)]);
    }

    public function clienteAsaas()
    {

    //    dd(auth('web')->user()->cnpj_cpf);
        $cliente = \Http::withHeaders([
            'access_token' => $this->access_token
        ])->get($this->url_asaas.'/customers?cpfCnpj='.auth('web')->user()->cnpj_cpf);

        dd($cliente);

        if($cliente->object()->totalCount > 0){
            return $cliente->object()->data[0];
        }else{
            $cliente = \Http::withHeaders([
                'access_token' => $this->access_token
            ])->post($this->url_asaas.'/customers', [
                'name' => auth('web')->user()->name,
                'email' => auth('web')->user()->email,
                'cpfCnpj' => auth('web')->user()->cnpj_cpf,
                'externalReference' => auth('web')->user()->id
            ]);

            return $cliente->object();
        }

        return null;
    }

    public function postback(Request $request)
    {
        // \Log::info($request);
        \Log::channel('asaas_response_all')->info($request->all());
        $request_data = $request->all();
        if($request_data['event'] == 'PAYMENT_CONFIRMED'){
            $order = Order::where('payment_id', $request_data['payment']['id'])->first();
            if($order){
                Order::where('payment_id', $request_data['payment']['id'])->update(['pay' => 1]);
                foreach(Order::where('payment_id', $request_data['payment']['id'])->whereNotNull('parent_id')->get() as $order){
                    // Mail::to($order->user_email)->send(new OrderPayment($order, 'paid', 'comprador'));
                    // Mail::to($order->seller->email)->send(new OrderPayment($order, 'paid', 'vendedor'));
                }
            }
        }
        if($request_data['event'] == 'PAYMENT_REFUNDED' || $request_data['event'] == 'PAYMENT_OVERDUE'){
            $order = Order::where('payment_id', $request_data['payment']['id'])->first();
            if($order){
                Order::where('payment_id', $request_data['payment']['id'])->update(['pay' => 3]);
                foreach(Order::where('payment_id', $request_data['payment']['id'])->whereNotNull('parent_id')->get() as $order){
                    foreach($order->orderProducts as $item){
                        // Esta em teste cuidaddo
                        $product_verif = Produto::find($item->product_id);
                        if($product_verif->stock_controller){
                            if(!empty($item->attributes)){
                                $json_attributes = collect(json_decode($item->attributes))->map(function($query){return $query->id;});

                                $variations = VariationsProduto::with(['variations'=>function($query) use ($json_attributes) {
                                    return $query->whereIn('attribute_id', $json_attributes);
                                }])->where('produto_id', $item->product_id)->whereHas('variations', function ($query) use ($json_attributes) {
                                    return $query->whereIn('attribute_id', $json_attributes);
                                })->get()->map(function($query) use ($json_attributes) {
                                    if($query->variations->count() == count($json_attributes)){
                                        return $query;
                                    }
                                })->reject(function ($query) {
                                    return empty($query);
                                })->first();
                                VariationsProduto::where('id', $variations->id)->update(['stock' => ($variations->stock + $item->quantity)]);
                            }else{
                                Produto::where('id', $item->product_id)->update(['stock' => ($product_verif->stock + $item->quantity)]);
                            }
                        }
                    }

                    // Mail::to($order->user_email)->send(new OrderPayment($order, 'canceled', 'comprador'));
                    // Mail::to($order->seller->email)->send(new OrderPayment($order, 'canceled', 'vendedor'));
                }
            }
        }
        // if($request['metadata']['type_order'] == 'plano'){
        //     $plan_asaas_id = $request['subscription']['id'];
        //     $signed_plan = SignedPlan::where('asaas_id',$plan_asaas_id)->first();
        //     if($request_origin['type'] == 'invoice.paid'){

        //         SubSignedPlan::create([
        //             'signed_plan_id' => $signed_plan->id,
        //             'fatura_id' => $request['id'],
        //             'cobranca_id' => $request['charge']['id'],
        //             'status' => 1,
        //         ]);

        //         Mail::to($signed_plan->user->email)->send(new OrderPlan($signed_plan->id, 'comprador', 'aprovacao'));
        //         Mail::to($signed_plan->seller->email)->send(new OrderPlan($signed_plan->id, 'vendedor', 'aprovacao'));
        //         Mail::to('comercial@raeasy.com')->send(new OrderPlan($signed_plan->id, 'biguacu', 'aprovacao'));
        //     }
        //     if($request_origin['type'] == 'invoice.canceled' || $request_origin['type'] == 'invoice.payment_failed'){
        //         SubSignedPlan::create([
        //             'signed_plan_id' => $signed_plan->id,
        //             'fatura_id' => $request['id'],
        //             'cobranca_id' => $request['charge']['id'],
        //             'status' => 2,
        //         ]);

        //         Mail::to($signed_plan->user->email)->send(new OrderPlanCancel($signed_plan->id, 'comprador', 'aprovacao'));
        //         Mail::to($signed_plan->seller->email)->send(new OrderPlanCancel($signed_plan->id, 'vendedor', 'aprovacao'));
        //         Mail::to('comercial@raeasy.com')->send(new OrderPlanCancel($signed_plan->id, 'biguacu', 'aprovacao'));
        //     }
        // }elseif($request['metadata']['type_order'] == 'product'){
        //     if($request['status'] == 'paid'){
        //         Order::where('payment_id',$request['id'])->update(['pay' => 1]);
        //         $orders = Order::where('payment_id', $request['id'])->with(['seller', 'orderProducts', 'shippingCustomer'])->first();

        //         if(!empty($orders->user_email)){
        //             foreach(Order::where('payment_id', $request['id'])->where('seller_id', '!=', null)->where('parent_id', '!=', null)->with(['seller', 'orderProducts', 'shippingCustomer'])->get() as $order_for){
        //                 Mail::to($orders->user_email)->send(new OrderPayment($order_for, 'paid', 'comprador'));
        //                 Mail::to($order_for->seller->email)->send(new OrderPayment($order_for, 'paid', 'vendedor'));
        //             }
        //         }
        //     }
        //     if($request['status'] == 'canceled' || $request['status'] == 'payment_failed'){
        //         Order::where('payment_id',$request['id'])->update(['pay' => 3]);
        //         $orders = Order::where('payment_id', $request['id'])->where('seller_id', '!=', null)->with(['seller', 'orderProducts', 'shippingCustomer'])->first();

        //         foreach(Order::where('payment_id', $request['id'])->whereHas('orderProducts')->get() as $order_item){
        //             foreach($order_item->orderProducts as $item){
        //                 // Esta em teste cuidaddo
        //                 $product_verif = Produto::find($item->product_id);
        //                 if($product_verif->stock_controller){
        //                     if(!empty($item->attributes)){
        //                         $json_attributes = collect(json_decode($item->attributes))->map(function($query){return $query->id;});

        //                         $variations = VariationsProduto::with(['variations'=>function($query) use ($json_attributes) {
        //                             return $query->whereIn('attribute_id', $json_attributes);
        //                         }])->where('produto_id', $item->product_id)->whereHas('variations', function ($query) use ($json_attributes) {
        //                             return $query->whereIn('attribute_id', $json_attributes);
        //                         })->get()->map(function($query) use ($json_attributes) {
        //                             if($query->variations->count() == count($json_attributes)){
        //                                 return $query;
        //                             }
        //                         })->reject(function ($query) {
        //                             return empty($query);
        //                         })->first();
        //                         VariationsProduto::where('id', $variations->id)->update(['stock' => ($variations->stock + $item->quantity)]);
        //                     }else{
        //                         Produto::where('id', $item->product_id)->update(['stock' => ($product_verif->stock + $item->quantity)]);
        //                     }
        //                 }
        //             }
        //         }

        //         if(!empty($orders->user_email)){
        //             foreach(Order::where('payment_id', $request['id'])->where('seller_id', '!=', null)->where('parent_id', '!=', null)->with(['seller', 'orderProducts', 'shippingCustomer'])->get() as $order_for){
        //                 Mail::to($orders->user_email)->send(new OrderPayment($order_for, 'canceled', 'comprador'));
        //                 Mail::to($order_for->seller->email)->send(new OrderPayment($order_for, 'canceled', 'vendedor'));
        //                 Mail::to('comercial@raeasy.com')->send(new OrderPayment($order_for, 'canceled', 'biguacu'));
        //             }
        //         }
        //     }
        // }elseif($request['metadata']['type_order'] == 'service'){
        //     if($request['status'] == 'paid'){
        //         OrderService::where('payment_id',$request['id'])->update(['pay' => 1]);
        //         $orders = OrderService::where('payment_id', $request['id'])->with(['seller', 'serviceReservation'])->first();

        //         if(!empty($orders->user_email)){
        //             Mail::to($orders->user_email)->send(new OrderServicePayment($orders, 'paid', 'comprador'));
        //         }
        //     }
        //     if($request['status'] == 'canceled' || $request['status'] == 'payment_failed'){
        //         OrderService::where('payment_id',$request['id'])->update(['pay' => 3]);
        //         $orders = OrderService::where('payment_id', $request['id'])->where('seller_id', '!=', null)->with(['seller', 'serviceReservation'])->first();

        //         if(!empty($orders->user_email)){
        //             Mail::to($orders->user_email)->send(new OrderServicePayment($orders, 'canceled', 'comprador'));
        //             Mail::to($orders->seller->email)->send(new OrderServicePayment($orders, 'canceled', 'vendedor'));
        //             Mail::to('comercial@raeasy.com')->send(new OrderServicePayment($orders, 'canceled', 'biguacu'));
        //         }
        //     }
        // }

        return response()->json('', 200);
    }

    public function agradecimento(Request $request, $pedido)
    {
        $pedidoAnterior = (session()->get('pedido-agradecimento') ?? null);
        session(['pedido-agradecimento' => $pedido]);
    
        $pedidoArray = explode('-', $pedido);
    
        switch ($pedidoArray[0]) {
            case 'P':
                $order = Order::with('orderParent.orderProducts.product.categories.category')->where('order_number', $pedidoArray[1])->first();
                $total = $order->total_value;
                $frete = $order->cost_freight;

                $pedido_asaas = \Http::withHeaders([
                    'access_token' => $this->access_token
                ])->get($this->url_asaas.'/payments'.'/'.$order->payment_id)->object();
    
                $dataItems = collect([]);
                foreach($order->orderParent as $order_r){
                    foreach($order_r->orderProducts as $order_p){
                        $dataItems->add([
                            'item_id' => 'P_'.$order_p->product_id,
                            'item_name' => $order_p->product_name,
                            // 'coupon' => $order_p,
                            // 'discount' => $order_p,
                            // 'affiliation' => $order_p,
                            'item_brand' => 'Biguaçu',
                            'item_category' => $order_p->product->categories[0]->category->name,
                            // 'item_variant' => $order_p,
                            'price' => number_format($order_p->product_price/$order_p->quantity, 2, '.', ''),
                            'currency' => 'BRL',
                            'quantity' => $order_p->quantity,
                        ]);
                    }
                }
                break;
            case 'S':
                $order = OrderService::with('serviceReservation.service.categories.category')->where('order_number', $pedidoArray[1])->first();
                $total = $order->service_value;
                $frete = 0;
    
                $pedido_pagarme = \Http::withHeaders(get_header_conf_pm())->get(url_pagarme('orders', '/'.$order->payment_id), [])->object();
    
                $dataItems = collect([]);
                $dataItems->add([
                    'item_id' => 'S_'.$order->serviceReservation->service_id,
                    'item_name' => $order->serviceReservation->service_name,
                    // 'coupon' => $order->serviceReservation,
                    // 'discount' => $order->serviceReservation,
                    // 'affiliation' => $order->serviceReservation,
                    'item_brand' => 'Biguaçu',
                    'item_category' => $order->serviceReservation->service->categories[0]->category->name,
                    // 'item_variant' => $order->serviceReservation,
                    'price' => number_format($order->serviceReservation->service_price/$order->serviceReservation->service_quantity, 2, '.', ''),
                    'currency' => 'BRL',
                    'quantity' => $order->serviceReservation->service_quantity,
                ]);
                break;
            case 'PN':
                $order = SignedPlan::with('produto.categories.category')->find($pedidoArray[1]);
                $total = $order->plan_value;
                $frete = number_format($order->shipping['price'], 2, '.', '');
    
                $pedido_pagarme = \Http::withHeaders(get_header_conf_pm())->get(url_pagarme('orders', '/'.$order->payment_id), [])->object();
    
                $dataItems = collect([]);
                $dataItems->add([
                    'item_id' => 'S_'.$order->produto->id,
                    'item_name' => $order->produto->nome,
                    // 'coupon' => $order->produto,
                    // 'discount' => $order->produto,
                    // 'affiliation' => $order->produto,
                    'item_brand' => 'Biguaçu',
                    'item_category' => $order->produto->categories[0]->category->name,
                    // 'item_variant' => $order->produto,
                    'price' => $order->plan_value,
                    'currency' => 'BRL',
                    'quantity' => 1,
                ]);
                break;
            default:
                # code...
                break;
        }
    
        $seo = json_decode(json_encode([
            'title' => 'Obrigada por sua compra!'
        ]));
    
        return view('site.indexAgradecimento', get_defined_vars());
    }

    // // Checkout de planos
    // public function checkoutSessionPlan()
    // {
    //     $seo = json_decode(json_encode([
    //         'title' => 'Assinar Plano'
    //     ]));

    //     $addresses = CustomerAddress::where('user_id', auth()->user()->id)->get();
    //     $cart_session = session()->get('cart_session_plan');
    //     // dd($cart_session);
    //     return view('site.indexCheckoutPlan', get_defined_vars());
    // }

    // public function checkoutSessionPlanPost(Request $request)
    // {
    //     // dd(session()->get('cart_session_plan'));
    //     #dd($request->all());
    //     #dd( $request->frete['dados_gerais'] );
    //     $cart_session = session()->get('cart_session_plan');

    //     $addresses['user_id'] = auth()->user()->id;
    //     $addresses['post_code'] = $request->postal_code;
    //     $addresses['state'] = $request->state;
    //     $addresses['city'] = $request->city;
    //     $addresses['address2'] = $request->address2;
    //     $addresses['address'] = $request->address;
    //     $addresses['number'] = $request->number;
    //     $addresses['complement'] = $request->complement;
    //     $addresses['phone1'] = $request->phone1;
    //     $addresses['phone2'] = $request->phone2;
    //     if ($request->address_id == 'newAddress') {
    //         CustomerAddress::create($addresses);
    //     } else {
    //         CustomerAddress::where('id', $request->address_id)->update($addresses);
    //     }
    //     User::find(auth()->guard('web')->user()->id)->update(['cnpj_cpf' => $request->cnpj_cpf2]);

    //     $addresses['transport'] = ($request->frete['type'] == 'proprio' ? 'Transportadora Própria' : ($request->frete['type'] == 'retirada' ? 'Retirada na Loja' : $request->frete['transport_name']));
    //     $addresses['price'] = $request->frete['price'];
    //     $addresses['time'] = null;
    //     $addresses['general_data'] = ($request->frete['dados_gerais'] ?? null) ? $request->frete['dados_gerais'] : null;

    //     // Criando o pedido
    //     $plano = SignedPlan::create([
    //         'user_id' => auth()->guard('web')->user()->id,
    //         'seller_id' => $cart_session['attributes']['seller_id'],
    //         'plan_id' => $cart_session['id'],
    //         'product_id' => $cart_session['reference_id'],
    //         'product_name' => $cart_session['name'],
    //         'plan_title' => $cart_session['plan_title'],
    //         'select_interval' => $cart_session['select_interval'],
    //         'duration_plan' => $cart_session['duration_plan'],
    //         'plan_value' => $cart_session['plan_value'],
    //         'select_entrega' => $cart_session['select_entrega'],
    //         'cart' => $cart_session,
    //         'product' => Produto::find($cart_session['reference_id'])->toArray(),
    //         'shipping' => $addresses,
    //         'observation' => $request->note,
    //         'finish' => date('Y-m-d', strtotime('+ '.$cart_session['duration_plan'].'Month')),
    //     ]);

    //     // criando pedidos no pagarme
    //     $items = [
    //         [
    //             'description' => $cart_session['name'],
    //             'pricing_scheme' => [
    //                 'price' => valor_enviar_pagar_me($cart_session['plan_value']),
    //             ],
    //             'quantity' => '1',
    //         ],
    //         [
    //             'description' => ($request->frete['type'] == 'proprio' ? 'Transportadora Própria' : ($request->frete['type'] == 'retirada' ? 'Retirada na Loja' : $request->frete['transport_name'])),
    //             'pricing_scheme' => [
    //                 'price' => valor_enviar_pagar_me($request->frete['price']),
    //             ],
    //             'quantity' => '1',
    //         ],
    //     ];

    //     $splits = [
    //         [
    //             'amount' => 83,
    //             'recipient_id' => Seller::find($cart_session['attributes']['seller_id'])->wallet_id,
    //             'type' => 'percentage',
    //             'options' => [
    //                 'charge_processing_fee' => false,
    //                 'charge_remainder_fee' => false,
    //                 'liable' => true,
    //             ]
    //         ],
    //         // [
    //         //     'amount' => valor_enviar_pagar_me($request->frete['price']),
    //         //     'recipient_id' => Seller::find($cart_session['attributes']['seller_id'])->wallet_id,
    //         //     'type' => 'percentage',
    //         //     'options' => [
    //         //         'charge_processing_fee' => false,
    //         //         'charge_remainder_fee' => false,
    //         //         'liable' => true,
    //         //     ]
    //         // ],
    //         [
    //             'amount' =>  17,
    //             'recipient_id' => env('asaas_API_RECEBEDOR_ID2'),
    //             'type' => 'percentage',
    //             'options' => [
    //                 'charge_processing_fee' => true,
    //                 'charge_remainder_fee' => true,
    //                 'liable' => true,
    //             ]
    //         ]
    //     ];

    //     $valorTotalCompra = valor_enviar_pagar_me($cart_session['plan_value']+$request->frete['price']);
    //     // \Log::info($valorTotalCompra);

    //     $zipCode = Str::of($request->postal_code)->replaceMatches('/[^A-Za-z0-9]++/', '')->padLeft(8, 0);

    //     $state_explode = explode(' ', $request->state);
    //     $state_two = $state_explode[0][0].$state_explode[count($state_explode)-1][0];

    //     $card_expiration_month_year = explode('/', ($request->card_expiration_month_year ?? '/'));

    //     $phone2 = explode(' ', str_replace(['(',')','-'], '', $request->phone2));
    //     $createTransaction = collect([
    //         'payment_method' => 'credit_card',
    //         'currency' => 'BRL',
    //         'interval' => explode('-', $cart_session['select_interval'])[1],
    //         'interval_count' => (int)explode('-', $cart_session['select_interval'])[0],
    //         'billing_type' => 'prepaid',
    //         'installments' => 1,
    //         'minimum_price' => $valorTotalCompra,
    //         'customer' => [
    //             'name' => auth()->guard('web')->user()->name,
    //             'email' => auth()->guard('web')->user()->email,
    //             'address' => [
    //                 'line_1' => $request->address.', '.$request->number.', '.$request->address2,
    //                 'country' => 'br',
    //                 'state' => $state_two,
    //                 'city' => $request->city,
    //                 'zip_code' => $zipCode
    //             ]
    //         ],
    //         'shipping' => [
    //             'description' => '.',
    //             'recipient_name' => auth()->guard('web')->user()->name,
    //             'recipient_phone' => "+55".str_replace(['(',')','-',' '], '', $request->phone2),
    //             'amount' => 0, // todo valor do frete
    //             'address' => [
    //                 'line_1' => $request->address.', '.$request->number.', '.$request->address2,
    //                 'country' => 'br',
    //                 'state' => $state_two,
    //                 'city' => $request->city,
    //                 'zip_code' => $zipCode
    //             ]
    //         ],
    //         'card' => [
    //             'holder_name' => replaceEspecial($request->card_holder_name),
    //             'number' => $request->card_number,
    //             'exp_month' => $card_expiration_month_year[0],
    //             'exp_year' => $card_expiration_month_year[1],
    //             'cvv' => $request->card_cvv,
    //             'billing_address' => [
    //                 'line_1' => $request->address.', '.$request->number.', '.$request->address2,
    //                 'country' => 'br',
    //                 'state' => $state_two,
    //                 'city' => $request->city,
    //                 'zip_code' => $zipCode
    //             ]
    //         ],
    //         'items' => $items,
    //         'split' => [
    //             'enabled' => true,
    //             'rules' => $splits
    //         ],
    //         'metadata' => [
    //             'plan_id_interno' => $plano->id,
    //             'type_order' => 'plano'
    //         ]
    //     ]);

    //     $pagarmeTransaction = \Http::withHeaders(get_header_conf_pm())->post(url_pagarme('subscriptions'), $createTransaction->toArray())->object();
    //     \Log::info(collect($pagarmeTransaction)->toArray());

    //     throw_unless(
    //         $pagarmeTransaction->status !== 'failed',
    //         \Exception::class, 'Erro na assinatura, verifique seus dados e tente novamente!'
    //     );

    //     $plano->asaas_id = $pagarmeTransaction->id;
    //     $plano->save();

    //     Mail::to(auth()->guard('web')->user()->email)->send(new OrderPlan($plano->id, 'comprador', 'compra'));
    //     Mail::to(Seller::find($cart_session['attributes']['seller_id'])->email)->send(new OrderPlan($plano->id, 'vendedor', 'compra'));
    //     Mail::to('comercial@raeasy.com')->send(new OrderPlan($plano->id, 'biguacu', 'compra'));

    //     session()->forget('cart_session_plan');
    //     return redirect()->route('agradecimento', 'PN-'.$plano->id);
    //     // return redirect()->route('perfil.assinaturaDetalhe', $plano->id)->with('success', 'Compra Efetuada com Sucesso!');
    // }
}


// if(($coupons[0]['fee'] ?? '') == 'admin'){
//     $somaBiguacu = $somaBiguacu-valor_enviar_pagar_me($discount['dp'] ?? 0);
// }

// // criando pedidos no pagarme
// $produtos = new Produto;
// $items = $produtosCart->map(function ($cart) use($coupons, $affiliate_value, $affiliates, $affiliate_controller) {
//     $attrs_json = $cart->attributes;
//     if(isset($attrs_json->affiliate_code)){
//         $affiliateps = AffiliatePs::with('user', 'item')->where('reference_id', $attrs_json->product_id)->where('codigo', $attrs_json->affiliate_code)->where('status', 1)->first();
//         if($affiliateps->item->reference_id == $attrs_json->product_id){
//             if($affiliateps->item->price_type == 'percentage'){
//                 $affiliate_value->add((($cart->price*$affiliateps->item->price)/100)*$cart->quantity);
//                 $value_affiliate = (($cart->price*$affiliateps->item->price)/100)*$cart->quantity;
//             }else{
//                 $affiliate_value->add($affiliateps->item->price*$cart->quantity);
//                 $value_affiliate = $affiliateps->item->price*$cart->quantity;
//             }
//             $affiliates->add([
//                 'amount' => valor_enviar_pagar_me($value_affiliate),
//                 'recipient_id' => $affiliateps->user->recipients_id_pagarme,
//                 'type' => 'flat',
//                 'options' => [
//                     'charge_processing_fee' => false,
//                     'charge_remainder_fee' => false,
//                     'liable' => true,
//                 ]
//             ]);
//             $affiliate_controller->add([
//                 'affiliate_id' => $affiliateps->affiliate_id,
//                 'reference_id' => $affiliateps->reference_id,
//                 'type_reference' => 'product',
//                 'order_number' => Order::find($this->parent_id->id)->order_number ?? null,
//                 'qty' => $cart->quantity,
//                 'value' => $value_affiliate,
//             ]);
//         }
//     }
//     $coupon = $coupons->where('seller_id', $attrs_json->seller_id)->first();
//     $discount = 0;
//     if(!empty($coupon)){
//         if($coupon['coupon_valid'] == 'product_discount'){
//             if($coupon['check_loja']){
//                 if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price)*$coupon['value_discount'])/100;
//                 if($coupon['discount_config'] == 'money') $discount = (($cart->price)*getPorcentCoupon())/100;
//             }else{
//                 if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price)*$coupon['value_discount'])/100;
//                 if($coupon['discount_config'] == 'money') $discount = $coupon['value_discount'];
//             }
//         }
//     }
//     return [
//         'code' => 'biguacu_id_'.$attrs_json->product_id,
//         'description' => $cart->name,
//         'amount' => valor_enviar_pagar_me($cart->price-$discount),
//         'quantity' => $cart->quantity,
//     ];
// })->filter();

// $sellers_query = new Seller;
// $splitsVendedores = $produtosCart->map(function ($cart) use ($produtos,$coupons) {
//     $attrs_json = $cart->attributes;
//     $produto = $produtos->find($attrs_json->product_id);
//     $coupon = $coupons->where('seller_id', $produto->seller->id)->first();
//     $discount = 0;
//     if(!empty($coupon)){
//         if($coupon['fee'] == 'seller'){
//             if($coupon['coupon_valid'] == 'product_discount'){
//                 if($coupon['check_loja']){
//                     if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price*$cart->quantity)*$coupon['value_discount'])/100;
//                     if($coupon['discount_config'] == 'money') $discount = (($cart->price*$cart->quantity)*getPorcentCoupon())/100;
//                 }else{
//                     if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price*$cart->quantity)*$coupon['value_discount'])/100;
//                     if($coupon['discount_config'] == 'money') $discount = $coupon['value_discount'];
//                 }
//             }
//         }
//     }
//     return [
//         'amount' => valor_enviar_pagar_me((($cart->price - getValorBiguacu($cart->price)) * $cart->quantity)-$discount),
//         'recipient_id' => $produto->seller->recipients_id_pagarme,
//         'type' => 'flat',
//         'options' => [
//             'charge_processing_fee' => false,
//             'charge_remainder_fee' => false,
//             'liable' => true,
//         ]
//     ];
// })->filter();

// // adicionar para os vendedores
// Seller::query()->findMany($valoresFrete->keys())->each(function (Seller $seller) use ($splitsVendedores, $valoresFrete, $items, $coupons) {
//     $amount = $valoresFrete[$seller->id];
//     $coupon = $coupons->where('seller_id', $seller->id)->first();
//     $discount = 0;
//     if(!empty($coupon)){
//         if($coupon['fee'] == 'seller'){
//             if($coupon['coupon_valid'] == 'delivery_free'){
//                 $amount = 0;
//             }elseif($coupon['coupon_valid'] == 'delivery_discount'){
//                 if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price*$cart->quantity)*$coupon['value_discount'])/100;
//                 if($coupon['discount_config'] == 'money') $discount = $coupon['value_discount'];
//             }
//         }
//     }
//     $splitsVendedores->add([
//         'amount' => $amount-valor_enviar_pagar_me($discount),
//         'recipient_id' => $seller->recipients_id_pagarme,
//         'type' => 'flat',
//         'options' => [
//             'charge_processing_fee' => false,
//             'charge_remainder_fee' => false,
//             'liable' => true,
//         ]
//     ]);
//     if($amount > 0){
//         $items->add([
//                 'code' => 'frete_vendedor_id_'.$seller->id,
//                 'description' => "Frete da compra para vendedor: {$seller->name}",
//                 'amount' => $amount-valor_enviar_pagar_me($discount),
//                 'quantity' => 1,
//             ]
//         );
//     }
// });