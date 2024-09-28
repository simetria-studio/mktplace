@extends('layouts.site')

@section('category-top')
    @include('components.category', ['bg' => '#3D550C', 'tipo_home' => 'home'])
@endsection
@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    <div class="container my-1 mt-md-4">

        {{-- Filtros --}}
        <div class="filters border rounded px-2 py-1 mt-3 mt-sm-0">
            <div class="legend border rounded px-2">FILTROS</div>
            <form action="" method="get">
                <div class="row mt-2">
                    <div class="form-group col-5 col-sm-3">
                        <label for="">Ordenar Por:</label>
                        <select name="filters[ordering]" class="form-control form-control-sm">
                            <option value="recente" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'recente') selected @endif @endisset>Mais Recente</option>
                            <option value="menor_preco" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'menor_preco') selected @endif @endisset>Menor Preço</option>
                            <option value="maior_preco" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'maior_preco') selected @endif @endisset>Maior Preço</option>
                        </select>
                    </div>
                    <div class="form-group col-5 col-sm-3">
                        <label for="">Preços:</label>
                        <div class="input-group">
                            <input name="filters[preco_ini]" type="text" class="form-control form-control-sm real" placeholder="Preço Menor 0,00" value="@if(!empty($_GET['filters']['preco_ini'])){{str_replace([',','.'],['.',','],$_GET['filters']['preco_ini'])}}@endif">
                            <input name="filters[preco_fin]" type="text" class="form-control form-control-sm real" placeholder="Preço Maior 0,00" value="@if(!empty($_GET['filters']['preco_fin'])){{str_replace([',','.'],['.',','],$_GET['filters']['preco_fin'])}}@endif">
                        </div>
                    </div>
                    <div class="form-group col-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-c-primary d-none d-sm-block"><i class="fas fa-filter"></i> Filtrar</button>
                        <button type="submit" class="btn btn-c-primary d-sm-none"><i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="mb-2 d-flex">
            <h3 class="border-bottom border-dark mb-0 pb-2 d-none d-sm-block">TODOS OS PRODUTOS</h3>
            <h6 class="border-bottom border-dark mb-0 pb-2 d-sm-none">TODOS OS PRODUTOS</h6>
        </div>

        <div class="row laze_load">
            @foreach ($produtos as $produto)
                @include('components.singleProduct', ['produto' => $produto, 'class' => 'col-md-3', 'product_gtag_type' => 'Todos os Produtos'])
            @endforeach
        </div>

        <div class="my-5 loading d-none justify-content-center">
            <div class="spinner-border d-none" style="width: 5rem; height: 5rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
@endsection
