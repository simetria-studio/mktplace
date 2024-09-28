<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Produto;
use App\Models\Service;
use App\Models\StarService;
use App\Models\TabelaGeral;
use \App\Models\StarProduct;
use App\Models\BannerConfig;
use App\Models\OrderProduct;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\ServiceVariantion;
use App\Models\ServiceReservation;
use App\Models\ViewProductsService;
use App\Models\ServiceVariantionValues;
use Illuminate\Support\Facades\Response;
use \App\Models\ValuesVariationsProduto as VVP;

if(!function_exists('banner_configs')){
    function banner_configs($local){
        return BannerConfig::where('local', $local)->get();
    }
}

if(!function_exists('getTabelaGeral')){
    function getTabelaGeral($tabela, $coluna, $metodo = 'first'){
        switch($metodo){
            case 'first':
                return TabelaGeral::where('tabela', $tabela)->where('coluna', $coluna)->first();
                break;
            case 'get':
                return TabelaGeral::where('tabela', $tabela)->where('coluna', $coluna)->get();
                break;
            case 'all':
                return TabelaGeral::all();
                break;
        }
    }
}

if (!function_exists('getCategories')) {
    function getCategories($type)
    {
        $categories = App\Models\Category::whereNull('parent_id')->where('type', $type)->with(['subCategories'])->get();

        return $categories;
    }
}

if(!function_exists('verificar_attrs')){
    function verificar_attrs($arrs){
        $vvp = VVP::whereIn('variations_produto_id', $arrs[1])->where('attribute_pai_id', $arrs[0])->get();

        $vvp_ids = $vvp->map(function($query){
            return $query->attribute_id;
        });
        // \Log::info(json_encode($vvp_ids));

        return collect([
            'all_options' => $vvp->contains('attribute_id', '0'),
            'options' => array_unique(json_decode($vvp_ids))
        ]);
    }
}
if(!function_exists('verificar_attrs_service')){
    function verificar_attrs_service($arrs){
        $vvp = ServiceVariantionValues::whereIn('service_variantion_id', $arrs[1])->where('attribute_pai_id', $arrs[0])->get();

        $vvp_ids = $vvp->map(function($query){
            return $query->attribute_id;
        });
        // \Log::info(json_encode($vvp_ids));

        return collect([
            'all_options' => $vvp->contains('attribute_id', '0'),
            'options' => array_unique(json_decode($vvp_ids))
        ]);
    }
}

if(!function_exists('getTimeDiff')){
    // Função para calcular diferença entre as horas.
    function getTimeDiff($dtime,$atime){
        // $since_start->days.' days total<br>';
        // $since_start->y.' years<br>';
        // $since_start->m.' months<br>';
        // $since_start->d.' days<br>';
        // $since_start->h.' hours<br>';
        // $since_start->i.' minutes<br>';
        // $since_start->s.' seconds<br>';

        $start_date = new DateTime(date('Y-m-d H:i:s', strtotime($dtime)));
        $since_start = $start_date->diff(new DateTime(date('Y-m-d H:i:s', strtotime($atime))));

        return $since_start;
    }
}

if(!function_exists('verificaUrlTrackind')){
    function verificaUrlTrackind($tracking){
        $return = '';
        if(!empty($tracking)){
            $tracking = explode(';', $tracking);

            if(empty(str_replace('link=', '', $tracking[1]))){
                $return = str_replace('code=', '', $tracking[0]);
            }else{
                $return = '<a target="_blank" href="'.str_replace('link=', '', $tracking[1]).'" class="btn btn-sm btn-primary" style="white-space: nowrap;">'.str_replace('code=', '', $tracking[0]).'</a>';
            }
        }
        return $return;
    }
}

