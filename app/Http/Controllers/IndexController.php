<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\Contact;
use App\Models\Order;
use App\Models\Store;

use App\Models\Bairro;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\Seller;

use App\Models\Produto;
use App\Models\Service;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\PageView;
use App\Mail\OrderFinish;

use App\Models\Attribute;

use App\Models\AviseMeQD;

use App\Models\EventHome;
use App\Models\SeoConfig;
use App\Models\Newsletter;
use App\Models\SignedPlan;
use App\Rules\ReCAPTCHAv3;
use App\Models\AffiliatePs;
use App\Models\FormContact;
use Illuminate\Support\Str;
use App\Models\OrderProduct;
use App\Models\OrderService;
use App\Models\OwnTransport;
use Illuminate\Http\Request;
use Spatie\Sitemap\Tags\Url;
use App\Models\AffiliateInfo;
use App\Models\AffiliateItem;
use App\Models\CustomerAddress;
use App\Mail\RegisterNewsletter;
use App\Models\VariationsProduto;
use App\Models\ServiceReservation;
use App\Models\ViewProductsService;
use App\Http\Controllers\Controller;
use App\Models\ProductSaleAffiliate;
use Illuminate\Support\Facades\Mail;
use Spatie\Sitemap\SitemapGenerator;
use App\Models\ValuesVariationsProduto as VVP;

