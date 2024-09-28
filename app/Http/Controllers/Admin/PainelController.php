<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Bairro;
use App\Models\Produto;
use App\Models\Service;
use App\Models\Category;
use App\Models\Seller;

use App\Models\Attribute;
use App\Models\AviseMeQD;
use App\Models\SeoConfig;
use App\Models\Newsletter;
use App\Models\FormContact;

use App\Models\StarProduct;
use App\Models\StarService;
use App\Models\TabelaGeral;
use Illuminate\Support\Str;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\AffiliateInfo;
use App\Models\CustomerAddress;
use App\Http\Controllers\Controller;
use App\Models\ProductSaleAffiliate;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

use App\Models\Cart as CartModel;

class PainelController extends Controller
{
    public function indexDashboard()
    {
        $fale_conosco = FormContact::where('status', 0)->orderBy('created_at', 'DESC')->get()->take(5);
        $star_product = StarProduct::whereHas('product')->where('status', 0)->orderBy('created_at', 'ASC')->get()->take(3);
        $star_service = StarService::whereHas('service')->where('status', 0)->orderBy('created_at', 'ASC')->get()->take(3);
        // dd($star_service);
        return view('painel.indexDashboard', get_defined_vars());
    }

    public function indexPerfil()
    {
        return view('painel.indexPerfil');
    }

    public function indexContas()
    {
        $accounts = Admin::where('id', '!=', auth()->guard('admin')->user()->id)->paginate(10);

        return view('painel.indexContas', compact('accounts'));
    }

    public function indexClientes(Request $request)
    {
        $accounts = User::with(['adresses'])->where(function($query) use($request){
            if(isset($request->search_value)) return $query->where($request->column_name, 'LIKE', '%'.$request->search_value.'%');
        })->paginate($perPage = ($request->per_page ?? 20), $columns = ['*'], $pageName = 'clientes');

        return view('painel.clientes.indexClientes', get_defined_vars());
    }

    public function enderecosCliente($id)
    {
        $user = User::where('id', $id)->first();
        $addresses = CustomerAddress::where('user_id', $id)->get();

        return view('painel.clientes.enderecosCliente', compact('user', 'addresses'));
    }

    public function indexVendedores(Request $request)
    {
        $accounts = Seller::where(function($query) use($request){
            if(isset($request->search_value)) return $query->where($request->column_name, 'LIKE', '%'.$request->search_value.'%');
        })->paginate($perPage = ($request->per_page ?? 20), $columns = ['*'], $pageName = 'vendedores');

        return view('painel.clientes.indexVendedores', get_defined_vars());
    }

    public function indexAfiliados(Request $request)
    {
        $afiliados = AffiliateInfo::with(['users'])->whereHas('users',function($query) use($request){
            if(isset($request->search_value)) return $query->where($request->column_name, 'LIKE', '%'.$request->search_value.'%');
        })->paginate($perPage = ($request->per_page ?? 20), $columns = ['*'], $pageName = 'afiliados');
        $users = User::with(['afiliados'])->orderBy('name', 'ASC')->get()->filter(function ($query) {
            return (empty($query->afiliados));
        });

        return view('painel.clientes.indexAfiliados', get_defined_vars());
    }

    public function pedidosAfiliados($id)
    {
        $psAfiliadosPedidos = ProductSaleAffiliate::where('affiliate_id', User::find($id)->afiliados->id)->orderBy('created_at', 'DESC')->paginate(15);

        return view('painel.clientes.indexAfiliadosPedidos', get_defined_vars());
    }

    public function lojaVendedor($id)
    {
        $seller = Seller::where('id', $id)->first();

        return view('painel.clientes.lojaVendedor', get_defined_vars());
    }

    public function indexCategoria($type = null,$id = null)
    {
        $type = $type == 'produtos' ? '0' : '1';
        if($id){
            // $categories = Category::where('parent_id', $id)->get();
            // $category_name = Category::where('id', $id)->first()->name;
        }else{
            $categories = Category::whereNull('parent_id')->where('type', $type)->with(['subCategories'])->get();
            $category_name = '';
        }
        return view('painel.cadastros.indexCategorias', compact('id', 'type', 'category_name', 'categories'));
    }

