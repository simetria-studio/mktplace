@extends('layouts.site')

@section('container')
    <div class="container-fluid" style="background-color: #EDEDED">
        <div class="container">
            <div class="row py-5">
                <div class="col-3 d-none d-lg-block">
                    <div class="row">
                        <div class="col-12 my-2">
                            <form action="{{route('search.service')}}" method="get">
                                <div class="row">
                                    <div class="col-12 form-group">
                                        <div class="input-group">
                                            <input type="search" name="q" value="@isset($_GET['q']){{$_GET['q']}}@endisset" class="form-control form-control-sm" placeholder="O que você procura hoje?">
                                            
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-dark"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group">
                                        <select name="c" class="form-control form-control-sm">
                                            <option value="">Todas as Categorias</option>
                                            @foreach (getCategories('1') as $category)
                                                <option value="{{$category->slug}}" @isset($_GET['c']) @if($_GET['c'] == $category->slug) selected @endif @endisset>{{mb_convert_case($category->name,MB_CASE_TITLE)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12"><h3>CATEGORIAS</h3></div>

                        @foreach (getCategories('1') as $category)
                            @php
                                $image      = Storage::get($category->icon);
                                $mime_type  = Storage::mimeType($category->icon);
                                $image      = 'data:'.$mime_type.';base64,'.base64_encode($image);
                            @endphp
                            <div class="col-12">
                                <a title="{{mb_convert_case($category->name,MB_CASE_TITLE)}}" class="link-category" href="{{route('category.service', $category->slug)}}">
                                    <span class="img"><img width="15%" src="{{$image}}" alt=""></span>
                                    <span class="title">{{mb_convert_case($category->name,MB_CASE_TITLE)}}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-12 col-lg-9">
                    {{-- Filtros --}}
                    <div class="filters border rounded px-2 py-1 mt-1 mb-3 mt-sm-0">
                        <div class="legend border rounded px-2">FILTROS</div>
                        <form action="" method="get">
                            <div class="row mt-2">
                                {{-- <div class="form-group col-2 col-sm-2">
                                    <label for="">Por Pagina:</label>
                                    <input name="filters[per_page]" type="number" class="form-control form-control-sm" value="@if(!empty($_GET['filters']['per_page'])){{(int)$_GET['filters']['per_page']}}@else{{'9'}}@endif">
                                </div> --}}
                                <div class="form-group col-6 col-sm-5">
                                    <label for="">Ordenar Por:</label>
                                    <select name="filters[ordering]" class="form-control form-control-sm">
                                        <option value="recente" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'recente') selected @endif @endisset>Mais Recente</option>
                                        <option value="menor_preco" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'menor_preco') selected @endif @endisset>Menor Preço</option>
                                        <option value="maior_preco" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'maior_preco') selected @endif @endisset>Maior Preço</option>
                                    </select>
                                </div>
                                <div class="form-group col-8 col-sm-5">
                                    <label for="">Preços:</label>
                                    <div class="input-group">
                                        <input name="filters[preco_ini]" type="text" class="form-control form-control-sm" placeholder="Preço Menor 0,00" value="@if(!empty($_GET['filters']['preco_ini'])){{str_replace([',','.'],['.',','],$_GET['filters']['preco_ini'])}}@endif">
                                        <input name="filters[preco_fin]" type="text" class="form-control form-control-sm" placeholder="Preço Maior 0,00" value="@if(!empty($_GET['filters']['preco_fin'])){{str_replace([',','.'],['.',','],$_GET['filters']['preco_fin'])}}@endif">
                                    </div>
                                </div>
                                <div class="form-group col-4 col-sm-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-c-primary"><i class="fas fa-filter"></i> Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row laze_load">
                        @foreach ($services as $service)
                            @include('components.singleService', ['service' => $service, 'class' => 'col-md-4'])
                        @endforeach
                    </div>

                    <div class="my-5 loading d-none justify-content-center">
                        <div class="spinner-border d-none" style="width: 5rem; height: 5rem;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    {{-- {{$services->links()}} --}}
                </div>
            </div>
        </div>
    </div>
@endsection