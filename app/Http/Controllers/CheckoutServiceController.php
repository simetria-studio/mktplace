<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Service;
use App\Models\Seller;
use App\Events\OrdemGerada;
use App\Mail\OrderServices;
use App\Models\AffiliatePs;
use Illuminate\Support\Str;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Mail\OrderServicePayment;
use App\Models\ServiceVariantion;
use App\Models\ServiceReservation;
use Illuminate\Support\Facades\DB;
use App\Models\ProductSaleAffiliate;
use Illuminate\Support\Facades\Mail;

class CheckoutServiceController extends Controller
{
    public function seviceSession(Request $request)
    {
        $service = Service::find($request->service_id);
        $progressiveDiscount = $service->progressiveDiscount;
        if($request->vaga_controller == '1' && $request->hospedagem_controller == '0'){
            $orderServiceReservation = ServiceReservation::where('service_id', $request->service_id)->where(function($query)use($request){
                // if(isset($request->calendar_fim)){
                //     $query = $query->where('date_reservation_ini', '>=', date('Y-m-d', strtotime(str_replace('/','-',$request->calendar_ini))))->where('date_reservation_fim', '<=', date('Y-m-d', strtotime(str_replace('/','-',$request->calendar_fim))));
                // }else{
                // }
                $query = $query->where('date_reservation_ini', date('Y-m-d', strtotime(str_replace('/','-',$request->calendar_ini))));
                if($request->hours){
                    $query = $query->where('hour_reservation', $request->hours);
                }
                return $query;
            })->get();

            if(($orderServiceReservation->count() + $request->quantidade) > $request->vagas){
                return response()->json(['erro_custom' => true, 'icon' => 'error', 'msg' => 'As vagas são limitadas por dia/horário! Vagas disponíveis para o dia/horário selecionado "'.($request->vagas - $orderServiceReservation->count()).'"'],412);
            }
        }
        // if($request->selecao_hospedagem == 'quartos' && $request->hospedagem_controller == '1'){
        //     $orderServiceReservation = ServiceReservation::where('service_id', $request->service_id)->where(function($query)use($request){
        //         return $query = $query->where('date_reservation_ini', date('Y-m-d', strtotime(str_replace('/','-',$request->calendar_ini))));
        //     })->get();

        //     if(($orderServiceReservation->count() + $request->quantidade) > $request->vagas){
        //         return response()->json(['erro_custom' => true, 'icon' => 'error', 'msg' => 'O número de quartos é limitado! Quartos disponíveis para os dias selecionados "'.($request->vagas - $orderServiceReservation->count()).'"'],412);
        //     }
        // }
        // if(isset($request->selecao_hospedagem)){
        //     if($request->selecao_hospedagem !== 'quartos'){
        //         if($request->qty_max_hospedagem < $request->quantidade) return response()->json(['erro_custom' => true, 'icon' => 'error', 'msg' => 'O limite de pessoas foi excedido! Máximo '.$request->qty_max_hospedagem],412);
        //     }
        // }

        if(!empty($request->atributo_valor)){
            $selected_attribute = [];
            $variation = json_decode($request->variacao[implode('-', $request->atributo_valor)]);
            foreach($request->atributo_valor as $atributo_valor){
                $selected_attribute[] = json_decode($request->atributo[$atributo_valor]);
            }
        }

        if(isset($variation->preco)){
            $valor_cart_p = $variation->preco;
        }elseif(isset($request->service_price)){
            $valor_cart_p = ($service->service_price == $request->service_price) ? $request->service_price : $service->preco;
        }

        if($progressiveDiscount->count() > 0){
            $progressiveDiscount = $progressiveDiscount->filter(function($query) use($request){
                return $request->quantidade >= $query->discount_quantity;
            })->last();

            if($progressiveDiscount) $valor_cart_p = $progressiveDiscount->discount_value;
        }

        if(isset($variation->var_id)){
            if(ServiceVariantion::with('progressiveDiscount')->find($variation->var_id)->progressiveDiscount->count() > 0){
                $progressiveDiscount = ServiceVariantion::with('progressiveDiscount')->find($variation->var_id)->progressiveDiscount->filter(function($query) use($request){
                    return $request->quantidade >= $query->discount_quantity;
                })->last();

                if($progressiveDiscount) $valor_cart_p = $progressiveDiscount->discount_value;
            }
        }

        $cart_session['name']       = $request->service_title;
        $cart_session['price']      = $valor_cart_p;
        $cart_session['quantity']   = $request->quantidade;
        $cart_session['attributes'] = [
            'service_id'            => $request->service_id,
            'affiliate_code'        => $request->affiliate_code,
            'var_id'                => $variation->var_id ?? null,
            'atributo_valor'        => isset($request->atributo_valor) ? implode('-', $request->atributo_valor) : null,
            'seller_id'           => $request->seller_id,
            'service_image'         => $request->service_image,
            'seller_name'         => $request->seller_name,
            'hospedagem'            => $request->hospedagem_controller,
            'diaria'                => $request->diaria ?? 1,
            'selecao_hospedagem'    => $request->selecao_hospedagem ?? null,
            'qty_max_hospedagem'    => $request->qty_max_hospedagem ?? null,
            'calendar'              => [
                'date_ini' => date('Y-m-d', strtotime(str_replace('/','-', $request->calendar_ini))),
                'date_fim' => $request->calendar_fim ? date('Y-m-d', strtotime(str_replace('/','-', $request->calendar_fim))) : null,
                'hours' => $request->hours ?? null,
            ],
            'selected_attribute'    => $selected_attribute ?? null,
        ];

        session(['cart_session' => $cart_session]);

        return response()->json('',200);
    }