if (!function_exists('cart_show')) {
    function cart_show()
    {
        $cart_contents = Darryldecode\Cart\Facades\CartFacade::getContent();

        $carts = [];
        foreach ($cart_contents as $contents) {
            $cart['id'] = $contents->id;
            $cart['row_id'] = $contents->id;
            $cart['name'] = $contents->name;
            $cart['price'] = $contents->price;
            $cart['quantity'] = $contents->quantity;
            $cart['attributes'] = [
                'product_id'        => $contents->attributes->product_id,
                'affiliate_code'    => $contents->attributes->affiliate_code,
                'var_id'            => $contents->attributes->var_id,
                'atributo_valor'    => $contents->attributes->atributo_valor ?? null,
                'seller_id'       => $contents->attributes->seller_id,
                'product_image'     => $contents->attributes->product_image,
                'seller_name'     => $contents->attributes->seller_name,
                'product_weight'    => $contents->attributes->product_weight,
                'product_width'     => $contents->attributes->product_width,
                'product_height'    => $contents->attributes->product_height,
                'product_length'    => $contents->attributes->product_length,
                'selected_attribute' => $contents->attributes->selected_attribute,
            ];

            if (auth()->check()) {
                unset($cart['id']);
                $cart['user_id'] = auth()->user()->id;
                $cart['active'] = 'S';
                Darryldecode\Cart\Facades\CartFacade::remove($contents->id); // essa linha não pode ser comentada pq precsia ser removido do carrinho os itens quando esta logado, se não fica adiconando ao carrihno do usuario infinitamente

                $cart_id = App\Models\Cart::where('user_id', auth()->user()->id)->get()->filter(function($query) use($cart){
                    return $query->attributes['product_id'] == $cart['attributes']['product_id'] && $query->attributes['atributo_valor'] == $cart['attributes']['atributo_valor'];
                })->first()->id ?? null;
    
                if($cart_id){
                    $cart_model = App\Models\Cart::find($cart_id);
                    $cart_model->update(['quantity' => ($cart_model->quantity + $cart['quantity'])]);
                }else{
                    App\Models\Cart::create($cart);
                }
            }

            $carts[] = $cart;
        }

        $carts = json_decode(json_encode($carts));

        if (auth()->check()) {
            $carts = App\Models\Cart::where('user_id', auth()->user()->id)->get();
        }

        $total_cart = 0;
        $quantity_cart = 0;
        foreach (($carts ?? []) as $total) {
            $total_cart += ($total->price * $total->quantity);
            $quantity_cart += $total->quantity;
        }

        return json_decode(json_encode(['content' => $carts, 'total' => $total_cart, 'quantidade' => $quantity_cart]));
    }
}

if(!function_exists('is_base64')){
    function is_base64($s){
        // Check if there are valid base64 characters
        if(is_array($s)) return false;
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return false;

        // Decode the string in strict mode and check the results
        $decoded = base64_decode($s, true);
        if(false === $decoded) return false;

        // Encode the string again
        if(base64_encode($decoded) != $s) return false;

        return true;
    }
}

if(!function_exists('stars')){
    function stars($id){
        // (5*252 + 4*0 + 3*10 + 2*0 + 1*0) / (252+0+10+0+0) // exemplo //
        $starx = 0;
        $starplus = 0;
        $star_media = 0;
        $starProduct = StarProduct::where('product_id', $id)->select(DB::raw('count(*) as star_count, star'))->groupBy('star')->get();
        foreach($starProduct as $star){
            $starx += $star->star * $star->star_count;
            $starplus += $star->star_count;
        }

        if($starx > 0 && $starplus > 0){
            $star_media = ($starx/$starplus);
        }

        return ['star_media' => $star_media];
    }
}

if(!function_exists('starsService')){
    function starsService($id){
        // (5*252 + 4*0 + 3*10 + 2*0 + 1*0) / (252+0+10+0+0) // exemplo //
        $starx = 0;
        $starplus = 0;
        $star_media = 0;
        $starService = StarService::where('service_id', $id)->select(DB::raw('count(*) as star_count, star'))->groupBy('star')->get();
        foreach($starService as $star){
            $starx += $star->star * $star->star_count;
            $starplus += $star->star_count;
        }

        if($starx > 0 && $starplus > 0){
            $star_media = ($starx/$starplus);
        }

        return ['star_media' => $star_media];
    }
}

if (!function_exists('get_pagarme')) {
    /**
     * @return \PagarMe\Client
     */
    function get_pagarme(): \PagarMe\Client
    {
        return new \PagarMe\Client(config('pagarme.api_key'));
    }
}

