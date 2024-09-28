<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Mail\OrderFinish;
use App\Mail\OrderPayment;

use App\Models\SignedPlan;
use App\Mail\OrderShipping;
use App\Models\OrderProduct;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Mail\OrderServiceFinish;
use App\Models\ShippingCustomer;
use App\Mail\OrderServicePayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\OrderNotifySellerShipping;

class PedidoSellerController extends Controller
{
    public function indexPedido(Request $request)
    {
        $orders = Order::with('seller')->where('seller_id', auth()->guard('seller')->user()->id);
        if(isset($request->status))
        {
            $orders = $orders->whereIn('pay', $request->status);
        }
        $start = isset($request->start) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->start))) : date('Y-m-d', strtotime(date('Y').'-'.date('m').'-01'));
        $end   = isset($request->end) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->end))) : date('Y-m-d');;
        $orders = $orders->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        $orders = $orders->orderBy('order_number', 'DESC')->get();

        return view('seller.comercial.indexPedido', get_defined_vars());
    }

    public function indexVerPedido($order_number)
    {
        $order = Order::with('user', 'seller', 'shippingCustomer', 'orderProducts')->where('seller_id', auth()->guard('seller')->user()->id)->where('order_number', $order_number)->first();

        if($order->status_v == 0) Order::where('seller_id', auth()->guard('seller')->user()->id)->where('order_number', $order_number)->update(['status_v' => 1]);

        return view('seller.comercial.indexPedidoVer', compact('order'));
    }

    public function indexPedidoServico(Request $request)
    {
        $orders = OrderService::with('seller')->where('seller_id', auth()->guard('seller')->user()->id);
        if(isset($request->status))
        {
            $orders = $orders->whereIn('pay', $request->status);
        }
        $start = isset($request->start) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->start))) : date('Y-m-d', strtotime(date('Y').'-'.date('m').'-01'));
        $end   = isset($request->end) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->end))) : date('Y-m-d');;
        $orders = $orders->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        $orders = $orders->orderBy('order_number', 'DESC')->get();

        return view('seller.comercial.indexPedidoServico', get_defined_vars());
    }

    public function indexVerPedidoServico($order_number)
    {
        $order = OrderService::with('user.adresses', 'seller', 'serviceReservation')->where('seller_id', auth()->guard('seller')->user()->id)->where('order_number', $order_number)->first();

        return view('seller.comercial.indexPedidoVerServico', get_defined_vars());
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

    public function alterarStatusOrder(Request $request)
    {
        $order = Order::where('order_number', $request->order_number)->update(['pay' => $request->status]);
        if($request->status == 2){
            Mail::to(Order::where('order_number', $request->order_number)->first()->user_email)->send(new OrderFinish(Order::where('order_number', $request->order_number)->first()));
        }
        if($request->status == 1){
            Mail::to(Order::where('order_number', $request->order_number)->first()->user_email)->send(new OrderPayment($order, 'paid', 'comprador'));
        }
        return response()->json('success', 200);
    }

    public function alterarStatusOrderService(Request $request)
    {
        $order = OrderService::where('order_number', $request->order_number)->update(['pay' => $request->status]);
        if($request->status == 2){
            Mail::to(OrderService::where('order_number', $request->order_number)->first()->user_email)->send(new OrderServiceFinish(OrderService::where('order_number', $request->order_number)->first()));
        }
        if(!empty($order->user_email)){
            if($request->status == 1){
                Mail::to($order->user_email)->send(new OrderServicePayment($order, 'paid', 'comprador'));
            }
        }
        return response()->json('success', 200);
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

    public function impressaoPedido($order_number)
    {
        $order = Order::where('order_number', $order_number)->where('seller_id', auth()->guard('seller')->user()->id)->first();

        return view('painel.comercial.impressaoPedido', get_defined_vars());
    }

    public function impressaoPedidoServico($order_number)
    {
        $order = OrderService::where('order_number', $order_number)->where('seller_id', auth()->guard('seller')->user()->id)->first();

        return view('painel.comercial.impressaoPedido', get_defined_vars());
    }

    public function indexAssinatura(Request $request){
        $assinaturas = SignedPlan::with('user', 'seller')->where('seller_id', auth()->guard('seller')->user()->id);
        if(isset($request->status))
        {
            $assinaturas = $assinaturas->whereIn('status', $request->status);
        }
        $start = isset($request->start) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->start))) : date('Y-m-d', strtotime(date('Y').'-'.date('m').'-01'));
        $end   = isset($request->end) ? date('Y-m-d', strtotime(str_replace('/', '-', $request->end))) : date('Y-m-d');;
        $assinaturas = $assinaturas->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end);
        $assinaturas = $assinaturas->orderBy('id', 'DESC')->get();

        return view('seller.comercial.indexAssinatura', get_defined_vars());
    }

    public function indexAssinaturaDetalhe($id)
    {
        $assinatura = SignedPlan::with('user', 'seller.store')->where('id', $id)->where('seller_id', auth()->guard('seller')->user()->id)->first();
        if(!$assinatura)
        {
            return redirect()->route('seller.assinaturas');
        }

        $seo = json_decode(json_encode([
            'title' => 'Assinatura - '.$assinatura->plan_title
        ]));

        return view('seller.comercial.indexAssinaturaDetalhe', get_defined_vars());
    }
}