    public function checkout()
    {
        $seo = json_decode(json_encode([
            'title' => 'Checkout do Serviço'
        ]));
        $cart_session = session()->get('cart_session');
        $service = Service::with('store')->find($cart_session['attributes']['service_id']);
        $coupons = collect(session()->get('coupons_service'));
        $addresses = CustomerAddress::where('user_id', auth()->user()->id)->get();
        $address = CustomerAddress::where('user_id', auth()->user()->id)->get()->last();
        // dd($cart_session);
        return view('site.service.indexCheckout', get_defined_vars());
    }

    public function finalizar(Request $request)
    {
        $cart_session = session()->get('cart_session');
        $shipping_customer = CustomerAddress::where('user_id', auth()->guard('web')->user()->id)->get()->last();
        $seller = Seller::find($cart_session['attributes']['seller_id']);

        DB::beginTransaction();
        try {
            $addresses['user_id'] = auth()->user()->id;
            $addresses['post_code'] = $request->post_code ?? '';
            $addresses['state'] = $request->state ?? '';
            $addresses['city'] = $request->city ?? '';
            $addresses['address2'] = $request->address2 ?? '';
            $addresses['address'] = $request->address ?? '';
            $addresses['number'] = $request->number ?? '';
            $addresses['complement'] = $request->complement ?? '';
            $addresses['phone2'] = $request->phone2 ?? '';
            if ($request->endereco == 'newAddress' || !isset($request->endereco)) {
                $shipping_customer = CustomerAddress::create($addresses);
                \Log::info($shipping_customer);
            } else {
                $shipping_customer = CustomerAddress::where('id', $request->endereco)->update(['phone2' => $request->phone2]);
                $shipping_customer = CustomerAddress::where('id', $request->endereco)->first();
            }

            $coupons = collect(session()->get('coupons_service'));

            User::find(auth()->user()->id)->update(['cnpj_cpf' => $request->cnpj_cpf2]);
            $order_number = OrderService::max('order_number');
            $order_number = str_pad(($order_number + 1), 8, "0", STR_PAD_LEFT);

            // Criando o pedido
            $order = OrderService::create([
                'order_number' => $order_number,
                'seller_id' => $cart_session['attributes']['seller_id'],
                'user_id' => auth()->user()->id,
                'user_name' => auth()->user()->name,
                'user_email' => auth()->user()->email,
                'user_cnpj_cpf' => auth()->user()->cnpj_cpf,
                'birth_date' => auth()->user()->birth_date,
                'total_value' => 0,
                'service_value' => ($cart_session['price']*$cart_session['quantity']),
                'discount' => null,
                'coupon_value' => null,
                'coupon' => $coupons[0] ?? null,
                'payment_method' => $request->post('method_payment'),
                'pay' => 0,
                'note' => $request->note
            ]);

            // Criando os produtos do pedido
            $order_product = ServiceReservation::create([
                'order_number' => $order_number,
                'service_id' => $cart_session['attributes']['service_id'],
                'seller_id' => $cart_session['attributes']['seller_id'],
                'user_id' => auth()->user()->id,
                'service_name' => $cart_session['name'],
                'service_price' => $cart_session['price'],
                'service_quantity' => $cart_session['quantity'],
                'date_reservation_ini' => $cart_session['attributes']['calendar']['date_ini'],
                'date_reservation_fim' => $cart_session['attributes']['calendar']['date_fim'] ? $cart_session['attributes']['calendar']['date_fim'] : null,
                'hour_reservation' => $cart_session['attributes']['calendar']['hours'] == '0:0' ? null : $cart_session['attributes']['calendar']['hours'],
                'attributes' => $cart_session['attributes'],
            ]);

            $this->integrarPagarMe($order, $request, $shipping_customer);

            session()->forget(['cart_session', 'coupons_service']);
            Mail::to(auth()->user()->email)->send(new OrderServices($order_number, 'comprador'));
            //Mail::to($seller->email)->send(new OrderServices($order_number, 'vendedor'));
            Mail::to('comercial@raeasy.com')->send(new OrderServices($order_number, 'biguacu'));

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('error', $exception->getMessage());
        }
        return redirect()->route('agradecimento', 'S-'.$order_number);
        // return redirect()->route('perfil.servico.pedido', $order_number)->with('success', 'Compra Efetuada com Sucesso!');
    }