if (!function_exists('bancos')) {
    /**
     * @return string[]
     */
    function bancos(): array
    {
        return $bancos = array(
            '001' => 'Banco do Brasil S.A.',
            '003' => 'Banco da Amazônia S.A.',
            '004' => 'Banco do Nordeste do Brasil S.A.',
            '007' => 'Banco Nacional de Desenvolvimento Econômico e Social - BNDES',
            '012' => 'Banco Inbursa S.A.',
            '014' => 'State Street Brasil S.A. - Banco Comercial',
            '017' => 'BNY Mellon Banco S.A.',
            '018' => 'Banco Tricury S.A.',
            '021' => 'BANESTES S.A. Banco do Estado do Espírito Santo',
            '024' => 'Banco BANDEPE S.A.',
            '025' => 'Banco Alfa S.A.',
            '027' => 'Besc',
            '029' => 'Banco Itaú Consignado S.A.',
            '031' => 'Banco Beg',
            '033' => 'Banco Santander  (Brasil)  S.A.',
            '036' => 'Banco Bradesco BBI S.A.',
            '037' => 'Banco do Estado do Pará S.A.',
            '038' => 'Banestado',
            '039' => 'BEP',
            '040' => 'Banco Cargill S.A.',
            '041' => 'Banco do Estado do Rio Grande do Sul S.A.',
            '044' => 'BVA',
            '045' => 'Banco Opportunity',
            '047' => 'Banco do Estado de Sergipe S.A.',
            '051' => 'Banco de Desenvolvimento do Espírito Santo S.A.',
            '062' => 'Hipercard Banco Múltiplo S.A.',
            '063' => 'Banco Bradescard S.A.',
            '064' => 'Goldman Sachs do Brasil Banco Múltiplo S.A.',
            '065' => 'Banco Andbank (Brasil) S.A.',
            '066' => 'Banco Morgan Stanley S.A.',
            '069' => 'Banco Crefisa S.A.',
            '070' => 'BRB - Banco de Brasília S.A.',
            '072' => 'Banco Rural',
            '073' => 'Banco Popular',
            '074' => 'Banco J. Safra S.A.',
            '075' => 'Banco ABN AMRO S.A.',
            '076' => 'Banco KDB S.A.',
            '077' => 'Banco Inter S.A.',
            '078' => 'Haitong Banco de Investimento do Brasil S.A.',
            '079' => 'Banco Original do Agronegócio S.A.',
            '081' => 'BancoSeguro S.A.',
            '082' => 'Banco Topázio S.A.',
            '083' => 'Banco da China Brasil S.A.',
            '084' => 'Uniprime Norte do Paraná - Coop de Economia e Crédito Mútuo dos Médicos, Profissionais das Ciências',
            '085' => 'Cooperativa Central de Crédito - AILOS',
            '092' => 'Brickell S.A. Crédito, Financiamento e Investimento',
            '094' => 'Banco Finaxis S.A.',
            '095' => 'Travelex Banco de Câmbio S.A.',
            '096' => 'Banco B3 S.A.',
            '097' => 'Cooperativa Central de Crédito Noroeste Brasileiro Ltda.',
            '102' => 'Banco XP S.A.',
            '104' => 'Caixa Econômica Federal',
            '107' => 'Banco BOCOM BBM S.A.',
            '118' => 'Standard Chartered Bank (Brasil) S/A–Bco Invest.',
            '116' => 'Banco Único',
            '119' => 'Banco Western Union do Brasil S.A.',
            '120' => 'Banco Rodobens S.A.',
            '121' => 'Banco Agibank S.A.',
            '122' => 'Banco Bradesco BERJ S.A.',
            '124' => 'Banco Woori Bank do Brasil S.A.',
            '125' => 'Banco Genial S.A.',
            '126' => 'BR Partners Banco de Investimento S.A.',
            '128' => 'MS Bank S.A. Banco de Câmbio',
            '129' => 'UBS Brasil Banco de Investimento S.A.',
            '132' => 'ICBC do Brasil Banco Múltiplo S.A.',
            '136' => 'Banco Unicred',
            '139' => 'Intesa Sanpaolo Brasil S.A. - Banco Múltiplo',
            '144' => 'BEXS Banco de Câmbio S.A.',
            '151' => 'Nossa Caixa',
            '163' => 'Commerzbank Brasil S.A. - Banco Múltiplo',
            '169' => 'Banco Olé Bonsucesso Consignado S.A.',
            '175' => 'Banco Finasa',
            '184' => 'Banco Itaú BBA S.A.',
            '204' => 'Banco Bradesco Cartões S.A.',
            '208' => 'Banco BTG Pactual S.A.',
            '212' => 'Banco Original S.A.',
            '213' => 'Banco Arbi S.A.',
            '214' => 'Banco Dibens',
            '217' => 'Banco John Deere S.A.',
            '218' => 'Banco BS2 S.A.',
            '222' => 'Banco Credit Agricole Brasil S.A.',
            '224' => 'Banco Fibra S.A.',
            '225' => 'Banco Brascan',
            '229' => 'Banco Cruzeiro',
            '230' => 'Unicard',
            '233' => 'Banco Cifra S.A.',
            '237' => 'Banco Bradesco S.A.',
            '241' => 'Banco Clássico S.A.',
            '243' => 'Banco Master S.A.',
            '246' => 'Banco ABC Brasil S.A.',
            '248' => 'Banco Boavista Interatlântico',
            '249' => 'Banco Investcred Unibanco S.A.',
            '250' => 'BCV - Banco de Crédito e Varejo S.A.',
            '252' => 'Fininvest',
            '254' => 'Paraná Banco S.A.',
            '260' => 'Nubank',
            '263' => 'Banco Cacique',
            '265' => 'Banco Fator S.A.',
            '266' => 'Banco Cédula S.A.',
            '269' => 'HSBC Brasil S.A. - Banco de Investimento',
            '276' => 'Banco Senff S.A.',
            '299' => 'Banco Sorocred S.A. - Banco Múltiplo (AFINZ)',
            '300' => 'Banco de La Nacion Argentina',
            '318' => 'Banco BMG S.A.',
            '320' => 'China Construction Bank (Brasil) Banco Múltiplo S.A.',
            '330' => 'Banco Bari de Investimentos e Financiamentos S/A',
            '341' => 'Itaú Unibanco S.A.',
            '347' => 'Sudameris',
            '351' => 'Banco Santander',
            '353' => 'Banco Santander Brasil',
            '356' => 'ABN Amro Real',
            '359' => 'Zema Credito, Financiamento e Investimento S.A.',
            '366' => 'Banco Société Générale Brasil S.A.',
            '370' => 'Banco Mizuho do Brasil S.A.',
            '376' => 'Banco J. P. Morgan S.A.',
            '389' => 'Banco Mercantil do Brasil S.A.',
            '394' => 'Banco Bradesco Financiamentos S.A.',
            '399' => 'Kirton Bank S.A. - Banco Múltiplo',
            '409' => 'Unibanco',
            '412' => 'Banco Capital S.A.',
            '422' => 'Banco Safra S.A.',
            '453' => 'Banco Rural',
            '456' => 'Banco MUFG Brasil S.A.',
            '464' => 'Banco Sumitomo Mitsui Brasileiro S.A.',
            '473' => 'Banco Caixa Geral - Brasil S.A.',
            '477' => 'Citibank N.A.',
            '479' => 'Banco ItauBank S.A',
            '487' => 'Deutsche Bank S.A. - Banco Alemão',
            '488' => 'JPMorgan Chase Bank, National Association',
            '492' => 'ING Bank N.V.',
            '494' => 'Banco de La Republica Oriental del Uruguay',
            '495' => 'Banco de La Provincia de Buenos Aires',
            '505' => 'Banco Credit Suisse (Brasil) S.A.',
            '600' => 'Banco Luso Brasileiro S.A.',
            '604' => 'Banco Industrial do Brasil S.A.',
            '610' => 'Banco VR S.A.',
            '611' => 'Banco Paulista S.A.',
            '612' => 'Banco Guanabara S.A.',
            '613' => 'Omni Banco S.A.',
            '623' => 'Banco PAN S.A.',
            '626' => 'Banco C6 Consignado S.A.',
            '630' => 'Banco Letsbank S.A.',
            '633' => 'Banco Rendimento S.A.',
            '634' => 'Banco Triângulo S.A.',
            '637' => 'Banco Sofisa S.A.',
            '638' => 'Banco Prosper',
            '641' => 'Banco Alvorada S.A.',
            '643' => 'Banco Pine S.A.',
            '652' => 'Itaú Unibanco Holding S.A.',
            '653' => 'Banco Voiter S.A.',
            '654' => 'Banco Digimais S.A.',
            '655' => 'Banco Votorantim S.A.',
            '658' => 'Banco Porto Real de Investimentos S.A.',
            '707' => 'Banco Daycoval S.A.',
            '712' => 'Banco Ourinvest S.A.',
            '719' => 'Banif',
            '720' => 'BANCO RNX S.A',
            '721' => 'Banco Credibel',
            '734' => 'Banco Gerdau',
            '735' => 'Banco Pottencial',
            '738' => 'Banco Morada',
            '739' => 'Banco Cetelem S.A.',
            '740' => 'Banco Barclays',
            '741' => 'Banco Ribeirão Preto S.A.',
            '743' => 'Banco Semear S.A.',
            '745' => 'Banco Citibank S.A.',
            '746' => 'Banco Modal S.A.',
            '747' => 'Banco Rabobank International Brasil S.A.',
            '748' => 'Banco Cooperativo Sicredi S.A.',
            '749' => 'Banco Simples',
            '751' => 'Scotiabank Brasil S.A. Banco Múltiplo',
            '752' => 'Banco BNP Paribas Brasil S.A.',
            '753' => 'Novo Banco Continental S.A. - Banco Múltiplo',
            '754' => 'Banco Sistema S.A.',
            '755' => 'Bank of America Merrill Lynch Banco Múltiplo S.A.',
            '756' => 'Banco Cooperativo do Brasil S.A. - BANCOOB',
            '757' => 'Banco KEB HANA do Brasil S.A.',
            '087-6' => 'Cooperativa Central de Economia e Crédito Mútuo das Unicreds de Santa Catarina e Paraná',
            '089-2' => 'Cooperativa de Crédito Rural da Região da Mogiana',
            '090-2' => 'Cooperativa Central de Economia e Crédito Mutuo - SICOOB UNIMAIS',
            '091-4' => 'Unicred Central do Rio Grande do Sul',
            '098-1' => 'CREDIALIANÇA COOPERATIVA DE CRÉDITO RURAL',
            '114-7' => 'Central das Cooperativas de Economia e Crédito Mútuo do Estado do Espírito Santo Ltda.',
            '-' => 'cc do norte catarinense e sul paranaense',
            '' => 'cc do norte catarinense e sul paranaense',
        );
    }
}