    public function indexAtributo($id = null)
    {
        if(auth('seller')->check()){
            $sellers = [auth('seller')->user()];
            Attribute::addGlobalScope(function ($query){
                $query->where('vendedor_id', auth('seller')->user()->id);
            });
        }else{
            $sellers = Seller::all();
        }
        if($id){
            $attributes = Attribute::where('parent_id', $id)->get();
            $attribute = Attribute::where('id', $id)->first();
        }else{
            $attributes = Attribute::whereNull('parent_id')->with(['variations'])->orderBy('vendedor_id')->get();
            $attribute = '';
        }

        return view('painel.cadastros.indexAtributo', get_defined_vars());
    }

    public function bairros()
    {
        $bairros = Bairro::paginate(15);
        return view('painel.cadastros.indexBairros', get_defined_vars());
    }

    public function rateProduct($status = 'nao-aprovado')
    {
        $status_code = $status == 'nao-aprovado' ? '0' : '1';
        // $star_products = StarProduct::with(['user','product.images'])->whereHas('product')->where('status', $status_code)->get();
        $form_contacts_table = [
            ['data' => 0, 'name'=> 'imagem', 'ordering' => false],
            ['data' => 1, 'name'=> 'usuario', 'ordering' => false],
            ['data' => 2, 'name'=> 'nome_produto', 'ordering' => false],
            ['data' => 3, 'name'=> 'estrelas', 'ordering' => false],
            ['data' => 4, 'name'=> 'comentario', 'ordering' => false],
            ['data' => 5, 'name'=> 'ação', 'orderable' => false],
        ];
        return view('painel.outros.rateProduct', get_defined_vars());
    }

    public function rateProductSend(Request $request)
    {
        if($request->info == '1'){
            StarProduct::find($request->id)->update([
                'comment' => $request->comment,
                'status' => 1
            ]);
        }else{
            StarProduct::find($request->id)->delete();
        }
        return response()->json($request->all(), 200);
    }

    public function rateService()
    {
        $star_services = StarService::with(['user','service.images'])->whereHas('service')->where('status', '0')->get();
        return view('painel.outros.rateService', get_defined_vars());
    }

    public function rateServiceSend(Request $request)
    {
        if($request->info == '1'){
            StarService::find($request->id)->update([
                'comment' => $request->comment,
                'status' => 1
            ]);
        }else{
            StarService::find($request->id)->delete();
        }
        return response()->json($request->all(), 200);
    }

    public function contacts()
    {
        // $contacts = FormContact::orderBy('updated_at', 'DESC')->paginate(10);
        $form_contacts_table = [
            ['data' => 0, 'name'=> 'created_at', 'ordering' => true],
            ['data' => 1, 'name'=> 'name'],
            ['data' => 2, 'name'=> 'email'],
            ['data' => 3, 'name'=> 'phone'],
            ['data' => 4, 'name'=> 'assunto'],
            ['data' => 5, 'name'=> 'mensagem'],
            ['data' => 6, 'name'=> 'status'],
            ['data' => 7, 'name'=> 'responsavel', 'orderable' => false],
            ['data' => 8, 'name'=> 'ação', 'orderable' => false],
        ];
        return view('painel.outros.indexContact', get_defined_vars());
    }

    public function contactsStatus(Request $request)
    {
        FormContact::find($request->id)->update(['status' => $request->status]);

        return response()->json('okay', 200);
    }

    public function contactsResponsavel(Request $request)
    {
        FormContact::find($request->id)->update(['responsavel_id' => $request->responsavel_id]);

        return response()->json('okay', 200);
    }

    public function contactsRemove(Request $request)
    {
        FormContact::find($request->id)->delete();

        return response()->json('okay', 200);
    }

    public function buscaAttrs(Request $request)
    {
        $attrs = Attribute::whereNull('parent_id')->where('vendedor_id', $request->seller_id)->get();

        return response()->json($attrs, 200);
    }

    public function buscaAttrsVar(Request $request)
    {
        $attrs = Attribute::with('variations')->where('id', $request->attr_id)->where('vendedor_id', $request->seller_id)->first();

        return response()->json($attrs, 200);
    }

    public function indexFaturamento()
    {
        return view('painel.outros.indexFaturamento', get_defined_vars());
    }

