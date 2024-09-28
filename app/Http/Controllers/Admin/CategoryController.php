<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Intervention\Image\ImageManagerStatic as Image;

use Spatie\Sluggable\SlugOptions;

class CategoryController extends Controller
{
    public function pesquisaCategoria(Request $request)
    {
        if($request->category_id) {
            $category = Category::whereNull('parent_id')->get();
            return response()->json($category);
        }

        if($request->parent_id) {
            $category = Category::where('parent_id', $request->parent_id)->get();
            return response()->json($category);
        }

        return response()->json(['msg' => 'Necessario um id!'], 412);
    }

    public function novaCategoria(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string',
            'type'          => 'required|string',
            'img_icon'      => 'required',
        ]);

        if($request->parent_id){
            // $categories = Category::where('parent_id', $request->parent_id)->get()->count();

            // if($categories == 18) return response()->json(['msg_alert' => 'Maximo 18 Sub Categorias criadas!', 'icon_alert' => 'warning'], 412);

            // $category['name'] = mb_convert_case($request->category_name, MB_CASE_TITLE);
            // $category['parent_id'] = $request->parent_id;
    
            // $category_id = Category::create($category);
    
            // return response()->json([
            //     'table' => '<tr class="tr-id-'.$category_id->id.'">
            //         <td>'.$category_id->id.'</td>
            //         <td>'.$category_id->name.'</td>
            //         <td>'.$category_id->slug.'</td>
            //         <td>
            //             <div class="btn-group" role="group" aria-label="">
            //                 <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarCategoria" data-dados="\''.json_encode($category_id).'\'"><i class="fas fa-edit"></i> Alterar</a>
            //                 <a href="#" class="btn btn-danger btn-xs btn-excluir-categoria" data-toggle="modal" data-target="#excluirCategoria" data-dados="\''.json_encode($category_id).'\'"><i class="fas fa-trash"></i> Apagar</a>
            //             </div>
            //         </td>
            //     </tr>'
            // ]);
        }else{
            $category_info = Category::whereNull('parent_id')->where('type', $request->type)->get()->last();
            // $categories = Category::whereNull('parent_id')->get()->count();

            // if($categories == 10) return response()->json(['msg_alert' => 'Maximo 10 Categorias criadas!', 'icon_alert' => 'warning'], 412);
            $originalPath = storage_path('/app/category-img/');
            if($request->img_icon){
                $width_max = 120;
                $height_max = 120;

                list($width_orig, $height_orig) = getimagesize($request->img_icon);
                $ratio_orig = $width_orig/$height_orig;
                if ($width_max/$height_max > $ratio_orig) {
                    $width_max = $height_max*$ratio_orig; //----
                } else {
                    $height_max = $width_max/$ratio_orig; //----
                }
                $img_icon = Image::make($request->img_icon)->resize($width_max, $height_max);
                $img_name = Str::random().'.'.$request->img_icon->extension();
                $img_icon->save($originalPath.$img_name);

                $category['icon'] = 'category-img/'.$img_name;
            }

            $category['name'] = mb_convert_case($request->category_name, MB_CASE_UPPER);
            $category['type'] = $request->type;
            $category['position'] = ($category_info->position ?? 0) + 1;

            $category['title'] = $request->title;
            $category['keywords'] = $request->keywords;
            $category['description'] = $request->description;
            if(isset($request->banner_path)){
                $originalPathSeo = storage_path('app/public/img_page_cat/');
                if (!file_exists($originalPathSeo)) {
                    mkdir($originalPathSeo, 0777, true);
                }

                $page_img = Image::make($request->banner_path);
                $page_img_name = Str::random().'.'.$request->banner_path->extension();
                $page_img->save($originalPathSeo.$page_img_name);

                $category['banner_path'] = 'img_page_cat/'.$page_img_name;
            }

            $category_id = Category::create($category);

            if(empty($request->link)){
                $category_id->link = 'categoria/'.$category_id->slug;
            }else{
                $category['link'] = $request->link;
            }
            $category_id->save();
            $category_id->refresh();

            $image          = Storage::get('category-img/'.$img_name);
            $mime_type      = Storage::mimeType('category-img/'.$img_name);
            $image          = 'data:'.$mime_type.';base64,'.base64_encode($image);
            $item           = '<img width="45px" src="'.$image.'">';

            return response()->json([
                'table' => '<tr>
                    <td>'.$category_id->id.'</td>
                    <td>'.$item.'</td>
                    <td>'.$category_id->name.'</td>
                    <td>'.$category_id->slug.'</td>
                    <td class="text-capitalize">'.($category_id->type == '0' ? 'Produto' : '').'</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="">
                            <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarCategoria" data-dados=\''.json_encode($category_id).'\'><i class="fas fa-edit"></i> Alterar</a>
                            <a href="#" class="btn btn-danger btn-xs btn-excluir-categoria" data-toggle="modal" data-target="#excluirCategoria" data-dados=\''.json_encode($category_id).'\'><i class="fas fa-trash"></i> Apagar</a>
                        </div>
                    </td>
                </tr>'
            ]);
        }
    }

    public function atualizarCategoria(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string',
        ]);
        $td_catgoria = '';
        $a_categoria = '';

        $originalPath = storage_path('/app/category-img/');
        if($request->img_icon){
            $width_max = 120;
            $height_max = 120;

            list($width_orig, $height_orig) = getimagesize($request->img_icon);
            $ratio_orig = $width_orig/$height_orig;
            if ($width_max/$height_max > $ratio_orig) {
                $width_max = $height_max*$ratio_orig; //----
            } else {
                $height_max = $width_max/$ratio_orig; //----
            }
            $img_icon = Image::make($request->img_icon)->resize($width_max, $height_max);
            $img_name = Str::random().'.'.$request->img_icon->extension();
            $img_icon->save($originalPath.$img_name);

            $category_update['icon'] = 'category-img/'.$img_name;
        }

        $verifica_category = Category::where('id', $request->id)->first();
        if($verifica_category->parent_id == null){
            $category_update['name'] = mb_convert_case($request->category_name, MB_CASE_UPPER);
            // $td_catgoria = '<td>'.Category::where('parent_id', $request->id)->get()->count().'</td>';
            // $a_categoria = '<a href="'.url('admin/cadastro/categoria_menu', $request->id).'" class="btn btn-primary btn-xs"><i class="fas fa-eye"></i> Vizualizar</a>';
        }else{
            $category_update['name'] = mb_convert_case($request->category_name, MB_CASE_TITLE);
        }

        // $model = new SlugOptions();
        // $model->name = $category_update['name'];
        // $model->save();
        $slug = Str::slug($category_update['name']);
        $category_slug = Category::where('slug', $slug)->get();
        $category_update['slug'] = Str::slug($category_update['name']).(($category_slug->count() - 1) > 0 ? '-'.($category_slug->count()) : '');

        $category_update['title'] = $request->title;
        $category_update['keywords'] = $request->keywords;
        $category_update['description'] = $request->description;
        if(isset($request->banner_path)){
            $originalPathSeo = storage_path('app/public/img_page_cat/');
            if (!file_exists($originalPathSeo)) {
                mkdir($originalPathSeo, 0777, true);
            }

            $page_img = Image::make($request->banner_path);
            $page_img_name = Str::random().'.'.$request->banner_path->extension();
            $page_img->save($originalPathSeo.$page_img_name);

            $category_update['banner_path'] = 'img_page_cat/'.$page_img_name;
        }

        if(empty($request->link)){
            $category_update['link'] = 'categoria/'.$slug;
        }else{
            $category_update['link'] = $request->link;
        }

        $category = Category::where('id', $request->id)->update($category_update);
        $category = Category::where('id', $request->id)->first();

        $image          = Storage::get($category->icon);
        $mime_type      = Storage::mimeType($category->icon);
        $image          = 'data:'.$mime_type.';base64,'.base64_encode($image);
        $item           = '<img width="45px" src="'.$image.'">';

        return response()->json([
            'tb_id' => $category->id,
            'tb_up' => '
                <td>'.$category->id.'</td>
                <td>'.$item.'</td>
                <td>'.$category->name.'</td>
                <td>'.$category->slug.'</td>
                <td class="text-capitalize">'.($category->type == '0' ? 'Produto' : '').'</td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarCategoria" data-dados=\''.json_encode($category).'\'><i class="fas fa-edit"></i> Alterar</a>
                        <a href="#" class="btn btn-danger btn-xs btn-excluir-categoria" data-toggle="modal" data-target="#excluirCategoria" data-dados=\''.json_encode($category).'\'><i class="fas fa-trash"></i> Apagar</a>
                    </div>
                </td>'
        ]);
    }

    public function pesquisaCategoriaProduto(Request $request)
    {
        // $dados = Product::where('main_category', $request->id)->where('status', '!=', '0')->get();
        // $tipo = 'produto';

        // if($dados->count() == 0){
        //     $dados = Category::where('parent_id', $request->id)->get();
        //     $tipo = 'categoria';
        // }

        return response()->json(['dados' => 0, 'tipo' => 'categoria']);
    }

    public function excluirCategoria(Request $request)
    {
        $category = Category::find($request->id);
        Storage::delete($category->icon);
        Storage::delete('public/'.$category->banner_path);
        $category->delete();

        return response()->json(['category_id' => $request->id]);
    }
}
