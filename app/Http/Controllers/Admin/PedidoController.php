<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;

use App\Mail\OrderFinish;
use App\Mail\OrderPayment;
use App\Models\SignedPlan;
use App\Mail\OrderShipping;
use App\Models\OrderProduct;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Mail\NotificaVendedor;
use App\Mail\SendConfirmCancel;
use App\Mail\OrderServiceFinish;
use App\Models\ShippingCustomer;
use App\Mail\OrderServicePayment;
use App\Models\OrderRequestCancel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\OrderNotifySellerShipping;

class PedidoController extends Controller
{
    public function indexPedido(Request $request)
    {
        $orders = Order::with('seller')->whereNotNull('parent_id');
        if(isset($request->status))
        {
            $orders = $orders->whereIn('pay', $request->status);
        }
        $start = isset($request->start) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->start))) : date('Y-m-d', strtotime(date('Y').'-'.date('m').'-01'));
        $end   = isset($request->end) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->end))) : date('Y-m-d');;
        $orders = $orders->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        $orders = $orders->orderBy('order_number', 'DESC')->get();

        return view('painel.comercial.indexPedido', get_defined_vars());
    }

    public function indexVerPedido($order_number)
    {
        $order = Order::with('user', 'seller', 'shippingCustomer', 'orderProducts')->where('order_number', $order_number)->first();

        return view('painel.comercial.indexPedidoVer', compact('order'));
    }

    // --------------------------
    public function indexPedidoServico(Request $request)
    {
        $orders = OrderService::with('seller');
        if(isset($request->status))
        {
            $orders = $orders->whereIn('pay', $request->status);
        }
        $start = isset($request->start) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->start))) : date('Y-m-d', strtotime(date('Y').'-'.date('m').'-01'));
        $end   = isset($request->end) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->end))) : date('Y-m-d');;
        $orders = $orders->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        $orders = $orders->orderBy('order_number', 'DESC')->get();
        return view('painel.comercial.indexPedidoServico', get_defined_vars());
    }

    public function indexVerPedidoServico($order_number)
    {
        $order = OrderService::with('user.adresses', 'seller', 'serviceReservation')->where('order_number', $order_number)->first();

        return view('painel.comercial.indexPedidoVerServico', get_defined_vars());
    }

    public function alterarStatusOrder(Request $request)
    {
        $order = Order::where('order_number', $request->order_number)->update(['pay' => $request->status]);
        if($request->status == 2){
            Mail::to(Order::where('order_number', $request->order_number)->first()->user_email)->send(new OrderFinish(Order::where('order_number', $request->order_number)->first()));
        }
        if($request->status == 1){
            Mail::to(Order::where('order_number', $request->order_number)->first()->user_email)->send(new OrderPayment(Order::where('order_number', $request->order_number)->first(), 'paid', 'comprador'));
        }
        return response()->json('success', 200);
    }

    public function alterarStatusOrderService(Request $request)
    {
        $order = OrderService::where('order_number', $request->order_number)->update(['pay' => $request->status]);
        if($request->status == 2){
            Mail::to(OrderService::where('order_number', $request->order_number)->first()->user_email)->send(new OrderServiceFinish(OrderService::where('order_number', $request->order_number)->first()));
        }
        if($request->status == 1){
            Mail::to(OrderService::where('order_number', $request->order_number)->first()->user_email)->send(new OrderServicePayment($order, 'paid', 'comprador'));
        }
        return response()->json('success', 200);
    }

    public function notificarVendedor(Request $request)
    {
        $order = Order::where('order_number', $request->order_number)->first();
        Mail::to($order->seller->email)->send(new NotificaVendedor($order));
        return response()->json('');
    }

    public function notificarVendedorService(Request $request)
    {
        $order = OrderService::where('order_number', $request->order_number)->first();
        Mail::to($order->seller->email)->send(new NotificaVendedor($order));
        return response()->json('');
    }

    public function verificarSolicitacaoCancelamento(Request $request)
    {
        $order = Order::where('order_number', $request->order_number)->first();
        $order_cancel = OrderRequestCancel::where('order_number', $request->order_number)->first();
        return response()->json(['order' => $order, 'order_cancel' => $order_cancel]);
    }

    public function verificarSolicitacaoCancelamentoServico(Request $request)
    {
        $order = OrderService::where('order_number', $request->order_number)->first();
        $order_cancel = OrderRequestCancel::where('order_number', 'S-'.$request->order_number)->first();
        return response()->json(['order' => $order, 'order_cancel' => $order_cancel]);
    }

    public function verificarSolicitacaoCancelamentoPlan(Request $request)
    {
        $order = SignedPlan::find($request->order_number);
        $order_cancel = OrderRequestCancel::where('order_number', 'ASS-'.$request->order_number)->first();
        return response()->json(['order' => $order, 'order_cancel' => $order_cancel]);
    }

    public function confirmarSolicitacaoCancelamento(Request $request)
    {
        $order = Order::where('order_number', $request->order_number)->first();
        $order_cancel = OrderRequestCancel::where('order_number', $request->order_number)->first();
        $pagarme = \Http::withHeaders(get_header_conf_pm())->get(url_pagarme('orders', '/'.$order->payment_id), [])->object();

        $split_rules_seller = collect($pagarme->charges[0]->last_transaction->split)->where('recipient_id', $order->seller->wallet_id)->map(function($query) use($order){
            return [
                'amount' => $query->amount,
                'recipient_id' => $query->recipient->id,
                "type" => "flat",
                "options" => [
                    'charge_processing_fee' => $query->options->charge_processing_fee,
                    'charge_remainder_fee' => $query->options->charge_remainder_fee,
                    'liable' => $query->options->liable,
                ]
            ];
        });

        $dados = [
            'amount' => valor_enviar_pagar_me($order->total_value),
            'split' => [],
            // 'split' => [
            //     [
            //         'amount' => valor_enviar_pagar_me(getValorBiguacu($order->product_value)),
            //         'recipient_id' => ENV('PAGARME_API_RECEBEDOR_ID2'),
            //         "type" => "flat",
            //         "options" => [
            //             'charge_processing_fee' => false,
            //             'charge_remainder_fee' => true,
            //             'liable' => true,
            //         ]
            //     ]
            // ],
        ];

        foreach($split_rules_seller as $srl){
            $dados['split'][] = $srl;
        }

        // if($order->payment_method == 'boleto'){
        //     $dados['bank_account'] = [
        //         'agencia' => $order_cancel->agencia,
        //         'agencia_dv' => $order_cancel->agencia_dv_id ?? 0,
        //         'bank_code' => $order->bank_code_id,
        //         'conta' => $order_cancel->conta_id,
        //         'conta_dv' => $order_cancel->conta_dv_id,
        //         'document_number' => $order_cancel->document_number_id,
        //         'legal_name' => $order_cancel->legal_name_id,
        //     ];
        // }

        $pagarme = \Http::withHeaders(get_header_conf_pm())->delete(url_pagarme('charges', '/'.$pagarme->charges[0]->id), [])->object();

        Order::where('order_number', $request->order_number)->update(['pay' => 11]);

        \Log::info(collect($pagarme));

        Mail::to($order->user_email)->send(new SendConfirmCancel('Produto - Número do Pedido '.$order->order_number, 'comprador'));
        Mail::to($order->seller->email)->send(new SendConfirmCancel('Produto - Número do Pedido '.$order->order_number, 'vendedor'));
        Mail::to('comercial@raeasy.com')->send(new SendConfirmCancel('Produto - Número do Pedido '.$order->order_number, 'raeasy'));
        return response()->json();
    }

    public function confirmarSolicitacaoCancelamentoServico(Request $request)
    {
        $order = OrderService::where('order_number', $request->order_number)->first();
        $order_cancel = OrderRequestCancel::where('order_number', 'S-'.$request->order_number)->first();
        $pagarme = \Http::withHeaders(get_header_conf_pm())->get(url_pagarme('orders', '/'.$order->payment_id), [])->object();

        $split_rules_seller = collect($pagarme->charges[0]->last_transaction->split)->where('recipient_id', $order->seller->wallet_id)->map(function($query) use($order){
            return [
                'amount' => $query->amount,
                'recipient_id' => $query->recipient->id,
                "type" => "flat",
                "options" => [
                    'charge_processing_fee' => $query->options->charge_processing_fee,
                    'charge_remainder_fee' => $query->options->charge_remainder_fee,
                    'liable' => $query->options->liable,
                ]
            ];
        });

        $dados = [
            'amount' => valor_enviar_pagar_me($order->total_value),
            'split' => [],
            // 'split' => [
            //     [
            //         'amount' => valor_enviar_pagar_me(getValorBiguacu($order->product_value)),
            //         'recipient_id' => ENV('PAGARME_API_RECEBEDOR_ID2'),
            //         "type" => "flat",
            //         "options" => [
            //             'charge_processing_fee' => false,
            //             'charge_remainder_fee' => true,
            //             'liable' => true,
            //         ]
            //     ]
            // ],
        ];

        foreach($split_rules_seller as $srl){
            $dados['split'][] = $srl;
        }

        // if($order->payment_method == 'boleto'){
        //     $dados['bank_account'] = [
        //         'agencia' => $order_cancel->agencia,
        //         'agencia_dv' => $order_cancel->agencia_dv_id ?? 0,
        //         'bank_code' => $order->bank_code_id,
        //         'conta' => $order_cancel->conta_id,
        //         'conta_dv' => $order_cancel->conta_dv_id,
        //         'document_number' => $order_cancel->document_number_id,
        //         'legal_name' => $order_cancel->legal_name_id,
        //     ];
        // }

        // $pagarme = Http::withHeaders(['Accept' => 'application/json'])->post('https://api.pagar.me/1/transactions/'.$order->payment_id.'/refund/', $dados)->object();
        $pagarme = \Http::withHeaders(get_header_conf_pm())->delete(url_pagarme('charges', '/'.$pagarme->charges[0]->id), [])->object();

        OrderService::where('order_number', $request->order_number)->update(['pay' => 11]);
        OrderRequestCancel::where('order_number', 'S-'.$request->order_number)->update(['status' => 'N']);

        \Log::info(collect($pagarme));

        Mail::to($order->user_email)->send(new SendConfirmCancel('Serviço - Número do Pedido '.$order->order_number, 'comprador'));
        Mail::to($order->seller->email)->send(new SendConfirmCancel('Serviço - Número do Pedido '.$order->order_number, 'vendedor'));
        Mail::to('comercial@raeasy.com')->send(new SendConfirmCancel('Serviço - Número do Pedido '.$order->order_number, 'biguacu'));
        return response()->json();
    }

    public function confirmarSolicitacaoCancelamentoPlan(Request $request)
    {
        $order = SignedPlan::find($request->order_number);
        $order_cancel = OrderRequestCancel::where('order_number', 'ASS-'.$request->order_number)->first();
        \Http::withHeaders(get_header_conf_pm())->delete(url_pagarme('subscriptions', '/'.$order->pagarme_id), [])->object();

        SignedPlan::find($request->order_number)->update(['status' => 5]);

        // Mail::to($order->user_email)->send(new SendConfirmCancel('Serviço - Número do Pedido '.$order->order_number, 'comprador'));
        // Mail::to($order->seller->email)->send(new SendConfirmCancel('Serviço - Número do Pedido '.$order->order_number, 'vendedor'));
        // Mail::to('comercial@raeasy.com')->send(new SendConfirmCancel('Serviço - Número do Pedido '.$order->order_number, 'biguacu'));
        return response()->json();
    }

    public function orderAnexarFiscal(Request $request)
    {
        $originalPath = storage_path('app/public/orders/order_'.$request->order_id.'/');
        if (!file_exists($originalPath)) {
            mkdir($originalPath, 0777, true);
        }
        $ext = explode('.', $request->path_fiscal->getClientOriginalName());
        $ext = $ext[count($ext) - 1];
        $save_foto = $request->path_fiscal->storeAs('public/orders/order_'.$request->order_id, \Str::slug($request->path_fiscal->getClientOriginalName()).'.'.$ext);
        $path_fiscal['path_fiscal'] = str_replace('public/','',$save_foto);
        $path_fiscal['url_fiscal'] = asset('storage/'.str_replace('public/','',$save_foto));
        Order::find($request->order_id)->update($path_fiscal);
        return response()->json($path_fiscal['url_fiscal'], 200);
    }

    public function orderDesanexarFiscal(Request $request)
    {
        $order = Order::find($request->order_id);
        Storage::delete('public/'.$order->path_fiscal);
        $order->update(['path_fiscal' => null, 'url_fiscal' => null]);
        return response()->json('okay', 200);
    }

    public function orderServiceAnexarFiscal(Request $request)
    {
        $originalPath = storage_path('app/public/orders/order_service_'.$request->order_id.'/');
        if (!file_exists($originalPath)) {
            mkdir($originalPath, 0777, true);
        }
        $ext = explode('.', $request->path_fiscal->getClientOriginalName());
        $ext = $ext[count($ext) - 1];
        $save_foto = $request->path_fiscal->storeAs('public/orders/order_service_'.$request->order_id, \Str::slug($request->path_fiscal->getClientOriginalName()).'.'.$ext);
        $path_fiscal['path_fiscal'] = str_replace('public/','',$save_foto);
        $path_fiscal['url_fiscal'] = asset('storage/'.str_replace('public/','',$save_foto));
        OrderService::find($request->order_id)->update($path_fiscal);
        return response()->json($path_fiscal['url_fiscal'], 200);
    }

    public function orderServiceDesanexarFiscal(Request $request)
    {
        $order = OrderService::find($request->order_id);
        Storage::delete('public/'.$order->path_fiscal);
        $order->update(['path_fiscal' => null, 'url_fiscal' => null]);
        return response()->json('okay', 200);
    }

    public function codigoAdd(Request $request)
    {
        $url_tracking = str_replace('{code}', $request->codigo,$request->rastreio_url);
        $url_tracking = 'code='.$request->codigo.';'.'link='.$url_tracking;
        if($request->rastreio_url == 'proprio') $url_tracking = 'code='.$request->codigo.';'.'link='.($request->link??'');

        $shipping_customer = ShippingCustomer::where('order_number', $request->order_number)->update(['tracking_id' => $url_tracking]);

        Mail::to(Order::where('order_number', $request->order_number)->first()->user_email)->send(new OrderShipping(Order::with('orderParentInverse')->where('order_number', $request->order_number)->first(), $url_tracking, $request->rastreio_url == 'proprio' ? 'envio_proprio' : 'envio_url'));
        Mail::to(Order::where('order_number', $request->order_number)->first()->seller->email)->send(new OrderNotifySellerShipping());

        return response()->json('success', 200);
    }

    public function codigoRemove(Request $request)
    {
        ShippingCustomer::where('order_number', $request->id)->update(['tracking_id' => null]);
        return response()->json('success', 200);
    }

    public function impressaoPedido($order_number)
    {
        $order = Order::with('user', 'seller.store', 'shippingCustomer', 'orderProducts', 'customerAddress')->where('order_number', $order_number)->first();
        
        return view('painel.comercial.impressaoPedido', get_defined_vars());
    }

    public function impressaoPedidoServico($order_number)
    {
        $order = OrderService::with('user', 'seller.store', 'serviceReservation.customerAddress')->where('order_number', $order_number)->first();

        return view('painel.comercial.impressaoPedido', get_defined_vars());
    }

    public function indexAssinatura(Request $request){
        $assinaturas = SignedPlan::with('user', 'seller');
        if(isset($request->status))
        {
            $assinaturas = $assinaturas->whereIn('status', $request->status);
        }
        $start = isset($request->start) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->start))) : date('Y-m-d', strtotime(date('Y').'-'.date('m').'-01'));
        $end   = isset($request->end) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->end))) : date('Y-m-d');;
        $assinaturas = $assinaturas->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        $assinaturas = $assinaturas->orderBy('id', 'DESC')->get();

        return view('painel.comercial.indexAssinatura', get_defined_vars());
    }

    public function indexAssinaturaDetalhe($id)
    {
        $assinatura = SignedPlan::with('user', 'seller.store')->where('id', $id)->first();
        if(!$assinatura)
        {
            return redirect()->route('assinaturas');
        }

        $seo = json_decode(json_encode([
            'title' => 'Assinatura - '.$assinatura->plan_title
        ]));

        return view('painel.comercial.indexAssinaturaDetalhe', get_defined_vars());
    }
}
