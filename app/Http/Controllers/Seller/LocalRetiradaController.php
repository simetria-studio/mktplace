<?php

namespace App\Http\Controllers\Seller;

use App\Models\Produto;
use Illuminate\Http\Request;
use App\Models\LocalRetirada;
use App\Models\LocalidadeRetirada;
use App\Http\Controllers\Controller;

class LocalRetiradaController extends Controller
{
    public function dataModalLocaldeRetirada($request)
    {
        $localidades    = LocalidadeRetirada::all();
        $produtos       = Produto::where('seller_id', auth('seller')->user()->id)->whereHas('images')->where('ativo', 'S')->where('status', 1)->get();
        if($request->edit_id ?? null){
            $localretirada = LocalRetirada::with(['localidade'])->find($request->edit_id);
            // \Log::info($localretirada->products->toArray());
        }

        return view('components.seller.fretes.modalLocalDeretirada', get_defined_vars());
    }

    public function dataLocaisDeRetirada($request)
    {
        $localretiradas = LocalRetirada::with(['localidade'])->paginate(15);

        return view('seller.fretes.indexLocalDeRetirada', get_defined_vars());
    }

    public function dataAnyLocalDeRetirada(Request $request)
    {
        $make_data['seller_id']     = auth('seller')->user()->id;
        $make_data['localidade_id'] = $request->localidade_id;
        $make_data['description']   = $request->description;
        // $make_data['products_id']   = !($request->all_products ?? null) ? $request->products_id : [];
        // $make_data['all_products']  = $request->all_products ?? null ? 1 : 0;
        $make_data['all_products']  = 1;

        LocalRetirada::updateOrCreate(
            ['id' => ($request->id ?? 0)],
            $make_data
        );

        $localretiradas = LocalRetirada::with(['localidade'])->paginate(15);

        $table_html = view('components.seller.fretes.tableLocalDeRetirada', get_defined_vars())->render();

        return response()->json([
            'table_html' => $table_html
        ]);
    }

    public function dataDeleteLocalDeRetirada($request)
    {
        LocalRetirada::destroy($request->id);

        $page = $request->page ?? 1;
        $localretiradas = LocalRetirada::with(['localidade'])->paginate(15);

        $table_html = view('components.seller.fretes.tableLocalDeRetirada', get_defined_vars())->render();

        return response()->json([
            'table_html' => $table_html
        ]);
    }
}