if (!function_exists('printInformacaoBanco')) {
    /**
     * @param $campo
     * @return string
     */
    function printInformacaoBanco($object, $campo, $label, callable $callbackGetValor = null): string
    {
        $valor = $object->{$campo};
        if (!is_null($callbackGetValor)) {
            $valor = $callbackGetValor($valor, $object);
        }

        return "$label: <strong>{$valor}</strong>";
    }
}

if (!function_exists('print_valor_vindo_pagar_me')) {
    /**
     * @param $valor
     * @return string
     */
    function print_valor_vindo_pagar_me($valor): string
    {
        $valor = number_format(($valor / 100),2, ',', '.');

        return "R$ {$valor}";
    }
}

if (!function_exists('valor_enviar_pagar_me')) {
    /**
     * @param $valor
     * @return int
     */
    function valor_enviar_pagar_me($valor)
    {
        return number_format(($valor*100), 0, '', '');
    }
}

if(!function_exists('getValorBiguacu')){
    function getValorBiguacu($value){
        $valorProduto = (float)$value;
        // aqui vira a regra de porcentagem que a biguaçu irá receber...
        // 16,7 / 100 = 0,167 round = 0,17
        return round((($valorProduto * 15.0)/100), 2);
    }
}

if(!function_exists('getValorBiguacuServico')){
    function getValorBiguacuServico($value){
        $valorProduto = (float)$value;
        // aqui vira a regra de porcentagem que a biguaçu irá receber...
        // 16,7 / 100 = 0,167 round = 0,17
        return round((($valorProduto * 15.0)/100), 2);
    }
}