    public function getFaturamento(Request $request)
    {
        $date_ini = date('Y-m-d', strtotime(str_replace('/','-', $request->date_ini)));
        $date_fim = date('Y-m-d', strtotime(str_replace('/','-', $request->date_fim)));

        #---#
        $order = Order::with('seller.store')->whereIn('pay', [1,2])->where('parent_id', '!=', null)->whereDate('created_at', '>=', $date_ini)->whereDate('created_at', '<=', $date_fim);
        $order = $order->get();
        #---#
        $order_service = OrderService::whereIn('pay', [1,2])->whereDate('created_at', '>=', $date_ini)->whereDate('created_at', '<=', $date_fim);
        $order_service = $order_service->get();
        #---#

        $seller = $order->map(function($query){
            return $query->seller;
        })->groupBy('id')->map(function($query){
            return $query->first();
        });
        // \Log::info($valores);
        $data = [
            'total_v' => $order->sum('total_value')+$order_service->sum('total_value'),
            'sellers' => $seller,
            'valor_total_vendedor_p' => $order->groupBy('seller_id')->map(function($query){return $query->sum('total_value');}),
            'valor_liquido_vendedor_p' => $order->groupBy('seller_id')->map(function($query){return $query->sum('product_value');}),
            'valor_liquido_vendedor_s' => $order_service->groupBy('seller_id')->map(function($query){return $query->sum('service_value');}),
        ];

        return response()->json($data);
    }

    // SEO
    public function seoConfig()
    {
        return view('painel.outros.indexSEO', get_defined_vars());
    }
    public function seoRegister(Request $request)
    {
        $seo_create['page'] = $request->page;
        $seo_create['title'] = $request->title;
        $seo_create['link'] = $request->link;
        $seo_create['keywords'] = $request->keywords;
        $seo_create['description'] = $request->description;
        if(isset($request->banner_path)){
            $originalPath = storage_path('app/public/img_page/');
            if (!file_exists($originalPath)) {
                mkdir($originalPath, 0777, true);
            }

            $page_img = Image::make($request->banner_path);
            $page_img_name = Str::random().'.'.$request->banner_path->extension();
            $page_img->save($originalPath.$page_img_name);

            $seo_create['banner_path'] = 'img_page/'.$page_img_name;
        }

        if($request->id){
            SeoConfig::find($request->id)->update($seo_create);
        }else{
            SeoConfig::create($seo_create);
        }
        return response()->json('');
    }

    public function seoBucaInfo(Request $request)
    {
        $seo = SeoConfig::where('page', $request->get('page'))->first();
        return response()->json($seo);
    }

    public function tabelaGeral(Request $request)
    {
        $create['tabela'] = $request->tabela;
        $create['coluna'] = $request->coluna;
        $create['valor'] = $request->valor ?? null;
        $create['array_text'] = $request->array_text ?? null;
        $create['long_text'] = $request->long_text ?? null;

        if(TabelaGeral::where('tabela', $request->tabela)->where('coluna', $request->coluna)->get()->count() == 0){
            TabelaGeral::create($create);
        }else{
            TabelaGeral::where('tabela', $request->tabela)->where('coluna', $request->coluna)->update($create);
        }
        return response()->json('', 200);
    }

    public function parcelamentoRegras(Request $request)
    {
        $create['tabela'] = 'regra_parcelamento';
        $create['coluna'] = 'parcelas';
        $create['valor'] = 'parcelas';
        $create['array_text'] = $request->parcela ?? null;
        $create['long_text'] = $request->long_text ?? null;

        if(TabelaGeral::where('tabela', 'regra_parcelamento')->where('coluna', 'parcelas')->get()->count() == 0){
            TabelaGeral::create($create);
        }else{
            TabelaGeral::where('tabela', 'regra_parcelamento')->where('coluna', 'parcelas')->update($create);
        }
        return response()->json('', 200);
    }

    public function indexNewsletter()
    {
        $form_newsletters_table = [
            ['data' => 0, 'name'=> 'created_at', 'ordering' => true],
            ['data' => 1, 'name'=> 'name'],
            ['data' => 2, 'name'=> 'email'],
            ['data' => 3, 'name'=> 'ação', 'orderable' => false],
        ];
        return view('painel.outros.indexNewsletter', get_defined_vars());
    }

    public function cancelNewsletter(Request $request)
    {
        Newsletter::find($request->id)->delete();

        return response()->json('okay', 200);
    }

    public function client_cart()
    {
        $carts = CartModel::all();
        $carts = $carts->groupBy('user_id');

        return view('painel.outros.indexClientCart', get_defined_vars());
    }

    public function client_cart_clean($user_id)
    {
        CartModel::where('user_id', $user_id)->delete();

        return redirect()->back();
    }

    public function client_cart_remove_item($item_id)
    {
        CartModel::find($item_id)->delete();

        return redirect()->back();
    }

