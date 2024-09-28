<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Service;
use App\Models\Category;
use App\Models\SeoConfig;
use App\Models\AffiliatePs;
use Illuminate\Http\Request;
use App\Models\EventHomeRural;
use App\Models\ServiceFavorite;
use App\Models\ServiceVariantion;
use App\Models\ViewProductsService;

class IndexServiceController extends Controller
{
    public function indexRuralTourism()
    {
        $bg_color_event_array = ['#5cab8b', '#edb300', '#e28138', '#cc5813'];
        $event_home_a = EventHomeRural::where('status', 1)->orderBy('posicao')->get();
        $seo = SeoConfig::where('page', 'rsa-rural_tourism')->first();
        // $services = $this->getServices(collect(['page' => 40]));
        $category_type = '1';

        $services_html = Service::with(['images', 'variations', 'categories.category'])->inRandomOrder()->limit(4)->get()->map(function($service){
            $class = 'col-md-3';
            $service_gtag_type = ['Serviços Proximos', 'servico_proximo'];
            return view('components.singleService', get_defined_vars())->render();
        })->join('');

        return view('site.service.indexHomeRural', get_defined_vars());
    }

    public function indexCategory($slug)
    {
        $seo = Category::where('slug', $slug)->first();

        $skip = $_GET['skip'] ?? 0;
        $services = $this->getServices(collect(['skip' => [$skip, 20], 'category' => $slug, 'filters' => ($_GET['filters'] ?? null)]));

        if(isset($_GET['return'])){
            // return response()->json();
            return $services->map(function ($query){
                $class = 'col-md-4';
                return view('components.singleService', get_defined_vars())->render();
            })->join('');
        }
        return view('site.service.indexCategory', get_defined_vars());
    }

    public function indexSearch ()
    {
        $seo = SeoConfig::where('page', 'rsa-search')->first();
        $category_slug = isset($_GET['c']) ? $_GET['c'] : null;
        $services = $this->getServices(collect(['page' => 40, 'pesquisa' => $_GET['q'], 'category_slug' => $category_slug, 'filters' => ($_GET['filters'] ?? null)]));
        return view('site.service.indexBusca', get_defined_vars());
    }

    public function indexService($slug, $affiliate = null)
    {
        if($affiliate){
            $affiliateps = AffiliatePs::where('codigo', $affiliate)->where('status', 1)->first();
            if(empty($affiliateps)) return redirect()->route('service', $slug);
        }
        $service = Service::with(['images', 'seller', 'variations.variations', 'attrAttrs.attribute.variations'])->where('service_slug', $slug)->first();
        if(empty($service)) return redirect()->route('home');

        // Regra de plano de contrato
        $servicesReferences = Service::with(['images', 'variations'])->where('id', '!=', $service->id)->whereHas('images')->where('status', 1)->get()->filter(function ($query) use($service){
            if($query->address_controller == 0){
                if($query->seller->store->lat && $query->seller->store->lng){
                    if($this->getDistance(['lat' => $service->latitude, 'lng' => $service->longitude], ['lat' => $query->seller->store->lat, 'lng' => $query->seller->store->lng]) < 30) return true;
                }
            }else{ 
                if($query->latitude && $query->longitude){
                    if($this->getDistance(['lat' => $service->latitude, 'lng' => $service->longitude], ['lat' => $query->latitude, 'lng' => $query->longitude]) < 30) return true;
                }
            }
        })->shuffle()->take(4);

        $seo = $service;

        $attributes = $service->attrAttrs ?? [];
        $variation_ids = $service->variations->map(function ($query){return $query->id;}) ?? [];

        if(!isset($_COOKIE['uuid_view_product_service'])) {
            $while_loop = true;
            while($while_loop){
                $uuid_temp = (string)\Str::uuid();
                if(ViewProductsService::where('uuid', $uuid_temp)->get()->count() == 0){
                    setCookie('uuid_view_product_service', $uuid_temp);
                    $while_loop = false;
                    break;
                }
            }

            $uuid_view_product_service = $uuid_temp;
        }else{
            $uuid_view_product_service = $_COOKIE['uuid_view_product_service'];
        }

        if(ViewProductsService::where('uuid', $uuid_view_product_service)->where('id_reference', $service->id)->where('reference_type', 'service')->get()->count() == 0){
            $view_service_cookie['uuid'] = $uuid_view_product_service;
            $view_product_cookie['seller_id'] = $service->seller_id;
            $view_service_cookie['id_reference'] = $service->id;
            $view_service_cookie['reference_type'] = 'service';
            ViewProductsService::create($view_service_cookie);
        }

        $whatsapp = $service->whatsapp;
        if(empty($whatsapp)) $whatsapp = $service->seller?->store?->phone1 ?? null;
        if(empty($whatsapp)) $whatsapp = $service->seller?->store?->phone2 ?? null;

        $whatsapp = str_replace(['(',')','-',' '], '', $whatsapp);

        $text_contact = $service->text_contact;
        if(empty($text_contact)){
            $text_contact = $service->title;
            $text_contact = "Olá, tudo bem? Estava navegando no site da Biguaçu e encontrei o serviço \"$text_contact\" .Como faço para reservar?";
        }

        $text_contact = (rawurlencode($text_contact));

        return view('site.service.indexService', get_defined_vars());
    }