if(!function_exists('getInfoDash')){
    function getInfoDash($request){
        $date_ini = date('Y-m-d', strtotime(str_replace('/','-', $request->date_ini)));
        $date_fim = date('Y-m-d', strtotime(str_replace('/','-', $request->date_fim)));
        $getTimeDiff = getTimeDiff($date_ini, $date_fim);
        $nome_mes = [
            '01' => 'Jan',
            '02' => 'Fev',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'Mai',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Set',
            '10' => 'Out',
            '11' => 'Nov',
            '12' => 'Dez',
        ];

        $order = Order::where('parent_id', '!=', null)->whereDate('created_at', '>=', $date_ini)->whereDate('created_at', '<=', $date_fim);
        if(auth()->guard('seller')->check()) $order = $order->where('seller_id', auth()->guard('seller')->user()->id);
        $order = $order->get();
        $order_service = OrderService::whereDate('created_at', '>=', $date_ini)->whereDate('created_at', '<=', $date_fim);
        if(auth()->guard('seller')->check()) $order_service = $order_service->where('seller_id', auth()->guard('seller')->user()->id);
        $order_service = $order_service->get();
        $acessos = ViewProductsService::whereDate('created_at', '>=', $date_ini)->whereDate('created_at', '<=', $date_fim);
        if(auth()->guard('seller')->check()) $acessos = $acessos->where('seller_id', auth()->guard('seller')->user()->id);
        $acessos = $acessos->get();

        $order_recent = $order->sortByDesc('order_number')->map(function ($query){
            $order_link = '';
            if(auth()->guard('admin')->check()) $order_link = route('ver_pedido', $query->order_number);
            if(auth()->guard('seller')->check()) $order_link = route('seller.ver_pedido', $query->order_number);
            return [
                'order' => $query->order_number,
                'order_link' => '<a class="order_link" target="_blank" href="'.$order_link.'">'.$query->order_number.' - P</a>',
                'data' => date('d/m/Y', strtotime($query->created_at)),
                'cliente' => explode(' ', $query->user_name)[0],
                'valor' => 'R$ '.number_format($query->total_value, 2, ',', '.'),
            ];
        });
        $order_service_recent = $order_service->sortByDesc('order_number')->map(function ($query){
            $order_link = '';
            if(auth()->guard('admin')->check()) $order_link = route('ver_pedido.servico', $query->order_number);
            if(auth()->guard('seller')->check()) $order_link = route('seller.ver_pedido.servico', $query->order_number);
            return [
                'order' => $query->order_number,
                'order_link' => '<a class="order_link" target="_blank" href="'.$order_link.'">'.$query->order_number.' - S</a>',
                'data' => date('d/m/Y', strtotime($query->created_at)),
                'cliente' => explode(' ', $query->user_name)[0],
                'valor' => 'R$ '.number_format($query->service_value, 2, ',', '.'),
            ];
        });
        if($order_recent->count() == 0){
            $order_recent = $order_service_recent->merge($order_recent)->take(7);
        }else{
            $order_recent = $order_recent->merge($order_service_recent)->take(7);
        }

        $vendas_p = $order->map(function ($query){return $query->total_value;})->sum();
        $vendas_s = $order_service->map(function ($query){return $query->service_value;})->sum();

        $order_valor = collect();
        $order->map(function ($query) use($order_valor){
            $order_valor->add([
                'date' => date('Y-m-d', strtotime($query->created_at)),
                'valor' => $query->total_value
            ]);
        });
        $order_service->map(function ($query) use($order_valor){
            $order_valor->add([
                'date' => date('Y-m-d', strtotime($query->created_at)),
                'valor' => $query->service_value
            ]);
        });
        $order_valor = $order_valor->sortBy('date')->groupBy('date')->map(function ($query){
            return $query->map(function ($query2){
                return $query2['valor'];
            })->sum();
        });
        $venda_graf_series = collect();

        $acessos_graf = $acessos->map(function ($query){
            return [
                'date' => date('Y-m-d', strtotime($query->created_at)),
                'view' => 1
            ];
        })->sortBy('date')->groupBy('date')->map(function ($query){
            return $query->map(function ($query2){
                return $query2['view'];
            })->sum();
        });

        $acessos_graf_series = collect();
        $venda_graf_category = collect();
        for($i=0;$i<=$getTimeDiff->days; $i++){
            $date_refe = date('Y-m-d', strtotime('+ '.$i.' Days', strtotime($date_ini)));
            $venda_graf_category->add($nome_mes[explode('-',$date_refe)[1]].' '.explode('-',$date_refe)[2]);
            if(isset($order_valor[$date_refe])){
                $venda_graf_series->add(number_format($order_valor[$date_refe], 2,'.'));
            }else{
                $venda_graf_series->add(0);
            }

            if(isset($acessos_graf[$date_refe])){
                $acessos_graf_series->add($acessos_graf[$date_refe]);
            }else{
                $acessos_graf_series->add(0);
            }
        }

        if(auth()->guard('admin')->check()){
            $crescimento = [
                'vendedor' => Seller::all()->count(),
                'produto_ativo' => Produto::where('status', 1)->get()->count(),
                'servico_ativo' => Service::where('status', 1)->get()->count(),
                'novos_produtos' => Produto::whereDate('created_at', '>=', date('Y').'-'.date('m').'-01')->whereDate('created_at', '<=', date('Y-m-d'))->get()->count(),
                'total_clientes' => User::all()->count(),
                'novos_vendedores' => Seller::whereDate('created_at', '>=', date('Y').'-'.date('m').'-01')->whereDate('created_at', '<=', date('Y-m-d'))->get()->count(),
                'produtos_pendente' => Produto::where('status', 0)->get()->count(),
                'servicos_pendentes' => Service::where('status', 0)->get()->count(),
                'novos_servicos' => Service::whereDate('created_at', '>=', date('Y').'-'.date('m').'-01')->whereDate('created_at', '<=', date('Y-m-d'))->get()->count(),
                'total_clientes' => User::whereDate('created_at', '>=', date('Y').'-'.date('m').'-01')->whereDate('created_at', '<=', date('Y-m-d'))->get()->count(),
            ];
            $top_cinco_acessos = collect();
            OrderProduct::with('viewProduct')->get()->groupBy('product_id')->map(function($query, $key)use($top_cinco_acessos){
                $top_cinco_acessos->add([
                    'name' => $query->map(function($query2){
                        return $query2->product_name;
                    })[0],
                    'slug' => $query->map(function($query2){
                        return route('product', $query2->product->slug ?? 0);
                    })[0],
                    'total' => $query->map(function($query2){
                        return $query2->viewProduct;
                    })->count()
                ]);
            });
            ServiceReservation::with('viewService')->get()->groupBy('seller_id')->map(function($query, $key)use($top_cinco_acessos){
                $top_cinco_acessos->add([
                    'name' => $query->map(function($query2){
                        return $query2->service_name;
                    })[0],
                    'slug' => $query->map(function($query2){
                        return route('service', $query2->product->service_slug ?? 0);
                    })[0],
                    'total' => $query->map(function($query2){
                        return $query2->viewService;
                    })->count()
                ]);
            });
            // ----- ####
            $top_cinco_Produtos = OrderProduct::with('product')->get()->groupBy('product_id')->map(function($query){
                return [
                    'name' => $query->map(function($query2){
                        return $query2->product_name;
                    })[0],
                    'slug' => $query->map(function($query2){
                        return route('product', $query2->product->slug ?? 0);
                    })[0],
                    'total' => $query->count()
                ];
            })->sortByDesc('total')->take(5)->map(function($query){
                return '<div><a title="'.$query['name'].'" href="'.$query['slug'].'" target="_blank">'.$query['name'].'</a></div>';
            })->join('');
            // ----- ####
            $top_cinco_servicos = ServiceReservation::with('service')->get()->groupBy('service_id')->map(function($query){
                return [
                    'name' => $query->map(function($query2){
                        return $query2->service_name;
                    })[0],
                    'slug' => $query->map(function($query2){
                        return route('service', $query2->service->service_slug ?? 0);
                    })[0],
                    'total' => $query->count()
                ];
            })->sortByDesc('total')->take(5)->map(function($query){
                return '<div><a title="'.$query['name'].'" href="'.$query['slug'].'" target="_blank">'.$query['name'].'</a></div>';
            })->join('');
            // ----- ####
            $top_cinco_vendedoresp = OrderProduct::with('seller')->get()->groupBy('seller_id')->map(function($query){
                return [
                    'name' => $query->map(function($query2){
                        return $query2->seller->store->store_name;
                    })[0],
                    'slug' => $query->map(function($query2){
                        return route('seller.store', $query2->seller->store->store_slug ?? 0);
                    })[0],
                    'total' => $query->count()
                ];
            })->sortByDesc('total')->take(5)->map(function($query){
                return '<div><a title="'.$query['name'].'" href="'.$query['slug'].'" target="_blank">'.$query['name'].'</a></div>';
            })->join('');
            // ----- ####
            $top_cinco_vendedoress = ServiceReservation::with('seller')->get()->groupBy('seller_id')->map(function($query){
                return [
                    'name' => $query->map(function($query2){
                        return $query2->seller->store->store_name;
                    })[0],
                    'slug' => $query->map(function($query2){
                        return route('service', $query2->seller->store->store_slug ?? 0);
                    })[0],
                    'total' => $query->count()
                ];
            })->sortByDesc('total')->take(5)->map(function($query){
                return '<div><a title="'.$query['name'].'" href="'.$query['slug'].'" target="_blank">'.$query['name'].'</a></div>';
            })->join('');
            //------------------------
            $ranking = [
                'top_produtos' => $top_cinco_Produtos,
                'top_servicos' => $top_cinco_servicos,
                'top_vendedores_p' => $top_cinco_vendedoresp,
                'top_vendedores_s' => $top_cinco_vendedoress,
                'top_acessos' => $top_cinco_acessos->take(5)->map(function($query){
                    return '<div><a title="'.$query['name'].'" href="'.$query['slug'].'" target="_blank">'.$query['name'].'</a></div>';
                })->join(''),
            ];
        }

        return [
            'vendas' => 'R$ '.number_format(($vendas_p + $vendas_s), 2, ',', '.'),
            'qty_produtos_servicos' => ($order->count() + $order_service->count()),
            'qty_acessos' => $acessos->count(),
            'pedidos' => [
                'total' => $order_recent->count(),
                'trs' => $order_recent->sortByDesc('order')->map(function($query){
                    return '
                        <tr>
                            <td style="font-size: 12px;">'.$query['order_link'].'</td>
                            <td style="font-size: 12px;">'.$query['data'].'</td>
                            <td style="font-size: 12px;">'.$query['cliente'].'</td>
                            <td style="font-size: 12px;">'.$query['valor'].'</td>
                        </tr>
                    ';
                })->join('')
            ],
            'venda_graf' => [
                'series' => $venda_graf_series,
                'category' => $venda_graf_category,
            ],
            'acessos_graf' => [
                'series' => $acessos_graf_series,
                'category' => $venda_graf_category,
            ],
            'crescimento' => $crescimento ?? null,
            'ranking' => $ranking ?? null,
        ];
    }
}

