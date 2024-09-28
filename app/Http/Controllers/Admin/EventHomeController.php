<?php

namespace App\Http\Controllers\Admin;

use App\Models\EventHome;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EventHomeRural;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class EventHomeController extends Controller
{
    public function indexEventoHome()
    {
        $event_home_a = EventHome::where('status', 1)->orderBy('posicao')->get();
        $event_home_i = EventHome::where('status', 0)->get();
        return view('painel.outros.indexEventoHome', get_defined_vars());
    }

    public function eventoHomeStore(Request $request)
    {
        // \Log::info($request->all());

        if(isset($request->imagem)){
            $imagens_create = [];
            foreach ($request->imagem as $p => $file) {
                if (isset($file['img'])) {
                    $random_path = Str::random(50);
                    $originalPath = storage_path('app/public/evento-home-page/'.$random_path.'/');
                    if (!file_exists($originalPath)) {
                        mkdir($originalPath, 0777, true);
                    }
    
                    // $width_max = $request->wmax;
                    // $height_max = $request->hmax;
    
                    // list($width_orig, $height_orig) = getimagesize($file['img']);
                    // $ratio_orig = $width_orig/$height_orig;
                    // if ($width_max/$height_max > $ratio_orig) {
                    //     $width_max = $height_max*$ratio_orig; //----
                    // } else {
                    //     $height_max = $width_max/$ratio_orig; //----
                    // }
    
                    // $banner = Image::make($file['img'])->resize($width_max, $height_max);
                    $banner = Image::make($file['img']);
                    $banner_name = Str::slug(preg_replace('/\..+$/', '', $file['img']->getClientOriginalName())).'.'.$file['img']->extension();
                    $banner->save($originalPath.$banner_name);
    
                    $imagens_create[$p]['posicao'] = $file['posicao'];
                    $imagens_create[$p]['descricao_curta'] = $file['descricao_curta'];
                    $imagens_create[$p]['status'] = isset($file['status']) ? 1 : 0;
                    $imagens_create[$p]['link'] = $file['link'];
                    $imagens_create[$p]['new_tab'] = isset($file['new_tab']) ? 1 : 0;
                    $imagens_create[$p]['file_name'] = $file['name'];
                    $imagens_create[$p]['path_file'] = 'public/evento-home-page/'.$random_path.'/'.$banner_name;
                    $imagens_create[$p]['url_file'] = asset('storage/evento-home-page/'.$random_path.'/'.$banner_name);
                }
            }
            ksort($imagens_create);
            foreach ($imagens_create as $imgsc){
                EventHome::create($imgsc);
            }
        }

        if(isset($request->imagem_update)){
            foreach($request->imagem_update as $key => $img_update){
                EventHome::find($key)->update([
                    'file_name'         => $img_update['name'],
                    'link'              => $img_update['link'],
                    'posicao'           => $img_update['posicao'],
                    'descricao_curta'   => $img_update['descricao_curta'],
                    'status'            => isset($img_update['status']) ? 1 : 0,
                    'new_tab'           => isset($img_update['new_tab']) ? 1 : 0,
                ]);
            }
        }

        if(isset($request->imagem_delete)){
            foreach ($request->imagem_delete as $img_id){
                $image = EventHome::find($img_id);
                $path_file = explode('/', $image->path_file);
                unset($path_file[3]);
                $path_file = collect($path_file)->join('/');

                Storage::deleteDirectory($path_file);
                $image->delete();
            }
        }

        return response()->json('', 200);
    }

    public function indexEventoHomeRural()
    {
        $event_home_a = EventHomeRural::where('status', 1)->orderBy('posicao')->get();
        $event_home_i = EventHomeRural::where('status', 0)->get();
        return view('painel.outros.indexEventoHomeRural', get_defined_vars());
    }

    public function eventoHomeRuralStore(Request $request)
    {
        // \Log::info($request->all());

        if(isset($request->imagem)){
            $imagens_create = [];
            foreach ($request->imagem as $p => $file) {
                if (isset($file['img'])) {
                    $random_path = Str::random(50);
                    $originalPath = storage_path('app/public/evento-home-page/'.$random_path.'/');
                    if (!file_exists($originalPath)) {
                        mkdir($originalPath, 0777, true);
                    }
    
                    // $width_max = $request->wmax;
                    // $height_max = $request->hmax;
    
                    // list($width_orig, $height_orig) = getimagesize($file['img']);
                    // $ratio_orig = $width_orig/$height_orig;
                    // if ($width_max/$height_max > $ratio_orig) {
                    //     $width_max = $height_max*$ratio_orig; //----
                    // } else {
                    //     $height_max = $width_max/$ratio_orig; //----
                    // }
    
                    // $banner = Image::make($file['img'])->resize($width_max, $height_max);
                    $banner = Image::make($file['img']);
                    $banner_name = Str::slug(preg_replace('/\..+$/', '', $file['img']->getClientOriginalName())).'.'.$file['img']->extension();
                    $banner->save($originalPath.$banner_name);
    
                    $imagens_create[$p]['posicao'] = $file['posicao'];
                    $imagens_create[$p]['descricao_curta'] = $file['descricao_curta'];
                    $imagens_create[$p]['status'] = isset($file['status']) ? 1 : 0;
                    $imagens_create[$p]['link'] = $file['link'];
                    $imagens_create[$p]['new_tab'] = isset($file['new_tab']) ? 1 : 0;
                    $imagens_create[$p]['file_name'] = $file['name'];
                    $imagens_create[$p]['path_file'] = 'public/evento-home-page/'.$random_path.'/'.$banner_name;
                    $imagens_create[$p]['url_file'] = asset('storage/evento-home-page/'.$random_path.'/'.$banner_name);
                }
            }
            ksort($imagens_create);
            foreach ($imagens_create as $imgsc){
                EventHomeRural::create($imgsc);
            }
        }

        if(isset($request->imagem_update)){
            foreach($request->imagem_update as $key => $img_update){
                EventHomeRural::find($key)->update([
                    'file_name'         => $img_update['name'],
                    'link'              => $img_update['link'],
                    'posicao'           => $img_update['posicao'],
                    'descricao_curta'   => $img_update['descricao_curta'],
                    'status'            => isset($img_update['status']) ? 1 : 0,
                    'new_tab'           => isset($img_update['new_tab']) ? 1 : 0,
                ]);
            }
        }

        if(isset($request->imagem_delete)){
            foreach ($request->imagem_delete as $img_id){
                $image = EventHomeRural::find($img_id);
                $path_file = explode('/', $image->path_file);
                unset($path_file[3]);
                $path_file = collect($path_file)->join('/');

                Storage::deleteDirectory($path_file);
                $image->delete();
            }
        }

        return response()->json('', 200);
    }
}
