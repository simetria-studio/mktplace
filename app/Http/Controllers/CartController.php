<?php

namespace App\Http\Controllers;

use Cart;

use App\Models\Produto;
use App\Models\PlanPurchase;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use App\Models\Cart as CartModel;
use App\Models\VariationsProduto as VP;

class CartController extends Controller
{
    public function indexCart()
    {
        $seo = json_decode(json_encode([
            'title' => 'Carrinho'
        ]));

        session()->forget(['frete', 'zip_code', 'cart_qty']);
        session()->forget(['session_cart', 'session_modalidades', 'session_address', 'field_card', 'session_coupons']);

        return view('site.indexCart', get_defined_vars());
    }

    public function addCart(Request $request)
    {
        $product = Produto::find($request->product_id);
        $progressiveDiscount = $product->progressiveDiscount;
        if($product->stock_controller == 'true'){
            if($product->stock){
                if($product->stock < $request->quantidade){
                    return redirect()->back()->with('error', 'Quantidade indisponível para solicitação!');
                }
            }elseif(!empty($request->atributo_valor)){
                $variation = json_decode($request->variacao[implode('-', $request->atributo_valor)]);
                $variation = $product->variations->where('id', $variation->var_id)->first();
                if($variation->stock < $request->quantidade){
                    return redirect()->back()->with('error', 'Quantidade indisponível para solicitação!');
                }
            }
        }

        if(!empty($request->atributo_valor)){
            $selected_attribute = [];
            $variation = json_decode($request->variacao[implode('-', $request->atributo_valor)]);
            foreach($request->atributo_valor as $atributo_valor){
                $selected_attribute[] = json_decode($request->atributo[$atributo_valor]);
            }
        }

        if(isset($variation->preco)){
            $valor_cart_p = $variation->preco;
        }elseif(isset($request->product_price)){
            $valor_cart_p = ($product->preco == $request->product_price) ? $request->product_price : $product->preco;
        }

        if($progressiveDiscount->count() > 0){
            $progressiveDiscount = $progressiveDiscount->filter(function($query) use($request){
                return $request->quantidade >= $query->discount_quantity;
            })->last();

            if($progressiveDiscount) $valor_cart_p = $progressiveDiscount->discount_value;
        }

        if(isset($variation->var_id)){
            if(isset(VP::with('progressiveDiscount')->find($variation->var_id)->progressiveDiscount)){
                if(VP::with('progressiveDiscount')->find($variation->var_id)->progressiveDiscount->count() > 0){
                    $progressiveDiscount = VP::with('progressiveDiscount')->find($variation->var_id)->progressiveDiscount->filter(function($query) use($request){
                        return $request->quantidade >= $query->discount_quantity;
                    })->last();

                    if($progressiveDiscount) $valor_cart_p = $progressiveDiscount->discount_value;
                }
            }
        }

        $cart['id']         = (string)\Str::uuid();
        // $cart['id']         = $request->product_id;
        $cart['name']       = $request->product_name;
        $cart['price']      = $valor_cart_p;
        $cart['quantity']   = $request->quantidade;
        $cart['attributes'] = [
            'product_id'            => $request->product_id,
            'affiliate_code'        => $request->affiliate_code,
            'var_id'                => $variation->var_id ?? null,
            'atributo_valor'        => isset($request->atributo_valor) ? implode('-', $request->atributo_valor) : null,
            'seller_id'           => $request->seller_id,
            'product_image'         => $request->product_image,
            'seller_name'         => $request->seller_name,
            'product_weight'        => $variation->peso ?? $request->product_weight,
            'product_width'         => $variation->dimensoes_L ?? $request->product_width,
            'product_height'        => $variation->dimensoes_A ?? $request->product_height,
            'product_length'        => $variation->dimensoes_C ?? $request->product_length,
            'selected_attribute'    => $selected_attribute ?? null,
            'progressive_discount'  => $progressiveDiscount ?? null,
        ];

        if(auth()->check()){
            unset($cart['id']);
            // $cart['row_id'] = ($row_id+1);
            $cart['row_id'] = $request->product_id;
            $cart['user_id'] = auth()->user()->id;
            $cart['active'] = 'S';

            $cart_id = CartModel::where('user_id', auth()->user()->id)->get()->filter(function($query) use($cart){
                return $query->attributes['product_id'] == $cart['attributes']['product_id'] && $query->attributes['atributo_valor'] == $cart['attributes']['atributo_valor'];
            })->first()->id ?? null;

            if($cart_id){
                $cart_model = CartModel::find($cart_id);
                $cart_model->update(['quantity' => ($cart_model->quantity + $cart['quantity'])]);
            }else{
                CartModel::create($cart);
            }

        }else{
            $cart_id = collect(Cart::getContent())->filter(function($query) use($cart){
                return $query->attributes->product_id == $cart['attributes']['product_id'] && $query->attributes->atributo_valor == $cart['attributes']['atributo_valor'];
            })->keys()[0] ?? null;
            if($cart_id){
                $cart['id'] = $cart_id;
            }

            Cart::add($cart);
        }

        if($request->criar_sessao_cart ?? null){
            $request->row_id = [auth('web')->check() ? $cart['row_id'] : $cart['id']];
            $this->createSessionCart($request);

            return redirect()->route('checkout.modalidade');
        }

        // return redirect()->route('cart')->with('success', 'Produto adicionado ao carrinho com sucesso!');
        return redirect()->back()->with('success_alert', 'Produto adicionado ao carrinho com sucesso!');
    }