if(!function_exists('copiar_diretorio')){
    function copiar_diretorio($diretorio, $destino, $ver_acao = false){
        if ($destino[strlen($destino) - 1] == '/'){
            $destino = substr($destino, 0, -1);
        }
        if (!is_dir($destino)){
            if ($ver_acao){
                echo "Criando diretorio {$destino}\n";
                }
            mkdir($destino, 0755);
        }

        $folder = opendir($diretorio);

        while ($item = readdir($folder)){
            if ($item == '.' || $item == '..'){
                continue;
                }
            if (is_dir("{$diretorio}/{$item}")){
                copy_dir("{$diretorio}/{$item}", "{$destino}/{$item}", $ver_acao);
            }else{
                if ($ver_acao){
                    echo "Copiando {$item} para {$destino}"."\n";
                }
                copy("{$diretorio}/{$item}", "{$destino}/{$item}");
            }
        }
    }
}

if(!function_exists('calcCoupon')){
    function calcCoupon(){
        $coupon = collect(session()->get('session_coupons') ?? []);
        $items = collect(session()->get('session_cart'));
        $discount = collect();

        if(($coupon['coupon_valid'] ?? '') == 'product_discount'){
            $items->map(function($query)use($coupon,$discount){
                if($coupon['seller_id'] == $query['attributes']['seller_id']){
                    if(($coupon['check_loja'] ?? true)){
                        $discount->add($query['price']*$query['quantity']);
                    }else{
                        if(in_array(($query['attributes']['product_id'] ?? 0), ($coupon['product_id'] ?? []))){
                            if($coupon['discount_config'] == 'porcentage') $discount->add((($query['price']*$query['quantity'])*$coupon['value_discount'])/100);
                            if($coupon['discount_config'] == 'money') $discount->add($coupon['value_discount']);
                        }
                    }
                }
            });

            if(($coupon['check_loja'] ?? true)){
                if($coupon['discount_config'] == 'porcentage') $discount['dp'] = (($discount->sum()*$coupon['value_discount'])/100);
                if($coupon['discount_config'] == 'money') $discount['dp'] = $coupon['value_discount'];
            }else{
                $discount['dp'] = $discount->sum();
            }
        }elseif(($coupon['coupon_valid'] ?? '') == 'delivery_free'){
            $discount['ftv'] = 'free';
        }elseif(($coupon['coupon_valid'] ?? '') == 'delivery_discount'){
            $discount['ftv'] = 'discount';
            $discount['ftd'] = $coupon['value_discount'];
            $discount['ftc'] = $coupon['discount_config'];
        }

        $discount['seller_id'] = $coupon['seller_id'] ?? 0;

        // \Log::info($coupon->toArray());

        return $discount;
    }
}

