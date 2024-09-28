<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Produto;
use App\Models\Service;
use App\Models\Seller;
use App\Models\CouponSeller;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $perPage = $_GET['per_page_c'] ?? 20;
        $coupons = Coupon::with('sellers')->whereHas('sellers',function($query) {
            if(auth()->guard('seller')->check()) return $query->where('seller_id', auth()->guard('seller')->user()->id);
        });
        if(isset($_GET['search_c'])) $coupons = $coupons->where('code_coupon', 'like', '%'.$_GET['search_c'].'%');
        if(isset($_GET['status_c'])){
            if($_GET['status_c'] !== 'todos'){
                $coupons = $coupons->where('status',($_GET['status_c'] == 'ativo' ? 1 : 0));
            }
        }
        $coupons = $coupons->paginate($perPage, $columns = ['*'], $pageName = 'coupons');

        $vendedores = Seller::all();
        if(auth()->guard('seller')->check()){
            $products = Produto::where('seller_id', auth()->guard('seller')->user()->id)->with('seller')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->get();
            $services = Service::where('seller_id', auth()->guard('seller')->user()->id)->with('seller')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->get();
        }else{
            $products = Produto::with('seller')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->get();
            $services = Service::with('seller')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->get();
        }

        return view('painel.cadastros.indexCupom', get_defined_vars());
    }

    public function store(Request $request)
    {
        if(empty($request->code_coupon)){
            return response()->json(['errors' => ['code_coupon' => ['O campo Codigo do Cupom é obrigatorio!']]], 412);
        }
        if(Coupon::where('code_coupon', mb_convert_case($request->code_coupon, MB_CASE_UPPER))->get()->count() > 0){
            return response()->json(['errors' => ['code_coupon' => ['O campo Codigo do Cupom já existe!']]], 412);
        }
        $data = $this->montaArrayC($request);
        $coupon = Coupon::create($data);

        foreach($request->seller_id as $seller_id){
            $data_seller = [
                'coupon_id' => $coupon->id,
                'seller_id' => $seller_id,
                'check_loja' => isset($request->check_loja[$seller_id]) ? 'true' : 'false',
                'product_id' => $request->product_id[$seller_id] ?? null,
                'service_id' => $request->service_id[$seller_id] ?? null,
            ];

            CouponSeller::create($data_seller);
        }

        return response()->json('okay', 200);
    }

    public function ativo(Request $request)
    {
        Coupon::find($request->id)->update(['status' => $request->ativo == 'N' ? 0 : 1]);
        return response()->json('');
    }

    public function show($id)
    {
        $coupon = Coupon::with(['sellers'])->find($id);
        return response()->json($coupon);
    }

    public function edit(Request $request)
    {
        $data = $this->montaArrayC($request);
        Coupon::find($request->id)->update($data);

        $coupon_seller_ids = collect();
        foreach($request->seller_id as $seller_id){
            if(CouponSeller::where('coupon_id', $request->id)->where('seller_id', $seller_id)->get()->count() > 0){
                $coupon_seller_ids->Add($seller_id);
                $data_seller = [
                    'check_loja' => isset($request->check_loja[$seller_id]) ? 'true' : 'false',
                    'product_id' => $request->product_id[$seller_id] ?? null,
                    'service_id' => $request->service_id[$seller_id] ?? null,
                ];
                CouponSeller::where('coupon_id', $request->id)->where('seller_id', $seller_id)->update($data_seller);
            }else{
                $coupon_seller_ids->Add($seller_id);
                $data_seller = [
                    'coupon_id' => $request->id,
                    'seller_id' => $seller_id,
                    'check_loja' => isset($request->check_loja[$seller_id]) ? 'true' : 'false',
                    'product_id' => $request->product_id[$seller_id] ?? null,
                    'service_id' => $request->service_id[$seller_id] ?? null,
                ];
                CouponSeller::create($data_seller);
            }
        }
        CouponSeller::where('coupon_id', $request->id)->whereNotIn('seller_id', $coupon_seller_ids)->delete();

        return response()->json('okay', 200);
    }

    public function destroy(Request $request)
    {
        if(auth()->guard('seller')->check()){
            Coupon::where('id',$request->id)->where('fee', 'seller')->delete();
            CouponSeller::where('seller_id', auth()->guard('seller')->user()->id)->where('coupon_id',$request->id)->delete();
        }else{
            Coupon::find($request->id)->delete();
            CouponSeller::where('coupon_id',$request->id)->delete();
        }
        return response()->json('okay', 200);
    }

    public function montaArrayC($request)
    {
        if(empty($request->id)){
            $data['fee'] = auth()->guard('seller')->check() ? 'seller' : 'admin';
            $data['code_coupon'] = mb_convert_case($request->code_coupon, MB_CASE_UPPER);
        }
        $data['coupon_valid'] = $request->coupon_valid;
        $data['discount_config'] = $request->discount_config;
        $data['value_discount'] = str_replace(['.',','],['','.'], $request->value_discount);
        $data['value_min'] = str_replace(['.',','],['','.'], $request->value_min);
        $data['value_max'] = str_replace(['.',','],['','.'], $request->value_max);

        return $data;
    }

    public function aplicarCupom(Request $request)
    {
        $coupons = collect();
        $items_cart = session()->get('session_cart');
        $soma_valores = collect();
        $erros = collect();

        $coupon = Coupon::with('sellers')->where('code_coupon', $request->code_coupon)->where('status', 1)->first();
        if(empty($coupon)) return response()->json(['statusText' => 'Cupom não encontrado!'],200);

        collect($items_cart)->map(function($query)use($coupon,$coupons,$soma_valores){
            $query = json_decode(json_encode($query));
            $coupon_seller = $coupon->sellers->where('seller_id', $query->attributes->seller_id)->first();
            if($coupon_seller){
                $soma_valores->add([
                    'seller_id' => $query->attributes->seller_id,
                    'value' => ($query->quantity*$query->price)
                ]);
                if($coupon->coupon_valid == 'product_discount'){
                    if($coupon_seller->check_loja == 'true'){
                        if($coupons->where('seller_id', $query->attributes->seller_id)->count() == 0){
                            $coupons->add([
                                'seller_id' => $query->attributes->seller_id,
                                'code_coupon' => $coupon->code_coupon,
                                'coupon_valid' => $coupon->coupon_valid,
                                'check_loja' => true,
                                'coupon_id' => $coupon->id,
                                'fee' => $coupon->fee,
                                'discount_config' => $coupon->discount_config,
                                'value_discount' => $coupon->value_discount,
                            ]);
                        }
                    }else{
                        if($coupons->where('seller_id', $query->attributes->seller_id)->count() == 0){
                            if(in_array($query->attributes->product_id,$coupon_seller->product_id)){
                                $coupons->add([
                                    'seller_id' => $query->attributes->seller_id,
                                    'code_coupon' => $coupon->code_coupon,
                                    'coupon_valid' => $coupon->coupon_valid,
                                    'check_loja' => false,
                                    'product_id' => $coupon_seller->product_id,
                                    'fee' => $coupon->fee,
                                    'discount_config' => $coupon->discount_config,
                                    'value_discount' => $coupon->value_discount,
                                ]);
                            }
                        }
                    }
                }else{
                    if($coupons->where('seller_id', $query->attributes->seller_id)->count() == 0){
                        $coupons->add([
                            'seller_id' => $query->attributes->seller_id,
                            'code_coupon' => $coupon->code_coupon,
                            'coupon_valid' => $coupon->coupon_valid,
                            'check_loja' => $coupon_seller->check_loja == 'true' ? true : false,
                            'fee' => $coupon->fee,
                            'discount_config' => $coupon->discount_config,
                            'value_discount' => $coupon->value_discount,
                        ]);
                    }
                }
            }
        });

        $soma_valores->mapToGroups(function($query){
            return [$query['seller_id'] => $query['value']];
        })->map(function($item, $key) use($coupon,$coupons,$erros){
            $total_pedido_vendedor = collect($item)->sum();
            if($coupon->value_min > 0){
                if($coupon->value_min > $total_pedido_vendedor){
                    $coupons->forget($coupons->where('seller_id', $key)->keys()->toArray());
                    $erros->add('Valor minimo não atingido');
                }
            }
            if($coupon->value_max > 0){
                if($coupon->value_max < $total_pedido_vendedor){
                    $coupons->forget($coupons->where('seller_id', $key)->keys()->toArray());
                    $erros->add('Valor maximo ultrapassado');
                }
            }
        });

        if($erros->count() > 0) return response()->json(['statusText' => $erros->toArray()[0]],200);

        if($coupons->count() == 0) return response()->json(['statusText' => 'Cupom não validos para os vendedores!'],200);

        session(['session_coupons' => $coupons->toArray()[0]]);

        // \Log::info(calcCoupon());

        return response()->json([
            'success' => true,
            'statusText' => 'Cupom aplicado com sucesso!',
            'coupon' => calcCoupon(),
            'seller_id' => $coupons[0]['seller_id'],
        ],200);
    }

    // public function aplicarCupomService(Request $request)
    // {
    //     $coupons = collect();
    //     $items_cart = json_decode(json_encode(session()->get('cart_session')));
    //     $soma_valores = $items_cart->price;

    //     $coupon = Coupon::with('sellers')->where('code_coupon', $request->code_coupon)->where('status', 1)->first();
    //     if(empty($coupon)) return response()->json(['error' => ['icon' => 'error', 'msg' => 'Cupom não encontrado!']],412);

    //     $coupon_seller = $coupon->sellers->where('seller_id', $items_cart->attributes->seller_id)->first();
    //     if($coupon_seller){
    //         if($coupon->coupon_valid == 'product_discount'){
    //             if($coupon_seller->check_loja == 'true'){
    //                 if($coupons->where('seller_id', $items_cart->attributes->seller_id)->count() == 0){
    //                     $coupons->add([
    //                         'seller_id' => $items_cart->attributes->seller_id,
    //                         'code_coupon' => $coupon->code_coupon,
    //                         'coupon_valid' => $coupon->coupon_valid,
    //                         'check_loja' => true,
    //                         'coupon_id' => $coupon->id,
    //                         'fee' => $coupon->fee,
    //                         'discount_config' => $coupon->discount_config,
    //                         'value_discount' => $coupon->value_discount,
    //                     ]);
    //                 }
    //             }else{
    //                 if($coupons->where('seller_id', $items_cart->attributes->seller_id)->count() == 0){
    //                     if(in_array($items_cart->attributes->service_id,$coupon_seller->service_id)){
    //                         $coupons->add([
    //                             'seller_id' => $items_cart->attributes->seller_id,
    //                             'code_coupon' => $coupon->code_coupon,
    //                             'coupon_valid' => $coupon->coupon_valid,
    //                             'check_loja' => false,
    //                             'service_id' => $coupon_seller->service_id,
    //                             'fee' => $coupon->fee,
    //                             'discount_config' => $coupon->discount_config,
    //                             'value_discount' => $coupon->value_discount,
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     if($coupon->value_min > 0){
    //         if($coupon->value_min > $soma_valores){
    //             $coupons->forget($coupons->where('seller_id', $key)->keys()->toArray());
    //         }
    //     }
    //     if($coupon->value_max > 0){
    //         if($coupon->value_max < $soma_valores){
    //             $coupons->forget($coupons->where('seller_id', $key)->keys()->toArray());
    //         }
    //     }

    //     if($coupons->count() == 0) return response()->json(['error' => ['icon' => 'error', 'msg' => 'Cupom não validos para os vendedores!']],412);

    //     // \Log::info($coupons->toArray());
    //     session(['coupons_service' => $coupons->toArray()]);
    // }
}
