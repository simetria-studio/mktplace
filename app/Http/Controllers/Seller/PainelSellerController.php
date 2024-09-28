<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Models\Store;
use App\Mail\CodeInfo;
use App\Models\Service;

use App\Models\Seller;
use App\Models\Attribute;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\SellerAddress;
use App\Models\ViewProductsService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class PainelSellerController extends Controller
{
    public function indexDashboard()
    {
        return view('seller.indexDashboard');
    }

    public function indexPerfil()
    {
        return view('seller.indexPerfil');
    }

    public function indexEnderecos()
    {
        $addresses = SellerAddress::where('user_id', auth()->guard('seller')->user()->id)->get();

        return view('seller.indexEnderecos', compact('addresses'));
    }

    public function indexLoja()
    {
        $seller = Seller::with(['store'])->find(auth()->guard('seller')->user()->id);
        return view('seller.indexLoja', compact('seller'));
    }

    public function envCodeDelete()
    {
        $code_delete = \Str::random(8);
        Seller::find(auth()->guard('seller')->user()->id)->update(['code_delete' => $code_delete]);

        Mail::to(auth()->guard('seller')->user()->email)->send(new CodeInfo(Seller::find(auth()->guard('seller')->user()->id), 'env_code'));

        return response()->json();
    }

    public function confirmCodeDelete(Request $request)
    {
        if(Seller::where('id', auth()->guard('seller')->user()->id)->where('code_delete', $request->code_delete)->count() == 0){
            return response()->json(['error_msg' => 'Codigo invalido!'],412);
        }

        $Seller = Seller::find(auth()->guard('seller')->user()->id);

        Seller::find($Seller->id)->delete();
        SellerAddress::where('user_id', $Seller->id)->delete();
        Store::where('user_id', $Seller->id)->delete();
        ApiMelhorEnvio::where('seller_id', $Seller->id)->delete();

        Mail::to($Seller->email)->send(new CodeInfo($Seller, 'delete_account'));

        return response()->json();
    }

    public function buscaAddressSeller(Request $request)
    {
        $data = Seller::find($request->seller_id)->store;

        return response()->json($data ?? false);
    }
}