// if(!function_exists('calcCouponService')){
//     function calcCouponService(){
//         $coupon = collect(session()->get('coupons_service')[0] ?? []);
//         $item = json_decode(json_encode(session()->get('cart_session')));
//         $discount = collect();

//         if(($coupon['coupon_valid'] ?? '') == 'product_discount'){
//             if($coupon['seller_id'] == $item->attributes->seller_id){
//                 if(($coupon['check_loja'] ?? true)){
//                     $discount->add($item->price*$item->quantity);
//                 }else{
//                     if(in_array(($item->attributes->service_id ?? 0), ($coupon['service_id'] ?? []))){
//                         if($coupon['discount_config'] == 'porcentage') $discount->add((($item->price*$item->quantity)*$coupon['value_discount'])/100);
//                         if($coupon['discount_config'] == 'money') $discount->add($coupon['value_discount']);
//                     }
//                 }
//             }

//             if(($coupon['check_loja'] ?? true)){
//                 if($coupon['discount_config'] == 'porcentage') $discount['dp'] = (($discount->sum()*$coupon['value_discount'])/100);
//                 if($coupon['discount_config'] == 'money') $discount['dp'] = $coupon['value_discount'];
//             }else{
//                 $discount['dp'] = $discount->sum();
//             }
//         }