class IndexController extends Controller
{
    public function geraSiteMap()
    {
        ini_set('max_execution_time', 10000);
        ini_set('memory_limit','32192M');
        $sitemap = SitemapGenerator::create('https://feitoporbiguacu.com/')->getSitemap();
        foreach (Produto::whereHas('images')->where('status', 1)->get() as $produto){
            $sitemap = $sitemap->add(Url::create('https://feitoporbiguacu.com/produto/'.$produto->slug)
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.8));
        }
        foreach (Category::all() as $category){
            $sitemap = $sitemap->add(Url::create('https://feitoporbiguacu.com/categoria/'.$category->slug)
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.8));
        }
        foreach (Seller::where('wallet_id', '!=', null)->whereHas('store')->get() as $seller){
            $sitemap = $sitemap->add(Url::create('https://feitoporbiguacu.com/loja-vendedor/'.$seller->store->store_slug)
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.8));
        }
        $sitemap = $sitemap->writeToFile('sitemap.xml');
        // SitemapGenerator::create('https://feitoporbiguacu.com/carrega-produtos')->writeToFile('sitemap_produtos.xml');
        // SitemapGenerator::create('https://feitoporbiguacu.com/carrega-categorias')->writeToFile('sitemap_categorias.xml');
        // SitemapGenerator::create('https://feitoporbiguacu.com/carrega-vendedores')->writeToFile('sitemap_vendedores.xml');

        return redirect('/sitemap.xml');
    }
    public function buscaEstado()
    {
        $estados = Estado::orderBy('titulo')->get();
        return response()->json($estados);
    }
    public function buscaCidade($id)
    {
        $cidades = Cidade::where('localidade_estado_id', $id)->orderBy('titulo')->get();
        return response()->json($cidades);
    }
    public function buscaBairro($id)
    {
        $bairros = Bairro::where('localidade_municipio_id', $id)->orderBy('titulo')->get();
        return response()->json($bairros);
    }

    public function ModalCepSession(Request $request)
    {
        // session(['cep_consulta' => $request->post_code]);
        // \Cookie::queue('cep_consulta', $request->post_code);
        setCookie('cep_consulta', $request->post_code, time()+604800);

        if(\Str::contains(($request->header()['referer'][0] ?? ''), 'loja-vendedor')){
            return redirect()->back();
        }else{
            return redirect()->route('produtoresLocais');
        }
    }

    public function indexHome()
    {
        $seo = SeoConfig::where('page', 'rsa-home')->first();
        $events_home = EventHome::where('status', 1)->orderBy('posicao')->get();
        if(auth()->guard('web')->check()){
            $address = CustomerAddress::where('user_id', auth()->guard('web')->user()->id)->get()->last();
            $produtosPereciveis = Produto::with(['images', 'variations'])->whereHas('seller', function ($query) {
                $query->where('wallet_id', '<>', '');
                $query->whereNotNull('wallet_id');
            })->whereHas('images')->where('status', 1)->where(function($query) use ($address){
                $whereIn = [];
                if($address){
                    foreach($query->get() as $q){
                        $transporte_proprio = OwnTransport::where('seller_id', $q->seller_id)->where('estado', $address->state)->where('cidade',$address->city)->where(function($query) use ($address){
                            return $query->where('toda_cidade', 1)->orWhere('bairro', $address->address2);
                        });

                        if($transporte_proprio->get()->count() > 0) $whereIn[] = $q->id;
                    }
                }

                return $query->whereIn('id', $whereIn);
            })->inRandomOrder()->limit(4)->get();
        // }elseif(session()->has('cep_consulta')){
        }elseif(isset($_COOKIE['cep_consulta'])){
            $cep = $this->consultaCep($_COOKIE['cep_consulta']);
            if(isset($cep->erro)) {
                setcookie('cep_consulta');
                return redirect()->route('home');
            }
            $produtosPereciveis = Produto::with(['images', 'variations'])
            ->whereHas('seller', function ($query) {
                $query->where('wallet_id', '<>', '')
                    ->whereNotNull('wallet_id');
            })->whereHas('images')->where('status', 1)
            ->whereHas('seller.transport', function ($query) use ($cep) {
                
                $query->where('estado', $cep->uf ?? '')
                ->where(function ($query) use ($cep) {
                    $query->where('cidade', $cep->localidade ?? '')->orWhere('cidade', 'todas as cidades');
                })
                ->where(function ($query) use ($cep) {
                    $query->where('bairro', $cep->bairro ?? '')->orWhere('bairro', 'todos os bairros');
                });
            })->inRandomOrder()->limit(4)->get();
        }

        $produtosNovos = Produto::with(['images', 'variations'])->whereHas('seller', function ($query) {
            $query->where('wallet_id', '<>', '');
            $query->whereNotNull('wallet_id');
        })->whereHas('images')->where('status', 1)->where('perecivel', 0)->whereDate('created_at', '>', date('Y-m-d', strtotime('-60 Days')))->inRandomOrder()->limit(4)->get();
        if($produtosNovos->count() == 0){
            $produtosNovos = Produto::with(['images', 'variations'])->whereHas('seller', function ($query) {
                $query->where('wallet_id', '<>', '');
                $query->whereNotNull('wallet_id');
            })->whereHas('images')->where('status', 1)->where('perecivel', 0)->orderBy('created_at', 'DESC')->inRandomOrder()->limit(4)->get();
        }

        // Foi retirado a seleção de produtos Especial
        // $produtosSelecaoEspecial = Produto::with(['images', 'variations'])->whereHas('seller', function ($query) {
        //     $query->where('wallet_id', '<>', '');
        //     $query->whereNotNull('wallet_id');
        // })->whereHas('images')->where('status', 1)->where('perecivel', 0)->inRandomOrder()->limit(4)->get();
        // $category_type = '0';

        // ##############################  SERVIÇOS RURAIS  ############################## //
            $services_html = Service::with(['images', 'variations', 'categories.category'])->inRandomOrder()->limit(4)->get()->map(function($service){
                $class = 'col-md-3';
                $service_gtag_type = ['Serviços Proximos', 'servico_proximo'];
                return view('components.singleService', get_defined_vars())->render();
            })->join('');
        // ############################################################################### //

        return view('site.indexHome', get_defined_vars());
    }
    public function indexProducts()
    {
        $seo = SeoConfig::where('page', 'rsa-products')->first();
        $ordering = $_GET['filters']['ordering'] ?? 'recente';
        $preco_ini = $_GET['filters']['preco_ini'] ?? null;
        $preco_fin = $_GET['filters']['preco_fin'] ?? null;
        $skip = $_GET['skip'] ??  0;
        $data_address = null;
        if(auth()->guard('web')->check()) $data_address = CustomerAddress::where('user_id', auth()->guard('web')->user()->id)->get()->last();
        if(isset($_COOKIE['cep_consulta'])) $data_address = $this->consultaCep($_COOKIE['cep_consulta']);

        $produtosPereciveis = Produto::with(['images', 'variations'])->where('status', 1);
        $produtosPereciveis->whereHas('seller', function ($query) {
            $query->where('wallet_id', '<>', '');
            $query->whereNotNull('wallet_id');
        })->whereHas('images')->where('status', 1)->where(function($query) use ($data_address){
            $whereIn = [];
            if($data_address){
                foreach($query->get() as $q){
                    $transporte_proprio = OwnTransport::where('seller_id', $q->seller_id)->where('estado', ($data_address->state ?? $data_address->uf))->where('cidade',($data_address->city ?? $data_address->localidade))->where(function($query) use ($data_address){
                        return $query->where('toda_cidade', 1)->orWhere('bairro', ($data_address->address2 ?? $data_address->bairro));
                    });

                    if($transporte_proprio->get()->count() > 0) $whereIn[] = $q->id;
                }
            }

            return $query->whereIn('id', $whereIn);
        })->where(function($query){
            $query_stock = $query->get()->map(function($query){
                if($query->stock_controller == 'true'){
                    if($query->variations->count() > 0){
                        if($query->variations->sum('stock') > 0){
                            return $query->id;
                        }
                    }else{
                        if($query->stock > 0){
                            return $query->id;
                        }
                    }
                }else{
                    return $query->id;
                }
            })->reject(function ($query) {
                return empty($query);
            });;

            return $query->whereIn('id', $query_stock);
        });
        if(!empty($preco_ini)) $produtosPereciveis = $produtosPereciveis->whereRaw("CAST(preco AS DECIMAL(8,2)) >= ".str_replace(['.',','], ['','.'],$preco_ini));
        if(!empty($preco_fin)) $produtosPereciveis = $produtosPereciveis->whereRaw("CAST(preco AS DECIMAL(8,2)) <= ".str_replace(['.',','], ['','.'],$preco_fin));

        $produtos = Produto::with(['images', 'variations', 'categories.category'])->whereHas('seller', function ($query) {
            $query->where('wallet_id', '<>', '');
            $query->whereNotNull('wallet_id');
        })->whereHas('images')->where('status', 1)->where('perecivel', 0)->where(function($query){
            $query_stock = $query->get()->map(function($query){
                if($query->stock_controller == 'true'){
                    if($query->variations->count() > 0){
                        if($query->variations->sum('stock') > 0){
                            return $query->id;
                        }
                    }else{
                        if($query->stock > 0){
                            return $query->id;
                        }
                    }
                }else{
                    return $query->id;
                }
            })->reject(function ($query) {
                return empty($query);
            });;

            return $query->whereIn('id', $query_stock);
        });
        if(!empty($preco_ini)) $produtos = $produtos->whereRaw("CAST(preco AS DECIMAL(8,2)) >= ".str_replace(['.',','], ['','.'],$preco_ini));
        if(!empty($preco_fin)) $produtos = $produtos->whereRaw("CAST(preco AS DECIMAL(8,2)) <= ".str_replace(['.',','], ['','.'],$preco_fin));
        if(isset($produtosPereciveis)) $produtos = $produtos->union($produtosPereciveis);
        switch($ordering){
            case 'recente':
                $produtos = $produtos->orderBy('created_at','DESC');
            break;
            case 'menor_preco':
                $produtos = $produtos->orderByRaw("CAST(preco AS DECIMAL(8,2)) ASC");
            break;
            case 'maior_preco':
                $produtos = $produtos->orderByRaw("CAST(preco AS DECIMAL(8,2)) DESC");
            break;
        }
        $produtos = $produtos->skip($skip)->take(20)->get();

        if(isset($_GET['return'])){
            // return response()->json();
            return view('components.products', get_defined_vars());
        }

        return view('site.indexAllProducts', get_defined_vars());
    }

    public function indexSearch ()
    {
        if(!isset($_GET['q'])) return redirect()->route('home');
        $seo = SeoConfig::where('page', 'rsa-search')->first();
        $category_slug = isset($_GET['c']) ? $_GET['c'] : null;
        $produtos = $this->getProducts(collect(['page' => 40, 'pesquisa' => $_GET['q'], 'category_slug' => $category_slug, 'filters' => ($_GET['filters'] ?? null)]));

        // dd($produtos);

        return view('site.indexBusca', get_defined_vars());
    }

    public function indexProduct($slug, $affiliate = null)
    {
        if($affiliate){
            $affiliateps = AffiliatePs::where('codigo', $affiliate)->where('status', 1)->first();
            if(empty($affiliateps)) return redirect()->route('product', $slug);
        }
        $product = Produto::with(['planPurchases', 'images', 'seller', 'variations.variations', 'attrAttrs.attribute.variations'])->where('slug', $slug)->first();
        if(empty($product)) return redirect()->route('home');
        if($product->ativo == 'N'){
            $redirect = true;
            if(auth()->guard('admin')->check()) $redirect = false;
            if(auth()->guard('seller')->check()){
                if(auth()->guard('seller')->user()->id == $product->seller_id) $redirect = false;
            }

            if($redirect) return redirect()->route('home');
        }
        if(empty($product)) return redirect()->route('home');
        $produtosNovos = Produto::with(['images', 'variations'])->where('seller_id', $product->seller_id)->whereHas('seller', function ($query) {
            $query->where('wallet_id', '<>', '');
            $query->whereNotNull('wallet_id');
        })->whereHas('images')->where('status', 1)->where('perecivel', 0)->where(function($query){
            $query_stock = $query->get()->map(function($query){
                if($query->stock_controller == 'true'){
                    if($query->variations->count() > 0){
                        if($query->variations->sum('stock') > 0){
                            return $query->id;
                        }
                    }else{
                        if($query->stock > 0){
                            return $query->id;
                        }
                    }
                }else{
                    return $query->id;
                }
            })->reject(function ($query) {
                return empty($query);
            });;

            return $query->whereIn('id', $query_stock);
        })->inRandomOrder()->limit(4)->get();
        $seo = $product;

        $attributes = $product->attrAttrs ?? [];
        $variation_ids = $product->variations->map(function ($query){return $query->id;}) ?? [];

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

        if(ViewProductsService::where('uuid', $uuid_view_product_service)->where('id_reference', $product->id)->where('reference_type', 'product')->get()->count() == 0){
            $view_product_cookie['uuid'] = $uuid_view_product_service;
            $view_product_cookie['seller_id'] = $product->seller_id;
            $view_product_cookie['id_reference'] = $product->id;
            $view_product_cookie['reference_type'] = 'product';
            ViewProductsService::create($view_product_cookie);
        }

        if(auth('web')->check()) $addresses = CustomerAddress::where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->get();

        $product_favorito = Favorite::where('user_id', auth()->guard('web')->user()->id ?? 0)->where('product_id', $product->id)->first();

        return view('site.indexProduct', get_defined_vars());
    }

    public function selectAttrsVariations(Request $request)
    {
        $variations = VariationsProduto::with(['progressiveDiscount','variations'=>function($query) use ($request) {
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

        return response()->json($variations);
    }

    public function buscaAttrVariations(Request $request)
    {
        foreach($request->attr_ids as $attributes){
            $vvp = VVP::whereIn('variations_produto_id', $request->variation_ids)->where(function($query) use($attributes){
                return $query->where('attribute_id', $attributes)->orWhere('attribute_id', 0);
            })->get();
        }
    }

    public function indexCategory($slug)
    {
        $seo = Category::where('slug', $slug)->first();
        $skip = $_GET['skip'] ?? 0;

        $class_css = 'col-md-4';
        $produtos = $this->getProducts(collect(['skip' => [$skip, 20], 'category' => $slug, 'filters' => ($_GET['filters'] ?? null)]));

        if(isset($_GET['return'])){
            // return response()->json();
            return view('components.products', get_defined_vars());
        }

        return view('site.indexCategory', get_defined_vars());
    }

    public function indexWhoweare()
    {
        $seo = json_decode(json_encode([
            'title' => 'Quem Somos'
        ]));

        return view('site.indexWhoweare', get_defined_vars());
    }

    public function indexTermsofuse()
    {
        $seo = json_decode(json_encode([
            'title' => 'Termos de Uso'
        ]));
        return view('site.indexTermsofuse', get_defined_vars());
    }

    public function indexExchangesreturns()
    {
        $seo = json_decode(json_encode([
            'title' => 'Política de Trocas e Devoluções'
        ]));
        return view('site.indexExchangesreturns', get_defined_vars());
    }

    public function indexFaq()
    {
        $seo = SeoConfig::where('page', 'rsa-faq')->first();
        return view('site.indexFaq', get_defined_vars());
    }

    public function sessionFreteCart(Request $request)
    {
        $fretes = [];
        foreach($request->frete as $key => $frete){
            $fretes[$key] = $frete;
            $fretes[$key]['dados_gerais'] = json_decode($frete['dados_gerais'], true);
        }
        session(['frete' => $fretes, 'zip_code' => $request->zip_code, 'cart_qty' => cart_show()->quantidade]);
        return response()->json(count($fretes) > 0 ? 'true' : 'false',200);
    }

    public function indexPrivacypolicy()
    {
        $seo = json_decode(json_encode([
            'title' => 'Politicas de Privacidade'
        ]));
        return view('site.indexPrivacypolicy', get_defined_vars());
    }

    public function indexContactus()
    {
        $seo = SeoConfig::where('page', 'rsa-contactus')->first();
        return view('site.indexContactus', get_defined_vars());
    }
    public function sendContactus(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:8',
            'email' => 'required|string|email',
            'phone' => 'required|string|min:14|max:15',
            'assunto' => 'required|string',
            'mensagem' => 'required|string|min:20|max:120',
            'grecaptcha' => ['required', new ReCAPTCHAv3],
        ]);

        FormContact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'assunto' => $request->assunto,
            'mensagem' => $request->mensagem,
        ]);


        Mail::to('contato@raeasy.com')->send(new Contact($request->all()));
        return response()->json($request->all());
    }

    public function indexBlog()
    {
        $seo = SeoConfig::where('page', 'rsa-blog')->first();
        return view('site.indexBlog', get_defined_vars());
    }

    public function cepConsulta($cep)
    {
        function consultaCep($cep){
            $cep = str_replace(['-'],'', $cep);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://opencep.com/v1/$cep");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, FALSE);

            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            return $response;
        }

        return response()->json(consultaCep($cep));
    }

    public function cepCheckout($cep)
    {
        function consultaCep($cep){
            $cep = str_replace(['-'],'', $cep);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://opencep.com/v1/$cep");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, FALSE);

            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            return $response;
        }

        return response()->json(consultaCep($cep));
    }

    public function consultaCep($cep){
        $cep = str_replace(['-'],'', $cep);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://opencep.com/v1/$cep");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, FALSE);

        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        return $response;
    }

    public function indexPerfil()
    {
        $addresses = CustomerAddress::where('user_id', auth()->user()->id)->get();
        $orders = Order::where('user_id', auth()->user()->id)->with('seller.store')->whereNull('parent_id')->orderBy('order_number', 'DESC')->paginate($perPage = 15, $columns = ['*'], $pageName = 'produtos');
        $orderServices = OrderService::where('user_id', auth()->user()->id)->with('seller.store')->orderBy('order_number', 'DESC')->paginate($perPage = 15, $columns = ['*'], $pageName = 'servicos');
        $affiliate = AffiliateInfo::where('user_id', auth()->user()->id)->first();
        $psAfiliados = AffiliateItem::with('affiliatePs.user')->where('status', 1)->orderBy('name', 'ASC')->get();
        $psAfiliadosPedidos = ProductSaleAffiliate::where('affiliate_id', (auth()->guard('web')->user()->afiliados->id ?? 0))->orderBy('created_at', 'DESC')->paginate($perPage = 15, $columns = ['*'], $pageName = 'resumo_pedidos_aff');
        $newsletter = Newsletter::where('email', auth()->user()->email)->first();
        $seo = json_decode(json_encode([
            'title' => 'Minha Conta'
        ]));

        return view('site.indexPerfil', get_defined_vars());
    }

    public function indexPedido($order_number)
    {
        $order = Order::with('user', 'seller', 'orderParent.orderProducts', 'orderParent.shippingCustomer')->where('user_id', auth()->user()->id)->where('order_number', $order_number)->first();
        $seo = json_decode(json_encode([
            'title' => 'Pedido - '.$order->order_number
        ]));

        $pedido_asaas = \Http::withHeaders([
            'access_token' => $this->access_token
        ])->get($this->url_asaas.'/payments'.'/'.$order->payment_id)->object();

        \Log::info([$order->orderParent[0]->shippingCustomer->general_data['frete']]);

        return view('site.indexPedido', get_defined_vars());
    }

    // public function indexPedidoServico($order_number)
    // {
    //     $order = OrderService::with('user.adresses', 'seller', 'serviceReservation')->where('user_id', auth()->user()->id)->where('order_number', $order_number)->first();
    //     $seo = json_decode(json_encode([
    //         'title' => 'Pedido - '.$order->order_number
    //     ]));

    //     $pagarme_v = explode('_', $order->payment_id)[0] == 'or' ? '2.0' : '1.0';

    //     if($pagarme_v == '2.0'){
    //         $pedido_pagarme = \Http::withHeaders(get_header_conf_pm())->get(url_pagarme('orders', '/'.$order->payment_id), [])->object();
    //     }

    //     return view('site.service.indexPedido', get_defined_vars());
    // }

    public function finalizarOrder(Request $request)
    {
        $order = Order::where('order_number', $request->order_number)->update(['pay' => '2']);
        Mail::to(Order::where('order_number', $request->order_number)->first()->user_email)->send(new OrderFinish(Order::where('order_number', $request->order_number)->first()));
        return response()->json('success', 200);
    }

    public function indexSellerStore($slug)
    {
        $skip = $_GET['skip'] ?? 0;
        $store = Store::where('store_slug', $slug)->first();
        if(empty($store)) return redirect()->route('home');
        $seo = $store;
        $produtos = $this->getProducts(collect(['skip' => [$skip, 20], 'seller_id' => $store->user_id]));

        if(isset($_GET['return'])){
            // return response()->json();
            return view('components.products', get_defined_vars());
        }

        $produtos_locais = Produto::where('seller_id',$store->user_id)->where('perecivel', 1)->get()->count();

        return view('site.indexSellerStore', get_defined_vars());
    }

    public function rateProduct()
    {
        $order_numbers = [];
        $orders = Order::where('user_id', auth()->user()->id)->whereDate('created_at', '>=', date('Y-m-d', strtotime('-1 Years')))->orderBy('order_number', 'DESC')->get(['order_number']);
        foreach($orders as $order){
            $order_numbers[] = $order->order_number;
        }

        $products = OrderProduct::with(['product.images','stars' => function($query){
            return $query->where('user_id', auth()->user()->id);
        }])->whereIn('order_number', $order_numbers)->groupBy('product_id')->get();

        // dd($product);
        // dd($products);

        return view('site.indexRateProduct', get_defined_vars());
    }

    public function rateService()
    {
        $order_numbers = [];
        $orders = OrderService::where('user_id', auth()->user()->id)->whereDate('created_at', '>=', date('Y-m-d', strtotime('-1 Years')))->orderBy('order_number', 'DESC')->get(['order_number']);
        foreach($orders as $order){
            $order_numbers[] = $order->order_number;
        }

        $services = ServiceReservation::with(['service.images','stars' => function($query){
            return $query->where('user_id', auth()->user()->id);
        }])->whereIn('order_number', $order_numbers)->groupBy('service_id')->get();

        // dd($services);

        return view('site.service.indexRateProduct', get_defined_vars());
    }

    public function produtoresLocais(Request $request)
    {
        $seo = SeoConfig::where('page', 'rsa-produtoresLocais')->first();
        $ordering = $_GET['filters']['ordering'] ?? 'recente';
        $preco_ini = $_GET['filters']['preco_ini'] ?? null;
        $preco_fin = $_GET['filters']['preco_fin'] ?? null;

        $endereço_procurado = null;
        if(auth()->guard('web')->check() && !isset($request->address_off)){
            $address = CustomerAddress::where('user_id', auth()->guard('web')->user()->id)->get()->last();
            $endereço_procurado = $address;
            if(empty($address)) {
                return redirect(route('produtoresLocais').'?address_off=true');
            }
            $produtos = Produto::with(['images', 'variations'])->whereHas('seller', function ($query) {
                $query->where('wallet_id', '<>', '');
                $query->whereNotNull('wallet_id');
            })
            ->whereHas('seller.transport', function ($query) use ($address) {
                $query->where('estado', $address->state ?? '')
                ->where(function ($query) use ($address) {
                    $query->where('cidade', $address->city ?? '')->orWhere('cidade', 'todas as cidades');
                })
                ->where(function ($query) use ($address) {
                    $query->where('bairro', $address->address2 ?? '')->orWhere('bairro', 'todos os bairros');
                });
            });
        }elseif(isset($_COOKIE['cep_consulta'])){
            $cep = $this->consultaCep($_COOKIE['cep_consulta']);
            $endereço_procurado = $cep;
            \Log::info([
                'IndexController::produtoresLocais()',
                'cepCookie' => $_COOKIE['cep_consulta'],
                'cep_encontrado' => json_encode($cep)
            ]);
            if(isset($cep->erro)) {
                setcookie('cep_consulta');
                return redirect()->route('home');
            }
            $produtos = Produto::with(['images', 'variations'])->whereHas('seller', function ($query) {
                $query->where('wallet_id', '<>', '');
                $query->whereNotNull('wallet_id');
            })->whereHas('images')->where('status', 1)
            ->whereHas('seller.transport', function ($query) use ($cep) {
                $query->where('estado', $cep->uf ?? '')
                ->where(function ($query) use ($cep) {
                    $query->where('cidade', $cep->localidade ?? '')->orWhere('cidade', 'todas as cidades');
                })
                ->where(function ($query) use ($cep) {
                    $query->where('bairro', $cep->bairro ?? '')->orWhere('bairro', 'todos os bairros');
                });
            });
        }

        if(isset($produtos)){
            if(!empty($preco_ini)) $produtos = $produtos->whereRaw("CAST(preco AS DECIMAL(8,2)) >= ".str_replace(['.',','], ['','.'],$preco_ini));
            if(!empty($preco_fin)) $produtos = $produtos->whereRaw("CAST(preco AS DECIMAL(8,2)) <= ".str_replace(['.',','], ['','.'],$preco_fin));
            switch($ordering){
                case 'recente':
                    $produtos = $produtos->orderBy('created_at','DESC');
                break;
                case 'menor_preco':
                    $produtos = $produtos->orderByRaw("CAST(preco AS DECIMAL(8,2)) ASC");
                break;
                case 'maior_preco':
                    $produtos = $produtos->orderByRaw("CAST(preco AS DECIMAL(8,2)) DESC");
                break;
            }

            $produtos = $produtos->paginate(20);
        }
        if(!isset($produtos)) $produtos = collect([]);

        return view('site.indexProdutoresLocais', get_defined_vars());
    }

    public function indexNew()
    {
        $seo = SeoConfig::where('page', 'rsa-indexnew')->first();
        $produtos = $this->getProducts(collect(['page' => 40, 'day_new' => 60]));

        return view('site.indexNew', get_defined_vars());
    }

    public function indexPageView($slug)
    {
        $page_view = PageView::where('link', 'pagina/'.$slug)->first();
        $seo = $page_view;
        return view('site.indexPageView', get_defined_vars());
    }

    public function indexSpecialSelection()
    {
        $seo = SeoConfig::where('page', 'rsa-specialselection')->first();
        $produtos = $this->getProducts(collect(['page' => 40]));

        return view('site.indexSpecialSelection', get_defined_vars());
    }

    public function indexFavorites()
    {
        $produtos = $this->getProducts(collect(['page' => 40, 'favorite' => true]));
        $seo = json_decode(json_encode([
            'title' => 'Meus Favoritos'
        ]));

        return view('site.indexFavorito', get_defined_vars());
    }

    public function addFavorites($product_id)
    {
        if(!auth()->guard('web')->check()){
            return response()->json(['error_login' => 'Precisa estar logado para adicionar aos favoritos!'], 412);
        }

        if(Favorite::where('user_id', auth()->guard('web')->user()->id)->where('product_id', $product_id)->get()->count() > 0){
            Favorite::where('user_id', auth()->guard('web')->user()->id)->where('product_id', $product_id)->delete();
            return response()->json(['msg' => 'Produto removido dos favoritos!', 'add' => false]);
        }else{
            Favorite::create([
                'product_id' => $product_id,
                'user_id' => auth()->guard('web')->user()->id,
            ]);
            return response()->json(['msg' => 'Produto adicionado aos favoritos!', 'add' => true]);
        }
    }

    public function indexAssinatura(){
        $assinaturas = SignedPlan::where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->paginate(10);

        return view('site.indexAssinatura', get_defined_vars());
    }

    public function indexAssinaturaDetalhe($id)
    {
        $assinatura = SignedPlan::with('seller.store')->where('user_id', auth()->user()->id)->where('id', $id)->first();
        if(!$assinatura)
        {
            return redirect()->route('perfil.assinatura');
        }

        $seo = json_decode(json_encode([
            'title' => 'Assinatura - '.$assinatura->plan_title
        ]));

        return view('site.indexAssinaturaDetalhe', get_defined_vars());
    }

    // -------------------------
    public function getProducts($data = null)
    {
        $data_address = null;
        if(auth()->guard('web')->check()) $data_address = CustomerAddress::where('user_id', auth()->guard('web')->user()->id)->get()->last();
        if(isset($_COOKIE['cep_consulta'])) $data_address = $this->consultaCep($_COOKIE['cep_consulta']);

        $produtosPereciveis = Produto::with(['images', 'variations'])->where('status', 1);
        if(isset($data['seller_id'])) $produtosPereciveis = $produtosPereciveis->where('seller_id', $data['seller_id']);
        if(isset($data['day_new'])) $produtosPereciveis = $produtosPereciveis->whereDate('created_at', '>', date('Y-m-d', strtotime('-'.$data['day_new'].' Days')));
        if(isset($data['pesquisa'])) $produtosPereciveis = $produtosPereciveis->where('nome', 'like', '%'.$data['pesquisa'].'%');
        if(!empty($data['category_slug'])) $produtosPereciveis = $produtosPereciveis->whereHas('categories.category', function ($query) use($data) {
            return $query->where('slug', $data['category_slug']);
        });
        if(isset($data['category'])) $produtosPereciveis = $produtosPereciveis->whereHas('categories.category', function ($query) use ($data){
            return $query->where('slug', $data['category']);
        });
        if(isset($data['favorite']))$produtosPereciveis = $produtosPereciveis->whereHas('favorite', function ($query) {
            return $query->where('user_id', auth()->guard('web')->user()->id);
        });
        $produtosPereciveis->whereHas('seller', function ($query) {
            $query->where('wallet_id', '<>', '');
            $query->whereNotNull('wallet_id');
        })->whereHas('images')->where('status', 1)->where(function($query) use ($data_address){
            $whereIn = [];
            if($data_address){
                foreach($query->get() as $q){
                    $transporte_proprio = OwnTransport::where('seller_id', $q->seller_id)->where('estado', ($data_address->state ?? ($data_address->uf ?? '')))->where('cidade',($data_address->city ?? ($data_address->localidade ?? '')))->where(function($query) use ($data_address){
                        return $query->where('toda_cidade', 1)->orWhere('bairro', ($data_address->address2 ?? ($data_address->bairro ?? '')));
                    });

                    if($transporte_proprio->get()->count() > 0) $whereIn[] = $q->id;
                }
            }

            return $query->whereIn('id', $whereIn);
        });
        //  Essa função talvez sera descontinuada
        // if(!isset($data['pesquisa'])) $produtosPereciveis = $produtosPereciveis->where(function($query){
        //     $query_stock = $query->get()->map(function($query){
        //         if($query->stock_controller == 'true'){
        //             if($query->variations->count() > 0){
        //                 if($query->variations->sum('stock') > 0){
        //                     return $query->id;
        //                 }
        //             }else{
        //                 if($query->stock > 0){
        //                     return $query->id;
        //                 }
        //             }
        //         }else{
        //             return $query->id;
        //         }
        //     })->reject(function ($query) {
        //         return empty($query);
        //     });;

        //     return $query->whereIn('id', $query_stock);
        // });
        if(isset($data['filters'])){
            if(!empty($data['filters']['preco_ini'])) $produtosPereciveis = $produtosPereciveis->whereRaw("CAST(preco AS DECIMAL(8,2)) >= ".str_replace(['.',','], ['','.'],$data['filters']['preco_ini']));
            if(!empty($data['filters']['preco_fin'])) $produtosPereciveis = $produtosPereciveis->whereRaw("CAST(preco AS DECIMAL(8,2)) <= ".str_replace(['.',','], ['','.'],$data['filters']['preco_fin']));
        }

        $produtos = Produto::with(['images', 'variations', 'categories.category'])->whereHas('seller', function ($query) {
            $query->where('wallet_id', '<>', '');
            $query->whereNotNull('wallet_id');
        })->whereHas('images')->where('status', 1)->where('perecivel', 0);
        //  Essa função talvez sera descontinuada
        // if(!isset($data['pesquisa'])) $produtos = $produtos->where(function($query){
        //     $query_stock = $query->get()->map(function($query){
        //         if($query->stock_controller == 'true'){
        //             if($query->variations->count() > 0){
        //                 if($query->variations->sum('stock') > 0){
        //                     return $query->id;
        //                 }
        //             }else{
        //                 if($query->stock > 0){
        //                     return $query->id;
        //                 }
        //             }
        //         }else{
        //             return $query->id;
        //         }
        //     })->reject(function ($query) {
        //         return empty($query);
        //     });;

        //     return $query->whereIn('id', $query_stock);
        // });
        if(isset($data['filters'])){
            if(!empty($data['filters']['preco_ini'])) $produtos = $produtos->whereRaw("CAST(preco AS DECIMAL(8,2)) >= ".str_replace(['.',','], ['','.'],$data['filters']['preco_ini']));
            if(!empty($data['filters']['preco_fin'])) $produtos = $produtos->whereRaw("CAST(preco AS DECIMAL(8,2)) <= ".str_replace(['.',','], ['','.'],$data['filters']['preco_fin']));
        }
        if(isset($data['day_new'])) $produtos = $produtos->whereDate('created_at', '>', date('Y-m-d', strtotime('-'.$data['day_new'].' Days')));
        if(isset($data['seller_id'])) $produtos = $produtos->where('seller_id', $data['seller_id']);
        if(isset($data['pesquisa'])) $produtos = $produtos->where('nome', 'like', '%'.$data['pesquisa'].'%');
        if(!empty($data['category_slug'])) $produtos = $produtos->whereHas('categories.category', function ($query) use($data) {
            return $query->where('slug', $data['category_slug']);
        });
        if(isset($data['category'])) $produtos = $produtos->whereHas('categories.category', function ($query) use ($data){
            return $query->where('slug', $data['category']);
        });
        if(isset($data['favorite']))$produtos = $produtos->whereHas('favorite', function ($query) {
            return $query->where('user_id', auth()->guard('web')->user()->id);
        });

        if(isset($produtosPereciveis)) $produtos = $produtos->union($produtosPereciveis);

        $ordering = $data['filters']['ordering'] ?? 'recente';
        switch($ordering){
            case 'recente':
                $produtos = $produtos->orderBy('created_at','DESC');
            break;
            case 'menor_preco':
                $produtos = $produtos->orderByRaw("CAST(preco AS DECIMAL(8,2)) ASC");
            break;
            case 'maior_preco':
                $produtos = $produtos->orderByRaw("CAST(preco AS DECIMAL(8,2)) DESC");
            break;
        }

        if(isset($data['skip'])){
            $produtos = $produtos->orderBy('nome')->skip($data['skip'][0])->take($data['skip'][1] ?? 20)->get();
        }else{
            $produtos = $produtos->orderBy('nome')->paginate($data['page'] ?? 20);
        }
        return $produtos;
    }

    public function aviseMeRegister(Request $request){
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email',
        ];

        $customMessages = [
            'name.required'  => 'O campo Nome é obrigatório!',
            'email.required' => 'O campo Email é obrigatório!',
        ];

        $this->validate($request, $rules, $customMessages);

        if(AviseMeQD::where('item_id', $request->id)->where('email', $request->email)->get()->count() > 0) return response()->json('já registrado');

        AviseMeQD::create([
            'item_id' => $request->id,
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if($request->newsletter == 'true'){
            if(Newsletter::where('email', $request->email)->get()->count() == 0){
                $token_newsletter = Str::orderedUuid();
                Newsletter::create([
                    'name'  => $request->name,
                    'email' => $request->email,
                    'token' => $token_newsletter,
                ]);

                Mail::to($request->email)->send(new RegisterNewsletter($token_newsletter));
            }
        }

        return response()->json('success');
    }

    public function registerNewsletter(Request $request){
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:newsletters',
        ];

        $customMessages = [
            'name.required'  => 'O campo Nome é obrigatório!',
            'email.required' => 'O campo Email é obrigatório!',
            'email.unique'   => 'O Email informado já está cadastrado em nossa base!',
        ];

        $this->validate($request, $rules, $customMessages);

        $token_newsletter = Str::orderedUuid();;
        Newsletter::create([
            'name'  => $request->name,
            'email' => $request->email,
            'token' => $token_newsletter,
        ]);

        Mail::to($request->email)->send(new RegisterNewsletter($token_newsletter));
        return response()->json($request->all());
    }

    public function cancelNewsletter(Request $request){
        $token = base64_decode($_GET['t']);
        $newsletter = Newsletter::where('token', $token)->first();

        if(!empty($newsletter))
        {
            $newsletter = $newsletter->delete();
        }

        return view('site.cancelNewsletter', get_defined_vars());
    }
}