    /**
     * @param $order
     * @param $shipping_customer
     * @param $request
     * @throws \Throwable
     */
    private function integrarPagarMe($order, $request, $shipping_customer)
    {
        $produtosCart = collect([session()->get('cart_session')]);
        $coupons = collect(session()->get('coupons_service'));
        $discount = calcCouponService();
        $affiliate_value = collect();
        $affiliates = collect();
        $affiliate_controller = collect();

        // criando pedidos no pagarme
        $services = new Service;
        $items = $produtosCart->map(function ($cart) use($coupons, $affiliate_value, $affiliates, $affiliate_controller) {
            $attrs_json = $cart['attributes'];
            if($attrs_json['affiliate_code']){
                $affiliateps = AffiliatePs::with('user', 'item')->where('reference_id', $attrs_json['service_id'])->where('codigo', $attrs_json['affiliate_code'])->where('status', 1)->first();
                if($affiliateps->item->reference_id == $attrs_json['service_id']){
                    if($affiliateps->item->price_type == 'percentage'){
                        $affiliate_value->add(((($cart['price']*$affiliateps->item->price)/100)*$attrs_json['diaria'])*$cart['quantity']);
                        $value_affiliate = ((($cart['price']*$affiliateps->item->price)/100)*$attrs_json['diaria'])*$cart['quantity'];
                    }else{
                        $affiliate_value->add($affiliateps->item->price*$cart['quantity']);
                        $value_affiliate = $affiliateps->item->price*$cart['quantity'];
                    }
                    $affiliates->add([
                        'amount' => valor_enviar_pagar_me($value_affiliate),
                        'recipient_id' => $affiliateps->user->wallet_id,
                        'type' => 'flat',
                        'options' => [
                            'charge_processing_fee' => false,
                            'charge_remainder_fee' => false,
                            'liable' => true,
                        ]
                    ]);
                    $affiliate_controller->add([
                        'affiliate_id' => $affiliateps->affiliate_id,
                        'reference_id' => $affiliateps->reference_id,
                        'type_reference' => 'service',
                        'order_number' => $order->order_number ?? null,
                        'qty' => $cart['quantity'],
                        'value' => $value_affiliate,
                    ]);
                }
            }
            $coupon = $coupons->where('seller_id', $attrs_json['seller_id'])->first();
            $discount = 0;
            if(!empty($coupon)){
                if($coupon['coupon_valid'] == 'product_discount'){
                    if($coupon['check_loja']){
                        if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price)*$coupon['value_discount'])/100;
                        if($coupon['discount_config'] == 'money') $discount = (($cart->price)*getPorcentCouponService())/100;
                    }else{
                        if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price)*$coupon['value_discount'])/100;
                        if($coupon['discount_config'] == 'money') $discount = $coupon['value_discount'];
                    }
                }
            }
            return [
                'code' => (string)'S_'.$attrs_json['service_id'],
                'description' => $cart['name'],
                'amount' => valor_enviar_pagar_me(($cart['price']-$discount)*$attrs_json['diaria']),
                'quantity' => $cart['quantity'],
            ];
        })->filter();


        $splitsVendedores = $produtosCart->map(function ($cart) use ($services, $coupons) {
            $attrs_json = $cart['attributes'];
            $service = $services->find($attrs_json['service_id']);
            $coupon = $coupons->where('seller_id', $service->seller->id)->first();
            $discount = 0;
            if(!empty($coupon)){
                if($coupon['fee'] == 'seller'){
                    if($coupon['coupon_valid'] == 'product_discount'){
                        if($coupon['check_loja']){
                            if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price*$cart->quantity)*$coupon['value_discount'])/100;
                            if($coupon['discount_config'] == 'money') $discount = (($cart->price*$cart->quantity)*getPorcentCouponService())/100;
                        }else{
                            if($coupon['discount_config'] == 'porcentage') $discount = (($cart->price*$cart->quantity)*$coupon['value_discount'])/100;
                            if($coupon['discount_config'] == 'money') $discount = $coupon['value_discount'];
                        }
                    }
                }
            }
            return [
                'amount' => valor_enviar_pagar_me(((($cart['price']*$attrs_json['diaria']) - getValorBiguacuServico($cart['price']*$attrs_json['diaria'])) * $cart['quantity'])-($discount*$attrs_json['diaria'])),
                'recipient_id' => $service->seller->wallet_id,
                'type' => 'flat',
                'options' => [
                    'charge_processing_fee' => false,
                    'charge_remainder_fee' => false,
                    'liable' => true,
                ]
            ];
        })->filter();

        // \Log::info($splitsVendedores);

        $somaBiguacu = $produtosCart->reduce(function ($result, $cart) {
            $attrs_json = $cart['attributes'];
            return $result + (valor_enviar_pagar_me(
                    (getValorBiguacuServico($cart['price']*$attrs_json['diaria'])*$cart['quantity'])
                ));
        }, 0);
        if(($coupons[0]['fee'] ?? '') == 'admin'){
            $somaBiguacu = $somaBiguacu-valor_enviar_pagar_me(($discount['dp'] ?? 0));
        }

        $valorTotalCompra = valor_enviar_pagar_me((($produtosCart[0]['price']*($produtosCart[0]['attributes']['diaria'] ?? 1))*$produtosCart[0]['quantity'])-((valor_enviar_pagar_me(($discount['dp'] ?? 0)))*($produtosCart[0]['attributes']['diaria'] ?? 1)));
        // \Log::info($valorTotalCompra);
        $regra_parcelamento = getTabelaGeral('regra_parcelamento','parcelas')->array_text ?? [];
        $taxaParcelamento = ((($valorTotalCompra/100)*(str_replace(',','.', ($regra_parcelamento[($request->installments ?? 1)]['porcentage'] ?? 0))))/100);
        if(($valorTotalCompra+valor_enviar_pagar_me($taxaParcelamento)) > $valorTotalCompra){
            $items->add([
                    'code' => 'taxa_parcelamento',
                    'description' => "Taxa de parcelamento",
                    'amount' => valor_enviar_pagar_me($taxaParcelamento),
                    'quantity' => 1,
                ]
            );
        }

        $splitsBiguacu = [
            // 'amount' => valor_enviar_pagar_me(cart_show()->total),
            'amount' => ($somaBiguacu-valor_enviar_pagar_me($affiliate_value->sum()))+valor_enviar_pagar_me($taxaParcelamento),
            'recipient_id' => env('PAGARME_API_RECEBEDOR_ID2'),
            'type' => 'flat',
            'options' => [
                'charge_processing_fee' => true,
                'charge_remainder_fee' => true,
                'liable' => true,
            ]
        ];

        
        /** @var User $user */
        $user = auth()->user();

        $zipCode = Str::of($shipping_customer->post_code)->replaceMatches('/[^A-Za-z0-9]++/', '')->padLeft(8, 0);
        if(strlen($shipping_customer->state) > 2){
            $state_explode = explode(' ', $shipping_customer->state);
            $state_two = $state_explode[0][0].$state_explode[count($state_explode)-1][0];
        }else{
            $state_two = $shipping_customer->state;
        }

        $phone2 = explode(' ', str_replace(['(',')','-'], '', $shipping_customer->phone2));
        $payment = $this->paymentMethodParaEnviarPagarme($request);
        $createTransaction = collect([
            'amount' => $valorTotalCompra,
            'customer' => [
                'external_id' => (string)$user->id,
                'name' => $user->name,
                'type' => 'individual',
                'country' => 'br',
                'document' => $request->cnpj_cpf2,
                "phones" => [
                    "home_phone" => [
                        "country_code" => "55",
                        "number" => $phone2[1],
                        "area_code" => $phone2[0]
                    ]
                ],
                'email' => $user->email,
                'address' => [
                    'line_1' => $shipping_customer->address.', '.$shipping_customer->number.', '.$shipping_customer->address2,
                    'country' => 'br',
                    'state' => $state_two,
                    'city' => $shipping_customer->city,
                    'zip_code' => $zipCode
                ]
            ],
            'shipping' => [
                'description' => '.',
                'recipient_name' => $user->name,
                'recipient_phone' => "+55".str_replace(['(',')','-',' '], '', $shipping_customer->phone2),
                'amount' => 0, // todo valor do frete
                'address' => [
                    'line_1' => $shipping_customer->address.', '.$shipping_customer->number.', '.$shipping_customer->address2,
                    'country' => 'br',
                    'state' => $state_two,
                    'city' => $shipping_customer->city,
                    'zip_code' => $zipCode
                ]
            ],
            'payments' => [collect($this->paymentMethodParaEnviarPagarme($shipping_customer))->merge(['split' => $splitsVendedores->merge([$splitsBiguacu])->merge($affiliates->toArray())->all()])],
            'items' => $items->values()->toArray(),
            'metadata' => [
                'type_order' => 'service'
            ]
        ]);

        $pagarmeTransaction = \Http::withHeaders(get_header_conf_pm())->post(url_pagarme('orders'), $createTransaction->toArray())->object();
        // \Log::channel('pagarme_s_send')->info(['array_pagarme', $createTransaction->toArray()]);
        \Log::channel('pagarme_s_send')->info(['carrinho',collect([session()->get('cart_session')])->toArray()]);
        \Log::channel('pagarme_s_response')->info(collect($pagarmeTransaction)->toArray());

        throw_unless(
            $pagarmeTransaction->status !== 'failed',
            \Exception::class, 'Erro no pagamento, verifique seus dados e tente novamente!'
        );

        if($affiliate_controller->count() > 0){
            foreach($affiliate_controller as $affC){
                ProductSaleAffiliate::create($affC);
            }
        }

        // $pagarmeTransaction = get_pagarme()
        //     ->transactions()
        //     ->create($createTransaction->merge($payment)->toArray());

        // \Log::info(json_encode($pagarmeTransaction));

        collect([$order])->each(function (OrderService $order) use ($pagarmeTransaction) {
            // atualizando o pedido com a integração do pagarme
            $order->update([
                'payment_id' => $pagarmeTransaction->id
            ]);
        });
    }

    private function paymentMethodParaEnviarPagarme($shipping_customer)
    {
        $carbon = new Carbon();
        if(strlen($shipping_customer->state) > 2){
            $state_explode = explode(' ', $shipping_customer->state);
            $state_two = $state_explode[0][0].$state_explode[count($state_explode)-1][0];
        }else{
            $state_two = $shipping_customer->state;
        }

        $card_expiration_month_year = explode('/', (\request()->post('card_expiration_month_year') ?? '/'));
        $payment_method = [
            'credit_card' => [
                'payment_method' => 'credit_card',
                'credit_card' => [
                    'recurrence' => false,
                    'installments' => 1,
                    'statement_descriptor' => 'Biguaçu',
                    'card' => [
                        'number' => \request()->post('card_number'),
                        'holder_name' => replaceEspecial(\request()->post('card_holder_name')),
                        'exp_month' => $card_expiration_month_year[0],
                        'exp_year' => $card_expiration_month_year[1],
                        'cvv' => \request()->post('card_cvv'),
                        'billing_address' => [
                            'line_1' => $shipping_customer->address.', '.$shipping_customer->number.', '.$shipping_customer->address2,
                            'country' => 'br',
                            'state' => $state_two,
                            'city' => $shipping_customer->city,
                            'zip_code' => collect(Str::of($shipping_customer->zip_code)->replaceMatches('/[^A-Za-z0-9]++/', '')->padLeft(8, 0))->first()
                        ]
                    ]
                ]
            ],
            'pix' => [
                "payment_method" => "pix",
                "pix" => [
                    "expires_in" => "172800",
                ]
            ],
            'boleto' => [
                "payment_method" => "boleto",
                "boleto" => [
                    "instructions" => "Pagar até o vencimento",
                    "due_at" => date('Y-m-d', strtotime('+10 Days'))."T00:00:00Z",
                    "document_number" => \request()->post('cnpj_cpf2'),
                ],
                "type" => "DM"
            ],
        ];

        return $payment_method[\request()->post('method_payment')];
    }

    public function postback(Request $request)
    {
        \Log::info($request->all());
        if($request->transaction['status'] == 'paid'){
            OrderService::where('payment_id',$request->id)->update(['pay' => 1]);
            $orders = OrderService::where('payment_id', $request->id)->with(['seller', 'orderProducts', 'shippingCustomer'])->first();

            if(!empty($orders->user_email)){
                Mail::to($orders->user_email)->send(new OrderServicePayment($orders, 'paid', 'comprador'));
            }
        }
        if($request->transaction['status'] == 'canceled' || $request->transaction['status'] == 'refused'){
            OrderService::where('payment_id',$request->id)->update(['pay' => 3]);
            $orders = OrderService::where('payment_id', $request->id)->where('seller_id', '!=', null)->with(['seller', 'orderProducts', 'shippingCustomer'])->first();

            if(!empty($orders->user_email)){
                Mail::to($orders->user_email)->send(new OrderServicePayment($orders, 'canceled', 'comprador'));
                Mail::to($orders->seller->email)->send(new OrderServicePayment($orders, 'canceled', 'vendedor'));
                Mail::to('comercial@raeasy.com')->send(new OrderServicePayment($orders, 'canceled', 'biguacu'));
            }
        }
    }
}
