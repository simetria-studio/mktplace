<?php

namespace App\Http\Controllers;

use App\Dtos\Vendedor;
use App\Models\Produto;
use App\Models\Category;
use App\Models\Seller;
use App\Models\Attribute;
use App\Models\AviseMeQD;
use Illuminate\Support\Str;
use App\Models\PlanPurchase;
use Illuminate\Http\Request;
use App\Models\ProdutoFactor;
use App\Models\ImagensProduto;
use App\Dtos\VariationsProduto;
use App\Mail\AvisarStockClient;
use App\Models\ProductCategory;
use App\Models\AttributeProduct;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\ProgressiveDiscount;
use Illuminate\Support\Facades\Mail;
use App\Models\VariationsProduto as VP;
use Illuminate\Support\Facades\Storage;
use App\Models\ValuesVariationsProduto as VVP;
use Intervention\Image\ImageManagerStatic as Image;

class ProdutoController extends Controller
{
    public function produtos()
    {
        return Produto::where(function($query){
            if(auth()->guard('seller')->check()) return $query->where('seller_id', auth()->guard('seller')->user()->id);
        });
    }

    public function index(Request $request, $function_slug = null)
    {
        $product = null;
        $podeCriarProduto = true;
        $seller = Seller::all();
        $categories = Category::where('type', 0)->get();
        $get_url_appends = !empty($_GET) ? '?'.http_build_query((isset($_GET['page']) ? collect($_GET)->forget('page')->toArray() : $_GET)) : '';
        if($function_slug){
            \Log::channel('log_edit_geral')->info([
                'usuario' => (auth()->guard('admin')->check() ? auth()->guard('admin')->user()->name : auth()->guard('seller')->user()->name),
                'url' => $request->route()->uri,
                'requests' => $request->all()
            ]);
        }
        switch($function_slug){
            case 'add-stock':
                return $this->addStock($request);
            break;
            case 'clonar':
                return $this->clone($request);
            break;
            case 'ativar-desativar':
                return $this->ativo($request);
            break;
            case 'apagar':
                return $this->destroy($request);
            break;
            case 'analisar':
            case 'novo':
            case 'editar':
                $novo_product_id = $_COOKIE['novo_product_id'] ?? null;
                if($function_slug == 'novo' && !isset($request->step)) {
                    setCookie('novo_product_id');
                    $novo_product_id = null;
                }
                if($request->id ?? $novo_product_id) $product = Produto::with(['planPurchases', 'images', 'seller', 'variations.atributos', 'categories.category', 'attrs.attribute.variations', 'fatoresProduto', 'progressiveDiscount'])->find($request->id ?? ($_COOKIE['novo_product_id'] ?? null));
                // \Log::info($product->toArray());
                $request->function_slug = $function_slug;
                if(isset($request->postProduct)) return $this->{$request->postType}($request);
                return view('painel.cadastros.produto.formProduto', get_defined_vars());
            break;
            default:
                $product_count = $this->productCount($request);
                $produtos = $this->getProducts($request, $function_slug);
                return view('painel.cadastros.produto.indexProduto', get_defined_vars());
            break;
        }
    }

    // Alterando entre ativo e inativo o produto
    public function addStock($request)
    {
        Produto::find($request->id)->update(['stock' => Produto::find($request->id)->stock+$request->stock]);

        AviseMeQD::where('item_id', $request->id.'-P')->each(function ($query) use($request){
            $produto = Produto::find($request->id);

            Mail::to($query->email)->send(new AvisarStockClient(route('product', $produto->slug)));

            $query->delete();
        });

        return response()->json('');
    }

    // Alterando entre ativo e inativo o produto
    public function ativo($request)
    {
        $produto = Produto::find($request->id);
        $produto->status = 0;
        if(auth()->guard('admin')->check()){
            $produto->status = $request->ativo == 'N' ? 0 : 1;
        }
        $produto->ativo = $request->ativo;
        $produto->save();
        return response()->json('');
    }