//         // \Log::info($coupon->toArray());

//         return $discount;
//     }
// }

if(!function_exists('getPorcentCoupon')){
    function getPorcentCoupon(){
        // $coupon = calcCoupon();
        $coupon = collect(session()->get('coupons')[0] ?? []);
        $items = collect(cart_show()->content);
        $discount = collect();

        $items->map(function($query)use($coupon,$discount){
            if($coupon['seller_id'] == $query->attributes->seller_id) $discount->add($query->price*$query->quantity);
        });

        return number_format((100-(($discount->sum()-$coupon['value_discount'])/$discount->sum())*100), 2, '.','');
    }
}

if(!function_exists('getPorcentCouponService')){
    function getPorcentCouponService(){
        // $coupon = calcCoupon();
        $coupon = collect(session()->get('coupons_service')[0] ?? []);
        $item = json_decode(json_encode(session()->get('cart_session')));
        $discount = collect();

        if($coupon['seller_id'] == $item->attributes->seller_id) $discount->add($item->price*$item->quantity);

        return number_format((100-(($discount->sum()-$coupon['value_discount'])/$discount->sum())*100), 2, '.','');
    }
}

if(!function_exists('account_types_pagarme')){
    function account_types_pagarme(){
        return [
            'conta_corrente',
            'conta_poupanca',
        ];
    }
}

if(!function_exists('planCobranca')){
    function planCobranca($select_interval){
        $select_interval = explode('-', $select_interval);
        return 'Cobrança a cada '.$select_interval[0].' - '.($select_interval[1] == 'week' ? 'Semana' : 'Mês');
    }
}

if(!function_exists('getTotalEntrega')){
    function getTotalEntrega($request){
        switch($request['select_entrega']){
            case 'semanal':
                // date('w', strtotime(date('Y-m-d')))
                // $get_time = getTimeDiff(date('Y-m-d'), date('Y-m-d', strtotime('+ '.$request['duration_plan'].'Months')));
                // $semana_total = ((int)($get_time->days/7))+1;
                return 5;
                break;
            case 'quinzenal':
                return 2;
                break;
            case 'mensal':
                return 1;
                break;
            case 'trimestral':
                return 1;
                break;
        }
    }
}

if(!function_exists('replaceEspecial')){
    function replaceEspecial($str){
        return preg_replace('/[0-9\?\!\#\$\%\&\@\.\;]+/', '', \Str::of($str)->ascii());
    }
}

if(!function_exists('sendWhatsapp')){
    function sendWhatsapp($phone, $msg){
        $teste = \Http::withHeaders(['api-key' => '$2a$06$ahzCMdeNCkbCVc3T7BYGKeRxtelec22cyOAGKEcaLXNHm3EAfbZZy'])->post('https://api.notiway.com.br/v1/send-text',[
            'to' => $phone,
            'message' => $msg,
        ])->object();

        return true;
    }
}