<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Service;
use App\Models\Category;
use App\Models\Seller;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ServiceFactor;
use App\Models\ImagensService;
use App\Models\ServiceCalendar;
use App\Models\ServiceCategory;
use App\Models\AttributeService;

use App\Models\ServiceVariantion;
use App\Models\ServiceReservation;
use App\Models\ProgressiveDiscount;
use App\Models\ServiceVariantionValues;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ServicoController extends Controller
{
    public function servicos()
    {
        return Service::where(function($query){
            if(auth()->guard('seller')->check()) return $query->where('seller_id', auth()->guard('seller')->user()->id);
        });
    }

    public function index(Request $request, $function_slug = null)
    {
        $service = null;
        $podeCriarServico = true;
        $seller = Seller::all();
        $categories = Category::where('type', 1)->get();
        $get_url_appends = !empty($_GET) ? '?'.http_build_query((isset($_GET['page']) ? collect($_GET)->forget('page')->toArray() : $_GET)) : '';
        switch($function_slug){
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
                $novo_service_id = $_COOKIE['novo_service_id'] ?? null;
                if($function_slug == 'novo' && !isset($request->step)) {
                    setCookie('novo_service_id');
                    $novo_service_id = null;
                }
                if($request->id ?? $novo_service_id) $service = Service::with(['images', 'seller', 'variations.variations', 'categories.category', 'attrAttrs.attribute.variations', 'fatoresServico', 'progressiveDiscount'])->find($request->id ?? ($_COOKIE['novo_service_id'] ?? null));
                // \Log::info($service->toArray());
                $request->function_slug = $function_slug;
                if(isset($request->postService)) return $this->{$request->postType}($request);
                return view('painel.cadastros.servico.formServico', get_defined_vars());
            break;
            default:
                $service_count = $this->serviceCount($request);
                $servicos = $this->getServices($request, $function_slug);
                return view('painel.cadastros.servico.indexServico', get_defined_vars());
            break;
        }
    }

    // Alterando entre ativo e inativo o serviço
    public function ativo($request)
    {
        $service = Service::find($request->id);
        $service->status = 0;
        if(auth()->guard('admin')->check()){
            $service->status = $request->ativo == 'N' ? 0 : 1;
        }
        $service->ativo = $request->ativo;
        $service->save();
        return response()->json('');
    }

    // Clonando um serviço
    public function clone($request)
    {
        $forget = ['id', 'created_at', 'updated_at'];
        $service = Service::find($request->id);
        $service_clone = collect($service)->forget($forget);
        $slug = \Str::slug($service_clone['service_title']);
        $service_clone['service_title'] = $service_clone['service_title'].' CLONE';
        $produto_slug = Service::where('service_slug', 'like', '%'.$slug.'%')->get();

        $produto_slug = Service::where('service_slug', 'like', '%'.$slug.'%')->get();
        $while_count = $produto_slug->count();
        $while_loop = true;
        while($while_loop){
            $produto_slug_consulta = Service::where('service_slug', $slug.($while_count > 0 ? '-'.($while_count) : ''))->get();
            if($produto_slug_consulta->count() == 0) {
                $service_clone['slug'] = $slug.'-'.($while_count);
                $while_loop = false;
                break;
            }
            $while_count++;
        }
        $service_clone = Service::create($service_clone->toArray());
        $service = Service::with('categories', 'attrAttrs', 'variations.variations', 'variations.calendars', 'images', 'calendars')->find($request->id);

        if(isset($service->categories)){
            foreach($service->categories as $cat){
                $cat_clone = collect($cat)->forget($forget);
                $cat_clone['service_id'] = $service_clone->id;
                ServiceCategory::create($cat_clone->toArray());
            }
        }

        if($service->attrAttrs){
            foreach($service->attrAttrs as $attr){
                $attr_clone = collect($attr)->forget($forget);
                $attr_clone['service_id'] = $service_clone->id;
                AttributeService::create($attr_clone->toArray());
            }
        }

        if($service->variations){
            foreach($service->variations as $var){
                $var_clone = collect($var)->forget(['id', 'created_at', 'updated_at', 'variations']);
                $var_clone['service_id'] = $service_clone->id;
                $variation = ServiceVariantion::create($var_clone->toArray());

                foreach ($var->variations as $attr_var){
                    $attr_var_clone = collect($attr_var)->forget($forget);
                    $attr_var_clone['service_variantion_id'] = $variation->id;
                    ServiceVariantionValues::create($attr_var_clone->toArray());
                }

                if(isset($var['calendars'])){
                    foreach($var['calendars'] as $calendar_var){
                        $calendar_var_clone = collect($calendar_var)->forget($forget);
                        $calendar_var_clone['reference_id'] = $variation->id;
                        ServiceCalendar::create($calendar_var_clone->toArray());
                    }
                }
            }
        }

        if($service->calendars){
            foreach($service->calendars as $calendar){
                $calendar_clone = collect($calendar)->forget($forget);
                $calendar_clone['reference_id'] = $service_clone->id;
                ServiceCalendar::create($calendar_clone->toArray());
            }
        }

        if($service->images){
            foreach ($service->images as $image) {
                $image_clone = collect($image)->forget($forget);
                $image_clone['service_id'] = $service_clone->id;
                $img_path = explode('/', $image->caminho);
                $originalPath = storage_path('app/public/servicos/'.$img_path[5].'/');
                $random_path = Str::random(50);
                $newPath = storage_path('app/public/servicos/'.$random_path.'/');
                if (!file_exists($newPath)) {
                    mkdir($newPath, 0777, true);
                }
                copiar_diretorio($originalPath,$newPath);
                $image_clone['caminho'] = asset('storage/servicos/'.$random_path.'/'.$img_path[6]);
                ImagensService::create($image_clone->toArray());
            }
        }

        // return response()->json(['id' => $service_clone->id, 'name' => $service->service_title]);
        return response()->json(['id' => $service_clone->id, 'redirect_url' => route('servico', 'editar').'?id='.$service_clone->id, 'type' => 'serviço']);
    }

    // Atualizações de Fotos
    public function postAddFotos($request)
    {
        $service_id = $request->service_id;
        $html = '';
        foreach($request->images as $image){
            $random_path = Str::random(50);
            $originalPath = storage_path('app/public/servicos/'.$random_path.'/');
            if (!file_exists($originalPath)) {
                mkdir($originalPath, 0777, true);
            }

            $service_img = Image::make($image);
            $service_img_name = Str::slug(preg_replace('/\..+$/', '', $image->getClientOriginalName())).'.'.$image->extension();
            $service_img->save($originalPath.$service_img_name);

            $imagens_create['service_id'] = $request->service_id;
            $imagens_create['legenda'] = $image->getClientOriginalName();
            $imagens_create['texto_alternativo'] = $image->getClientOriginalName();
            $imagens_create['caminho'] = asset('storage/servicos/'.$random_path.'/'.$service_img_name);
            $imagens_create['pasta'] = 'public/servicos/'.$random_path;
            $imagens_create['position'] = ImagensService::where('service_id', $request->service_id)->get()->count()+1;
            $image = ImagensService::create($imagens_create);
            $html .= view('painel.cadastros.servico.imagesServico', get_defined_vars())->render();
        }

        setCookie('novo_service_id', $service_id);

        return response()->json($html);
    }
    public function postUpdatePositionFotos($request)
    {
        foreach($request->positions as $position){
            ImagensService::find($position['foto_id'])->update(['position' => $position['position']]);
        }
    }
    public function postDeleteFoto($request)
    {
        $imagens_service = ImagensService::find($request->foto_id);

        Storage::deleteDirectory($imagens_service->pasta);
        $imagens_service->delete();
    }

    #####FUNÇÕES DOS SERVIÇOS#####
    public function postDataInicio($request)
    {
        $slug = !empty($request->service_slug) ? Str::slug($request->service_slug) : $slug = Str::slug($request->service_title);

        $service_slug = Service::where(function($query) use($request){
            if(isset($request->id)) $query = $query->where('id', '!=', $request->id);
            return $query;
        })->where('service_slug', 'like', '%'.$slug.'%')->get();
        if($service_slug->count() > 0) {
            $while_count = $service_slug->count();
            $while_loop = true;
            while($while_loop){
                $service_slug_consulta = Service::where(function($query) use($request){
                    if(isset($request->id)) $query = $query->where('id', '!=', $request->id);
                    return $query;
                })->where('service_slug', $slug.($while_count > 0 ? '-'.($while_count) : ''))->get();
                if($service_slug_consulta->count() == 0) {
                    $slug = $slug.'-'.($while_count);
                    $while_loop = false;
                    break;
                }
                $while_count++;
            }
        }

        $service_iu['service_title'] = $request->service_title;
        $service_iu['service_slug'] = $slug;
        $service_iu['seller_id'] = $request->seller_id;
        $service_iu['preco'] = str_replace(['.',','],['','.'], ($request->preco ?? '0'));
        $service_iu['check_variation'] = isset($request->service_variations) ? 1 : 0;
        // $service_iu['vaga_controller'] = isset($request->stock_controller) ? 1 : 0;
        $service_iu['vaga_controller'] = 1;
        $service_iu['vaga'] = $request->vaga;
        $service_iu['hospedagem_controller'] = isset($request->hospedagem) ? 1 : 0;
        $service_iu['selecao_hospedagem'] = isset($request->hospedagem) ? $request->selecao_hospedagem : '';
        $service_iu['qty_max_hospedagem'] = 0; // Verificar
        if($request->function_slug !== 'analisar'){
            if(auth()->guard('admin')->check()) $service_iu['status'] = 1;
            if(auth()->guard('seller')->check()) $service_iu['status'] = 0;
        }

        if(isset($request->address_controller)){
            $service_iu['address_controller'] = 1;
            $service_iu['postal_code'] = $request->postal_code;
            $service_iu['address'] = $request->address;
            $service_iu['number'] = $request->number;
            $service_iu['complement'] = $request->complement;
            $service_iu['address2'] = $request->address2;
            $service_iu['state'] = $request->state;
            $service_iu['city'] = $request->city;
            $service_iu['phone'] = $request->phone;
            $service_iu['latitude'] = $request->latitude;
            $service_iu['longitude'] = $request->longitude;
        }else{
            $store = Store::where('user_id', $request->seller_id)->first();
            $service_iu['postal_code'] = $store->post_code;
            $service_iu['address'] = $store->address;
            $service_iu['number'] = $store->number;
            $service_iu['complement'] = $store->complement;
            $service_iu['address2'] = $store->address2;
            $service_iu['state'] = $store->state;
            $service_iu['city'] = $store->city;
            $service_iu['phone'] = $store->phone2 ?? $store->phone1;
            $service_iu['latitude'] = $store->lat;
            $service_iu['longitude'] = $store->lng;
        }

        if(isset($request->id) && !empty((int)$request->id)){
            $seller = Seller::find($request->seller_id);

            if(empty(Service::find($request->id)->title)){
                $seo['title'] = $service_iu['title'] = $service_iu['service_title'];
            }else{
                $seo['title'] = Service::find($request->id)->title;
            }
            if(empty(Service::find($request->id)->link)){
                $seo['link'] = $service_iu['link'] = 'servico/'.$slug;
            }else{
                $seo['link'] = Service::find($request->id)->link;
            }
            if(empty(Service::find($request->id)->description)){
                $seo['description'] = $service_iu['description'] = $service_iu['short_description'];
            }else{
                $seo['description'] = strip_tags(Service::find($request->id)->description);
            }
            if(empty(Service::find($request->id)->whatsapp)){
                $seo['whatsapp'] = $service_iu['whatsapp'] = $seller->phone ?? '';
            }else{
                $seo['whatsapp'] = Service::find($request->id)->whatsapp;
            }
            if(empty(Service::find($request->id)->text_contact)){
                $seo['text_contact'] = $service_iu['text_contact'] = $service_iu['service_title'];
            }else{
                $seo['text_contact'] = Service::find($request->id)->text_contact;
            }

            $service_iu = Service::find($request->id)->update($service_iu);
            $service_id = $request->id;
        }else{
            $seo['title'] = $service_iu['title'] = $service_iu['service_title'];
            $seo['link'] = $service_iu['link'] = 'servico/'.$slug;

            $seller = Seller::find($request->seller_id);
            $contacts['whatsapp'] = $service_iu['whatsapp'] = $seller->phone ?? '';
            $contacts['text_contact'] = $service_iu['text_contact'] = $service_iu['service_title'];

            $service_iu = Service::create($service_iu);
            $service_id = $service_iu->id;
        }

        #########CATEGORIAS#########
            $categories = ServiceCategory::where('service_id', $service_id)->get();
            $categories_exist = [];
            foreach($categories as $category){
                $categories_exist[] = $category->category_id;
                if(!in_array($category->category_id, $request->categories)){
                    ServiceCategory::find($category->id)->delete();
                }
            }
            foreach(($request->categories ?? []) as $category){
                if(!in_array($category, $categories_exist)){
                    ServiceCategory::create([
                        'service_id' => $service_id,
                        'category_id' => $category,
                    ]);
                }
            }
        #########CATEGORIAS#########

        #########DESCONTO-PROGRESIVO#########
            if(isset($request->check_desconto)){
                $delete_desconto = [];
                foreach(($request->discount ?? []) as $discount){
                    if(!empty($discount['discount_value']) && !empty($discount['discount_quantity'])){
                        $desconto = [
                            'discount_quantity' => $discount['discount_quantity'],
                            'discount_value'    => str_replace(',','.',str_replace('.','', $discount['discount_value'])),
                            'reference_id'      => $service_id,
                            'reference_type'    => 'service',
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

                ProgressiveDiscount::where('reference_id', $service_id)->where('reference_type', 'service')->whereNotIn('id', $delete_desconto)->delete();
            }
            if(!isset($request->check_desconto)) ProgressiveDiscount::where('reference_id', $service_id)->where('reference_type', 'service')->delete();
            if(isset($request->produto_variavel) || isset($request->plano_assinatura)) ProgressiveDiscount::where('reference_id', $service_id)->where('reference_type', 'service')->delete();
        #########DESCONTO-PROGRESIVO#########

        ######SALVANDO ANALISE DO PRODUTO######
            if(auth()->guard('admin')->check()){
                if($request->field_native){
                    foreach($request->field_native as $key => $field_native){
                        if(isset($field_native['field_name'])){
                            ServiceFactor::find($key)->update([
                                'field_name' => $field_native['field_name'],
                                'field_value' => $field_native['field_value'],
                                'status' => (isset($field_native['field_status']) ? '1' : '0'),
                            ]);
                        }
                    }
                }

                if($request->field_text_native){
                    foreach($request->field_text_native as $key => $field_text_native){
                        ServiceFactor::findOrFail($key)->update([
                            'field_value' => $field_text_native
                        ]);
                    }
                }
            }
        ######SALVANDO ANALISE DO PRODUTO######

        setCookie('novo_service_id', $service_id);
        return response()->json(['service_id' => $service_id, 'seo' => ($seo ?? false)]);
    }
    public function postDataDescricao($request)
    {
        if(isset($request->id)){
            $service_iu['short_description'] = $request->descricao_curta;
            $service_iu['full_description'] = $request->descricao_completa;
            if(empty(Service::find($request->id)->description)) $seo['description'] = $service_iu['description'] = strip_tags($request->descricao_curta);
            $service_iu = Service::find($request->id)->update($service_iu);
        }

        return response()->json(['seo' => ($seo ?? false)]);
    }
    public function postDataContato($request)
    {
        if(isset($request->id)){
            $service_iu['whatsapp'] = $request->whatsapp;
            $service_iu['text_contact'] = $request->text_contact;
            $service_iu = Service::find($request->id)->update($service_iu);
        }

        $service = Service::find($request->id);

        return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'aprovados').'?service_name='.$service->service_title.'&type_search=service&per_page=20']);
    }
    public function postDataFotos($request)
    {
        foreach(($request->fotos ?? []) as $key => $value){
            $image_update['legenda'] = $value['legenda'];
            $image_update['texto_alternativo'] = $value['texto_alternativo'];
            $image_update['principal'] = (int)($request->img_principal ?? 0) == $key ? 1 : 0;
            ImagensService::find($key)->update($image_update);
        }

        if(empty(Service::find($request->id)->banner_path)) $seo['banner_path'] = Service::find($request->id)->images->sortBy('position')->first()->caminho ?? '';

        $service = Service::find($request->id);
        // if((count($service->variations ?? []) == 0 && count($service->planPurchases ?? []) == 0)){
        //     if(auth()->guard('seller')->check()) return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'analise').'?service_name='.$service->service_title.'&type_search=service&per_page=20']);
        // }

        if(Service::whereHas('images')->where('id', $request->id)->get()->count() == 0){
            ServiceFactor::create([
                'service_id' => $request->id,
                'field_name' => 'imagens',
                'field_value' => 'Faltam imagens no produto'
            ]);

            Service::find($request->id)->update(['status' => 0]);
        }

        return response()->json(['seo' => ($seo ?? false)]);
    }
    public function postDataVariacoes($request)
    {

        // \Log::info($request->all());
        if($request->attrs){
            $attrs = AttributeService::where('service_id', $request->id)->get();
            $attrs_exist = [];
            foreach($attrs as $attr){
                $attrs_exist[] = $attr->attr_id;
                if(!in_array($attr->attr_id, $request->attrs)){
                    AttributeService::find($attr->id)->delete();
                }
            }

            foreach($request->attrs as $attr){
                if(!in_array($attr, $attrs_exist)){
                    AttributeService::create([
                        'service_id' => $request->id,
                        'attribute_id' => $attr,
                    ]);
                }
            }
        }else{
            AttributeService::where('service_id', $request->id)->delete();
        }

        if($request->variations){
            $var_id = [];
            $var_price = [];
            $delete_desconto = [];
            foreach($request->variations as $var){
                $var_price[] = str_replace(['.',','],['','.'], ($var['preco'] ?? '0'));
                if(isset($var['variation_id'])){
                    $var_id[] = $var['variation_id'];
                    ServiceVariantion::where('id', $var['variation_id'])->update([
                        'preco' => str_replace(['.',','],['','.'], ($var['preco'] ?? '0')),
                        'vaga' => $var['vaga'] ?? '',
                    ]);

                    $attr_var_id = [];
                    $attr_attr_id = [];
                    foreach ($var['attributos'] as $attr_var){
                        $attr_attr_id[] = explode('-', $attr_var)[1];
                        if(explode('-', $attr_var)[0] == 0){
                            foreach (Attribute::where('parent_id', explode('-', $attr_var)[1])->get() as $attr_var_temp){
                                $attr_var_id[] = $attr_var_temp->id;
                                
                                if(ServiceVariantionValues::where('service_variantion_id', $var['variation_id'])->where('attribute_pai_id', explode('-', $attr_var)[1])->where('attribute_id', $attr_var_temp->id)->get()->count() == 0){
                                    ServiceVariantionValues::create([
                                        'service_variantion_id' => $var['variation_id'],
                                        'attribute_id' => $attr_var_temp->id,
                                        'attribute_pai_id' => $attr_var_temp->parent_id,
                                    ]);
                                }
                            }
                        }else{
                            $attr_var_id[] = explode('-', $attr_var)[0];
                            if(ServiceVariantionValues::where('service_variantion_id', $var['variation_id'])->where('attribute_pai_id', explode('-', $attr_var)[1])->where('attribute_id', explode('-', $attr_var)[0])->get()->count() == 0){
                                ServiceVariantionValues::create([
                                    'service_variantion_id' => $var['variation_id'],
                                    'attribute_id' => explode('-', $attr_var)[0],
                                    'attribute_pai_id' => explode('-', $attr_var)[1],
                                ]);
                            }
                        }
                    }

                    if(ServiceVariantionValues::where('service_variantion_id', $var['variation_id'])->whereIn('attribute_pai_id', $attr_attr_id)->whereNotIn('attribute_id', $attr_var_id)->get()->count() > 0){
                        ServiceVariantionValues::where('service_variantion_id', $var['variation_id'])->whereIn('attribute_pai_id', $attr_attr_id)->whereNotIn('attribute_id', $attr_var_id)->delete();
                    }

                    if(isset($var['discount'])){
                        foreach($var['discount'] as $discount)
                        {
                            $desconto = [
                                'discount_quantity' => $discount['discount_quantity'],
                                'discount_value'    => str_replace(',','.',str_replace('.','', $discount['discount_value'])),
                                'reference_id'      => $var['variation_id'],
                                'reference_type'    => 'service_attr',
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

                    if(isset($var['calendar'])){
                        $cal_var_ids = [];
                        foreach($var['calendar'] as $calendar_var){
                            $calendarArray_var = $this->montaArrayCalendarServico($calendar_var, $var['variation_id'], 'service_var');

                            if(isset($calendar_var['id'])){
                                $cal_var_ids[] = $calendar_var['id'];
                                ServiceCalendar::find($calendar_var['id'])->update($calendarArray_var);
                            }else{
                                ServiceCalendar::create($calendarArray_var);
                            }
                        }
                        if(count($cal_var_ids) > 0){
                            if(isset($var['variation_id'])){
                                ServiceCalendar::where('reference_type', 'service_var')->where('reference_id', $var['variation_id'])->whereNotIn('id', $cal_var_ids)->delete();
                            }
                        }
                    }else{
                        if(isset($var['variation_id'])){
                            ServiceCalendar::where('reference_type', 'service_var')->where('reference_id', $var['variation_id'])->delete();
                        }
                    }
                }
            }

            Service::find($request->id)->update(['preco' => collect($var_price)->min()]);

            $vp_var_id = ServiceVariantion::where('service_id', $request->id)->get()->map(function ($query){return $query->id;});
            if(ServiceVariantion::whereNotIn('id', $var_id)->where('service_id', $request->id)->get()->count() > 0){
                ServiceVariantion::whereNotIn('id', $var_id)->where('service_id', $request->id)->delete();
                ProgressiveDiscount::whereNotIn('reference_id', $var_id)->where('reference_type', 'service_attr')->delete();
                ServiceCalendar::where('reference_type', 'service_attr')->whereNotIn('reference_id', $var_id)->delete();
            }
            if(ServiceVariantionValues::whereNotIn('service_variantion_id', $var_id)->get()->count() > 0){
                ServiceVariantionValues::whereNotIn('service_variantion_id', $var_id)->whereIn('service_variantion_id', $vp_var_id)->delete();
            }

            foreach($request->variations as $var){
                if(empty($var['variation_id'])){
                    $variation = ServiceVariantion::create([
                        'service_id' => $request->id,
                        'preco' => str_replace(['.',','],['','.'], ($var['preco'] ?? '0')),
                        'vaga' => $var['vaga'] ?? '',
                    ]);

                    $var_id[] = $variation->id;

                    foreach ($var['attributos'] as $attr_var){
                        ServiceVariantionValues::create([
                            'service_variantion_id' => $variation->id,
                            'attribute_id' => explode('-', $attr_var)[0],
                            'attribute_pai_id' => explode('-', $attr_var)[1],
                        ]);
                    }

                    if(isset($var['discount'])){
                        foreach($var['discount'] as $discount){
                            if(!empty($discount['discount_value']) && !empty($discount['discount_quantity'])){
                                $desconto = [
                                    'discount_quantity' => $discount['discount_quantity'],
                                    'discount_value'    => str_replace(',','.',str_replace('.','', $discount['discount_value'])),
                                    'reference_id'      => $variation->id,
                                    'reference_type'    => 'service_attr',
                                ];

                                $desconto_id = ProgressiveDiscount::create($desconto);
                                $delete_desconto[] = $desconto_id->id;
                            }
                        }
                    }

                    if(isset($var['calendar'])){
                        foreach($var['calendar'] as $calendar_var){
                            $calendarArray_var = $this->montaArrayCalendarServico($calendar_var, $variation->id, 'service_var');
                            ServiceCalendar::create($calendarArray_var);
                        }
                    }
                }
            }

            ProgressiveDiscount::whereIn('reference_id', $var_id)->where('reference_type', 'service_attr')->whereNotIn('id', $delete_desconto)->delete();
            ServiceCalendar::where('reference_type', 'service_attr')->whereIn('reference_id', $var_id)->delete();
        }else{
            $variation_produto = ServiceVariantion::where('service_id', $request->id)->get();
            foreach($variation_produto as $vp){
                ServiceVariantionValues::where('service_variantion_id', $vp->id)->delete();
                ProgressiveDiscount::where('reference_id', $vp->id)->where('reference_type', 'service_attr')->delete();
                ServiceCalendar::where('reference_type', 'service_attr')->whereIn('reference_id', $vp->id)->delete();
            }
            ServiceVariantion::where('service_id', $request->id)->delete();
        }

        $service = Service::find($request->id);

        // if(auth()->guard('seller')->check()) return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'analise').'?service_name='.$service->nome.'&type_search=service&per_page=20']);
    }
    public function postDataCalendar($request)
    {
        if(isset($request->calendar)){
            $cal_ids = [];
            foreach($request->calendar as $calendar){
                $calendarArray = $this->montaArrayCalendarServico($calendar, $request->id, 'service');
                // \Log::info($calendarArray);

                if(isset($calendar['id'])){
                    $cal_ids[] = $calendar['id'];
                    ServiceCalendar::find($calendar['id'])->update($calendarArray);
                }else{
                    $service_calendar = ServiceCalendar::create($calendarArray);
                    $cal_ids[] = $service_calendar->id;
                }
            }
            ServiceCalendar::where('reference_type', 'service')->where('reference_id', $request->id)->whereNotIn('id', $cal_ids)->delete();
        }else{
            ServiceCalendar::where('reference_type', 'service')->where('reference_id', $request->id)->delete();
        }

        $service = Service::find($request->id);
        if(auth()->guard('seller')->check()) return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'analise').'?service_name='.$service->nome.'&type_search=service&per_page=20']);
    }
    public function postDataSeo($request)
    {
        $service = Service::find($request->id);
        $seo['title'] = $request->title;
        $seo['link'] = $request->link;
        $seo['keywords'] = $request->keywords;
        $seo['description'] = $request->description;

        if(!empty($request->banner_path)) {
            $originalPathSeo = storage_path('app/public/img_page_prod/');
            if (!file_exists($originalPathSeo)) {
                mkdir($originalPathSeo, 0777, true);
            }

            if(!empty($service->banner_path)) Storage::delete('public/'.$service->banner_path);

            $page_img = Image::make($request->banner_path);
            $page_img_name = Str::random().'.'.$request->banner_path->extension();
            $page_img->save($originalPathSeo.$page_img_name);

            $seo['banner_path'] = 'img_page_prod/'.$page_img_name;
        }

        Service::find($request->id)->update($seo);
        return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'aprovados').'?service_name='.$service->service_title.'&type_search=service&per_page=20']);
    }
    ######ANALISANDO PRODUTO######
    public function postLiberarService($request)
    {
        if(Service::whereHas('images')->where('id', $request->id)->get()->count() == 0){
            return response()->json(['msg' => 'Antes de liberar o produto, adicione pelo menos uma imagem!']);
        }
        ServiceFactor::where('service_id', $request->id)->delete();
        Service::find($request->id)->update(['status' => 1]);
        $service = Service::find($request->id);
        return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'aprovados').'?service_name='.$service->service_title.'&type_search=service&per_page=20', 'msg' => 'Service Liberado!']);
    }
    public function postNegarService($request)
    {
        if($request->fields){
            foreach($request->fields as $field){
                if(!empty($field['field_name'])){
                    ServiceFactor::create([
                        'service_id' => $request->id,
                        'field_name' => $field['field_name'],
                        'field_value' => $field['field_value']
                    ]);
                }
            }
        }

        if($request->field_text){
            if(ServiceFactor::where('service_id', $request->id)->where('field_name', 'textarea')->get()->count() > 0){
                ServiceFactor::where('service_id', $request->id)->where('field_name', 'textarea')->update([
                    'service_id' => $request->id,
                    'field_name' => 'textarea',
                    'field_value' => $request->field_text
                ]);
            }else{
                ServiceFactor::create([
                    'service_id' => $request->id,
                    'field_name' => 'textarea',
                    'field_value' => $request->field_text
                ]);
            }
        }

        $service = Service::find($request->id);
        return response()->json(['redirect_url' => route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'aprovados').'?service_name='.$service->service_title.'&type_search=service&per_page=20', 'msg' => 'Service Liberado!']);
    }
    #####FUNÇÕES DOS PRODUTOS#####

    public function destroy($request)
    {
        Storage::delete('public/'.Service::find($request->id)->banner_path);
        $servico = Service::find($request->id)->delete();
        ServiceVariantion::where('service_id', $request->id)->each(function($query){
            ServiceVariantionValues::where('service_variantion_id', $query->id)->delete();
            $query->delete();
        });

        foreach (ImagensService::where('service_id', $request->id)->get() as $img){
            $img_path = explode('/', $img->caminho);

            Storage::deleteDirectory('public/servicos/'.$img_path[5]);
        }

        ServiceCategory::where('service_id', $request->id)->delete();
        AttributeService::where('service_id', $request->id)->delete();

        ImagensService::where('service_id', $request->id)->delete();

        return redirect()->back();
    }

    public function montaArrayCalendarServico($request, $service_id, $reference_type)
    {
        $arrayServico['reference_id'] = $service_id;
        $arrayServico['reference_type'] = $reference_type;
        $arrayServico['data_inicial'] = date('Y-m-d', strtotime(str_replace('/','-',$request['data_inicial'])));
        $arrayServico['data_fim'] = date('Y-m-d', strtotime(str_replace('/','-',$request['data_fim'])));
        $arrayServico['select_termino'] = $request['select_termino'];
        $arrayServico['antecedencia'] = $request['antecedencia'];
        $arrayServico['number_select'] = $request['number_select'];
        $arrayServico['select_control'] = $request['select_control'];
        $arrayServico['ocorrencia'] = $request['ocorrencia'];

        $array_semana = [];
        foreach($request['semana'] as $key => $value){
            $array_temp = [];
            foreach($value as $key2 => $value2){
                if(is_array($value2)){
                    foreach($value2 as $hours){
                        if($hours){
                            $array_temp[$key2][] = $hours;
                        }
                    }
                }else{
                    $array_temp[$key2] = $value2;
                }
            }
            $array_semana[$key] = $array_temp;
            if(count($array_semana[$key]) < 2) {
                if(isset($array_semana[$key]['horario'])) unset($array_semana[$key]);
            }
        }
        $arrayServico['semana'] = array_filter($array_semana);

        return $arrayServico;
    }

    public function servicoVariationComponent(Request $request)
    {
        $data = $request->all();

        return view('components.servicoVariacoes', get_defined_vars());
    }
    
    public function servicoDateCardComponent(Request $request)
    {
        $data = $request->all();

        return view('components.dateCardComponent', get_defined_vars());
    }

    public function servicoUpdateVariationComponent($id)
    {
        $service = Service::find($id);
        return view('components.variationsService', get_defined_vars());
    }

    // --------------------------

    public function reservaManual(Request $request, $function_slug = null)
    {
        switch($function_slug){
            case 'apagar-reserva':
                ServiceReservation::find($request->id)->delete();
                return response()->json();
            break;
            case 'post-reserva':
                return $this->servicoReservar($request);
            break;
            case 'servico-calendar':
                return $this->servicoCalendar($request);
            break;
        }

        $services = Service::where(function($query){
            if(auth()->guard('seller')->check()) $query = $query->where('seller_id', auth()->guard('seller')->user()->id);
        })->where('ativo', 'S')->get();

        $reservas = ServiceReservation::whereNull('order_number')->where(function($query) use($request){
            if(auth()->guard('seller')->check()) $query = $query->where('seller_id', auth()->guard('seller')->user()->id);
            if($request->search_value) $query = $query->where('service_name', 'LIKE', '%'.$request->search_value.'%');
            return $query;
        })->paginate($request->per_page ?? 20);
        return view('painel.comercial.indexReservaManual', get_defined_vars());
    }

    public function servicoCalendar($request)
    {
        $service = Service::with(['calendars', 'serviceReservation', 'variations.calendars'])->find($request->service_id);
        if(empty($service)) return response()->json('',412);
        $calendars = collect($service->calendars->toArray());
        $service->variations->map(function($query) use($calendars) {
            $calendars->add($query->calendars);
        });
        return view('components.modalReservarServicos', get_defined_vars());
    }

    public function servicoReservar(Request $request)
    {
        foreach($request->reservation as $reservation){
            $create_reservation = [
                'service_id' => $reservation['service_id'],
                'seller_id' => $reservation['seller_id'],
                'service_name' => $reservation['service_name'],
                'service_quantity' => 1,
                'date_reservation_ini' => date('Y-m-d', strtotime(str_replace('/','-', $reservation['calendar_ini']))),
                'date_reservation_fim' => $reservation['calendar_fim'] ? date('Y-m-d', strtotime(str_replace('/','-', $reservation['calendar_fim']))) : null,
                'hour_reservation' => $reservation['hours'] ? $reservation['hours'] : null,
            ];

            if($reservation['day_inactive'] == 'true') $create_reservation['status'] = 1;

            ServiceReservation::create($create_reservation);
        }

        return response()->json('',200);
    }

    // Contando os serviços
    public function serviceCount($request)
    {
        $service_count = [
            'aprovados' => $this->servicos()->where('service_title', 'LIKE', '%'.($request->service_name ?? '').'%')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->get()->count(),
            'analise' => $this->servicos()->where('service_title', 'LIKE', '%'.($request->service_name ?? '').'%')->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 0)->get()->count(),
            'rascunho' => $this->servicos()->where('service_title', 'LIKE', '%'.($request->service_name ?? '').'%')->where('ativo', 'S')->wherehas('fatoresServico', function($query) {
                        return $query->where('status', 0)->where('field_name', '!=', 'textarea');
            })->get()->count(),
            'inativo' => $this->servicos()->where('service_title', 'LIKE', '%'.($request->service_name ?? '').'%')->where('ativo', 'N')->get()->count(),
        ];
        return $service_count;
    }

    // Buscando os serviços
    public function getServices($request, $function_slug)
    {
        $servicos = $this->servicos()->with('seller.store')->where('service_title', 'LIKE', '%'.($request->service_name ?? '').'%');
        if(($request->type_search ?? null) == 'seller'){
            $servicos = $this->servicos()->with('seller.store')->whereHas('seller.store',function($query) use($request){
                return $query->where('store_name', 'LIKE', '%'.($request->service_name ?? '').'%');
            });
        }
        if((($request->order_collumn ?? null) == 'id' || ($request->order_collumn ?? null) == 'name') && ($request->order_by ?? null)){
            $servicos = $this->servicos()->orderBy(($request->order_collumn == 'id' ? 'id' : 'service_title'),$request->order_by);
        }
        switch($function_slug){
            case 'aprovados':
                $servicos = $servicos->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 1)->paginate($request->per_page ?? 20);
            break;
            case 'analise':
                $servicos = $servicos->whereHas('seller')->whereHas('images')->where('ativo', 'S')->where('status', 0)->paginate($request->per_page ?? 20);
            break;
            case 'rascunho':
                $servicos = $servicos->where('ativo', 'S')->wherehas('fatoresServico', function($query) {
                    return $query->where('status', 0)->where('field_name', '!=', 'textarea');
                })->paginate($request->per_page ?? 20);
            break;
            case 'inativo':
                $servicos = $servicos->where('ativo', 'N')->paginate($request->per_page ?? 20);
            break;
        }

        if((($request->order_collumn ?? null) == 'store_name') && ($request->order_by ?? null)){
            $servicos->setCollection(
                $servicos->sortBy(function($query) use($request) {
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
        // \Log::info($servicos->toArray());
        return $servicos;
    }
}