    public function selectAttrsVariations(Request $request)
    {
        $variations = ServiceVariantion::with(['progressiveDiscount','calendars','variations'=>function($query) use ($request) {
            return $query->whereIn('attribute_id', $request->attributes_value);
        }])->where('service_id', $request->service_id)->whereHas('variations', function ($query) use ($request) {
            return $query->whereIn('attribute_id', $request->attributes_value);
        })->get()->map(function($query) use ($request) {
            if($query->variations->count() >= count($request->attributes_value)){
                return $query;
            }
        })->reject(function ($query) {
            return empty($query);
        })->first();

        return response()->json($variations);
    }

    public function indexFavorites(Type $var = null)
    {
        $services = $this->getServices(collect(['page' => 40, 'favorite' => true]));
        $seo = json_decode(json_encode([
            'title' => 'Meus Serviços Favoritos'
        ]));

        return view('site.service.indexFavorito', get_defined_vars());
    }

    public function indexSellerStore($slug)
    {
        $skip = $_GET['skip'] ?? 0;
        $store = Store::where('store_slug', $slug)->first();
        $seo = $store;
        $services = $this->getServices(collect(['skip' => [$skip, 20], 'seller_id' => $store->user_id]));

        if(isset($_GET['return'])){
            // return response()->json();
            return $services->map(function ($query){
                return view('components.singleService', get_defined_vars())->render();
            })->join('');
        }

        return view('site.service.indexSellerStore', get_defined_vars());
    }

    public function servicosProximos(Request $request)
    {
        $services_id_search_prox = session()->get('services_id_search_prox');
        // $seo = SeoConfig::where('page', 'rsa-servicosproximos')->first();
        $seo = json_decode(json_encode([
            'title' => 'Serviços locais'
        ]));

        $services = Service::paginate(40);

        return view('site.service.indexServicosProximos', get_defined_vars());
    }

    public function addFavorites($service_id)
    {
        \Log::info($service_id);
        if(!auth()->guard('web')->check()){
            return response()->json(['error_login' => 'Precisa estar logado para adicionar aos favoritos!'], 412);
        }

        if(ServiceFavorite::where('user_id', auth()->guard('web')->user()->id)->where('service_id', $service_id)->get()->count() > 0){
            ServiceFavorite::where('user_id', auth()->guard('web')->user()->id)->where('service_id', $service_id)->delete();
            return response()->json(['msg' => 'Produto removido dos favoritos!']);
        }else{
            ServiceFavorite::create([
                'service_id' => $service_id,
                'user_id' => auth()->guard('web')->user()->id,
            ]);
            return response()->json(['msg' => 'Produto adicionado aos favoritos!']);
        }
    }