    // Clonando um produto
    public function clone($request)
    {
        $forget = ['id', 'created_at', 'updated_at'];
        $produto = Produto::find($request->id);
        $produto_clone = collect($produto)->forget($forget);
        $slug = \Str::slug($produto_clone['nome']);
        $produto_clone['nome'] = $produto_clone['nome'].' CLONE';
        $produto_slug = \App\Models\Produto::where('slug', 'like', '%'.$slug.'%')->get();

        $produto_slug = Produto::where('slug', 'like', '%'.$slug.'%')->get();
        $while_count = $produto_slug->count();
        $while_loop = true;
        while($while_loop){
            $produto_slug_consulta = Produto::where('slug', $slug.($while_count > 0 ? '-'.($while_count) : ''))->get();
            if($produto_slug_consulta->count() == 0) {
                $produto_clone['slug'] = $slug.'-'.($while_count);
                $while_loop = false;
                break;
            }
            $while_count++;
        }
        $produto_clone = Produto::create($produto_clone->toArray());
        $produto = Produto::with('categories', 'attrAttrs', 'variations.variations', 'images')->find($request->id);

        if(isset($produto->categories)){
            foreach($produto->categories as $cat){
                $cat_clone = collect($cat)->forget($forget);
                $cat_clone['product_id'] = $produto_clone->id;
                ProductCategory::create($cat_clone->toArray());
            }
        }

        if($produto->attrAttrs){
            foreach($produto->attrAttrs as $attr){
                $attr_clone = collect($attr)->forget($forget);
                $attr_clone['product_id'] = $produto_clone->id;
                AttributeProduct::create($attr_clone->toArray());
            }
        }

        if($produto->variations){
            foreach($produto->variations as $var){
                $var_clone = collect($var)->forget(['id', 'created_at', 'updated_at', 'variations']);
                $var_clone['produto_id'] = $produto_clone->id;
                $variation = VP::create($var_clone->toArray());

                foreach ($var->variations as $attr_var){
                    $attr_var_clone = collect($attr_var)->forget($forget);
                    $attr_var_clone['variations_produto_id'] = $variation->id;
                    VVP::create($attr_var_clone->toArray());
                }
            }
        }

        if($produto->images){
            foreach ($produto->images as $image) {
                $image_clone = collect($image)->forget($forget);
                $image_clone['produto_id'] = $produto_clone->id;
                $img_path = explode('/', $image->caminho);
                $originalPath = storage_path('app/public/produtos/'.$img_path[5].'/');
                $random_path = Str::random(50);
                $newPath = storage_path('app/public/produtos/'.$random_path.'/');
                if (!file_exists($newPath)) {
                    mkdir($newPath, 0777, true);
                }
                copiar_diretorio($originalPath,$newPath);
                $image_clone['caminho'] = asset('storage/produtos/'.$random_path.'/'.$img_path[6]);
                ImagensProduto::create($image_clone->toArray());
            }
        }

        // return response()->json(['id' => $produto_clone->id, 'name' => $produto->nome]);
        return response()->json(['id' => $produto_clone->id, 'redirect_url' => route('produto', 'editar').'?id='.$produto_clone->id, 'type' => 'produto']);
    }

    // Atualizações de Fotos
    public function postAddFotos($request)
    {
        $html = '';
        foreach($request->images as $image){
            $random_path = Str::random(50);
            $originalPath = storage_path('app/public/produtos/'.$random_path.'/');
            if (!file_exists($originalPath)) {
                mkdir($originalPath, 0777, true);
            }

            $product_img = Image::make($image);
            $product_img_name = Str::slug(preg_replace('/\..+$/', '', $image->getClientOriginalName())).'.'.$image->extension();
            $product_img->save($originalPath.$product_img_name);

            $imagens_create['produto_id'] = $request->product_id;
            $imagens_create['legenda'] = $image->getClientOriginalName();
            $imagens_create['texto_alternativo'] = $image->getClientOriginalName();
            $imagens_create['caminho'] = asset('storage/produtos/'.$random_path.'/'.$product_img_name);
            $imagens_create['pasta'] = 'public/produtos/'.$random_path;
            $imagens_create['position'] = ImagensProduto::where('produto_id', $request->product_id)->get()->count()+1;
            $image = ImagensProduto::create($imagens_create);
            $html .= view('painel.cadastros.produto.imagesProduto', get_defined_vars())->render();
        }

        return response()->json($html);
    }
    public function postUpdatePositionFotos($request)
    {
        foreach($request->positions as $position){
            ImagensProduto::find($position['foto_id'])->update(['position' => $position['position']]);
        }
    }
    public function postDeleteFoto($request)
    {
        $imagens_produto = ImagensProduto::find($request->foto_id);

        Storage::deleteDirectory($imagens_produto->pasta);
        $imagens_produto->delete();
    }

