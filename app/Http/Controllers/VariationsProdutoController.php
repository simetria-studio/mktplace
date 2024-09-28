<?php

namespace App\Http\Controllers;

use App\Dtos\Produto;
use App\Models\VariationsProduto;
use Illuminate\Http\Request;

class VariationsProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        VendedorController::loadScopes();
        $data = $request->all();

        return view('components.produtoVariacoes', get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\VariationsProduto $variationsProduto
     * @return \Illuminate\Http\Response
     */
    public function show(VariationsProduto $variationsProduto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\VariationsProduto $variationsProduto
     * @return \Illuminate\Http\Response
     */
    public function edit(VariationsProduto $variationsProduto)
    {
        //
    }

    /**
     * @param Request $request
     * @param \App\Models\Produto $produto
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function update(Request $request, \App\Models\Produto $produto)
    {
        VendedorController::loadScopes();

        return view('components.variationsProduto', get_defined_vars());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\VariationsProduto $variationsProduto
     * @return \Illuminate\Http\Response
     */
    public function destroy(VariationsProduto $variationsProduto)
    {
        //
    }
}