    // public function getDistanceMap(Request $request)
    // {
    //     // \Log::info($request->all());
    //     $services_id = collect();
    //     $servicos = Service::with(['seller'])->whereHas('images')->where('status', 1)->get()->filter(function ($query) use($request) {
    //         if($query->address_controller == 0){
    //             if($query->seller->store->lat && $query->seller->store->lng){
    //                 if($this->getDistance($request->geometry, ['lat' => $query->seller->store->lat, 'lng' => $query->seller->store->lng]) < $request->km_max) return true;
    //             }
    //         }else{ 
    //             if($query->latitude && $query->longitude){
    //                 if($this->getDistance($request->geometry, ['lat' => $query->latitude, 'lng' => $query->longitude]) < $request->km_max) return true;
    //             }
    //         }
    //     })->map(function ($query) use($services_id){
    //         // \Log::info($query);
    //         $lat = $query->latitude;
    //         $lng = $query->longitude;
    //         if($query->address_controller == 0){
    //             $lat = $query->seller->store->lat;
    //             $lng = $query->seller->store->lng;
    //         }
    //         $services_id->add($query->id);
    //         return $retorno[] = [
    //             'id' => $query->id,
    //             'loja' => $query->seller->store->store_name,
    //             'slug' => route('seller.turismo.store', $query->seller->store->store_slug),
    //             'lat' => $lat,
    //             'lng' => $lng,
    //         ];
    //     })->unique(function ($query){
    //         return $query['lat'].$query['lng'];
    //     });

    //     // \Log::info($servicos->toArray());
    //     session(['services_search_prox_address' => ['address' => $request->address_search, 'km_max' => $request->km_max]]);
    //     session(['services_search_prox_geometry' => $request->geometry]);
    //     session(['services_search_prox' => $servicos]);
    //     session(['services_id_search_prox' => $services_id]);

    //     return response()->json([$servicos, $services_id]);
    // }

    public function getDistance($geometry1, $geometry2)
    {
        $latitude1 = $geometry1['lat']; //$request->geometry['lat'];
        $longitude1 = $geometry1['lng']; //$request->geometry['lng'];
        $latitude2 = $geometry2['lat'];
        $longitude2 = $geometry2['lng'];
        $earth_radius = 6371; // 6371 para km, 6371000 para m (metro)

        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;

        return $d;
    }

    // ------------
    public function getServices($data = null) 
    {
        // Regra de plano de contrato
        $services = Service::with(['images', 'variations', 'categories.category'])->whereHas('images')->where('status', 1);
        if(isset($data['filters'])){
            if(!empty($data['filters']['preco_ini'])) $services = $services->whereRaw("CAST(preco AS DECIMAL(8,2)) >= ".str_replace(['.',','], ['','.'],$data['filters']['preco_ini']));
            if(!empty($data['filters']['preco_fin'])) $services = $services->whereRaw("CAST(preco AS DECIMAL(8,2)) <= ".str_replace(['.',','], ['','.'],$data['filters']['preco_fin']));
        }
        if(isset($data['day_new'])) $services = $services->whereDate('created_at', '>', date('Y-m-d', strtotime('-'.$data['day_new'].' Days')));
        if(isset($data['seller_id'])) $services = $services->where('seller_id', $data['seller_id']);
        if(isset($data['pesquisa'])) $services = $services->where('service_title', 'like', '%'.$data['pesquisa'].'%');
        if(!empty($data['category_slug'])) $services = $services->whereHas('categories.category', function ($query) use($data) {
            return $query->where('slug', $data['category_slug']);
        });
        if(isset($data['category'])) $services = $services->whereHas('categories.category', function ($query) use ($data){
            return $query->where('slug', $data['category']);
        });
        if(isset($data['favorite']))$services = $services->whereHas('favorite', function ($query) {
            return $query->where('user_id', auth()->guard('web')->user()->id);
        });

        $ordering = $data['filters']['ordering'] ?? 'recente';
        switch($ordering){
            case 'recente':
                $services = $services->orderBy('created_at','DESC');
            break;
            case 'menor_preco':
                $services = $services->orderByRaw("CAST(preco AS DECIMAL(8,2)) ASC");
            break;
            case 'maior_preco':
                $services = $services->orderByRaw("CAST(preco AS DECIMAL(8,2)) DESC");
            break;
        }

        if(isset($data['skip'])){
            $services = $services->orderBy('service_title')->skip($data['skip'][0])->take($data['skip'][1] ?? 20)->get();
        }else{
            $services = $services->orderBy('service_title')->paginate($data['page'] ?? 20);
        }
        return $services;
    }
}
