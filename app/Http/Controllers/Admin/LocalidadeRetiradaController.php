<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\LocalidadeRetirada;
use App\Http\Controllers\Controller;

class LocalidadeRetiradaController extends Controller
{
    public function dataModalLocalidadeRetirada($request)
    {
        if($request->edit_id ?? null){
            $localidade = LocalidadeRetirada::find($request->edit_id);
        }

        return view('components.admin.cadastros.modalLocalidadeRetirada', get_defined_vars());
    }

    public function dataLocalidadesDeRetirada($request)
    {
        $localidades = LocalidadeRetirada::paginate(15);

        return view('painel.cadastros.indexLocalidadesRetirada', get_defined_vars());
    }

    public function dataAnyLocalidadeRetirada($request)
    {
        $make_data['title']         = $request->title;
        $make_data['description']   = $request->description;
        $make_data['zip_code']      = $request->zip_code;
        $make_data['address']       = $request->address;
        $make_data['number']        = $request->number;
        $make_data['district']      = $request->district;
        $make_data['city']          = $request->city;
        $make_data['state']         = $request->state;

        LocalidadeRetirada::updateOrCreate(
            ['id' => ($request->id ?? 0)],
            $make_data
        );

        $localidades = LocalidadeRetirada::paginate(15);

        $table_html = view('components.admin.cadastros.tableLocalidadesRetirada', get_defined_vars())->render();

        return response()->json([
            'table_html' => $table_html
        ]);
    }

    public function dataDeleteLocalidadeRetirada($request)
    {
        LocalidadeRetirada::destroy($request->id);

        $page = $request->page ?? 1;
        $localidades = LocalidadeRetirada::paginate(15);

        $table_html = view('components.admin.cadastros.tableLocalidadesRetirada', get_defined_vars())->render();

        return response()->json([
            'table_html' => $table_html
        ]);
    }
}