    #####FUNÇÕES DOS PRODUTOS#####
    public function postDataInicio($request)
    {
        $slug = !empty($request->slug) ? Str::slug($request->slug) : $slug = Str::slug($request->name);

        $produto_slug = Produto::where(function($query) use($request){
            if(isset($request->id)) $query = $query->where('id', '!=', $request->id);
            return $query;
        })->where('slug', 'like', '%'.$slug.'%')->get();
        if($produto_slug->count() > 0) {
            $while_count = $produto_slug->count();
            $while_loop = true;
            while($while_loop){
                $produto_slug_consulta = Produto::where(function($query) use($request){
                    if(isset($request->id)) $query = $query->where('id', '!=', $request->id);
                    return $query;
                })->where('slug', $slug.($while_count > 0 ? '-'.($while_count) : ''))->get();
                if($produto_slug_consulta->count() == 0) {
                    $slug = $slug.'-'.($while_count);
                    $while_loop = false;
                    break;
                }
                $while_count++;
            }
        }

        $product_iu['nome'] = $request->name;
        $product_iu['slug'] = $slug;
        $product_iu['seller_id'] = $request->seller_id;
        $product_iu['stock_controller'] = $request->stock_controller ?? null;
        $product_iu['perecivel'] = isset($request->perecivel) ? 1 : 0;
        $product_iu['preco'] = $request->preco ?? 0;
        $product_iu['stock'] = $request->stock;
        $product_iu['weight'] = $request->weight;
        $product_iu['height'] = $request->height;
        $product_iu['width'] = $request->width;
        $product_iu['length'] = $request->length;
        if($request->function_slug !== 'analisar'){
            if(auth()->guard('admin')->check()) $product_iu['status'] = 1;
            if(auth()->guard('seller')->check()) $product_iu['status'] = 0;
        }

        if(isset($request->id) && !empty((int)$request->id)){
            if(empty(Produto::find($request->id)->title)){
                $seo['title'] = $product_iu['title'] = $product_iu['nome'];
            }else{
                $seo['title'] = Produto::find($request->id)->title;
            }
            if(empty(Produto::find($request->id)->link)){
                $seo['link'] = $product_iu['link'] = 'produto/'.$slug;
            }else{
                $seo['link'] = Produto::find($request->id)->link;
            }

            $product_iu = Produto::find($request->id)->update($product_iu);
            $product_id = $request->id;
        }else{
            $seo['title'] = $product_iu['title'] = $product_iu['nome'];
            $seo['link'] = $product_iu['link'] = 'produto/'.$slug;

            $product_iu = Produto::create($product_iu);
            $product_id = $product_iu->id;
        }

        #########CATEGORIAS#########
            $categories = ProductCategory::where('product_id', $product_id)->get();
            $categories_exist = [];
            foreach($categories as $category){
                $categories_exist[] = $category->category_id;
                if(!in_array($category->category_id, $request->categories)){
                    ProductCategory::find($category->id)->delete();
                }
            }
            foreach(($request->categories ?? []) as $category){
                if(!in_array($category, $categories_exist)){
                    ProductCategory::create([
                        'product_id' => $product_id,
                        'category_id' => $category,
                    ]);
                }
            }
        #########CATEGORIAS#########

        #########DESCONTO-PROGRESIVO#########
            if(isset($request->check_desconto)){
                $delete_desconto = [];
                foreach(($request->discount ?? []) as $discount)
                {
                    $desconto = [
                        'discount_quantity' => $discount['discount_quantity'],
                        'discount_value'    => str_replace(',','.',str_replace('.','', $discount['discount_value'])),
                        'reference_id'      => $product_id,
                        'reference_type'    => 'product',
                    ];

                    if(isset($discount['id'])){
                        ProgressiveDiscount::find($discount['id'])->update($desconto);
                        $delete_desconto[] = $discount['id'];
                    }
                    else{
                        $desconto_id = ProgressiveDiscount::create($desconto);
                        $delete_desconto[] = $desconto_id->id;
                    }
                }

                ProgressiveDiscount::where('reference_id', $product_id)->where('reference_type', 'product')->whereNotIn('id', $delete_desconto)->delete();
            }
            if(!isset($request->check_desconto)) ProgressiveDiscount::where('reference_id', $product_id)->where('reference_type', 'product')->delete();
            if(isset($request->produto_variavel) || isset($request->plano_assinatura)) ProgressiveDiscount::where('reference_id', $product_id)->where('reference_type', 'product')->delete();
        #########DESCONTO-PROGRESIVO#########

        ###########APAGAR VARIAÇÕES E PLANOS###########
            if(isset($request->produto_simples)){
                ###########VARIAÇÃO###########
                $variation_produto = VP::where('produto_id', $product_id)->get();
                foreach($variation_produto as $vp){
                    VVP::where('variations_produto_id', $vp->id)->delete();
                    ProgressiveDiscount::where('reference_id', $vp->id)->where('reference_type', 'product_attr')->delete();
                }
                VP::where('produto_id', $product_id)->delete();
                ###########PLANOS###########
                PlanPurchase::where('reference_id', $request->id)->where('reference_type', 'product')->delete();
            }
            if(isset($request->produto_variavel)){
                ###########PLANOS###########
                PlanPurchase::where('reference_id', $request->id)->where('reference_type', 'product')->delete();
            }
            if(isset($request->plano_assinatura)){
                ###########VARIAÇÃO###########
                $variation_produto = VP::where('produto_id', $product_id)->get();
                foreach($variation_produto as $vp){
                    VVP::where('variations_produto_id', $vp->id)->delete();
                    ProgressiveDiscount::where('reference_id', $vp->id)->where('reference_type', 'product_attr')->delete();
                }
                VP::where('produto_id', $product_id)->delete();
            }
        ###########APAGAR VARIAÇÔES E PLANOS###########

        ######SALVANDO ANALISE DO PRODUTO######
            if(auth()->guard('admin')->check()){
                if($request->field_native){
                    foreach($request->field_native as $key => $field_native){
                        if(isset($field_native['field_name'])){
                            ProdutoFactor::find($key)->update([
                                'field_name' => $field_native['field_name'],
                                'field_value' => $field_native['field_value'],
                                'status' => (isset($field_native['field_status']) ? '1' : '0'),
                            ]);
                        }
                    }
                }

                if($request->field_text_native){
                    foreach($request->field_text_native as $key => $field_text_native){
                        ProdutoFactor::findOrFail($key)->update([
                            'field_value' => $field_text_native
                        ]);
                    }
                }
            }
        ######SALVANDO ANALISE DO PRODUTO######

        setCookie('novo_product_id', $product_id);
        return response()->json(['product_id' => $product_id, 'seo' => ($seo ?? false)]);
    }
    public function postDataDescricao($request)
    {
        if(isset($request->id)){
            $product_iu['descricao_curta'] = $request->descricao_curta;
            $product_iu['descricao_completa'] = $request->descricao_completa;
            if(empty(Produto::find($request->id)->description)) $product_iu['description'] = $request->descricao_curta;
            $product_iu = Produto::find($request->id)->update($product_iu);
        }
    }
    public function postDataFotos($request)
    {
        foreach(($request->fotos ?? []) as $key => $value){
            $image_update['legenda'] = $value['legenda'];
            $image_update['texto_alternativo'] = $value['texto_alternativo'];
            $image_update['principal'] = (int)($request->img_principal ?? 0) == $key ? 1 : 0;
            ImagensProduto::find($key)->update($image_update);
        }

        if(empty(Produto::find($request->id)->banner_path)) $seo['banner_path'] = Produto::find($request->id)->images->sortBy('position')->first()->caminho ?? '';

        $product = Produto::find($request->id);
        if((count($product->variations ?? []) == 0 && count($product->planPurchases ?? []) == 0)){
            if(auth()->guard('seller')->check()) return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'analise').'?product_name='.$product->nome.'&type_search=product&per_page=20']);
        }

        if(Produto::whereHas('images')->where('id', $request->id)->get()->count() == 0){
            ProdutoFactor::create([
                'product_id' => $request->id,
                'field_name' => 'imagens',
                'field_value' => 'Faltam imagens no produto'
            ]);

            Produto::find($request->id)->update(['status' => 0]);
        }

        if(auth()->guard('seller')->check()) return response()->json(['seo' => ($seo ?? false), 'redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'analise').'?product_name='.$product->nome.'&type_search=product&per_page=20']);
    }
    public function postDataVariacoes($request)
    {
        // \Log::info($request->all());
        if($request->attrs){
            $attrs = AttributeProduct::where('product_id', $request->id)->get();
            $attrs_exist = [];
            foreach($attrs as $attr){
                $attrs_exist[] = $attr->attr_id;
                if(!in_array($attr->attr_id, $request->attrs)){
                    AttributeProduct::find($attr->id)->delete();
                }
            }

            foreach($request->attrs as $attr){
                if(!in_array($attr, $attrs_exist)){
                    AttributeProduct::create([
                        'product_id' => $request->id,
                        'attribute_id' => $attr,
                    ]);
                }
            }
        }else{
            AttributeProduct::where('product_id', $request->id)->delete();
        }

        if($request->variations){
            $var_id = [];
            $var_price = [];
            $delete_desconto = [];
            foreach($request->variations as $var){
                $var_price[] = $var['preco'];
                if(isset($var['variation_id'])){
                    $var_id[] = $var['variation_id'];
                    VP::where('id', $var['variation_id'])->update([
                        'preco' => $var['preco'],
                        'stock' => ($var['stock'] ?? null),
                        'peso' => $var['peso'] ?? '',
                        'dimensoes_A' => $var['dimensoes_A'] ?? '',
                        'dimensoes_C' => $var['dimensoes_C'] ?? '',
                        'dimensoes_L' => $var['dimensoes_L'] ?? '',
                    ]);

                    $attr_var_id = [];
                    $attr_attr_id = [];
                    foreach ($var['attributos'] as $attr_var){
                        $attr_attr_id[] = explode('-', $attr_var)[1];
                        if(explode('-', $attr_var)[0] == 0){
                            foreach (Attribute::where('parent_id', explode('-', $attr_var)[1])->get() as $attr_var_temp){
                                $attr_var_id[] = $attr_var_temp->id;
                                
                                if(VVP::where('variations_produto_id', $var['variation_id'])->where('attribute_pai_id', explode('-', $attr_var)[1])->where('attribute_id', $attr_var_temp->id)->get()->count() == 0){
                                    VVP::create([
                                        'variations_produto_id' => $var['variation_id'],
                                        'attribute_id' => $attr_var_temp->id,
                                        'attribute_pai_id' => $attr_var_temp->parent_id, // problemas
                                    ]);
                                }
                            }
                        }else{
                            $attr_var_id[] = explode('-', $attr_var)[0];
                            if(VVP::where('variations_produto_id', $var['variation_id'])->where('attribute_pai_id', explode('-', $attr_var)[1])->where('attribute_id', explode('-', $attr_var)[0])->get()->count() == 0){
                                VVP::create([
                                    'variations_produto_id' => $var['variation_id'],
                                    'attribute_id' => explode('-', $attr_var)[0],
                                    'attribute_pai_id' => explode('-', $attr_var)[1],
                                ]);
                            }
                        }
                    }

                    if(VVP::where('variations_produto_id', $var['variation_id'])->whereIn('attribute_pai_id', $attr_attr_id)->whereNotIn('attribute_id', $attr_var_id)->get()->count() > 0){
                        VVP::where('variations_produto_id', $var['variation_id'])->whereIn('attribute_pai_id', $attr_attr_id)->whereNotIn('attribute_id', $attr_var_id)->delete();
                    }

                    if(isset($var['discount'])){
                        foreach($var['discount'] as $discount)
                        {
                            $desconto = [
                                'discount_quantity' => $discount['discount_quantity'],
                                'discount_value'    => str_replace(',','.',str_replace('.','', $discount['discount_value'])),
                                'reference_id'      => $var['variation_id'],
                                'reference_type'    => 'product_attr',
                            ];

                            if(isset($discount['id'])){
                                ProgressiveDiscount::find($discount['id'])->update($desconto);

                                $delete_desconto[] = $discount['id'];
                            }
                            else{
                                $desconto_id = ProgressiveDiscount::create($desconto);

                                $delete_desconto[] = $desconto_id->id;
                            }
                        }
                    }
                }
            }

            Produto::find($request->id)->update(['preco' => collect($var_price)->min()]);

            $vp_var_id = VP::where('produto_id', $request->id)->get()->map(function ($query){return $query->id;});
            if(VP::whereNotIn('id', $var_id)->where('produto_id', $request->id)->get()->count() > 0){
                VP::whereNotIn('id', $var_id)->where('produto_id', $request->id)->delete();
                ProgressiveDiscount::whereNotIn('reference_id', $var_id)->where('reference_type', 'product_attr')->delete();
            }
            if(VVP::whereNotIn('variations_produto_id', $var_id)->get()->count() > 0){
                VVP::whereNotIn('variations_produto_id', $var_id)->whereIn('variations_produto_id', $vp_var_id)->delete();
            }

            foreach($request->variations as $var){
                if(empty($var['variation_id'])){
                    $variation = VP::create([
                        'produto_id' => $request->id,
                        'preco' => $var['preco'],
                        'peso' => $var['peso'] ?? '',
                        'stock' => ($var['stock'] ?? null),
                        'dimensoes_A' => $var['dimensoes_A'] ?? '',
                        'dimensoes_C' => $var['dimensoes_C'] ?? '',
                        'dimensoes_L' => $var['dimensoes_L'] ?? '',
                    ]);

                    $var_id[] = $variation->id;

                    foreach ($var['attributos'] as $attr_var){
                        VVP::create([
                            'variations_produto_id' => $variation->id,
                            'attribute_id' => explode('-', $attr_var)[0],
                            'attribute_pai_id' => explode('-', $attr_var)[1],
                        ]);
                    }

                    if(isset($var['discount'])){
                        foreach($var['discount'] as $discount)
                        {
                            $desconto = [
                                'discount_quantity' => $discount['discount_quantity'],
                                'discount_value'    => str_replace(',','.',str_replace('.','', $discount['discount_value'])),
                                'reference_id'      => $variation->id,
                                'reference_type'    => 'product_attr',
                            ];
    
                            $desconto_id = ProgressiveDiscount::create($desconto);
                            $delete_desconto[] = $desconto_id->id;
                        }
                    }
                }
            }

            ProgressiveDiscount::whereIn('reference_id', $var_id)->where('reference_type', 'product_attr')->whereNotIn('id', $delete_desconto)->delete();
        }else{
            $variation_produto = VP::where('produto_id', $request->id)->get();
            foreach($variation_produto as $vp){
                VVP::where('variations_produto_id', $vp->id)->delete();
                ProgressiveDiscount::where('reference_id', $vp->id)->where('reference_type', 'product_attr')->delete();
            }
            VP::where('produto_id', $request->id)->delete();
        }

        $product = Produto::find($request->id);

        if(auth()->guard('seller')->check()) return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'analise').'?product_name='.$product->nome.'&type_search=product&per_page=20']);
    }
    public function postDataPlanos($request)
    {
        $not_delete_plans = [];
        $plan_price = [];
        foreach(($request->plan ?? []) as $plan){
            $plan_price[] = str_replace(['.',','],['','.'], $plan['plan_value']);
            $data = [
                'reference_id' => $request->id,
                'reference_type' => 'product',
                'plan_title' => $plan['plan_title'],
                'select_interval' => $plan['select_interval'],
                'duration_plan' => $plan['duration_plan'],
                'plan_value' => str_replace(['.',','],['','.'], $plan['plan_value']),
                'select_entrega' => $plan['select_entrega'],
                'descption_plan' => $plan['descption_plan'],
                'peso' => str_replace(',','.', $plan['peso']),
                'dimensoes_C' => $plan['dimensoes_C'],
                'dimensoes_L' => $plan['dimensoes_L'],
                'dimensoes_A' => $plan['dimensoes_A'],
            ];
            if(isset($plan['plan_id'])){
                $not_delete_plans[] = $plan['plan_id'];
                PlanPurchase::find($plan['plan_id'])->pdate($data);
            }else{
                $new_id = PlanPurchase::create($data);
                $not_delete_plans[] = $new_id->id;
            }
        }
        Produto::find($request->id)->update(['preco' => collect($plan_price)->min()]);
        PlanPurchase::where('reference_id', $request->id)->where('reference_type', 'product')->whereNotIn('id', $not_delete_plans)->delete();

        if(auth()->guard('seller')->check()) return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'analise').'?product_name='.$product->nome.'&type_search=product&per_page=20']);
    }
    public function postDataSeo($request)
    {
        $product = Produto::find($request->id);
        $seo['title'] = $request->title;
        $seo['link'] = $request->link;
        $seo['keywords'] = $request->keywords;
        $seo['description'] = $request->description;

        if(!empty($request->banner_path)) {
            $originalPathSeo = storage_path('app/public/img_page_prod/');
            if (!file_exists($originalPathSeo)) {
                mkdir($originalPathSeo, 0777, true);
            }

            if(!empty($product->banner_path)) Storage::delete('public/'.$product->banner_path);

            $page_img = Image::make($request->banner_path);
            $page_img_name = Str::random().'.'.$request->banner_path->extension();
            $page_img->save($originalPathSeo.$page_img_name);

            $seo['banner_path'] = 'img_page_prod/'.$page_img_name;
        }

        Produto::find($request->id)->update($seo);
        return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'aprovados').'?product_name='.$product->nome.'&type_search=product&per_page=20']);
    }
    ######ANALISANDO PRODUTO######
    public function postLiberarProduto($request)
    {
        if(Produto::whereHas('images')->where('id', $request->id)->get()->count() == 0){
            return response()->json(['msg' => 'Antes de liberar o produto, adicione pelo menos uma imagem!']);
        }
        ProdutoFactor::where('product_id', $request->id)->delete();
        Produto::find($request->id)->update(['status' => 1]);
        $product = Produto::find($request->id);
        return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'aprovados').'?product_name='.$product->nome.'&type_search=product&per_page=20', 'msg' => 'Produto Liberado!']);
    }
    public function postNegarProduto($request)
    {
        if($request->fields){
            foreach($request->fields as $field){
                if(!empty($field['field_name'])){
                    ProdutoFactor::create([
                        'product_id' => $request->id,
                        'field_name' => $field['field_name'],
                        'field_value' => $field['field_value']
                    ]);
                }
            }
        }

        if($request->field_text){
            if(ProdutoFactor::where('product_id', $request->id)->where('field_name', 'textarea')->get()->count() > 0){
                ProdutoFactor::where('product_id', $request->id)->where('field_name', 'textarea')->update([
                    'product_id' => $request->id,
                    'field_name' => 'textarea',
                    'field_value' => $request->field_text
                ]);
            }else{
                ProdutoFactor::create([
                    'product_id' => $request->id,
                    'field_name' => 'textarea',
                    'field_value' => $request->field_text
                ]);
            }
        }

        $product = Produto::find($request->id);
        return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'aprovados').'?product_name='.$product->nome.'&type_search=product&per_page=20', 'msg' => 'Produto Liberado!']);
    }
    #####FUNÇÕES DOS PRODUTOS#####

    public function destroy($request)
    {
        $id = $request->id;
        if(Produto::find($id)->banner_path??null) Storage::delete('public/'.Produto::find($id)->banner_path);
        if(Produto::find($id)??null) $produto = Produto::find($id)->delete();
        $variation_produto = VP::where('produto_id', $id)->get();
        foreach($variation_produto as $vp){
            VVP::where('variations_produto_id', $vp->id)->delete();
        }
        VP::where('produto_id', $id)->delete();

        foreach (ImagensProduto::where('produto_id', $id)->get() as $img){
            $img_path = explode('/', $img->caminho);

            Storage::deleteDirectory('public/produtos/'.$img_path[5]);
        }

        ProductCategory::where('product_id', $id)->delete();
        AttributeProduct::where('product_id', $id)->delete();

        ImagensProduto::where('produto_id', $id)->delete();

        return redirect()->back();
    }

    // Contando os produtos
    public function productCount($request)
    {
        $product_count = [
            'aprovados' => $this->produtos()->where('nome', 'LIKE', '%'.($request->product_name ?? '').'%')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->get()->count(),
            'analise' => $this->produtos()->where('nome', 'LIKE', '%'.($request->product_name ?? '').'%')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 0)->get()->count(),
            'rascunho' => $this->produtos()->where('nome', 'LIKE', '%'.($request->product_name ?? '').'%')->where('ativo', 'S')->wherehas('fatoresProduto', function($query) {
                        return $query->where('status', 0)->where('field_name', '!=', 'textarea');
            })->get()->count(),
            'inativo' => $this->produtos()->where('nome', 'LIKE', '%'.($request->product_name ?? '').'%')->where('ativo', 'N')->get()->count(),
        ];
        return $product_count;
    }

    // Buscando os produtos
    public function getProducts($request, $function_slug)
    {
        $produtos = $this->produtos()->with('seller.store')->where('nome', 'LIKE', '%'.($request->product_name ?? '').'%');
        if(($request->type_search ?? null) == 'seller'){
            $produtos = $this->produtos()->with('seller.store')->whereHas('seller.store',function($query) use($request){
                return $query->where('store_name', 'LIKE', '%'.($request->product_name ?? '').'%');
            });
        }
        if((($request->order_collumn ?? null) == 'id' || ($request->order_collumn ?? null) == 'name') && ($request->order_by ?? null)){
            $produtos = $this->produtos()->orderBy(($request->order_collumn == 'id' ? 'id' : 'nome'),$request->order_by);
        }
        switch($function_slug){
            case 'aprovados':
                $produtos = $produtos->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->paginate($request->per_page ?? 20);
            break;
            case 'analise':
                $produtos = $produtos->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 0)->paginate($request->per_page ?? 20);
            break;
            case 'rascunho':
                $produtos = $produtos->where('ativo', 'S')->wherehas('fatoresProduto', function($query) {
                    return $query->where('status', 0)->where('field_name', '!=', 'textarea');
                })->paginate($request->per_page ?? 20);
            break;
            case 'inativo':
                $produtos = $produtos->where('ativo', 'N')->paginate($request->per_page ?? 20);
            break;
        }

        if((($request->order_collumn ?? null) == 'store_name') && ($request->order_by ?? null)){
            $produtos->setCollection(
                $produtos->sortBy(function($query) use($request) {
                    if(($query->seller->store->store_name ?? null)){
                        if($request->order_by == 'ASC') return $query->seller->store->store_name;
                    }
                })->sortByDesc(function($query) use($request) {
                    if(($query->seller->store->store_name ?? null)){
                        if($request->order_by == 'DESC') return $query->seller->store->store_name;
                    }
                })
            );
        }
        // \Log::info($produtos->toArray());
        return $produtos;
    }
}