    // Carregamento dos logs
    public function logs_view(Request $request)
    {
        if(($request->select_log_name ?? null)){
            $data_log = file_get_contents(realpath('../storage/'.$request->select_log_name));
            return response()->json(['data_response' => $data_log]);
        }
        //----------------------------------------------------
        $log_pagarme = scandir(realpath('../storage/logs_pagarme'));
        $log_pagarme = collect($log_pagarme)->filter(function($query){
            if($query == '.') return false;
            if(strstr($query, '..')) return false;
            if(strstr($query, '.gitignore')) return false;
            return $query;
        });
        $log_pagarme = $log_pagarme->map(function($query){
            return ['logs_pagarme/'.$query, $query];
        });
        //----------------------------------------------------
        $log_geral = scandir(realpath('../storage/logs'));
        $log_geral = collect($log_geral)->filter(function($query){
            if($query == '.') return false;
            if(strstr($query, '..')) return false;
            if(strstr($query, '.gitignore')) return false;
            return $query;
        });
        $log_geral = $log_geral->map(function($query){
            return ['logs/'.$query, $query];
        });

        $logs = $log_geral->merge($log_pagarme->toArray());
        // dd($log_pagarme);
        // dd($log_geral);
        return view('painel.outros.indexLogs', get_defined_vars());
    }

    // Verificando clientes que estão registrado para receebr aviso de produtos indicponiveis
    public function indexClienteAviseMeQD()
    {
        $form_avisemeqd_table = [
            ['data' => 0, 'name'=> 'created_at', 'ordering' => true],
            ['data' => 1, 'name'=> 'product', 'orderable' => false],
            ['data' => 2, 'name'=> 'name'],
            ['data' => 3, 'name'=> 'email'],
            ['data' => 4, 'name'=> 'ação', 'orderable' => false],
        ];
        return view('painel.outros.indexClienteAviseMeQD', get_defined_vars());
    }

    public function cancelClienteAviseMeQD(Request $request)
    {
        AviseMeQD::find($request->id)->delete();

        return response()->json('okay', 200);
    }

