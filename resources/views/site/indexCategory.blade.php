@extends('layouts.site')

@section('container')
    <div class="container-fluid" style="background-color: #EDEDED">
        <div class="container">
            <div class="row py-5">
                <div class="col-3 d-none d-lg-block">
                    <div class="row">
                        <div class="col-12 my-2">
                            <form action="{{route('search')}}" method="get">
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
                                            @foreach (getCategories('0') as $category)
                                                <option value="{{$category->slug}}" @isset($_GET['c']) @if($_GET['c'] == $category->slug) selected @endif @endisset>{{mb_convert_case($category->name,MB_CASE_TITLE)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-12"><h3>CATEGORIAS</h3></div>

                        @foreach (getCategories('0') as $category)
                            @php
                                $image      = Storage::get($category->icon);
                                $mime_type  = Storage::mimeType($category->icon);
                                $image      = 'data:'.$mime_type.';base64,'.base64_encode($image);
                            @endphp
                            <div class="col-12">
                                <a title="{{mb_convert_case($category->name,MB_CASE_TITLE)}}" class="link-category" href="{{route('category', $category->slug)}}">
                                    <span class="img"><img width="15%" src="{{$image}}" alt=""></span>
                                    <span class="title">{{mb_convert_case($category->name,MB_CASE_TITLE)}}</span>
                                </a>
                            </div>
                        @endforeach

                        {{-- <div class="col-12">
                            <a class="link-category" style="font-weight: bold;" href="{{route('indexnew')}}">
                                <span class="title">Novidades</span>
                            </a>
                        </div>
                        <div class="col-12">
                            <a class="link-category" style="font-weight: bold;" href="{{route('specialselection')}}">
                                <span class="title">Seleção Especial</span>
                            </a>
                        </div> --}}

                        {{-- <div class="col-12 my-5 d-flex flex-column banner-categoria">
                            <div class="div-1">
                                <img class="img-fluid-edit" src="{{asset('site/imgs/direto-do-produtor.png')}}" alt="">
                            </div>
                            <div class="row div-2">
                                <div class="col-12">
                                    <div class="div-custom">
                                        <div><img src="{{asset('site/imgs/icone-02.png')}}" alt=""></div>
                                        <div>
                                            <p class="title">PRODUTOR</p>
                                            <p class="text">O Produtor cadastra a sua conta e publica seus produtos na Biguaçu.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="div-custom">
                                        <div><img src="{{asset('site/imgs/icone-03.png')}}" alt=""></div>
                                        <div>
                                            <p class="title">PRODUTOS</p>
                                            <p class="text">Priorizamos alimentos de qualidade, com a certeza que você é fudamental.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="div-custom">
                                        <div><img src="{{asset('site/imgs/icone-04.png')}}" alt=""></div>
                                        <div>
                                            <p class="title">SEGURANÇA</p>
                                            <p class="text">Todas as compras são seguras e os seus dados criptografados.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="div-custom">
                                        <div><img src="{{asset('site/imgs/icone-01.png')}}" alt=""></div>
                                        <div>
                                            <p class="title">ENTREGA</p>
                                            <p class="text">O cliente escolhe seus produtos e o melhor lugar que deseja receber.</p>
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 my-2"><a class="link" href="#">CONHEÇA TODAS AS LINHAS -------></a></div>
                                        <div class="col-12 my-2"><a class="link" href="#">ANUNCIE SEUS PRODUTOS -------></a></div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>

                <div class="col-12 col-lg-9">
                    {{-- Filtros --}}
                    <div class="filters border rounded px-2 py-1 mt-1 mb-3 mt-sm-0">
                        <div class="legend border rounded px-2">FILTROS</div>
                        <form action="" method="get">
                            <div class="row mt-2">
                                {{-- <div class="form-group col-5 col-md-2">
                                    <label for="">Por Pagina:</label>
                                    <input name="filters[per_page]" type="number" class="form-control form-control-sm" value="@if(!empty($_GET['filters']['per_page'])){{(int)$_GET['filters']['per_page']}}@else{{'9'}}@endif">
                                </div> --}}
                                <div class="form-group col-7 col-md-5">
                                    <label for="">Ordenar Por:</label>
                                    <select name="filters[ordering]" class="form-control form-control-sm">
                                        <option value="recente" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'recente') selected @endif @endisset>Mais Recente</option>
                                        <option value="menor_preco" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'menor_preco') selected @endif @endisset>Menor Preço</option>
                                        <option value="maior_preco" @isset($_GET['filters']['ordering']) @if($_GET['filters']['ordering'] == 'maior_preco') selected @endif @endisset>Maior Preço</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-5">
                                    <label for="">Preços:</label>
                                    <div class="input-group">
                                        <input name="filters[preco_ini]" type="text" class="form-control form-control-sm" placeholder="Preço Menor 0,00" value="@if(!empty($_GET['filters']['preco_ini'])){{str_replace([',','.'],['.',','],$_GET['filters']['preco_ini'])}}@endif">
                                        <input name="filters[preco_fin]" type="text" class="form-control form-control-sm" placeholder="Preço Maior 0,00" value="@if(!empty($_GET['filters']['preco_fin'])){{str_replace([',','.'],['.',','],$_GET['filters']['preco_fin'])}}@endif">
                                    </div>
                                </div>
                                <div class="form-group col-8 col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-c-primary"><i class="fas fa-filter"></i> Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row laze_load">
                        @foreach ($produtos as $produto)
                            @include('components.singleProduct', ['produto' => $produto, 'class' => 'col-md-3'])
                        @endforeach
                        {{-- <div class="col-4 mb-5 d-flex flex-column produtos">
                            <div>
                                <div class="div-img"><a href="{{route('product', 'teste-produto')}}"><img class="img-produto" src="{{asset('site/imgs/produto-1.png')}}" alt=""></a></div>
                                <div class="pl-2 pt-2 py-1 t-categoria">Categorias do produto</div>
                                <div class="pl-2 short-description">
                                    <p>Amora congelada - 1kg Amoras Congelados Selecionadas</p>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="pl-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="pl-2 d-flex flex-column values">
                                    <span class="value-1 pt-1">R$ 29,00</span>
                                    <span class="value-2 py-1">R$ 22,00</span>
                                </div>
                                <div class="pl-2 div-btn my-2">
                                    <a class="link" href="{{route('product', 'teste-produto')}}">COMPRAR</a>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    <div class="my-5 loading d-none justify-content-center">
                        <div class="spinner-border d-none" style="width: 5rem; height: 5rem;" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    {{-- {{$produtos->links()}} --}}

                    {{-- <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav> --}}
                </div>
            </div>
        </div>
    </div>
@endsection