<?php

namespace App\Http\Controllers\Seller;

use App\Models\User;
use App\Models\Store;

use App\Models\Seller;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\ApiIntegration;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class StoreController extends Controller
{
    public function atualizarLoja(Request $request)
    {
        $request->validate([
            'store_name'    => 'required',
            'post_code'     => 'required',
            'address'       => 'required',
            'number'        => 'required',
            'address2'      => 'required',
            'state'         => 'required',
            'city'          => 'required',
            'phone2'        => 'required',
        ]);

        $store['user_id']       = auth('seller')->user()->id;
        $store['store_name']    = $request->store_name;
        $store['post_code']     = $request->post_code;
        $store['address']       = $request->address;
        $store['number']        = $request->number;
        $store['complement']    = $request->complement;
        $store['address2']      = $request->address2;
        $store['state']         = $request->state;
        $store['city']          = $request->city;
        $store['phone1']        = $request->phone1;
        $store['phone2']        = $request->phone2;
        $store['lat']           = $request->latitude ?? null;
        $store['lng']           = $request->longitude ?? null;
        $store['title']         = $request->store_name;

        $store['retirada'] = 'false';
        $store['ob_retirada'] = null;

        if($request->id){
            Store::find($request->id)->update($store);
        }else{
            Store::create($store);
        }

        return response()->json();
    }

    public function atualizarLojaLogoBanner(Request $request)
    {
        $originalPath = storage_path('app/public/salemans'.auth('seller')->user()->id.'/logo_banner/');
        if (!file_exists($originalPath)) mkdir($originalPath, 0777, true);

        if($request->logo_path){
            $file_path = Store::find($request->store_id)->first();
            if($file_path){
                \Storage::delete('public/'.$file_path->logo_path);
            }

            // $width_max = 260;
            // $height_max = 260;

            // list($width_orig, $height_orig) = getimagesize($request->logo_path);
            // $ratio_orig = $width_orig/$height_orig;
            // if ($width_max/$height_max > $ratio_orig) {
            //     $width_max = $height_max*$ratio_orig; //----
            // } else {
            //     $height_max = $width_max/$ratio_orig; //----
            // }
            // $logo_path = Image::make($request->logo_path)->resize($width_max, $height_max);
            $logo_path = Image::make($request->logo_path);
            $logo_path_name = Str::random().'.'.$request->logo_path->extension();
            $logo_path->save($originalPath.$logo_path_name);

            $store['logo_path'] = 'salemans'.auth('seller')->user()->id.'/logo_banner/'.$logo_path_name;
        }
        if($request->banner_path){
            $file_path = Store::find($request->store_id)->first();
            if($file_path){
                \Storage::delete('public/'.$file_path->banner_path);
            }

            // $width_max = 580;
            // $height_max = 260;

            // list($width_orig, $height_orig) = getimagesize($request->banner_path);
            // $ratio_orig = $width_orig/$height_orig;
            // if ($width_max/$height_max > $ratio_orig) {
            //     $width_max = $height_max*$ratio_orig; //----
            // } else {
            //     $height_max = $width_max/$ratio_orig; //----
            // }
            // $banner_path = Image::make($request->banner_path)->resize($width_max, $height_max);
            $banner_path = Image::make($request->banner_path);
            $banner_path_name = Str::random().'.'.$request->banner_path->extension();
            $banner_path->save($originalPath.$banner_path_name);

            $store['banner_path'] = 'salemans'.auth('seller')->user()->id.'/logo_banner/'.$banner_path_name;
        }

        Store::find($request->store_id)->update($store);
    }

    // Conta de Recebiemnto
        ###### Asaas ######
            public function dataAsaas()
            {
                $apiAsaas = ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'CHAVE-API-ASAAS')->first();
                if ($apiAsaas) {
                    $balance = \Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'access_token' => $apiAsaas->token,
                    ])->get("{$this->url_asaas}/finance/balance")->object();

                    $splits = \Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'access_token' => $apiAsaas->token,
                    ])->get("{$this->url_asaas}/payments/splits/received")->object();
                }

                $loja = Store::where('user_id', auth('seller')->user()->id)->first();
                return view('seller.indexContaAsaas', get_defined_vars());
            }

            public function dataUpdateWalletId(Request $request)
            {
                $seller = Seller::where('id', auth('seller')->user()->id)->first();
                $seller->update(['wallet_id' => $request->wallet_id]);
                return response()->json(['status' => 'success']);
            }

            public function dataUpdateChaveApi(Request $request)
            {
                $apiAsaas = ApiIntegration::where('user_id', auth('seller')->user()->id)->where('api_name', 'CHAVE-API-ASAAS')->first();
                if($apiAsaas){
                    $apiAsaas->update(['token' => $request->chave_api]);
                }else{
                    ApiIntegration::create([
                        'user_id' => auth('seller')->user()->id,
                        'api_name' => 'CHAVE-API-ASAAS',
                        'token' => $request->chave_api,
                    ]);
                }
                return response()->json(['status' => 'success']);
            }
        #####################
    // ---------------------
}