    // FUnção para buscar todas a tabelas por ajax
    public function allTables(Request $request)
    {
        // \Log::info($_GET);
        switch($_GET['table']){
            case 'form_contact':
                $draw = $request->get('draw');
                $start = $request->get("start");
                $rowperpage = $request->get("length"); // Rows display per page

                $columnIndex_arr = $request->get('order');
                $columnName_arr = $request->get('columns');
                $order_arr = $request->get('order');
                $search_arr = $request->get('search');

                $columnIndex = $columnIndex_arr[0]['column']; // Column index
                $columnName = $columnName_arr[$columnIndex]['name']; // Column name
                $columnSortOrder = $order_arr[0]['dir']; // asc or desc
                $searchValue = $search_arr['value']; // Search value

                // Total records
                $totalRecords = FormContact::select('count(*) as allcount')->count();
                $totalRecordswithFilter = FormContact::select('count(*) as allcount')->where('name', 'like', '%' .$searchValue . '%')->count();

                // Fetch records
                $records = FormContact::orderBy($columnName, $columnSortOrder)
                ->where('form_contacts.name', 'like', '%' .$searchValue . '%')
                    ->select('form_contacts.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->get();

                $data_arr = array();
                $sno = $start+1;
                foreach($records as $record){
                    if($record->status == 0){
                        $status = '<button type="button" class="btn btn-sm btn-block btn-status-contact" style="background-color: #FF8300;">Aguardando</button>';
                    }elseif($record->status == 1){
                        $status = '<button type="button" class="btn btn-sm btn-block btn-status-contact" style="background-color: #3D550C;">Em Andamento</button>';
                    }elseif($record->status == 2){
                        $status = '<button type="button" class="btn btn-sm btn-block btn-status-contact" style="background-color: #59981A;">Já Resolvido</button>';
                    }
                    $data_arr[] = array(
                        date('d/m/Y', strtotime(str_replace('-','/',$record->created_at))),
                        $record->name,
                        $record->email,
                        $record->phone,
                        '<span style="white-space: nowrap;width: 145px;display: block;" data-toggle="tooltip" title="'.($record->assunto).'">'.(strlen($record->assunto) > 12 ? substr(\Str::ascii($record->assunto), 0, 12).'...' : $record->assunto).'</span>',
                        '<span style="white-space: nowrap;width: 145px;display: block;" data-toggle="tooltip" title="'.($record->mensagem).'">'.(strlen($record->mensagem) > 12 ? substr(\Str::ascii($record->mensagem), 0, 12).'...' : $record->mensagem).'</span>',
                        $status,
                        '
                            <select class="form-control form-control-sm selectResponsavel" data-id="'.$record->id.'" data-url="'.route('admin.atualizar_responsavel_contact').'">
                                <option value="">Sem Responsavel</option>
                                '.Admin::get()->map(function($query) use($record){
                                    return '<option value="'.$query->id.'" '.($record->responsavel_id == $query->id ? 'selected' : '').'>'.$query->name.'</option>';
                                })->join('').'
                            </select>
                        ',
                        '
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-sm alterarStatusContact" data-url="'.route('admin.atualizar_status_contact').'" data-id="'.$record->id.'">Alterar Status</button>
                                <button type="button" class="btn btn-danger btn-sm btn-destroy" data-url="'.route('admin.remove_contact').'" data-id="'.$record->id.'">Apagar Contato</button>
                            </div>
                        '
                    );
                }

                $response = array(
                    "draw" => intval($draw),
                    "iTotalRecords" => $totalRecords,
                    "iTotalDisplayRecords" => $totalRecordswithFilter,
                    "aaData" => $data_arr
                );

                return response()->json($response, 200);
            break;
            case 'rate_product':
                $status_code = $request->get('status_code');

                $draw = $request->get('draw');
                $start = $request->get("start");
                $rowperpage = $request->get("length"); // Rows display per page

                $columnIndex_arr = $request->get('order');
                $columnName_arr = $request->get('columns');
                $order_arr = $request->get('order');
                $search_arr = $request->get('search');

                $columnIndex = $columnIndex_arr[0]['column']; // Column index
                $columnName = $columnName_arr[$columnIndex]['name']; // Column name
                $columnSortOrder = $order_arr[0]['dir']; // asc or desc
                $searchValue = $search_arr['value']; // Search value

                // Total records
                $totalRecords = StarProduct::select('count(*) as allcount')->whereHas('product')->where('status', $status_code)->count();
                $totalRecordswithFilter = StarProduct::select('count(*) as allcount')->whereHas('product')->where('status', $status_code)->count();

                // Fetch recordsStarProduct::with(['user','product.images'])->whereHas('product')->where('status', $status_code)->get();
                $records = StarProduct::with(['user','product.images'])
                ->whereHas('product')
                ->orderBy('created_at', 'DESC')
                ->where('status', $status_code)
                ->skip($start)
                ->take($rowperpage)
                ->get();

                $data_arr = array();
                $sno = $start+1;
                foreach($records as $record){
                    $stars = '';
                    for($i = 1; $i <= 5; $i++){
                        if($i <= $record->star){
                            $stars .= '<i class="fas fa-star text-warning"></i>';
                        }else{
                            $stars .= '<i class="fas fa-star"></i>';
                        }
                    }

                    $data_arr[] = array(
                        "<img width='60px' class='img-fluid rounded' src='".(($record->product->images->sortBy('position')->first() ?? null) ? $record->product->images->sortBy('position')->first()->caminho : asset('site/imgs/logo.png'))."' alt=''>",
                        "<span style='white-space: nowrap;width: 145px;display: block;' data-toggle='tooltip' title='{$record->user->name}'>".(strlen($record->user->name) > 12 ? substr($record->user->name, 0, 12).'...' : $record->user->name)."</span>",
                        "<span style='white-space: nowrap;width: 145px;display: block;' data-toggle='tooltip' title='{$record->product->nome}'>".(strlen($record->product->nome) > 12 ? substr($record->product->nome, 0, 12).'...' : $record->product->nome)."</span>",
                        $stars,
                        "<textarea class='form-control'>{$record->comment}</textarea>",
                        "
                            <div class='btn-group' role='group' aria-label=''>
                                <a href='#' class='btn btn-primary btn-send-admin-star' data-info='1' data-id='{$record->id}' data-route='".route('admin.rateProduct.send')."'>Aprovar</a>

                                <a href='#' class='btn btn-danger btn-send-admin-star' data-info='2' data-id='{$record->id}' data-route='".route('admin.rateProduct.send')."'>Excluir</a>
                            </div>
                        ",
                    );
                }

                $response = array(
                    "draw" => intval($draw),
                    "iTotalRecords" => $totalRecords,
                    "iTotalDisplayRecords" => $totalRecordswithFilter,
                    "aaData" => $data_arr
                ); 

                return response()->json($response, 200);
            break;
            case 'form_newsletter':
                $draw = $request->get('draw');
                $start = $request->get("start");
                $rowperpage = $request->get("length"); // Rows display per page

                $columnIndex_arr = $request->get('order');
                $columnName_arr = $request->get('columns');
                $order_arr = $request->get('order');
                $search_arr = $request->get('search');

                $columnIndex = $columnIndex_arr[0]['column']; // Column index
                $columnName = $columnName_arr[$columnIndex]['name']; // Column name
                $columnSortOrder = $order_arr[0]['dir']; // asc or desc
                $searchValue = $search_arr['value']; // Search value

                // Total records
                $totalRecords = Newsletter::select('count(*) as allcount')->count();
                $totalRecordswithFilter = Newsletter::select('count(*) as allcount')->where('name', 'like', '%' .$searchValue . '%')->count();

                // Fetch records
                $records = Newsletter::orderBy($columnName, $columnSortOrder)
                ->where('newsletters.name', 'like', '%' .$searchValue . '%')
                    ->select('newsletters.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->get();

                $data_arr = array();
                $sno = $start+1;
                foreach($records as $record){
                    if($record->status == 0){
                        $status = '<button type="button" class="btn btn-sm btn-block btn-status-contact" style="background-color: #fdc300;">Em Andamento</button>';
                    }elseif($record->status == 1){
                        $status = '<button type="button" class="btn btn-sm btn-block btn-status-contact" style="background-color: #58bc9a;">Já Resolvido</button>';
                    }
                    $data_arr[] = array(
                        date('d/m/Y', strtotime(str_replace('-','/',$record->created_at))),
                        $record->name,
                        $record->email,
                        '
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger btn-sm btn-destroy" data-url="'.route('admin.cancelNewsletter').'" data-id="'.$record->id.'">Cancelar Newsletter</button>
                            </div>
                        '
                    );
                }

                $response = array(
                    "draw" => intval($draw),
                    "iTotalRecords" => $totalRecords,
                    "iTotalDisplayRecords" => $totalRecordswithFilter,
                    "aaData" => $data_arr
                ); 

                return response()->json($response, 200);
            break;
            case 'form_avisemeqd':
                $draw = $request->get('draw');
                $start = $request->get("start");
                $rowperpage = $request->get("length"); // Rows display per page

                $columnIndex_arr = $request->get('order');
                $columnName_arr = $request->get('columns');
                $order_arr = $request->get('order');
                $search_arr = $request->get('search');

                $columnIndex = $columnIndex_arr[0]['column']; // Column index
                $columnName = $columnName_arr[$columnIndex]['name']; // Column name
                $columnSortOrder = $order_arr[0]['dir']; // asc or desc
                $searchValue = $search_arr['value']; // Search value

                // Total records
                $totalRecords = AviseMeQD::select('count(*) as allcount')->count();
                $totalRecordswithFilter = AviseMeQD::select('count(*) as allcount')->where('email', 'like', '%' .$searchValue . '%')->count();

                // Fetch records
                $records = AviseMeQD::orderBy($columnName, $columnSortOrder)
                ->where('avise_me_q_d_s.email', 'like', '%' .$searchValue . '%')
                    ->select('avise_me_q_d_s.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->get();

                $data_arr = array();
                $sno = $start+1;
                foreach($records as $record){
                    $data_arr[] = array(
                        date('d/m/Y', strtotime(str_replace('-','/',$record->created_at))),
                        Produto::find(str_replace('-P','', $record->item_id))->nome,
                        $record->name,
                        $record->email,
                        '
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger btn-sm btn-destroy" data-url="'.route('admin.clienteAviseMeQD').'" data-id="'.$record->id.'">Cancelar Aviso</button>
                            </div>
                        '
                    );
                }

                \Log::info($data_arr);

                $response = array(
                    "draw" => intval($draw),
                    "iTotalRecords" => $totalRecords,
                    "iTotalDisplayRecords" => $totalRecordswithFilter,
                    "aaData" => $data_arr
                ); 

                return response()->json($response, 200);
            break;
            case 'getProductsSellers':
                $data = collect([]);

                foreach(explode(',', $request->sellers_id) as $seller_id){
                    $products = Produto::where('seller_id', $seller_id)->with('seller')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->get();
                    $services = Service::where('seller_id', $seller_id)->with('seller')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->get();

                    $data->add(['seller_id' => $seller_id, 'seller' => Seller::with('store')->find($seller_id), 'products' => $products->toArray(), 'services' => $services->toArray()]);
                }
                return response()->json($data);
            break;
        }
    }
}