    public function addCartQty(Request $request)
    {
        $product = Produto::find($request->product_id);
        $progressiveDiscount = $product->progressiveDiscount ?? collect();
        if(isset($product->stock_controller)){
            if($product->stock_controller == 'true'){
                if($product->stock){
                    if($product->stock < $request->qty_new){
                        return response()->json(['error' => 'Quantidade indisponível para solicitação!'],412);
                    }
                }elseif($product->variations){
                    $variation = $product->variations->where('id', $request->var_id)->first();
                    if($variation->stock < $request->qty_new){
                        return response()->json(['error' => 'Quantidade indisponível para solicitação!'],412);
                    }
                }
            }
        }

        if(auth()->check()){
            $var_id = CartModel::find($request->row_id)->attributes['var_id'];
            $valor_cart_p = CartModel::find($request->row_id)->price;
        }else{
            $var_id = Cart::get($request->row_id)->attributes['var_id'];
            $valor_cart_p = Cart::get($request->row_id)->price;
        }

        if($progressiveDiscount->count() > 0){
            $progressiveDiscount = $progressiveDiscount->filter(function($query) use($request){
                return $request->qty_new >= $query->discount_quantity;
            })->last();

            if($progressiveDiscount){
                $valor_cart_p = $progressiveDiscount->discount_value;
            }else{
                $valor_cart_p = $product->preco;
            }
        }

        if(isset(VP::with('progressiveDiscount')->find($var_id)->progressiveDiscount)){
            if(VP::with('progressiveDiscount')->find($var_id)->progressiveDiscount->count() > 0){
                $progressiveDiscount = VP::with('progressiveDiscount')->find($var_id)->progressiveDiscount->filter(function($query) use($request){
                    return $request->qty_new >= $query->discount_quantity;
                })->last();

                if($progressiveDiscount){
                    $valor_cart_p = $progressiveDiscount->discount_value;
                }else{
                    $valor_cart_p = VP::with('progressiveDiscount')->find($var_id)->preco;
                }
            }
        }

        session()->forget(['frete', 'zip_code', 'cart_qty']);

        if(auth()->check()){
            $cart_model = CartModel::find($request->row_id);
            $cart_model->update(['quantity' => ($request->qty_new)]);
            if(isset($valor_cart_p)) $cart_model->update(['price' => ($valor_cart_p)]);

        }else{
            Cart::update($request->row_id, array(
                'price' => $valor_cart_p,
                'quantity' => array(
                    'relative' => false,
                    'value' => $request->qty_new
                ),
            ));
        }

        return response()->json(['new_value' => $valor_cart_p],200);
    }

    public function removeCart(Request $request)
    {
        if(auth()->check()){
            CartModel::where('user_id', auth()->user()->id)->where('row_id', $request->row_id)->delete();
        }else{
            Cart::remove($request->row_id);
        }
    }

    public function clearCart()
    {
        if(auth()->check()){
            CartModel::where('user_id', auth()->user()->id)->delete();
        }else{
            Cart::clear();
        }

        return redirect()->back();
    }

    public function cartSessionPlan(Request $request)
    {
        if(!empty($request->atributo_valor)){
            $selected_attribute = [];
            $variation = json_decode($request->variacao[implode('-', $request->atributo_valor)]);
            foreach($request->atributo_valor as $atributo_valor){
                $selected_attribute[] = json_decode($request->atributo[$atributo_valor]);
            }
        }

        $cart['product_id']         = $request->product_id;
        // $cart['id']         = $request->product_id;
        $cart['name']       = $request->product_name;
        $cart['attributes'] = [
            'product_id'            => $request->product_id,
            'var_id'                => $variation->var_id ?? null,
            'atributo_valor'        => isset($request->atributo_valor) ? implode('-', $request->atributo_valor) : null,
            'seller_id'           => $request->seller_id,
            'product_image'         => $request->product_image,
            'seller_name'         => $request->seller_name,
            'product_weight'        => $variation->peso ?? $request->product_weight,
            'product_width'         => $variation->dimensoes_L ?? $request->product_width,
            'product_height'        => $variation->dimensoes_A ?? $request->product_height,
            'product_length'        => $variation->dimensoes_C ?? $request->product_length,
            'selected_attribute'    => $selected_attribute ?? null,
        ];

        session(['cart_session_plan' => collect($cart)->merge(collect(PlanPurchase::find($request->plan_id))->forget(['created_at', 'updated_at'])->toArray())->toArray()]);
    }

    public function createSessionCart(Request $request)
    {
        $session_cart_temp = collect();
        foreach ($request->row_id as $row_id) {
            if(auth()->check()){
                $cart = CartModel::where('row_id', $row_id)->first();
            }else{
                $cart = collect(Cart::getContent()[$row_id]);
                $cart = $cart->merge(['row_id' => $row_id]);
            }

            $session_cart_temp->push($cart->toArray());
        }

        session(['session_cart' => $session_cart_temp]);
    }
}