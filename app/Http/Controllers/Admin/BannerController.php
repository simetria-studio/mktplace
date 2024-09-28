<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use App\Models\BannerConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class BannerController extends Controller
{
    public function indexBanner()
    {
        return view('painel.outros.indexBanner', get_defined_vars());
    }

    public function bannerStore(Request $request)
    {
        // \Log::info($request->all());
        $imagens_create = [];
        foreach ($request->imagem as $p => $file) {
            if (isset($file['img'])) {
                $random_path = Str::random(50);
                $originalPath = storage_path('app/public/'.$request->local.'/'.$random_path.'/');
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

                $imagens_create[$p]['local'] = $request->local;
                $imagens_create[$p]['link'] = $file['link'];
                $imagens_create[$p]['new_tab'] = isset($file['new_tab']) ? 1 : 0;
                $imagens_create[$p]['file_name'] = $file['name'];
                $imagens_create[$p]['path_file'] = 'public/'.$request->local.'/'.$random_path.'/'.$banner_name;
                $imagens_create[$p]['url_file'] = asset('storage/'.$request->local.'/'.$random_path.'/'.$banner_name);
            }
        }
        ksort($imagens_create);
        foreach ($imagens_create as $imgsc){
            BannerConfig::create($imgsc);
        }

        if(isset($request->imagem_update)){
            foreach($request->imagem_update as $key => $img_update){
                BannerConfig::find($key)->update([
                    'file_name' => $img_update['name'],
                    'link'      => $img_update['link'],
                    'new_tab'   => isset($img_update['new_tab']) ? 1 : 0,
                ]);
            }
        }

        if(isset($request->imagem_delete)){
            foreach ($request->imagem_delete as $img_id){
                $image = BannerConfig::find($img_id);
                $path_file = explode('/', $image->path_file);
                unset($path_file[3]);
                $path_file = collect($path_file)->join('/');

                Storage::deleteDirectory($path_file);
                $image->delete();
            }
        }

        return response()->json('',200);
    }
}
