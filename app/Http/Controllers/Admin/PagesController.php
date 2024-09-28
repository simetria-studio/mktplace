<?php

namespace App\Http\Controllers\Admin;

use App\Models\PageView;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManagerStatic as Image;

class PagesController extends Controller
{
    public function indexPages()
    {
        $page_views = PageView::get();
        return view('painel.outros.pages.indexPages', get_defined_vars());
    }

    public function indexPagenew()
    {
        return view('painel.outros.pages.indexPageNewEdit', get_defined_vars());
    }

    public function storePage(Request $request)
    {
        // dd($request->all());
        $this->pagewView($request);
        return redirect()->route('admin.pages')->with('success', 'Pagina Criada com Sucesso!');
    }

    public function indexPageEdit($id)
    {
        $page_view = PageView::find($id);
        return view('painel.outros.pages.indexPageNewEdit', get_defined_vars());
    }

    public function updatePage(Request $request)
    {
        // dd($request->all());
        $this->pagewView($request);
        return redirect()->route('admin.pages')->with('success', 'Pagina Criada com Sucesso!');
    }

    public function destroyPage(Request $request)
    {
        Storage::delete('public/'.PageView::find($request->id)->banner_path);
        PageView::find($request->id)->delete();
    }

    public function pagewView($request)
    {
        $page['title'] = $request->title;
        $page['link'] = 'pagina/'.$request->link;
        $page['keywords'] = $request->keywords;
        $page['description'] = $request->description;
        $page['body_page'] = $request->body_page;
        if(isset($request->banner_path)){
            $originalPath = storage_path('app/public/img_page_view/');
            if (!file_exists($originalPath)) {
                mkdir($originalPath, 0777, true);
            }

            $page_img = Image::make($request->banner_path);
            $page_img_name = Str::random().'.'.$request->banner_path->extension();
            $page_img->save($originalPath.$page_img_name);

            $page['banner_path'] = 'img_page_view/'.$page_img_name;
        }

        if($request->id){
            if(isset($request->banner_path)){
                Storage::delete('public/'.PageView::find($request->id)->banner_path);
            }
            PageView::find($request->id)->update($page);
        }else{
            PageView::create($page);
        }
    }
}
