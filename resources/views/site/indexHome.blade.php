@extends('layouts.site')

@section('category-top')
    @include('components.category', ['bg' => '#3D550C', 'tipo_home' => 'home'])
@endsection
@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    {{-- Banner --}}
    <div class="banner d-none d-md-block">
        <div id="carousel_banner" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @for ($i = 0; $i < banner_configs('home-page-principal')->count(); $i++)
                    <li data-target="#carousel_banner" data-slide-to="{{$i}}" @if($i == 0)class="active"@endif></li>
                @endfor
            </ol>
            <div class="carousel-inner">
                @foreach (banner_configs('home-page-principal') as $key => $banner)
                    <div class="carousel-item @if($key == 0) active @endif">
                        <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                    </div>
                @endforeach
                {{-- <div class="carousel-item">
                    <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                </div> --}}
            </div>
            <a class="carousel-control-prev" href="#carousel_banner" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel_banner" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    {{-- Banner Mobile --}}
    <div class="banner d-md-none">
        <div id="carousel_banner_mobile" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @for ($i = 0; $i < banner_configs('home-page-principal-mobile')->count(); $i++)
                    <li data-target="#carousel_banner_mobile" data-slide-to="{{$i}}" @if($i == 0)class="active"@endif></li>
                @endfor
            </ol>
            <div class="carousel-inner">
                @foreach (banner_configs('home-page-principal-mobile') as $key => $banner)
                    <div class="carousel-item @if($key == 0) active @endif">
                        <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                    </div>
                @endforeach
                {{-- <div class="carousel-item">
                    <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                </div> --}}
            </div>
            <a class="carousel-control-prev" href="#carousel_banner_mobile" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel_banner_mobile" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    <div class="custom-container my-1 mt-md-4">
        @isset($produtosPereciveis)
            <div class="mb-2 d-flex justify-content-between justify-content-md-start">
                <h3 class="border-bottom border-dark mb-0 pb-2 d-none d-md-block">EXPLORE OS PRODUTOS LOCAIS</h3>
                <h6 class="border-bottom border-dark mb-0 pb-2 d-md-none">EXPLORE OS PRODUTOS LOCAIS</h6>
                <div class="d-flex align-items-center ml-2">
                    <a href="{{route('produtoresLocais')}}" class="text-dark btn-link-vermais">VER MAIS >>></a>
                </div>
            </div>

            @if($produtosPereciveis->count() > 0)
                <div class="row mb-1">
                    @foreach ($produtosPereciveis as $produto)
                        @include('components.singleProduct', ['produto' => $produto, 'class' => 'col-md-3', 'product_gtag_type' => 'Produtos Pereciveis'])
                    @endforeach
                </div>
            @else
                <div class="container mb-1">
                    <div id="carousel_banner_two" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach (banner_configs('home-page-produtor-local') as $key => $banner)
                                <div class="carousel-item @if($key == 0) active @endif">
                                    <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                                </div>
                            @endforeach
                            {{-- <div class="carousel-item">
                                <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                            </div> --}}
                        </div>
                        <a class="carousel-control-prev" href="#carousel_banner_two" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carousel_banner_two" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    {{-- <a href="{{route('send.contactus')}}"><img class="img-fluid" src="{{asset('site/imgs/banner-para-cep-sem-produto-local-cadastrado.png')}}" alt=""></a> --}}
                </div>
            @endif
        @endisset

        <div class="mb-2 d-flex justify-content-between justify-content-md-start" style="margin-top: 25px">
            <h3 class="border-bottom border-dark mb-0 pb-2 d-none d-md-block">O QUE HÁ DE NOVO POR AQUI</h3>
            <h6 class="border-bottom border-dark mb-0 pb-2 d-md-none">O QUE HÁ DE NOVO POR AQUI</h6>
            <div class="d-flex align-items-center ml-2">
                <a href="{{route('indexnew')}}" class="text-dark btn-link-vermais">VER MAIS >>></a>
            </div>
        </div>

        <div class="row">
            @foreach ($produtosNovos as $produto)
                @include('components.singleProduct', ['produto' => $produto, 'class' => 'col-md-3', 'product_gtag_type' => 'Produtos Novos'])
            @endforeach
        </div>

 

        <div class="my-5 d-none d-sm-block text-center" style="position: relative;">
            <img src="{{asset('site/imgs/Feito-por-Biguacu.png')}}" alt="" class="img-fluid">
            <a href="{{route('seller.register')}}" target="_blank" style="background-color: transparent;position: absolute;height: 100%;width: 100%;left: 0;"></a>
        </div>
        <div class="my-5 d-sm-none text-center" style="position: relative;">
            <img src="{{asset('site/imgs/Feito-por-Biguacu-mobile.png')}}" alt="" class="img-fluid">
            {{-- <a href="{{route('products')}}" target="_blank" style="background-color: transparent;position: absolute;bottom: 17%;height: 10%;width: 98%;right: 1%;"></a> --}}
            {{-- <a href="{{route('seller.register')}}" target="_blank" style="background-color: transparent;position: absolute;bottom: 7%;height: 10%;width: 98%;right: 1%;"></a> --}}
        </div>

        {{-- <div class="my-5 d-flex justify-content-center">
            <h3 class="border-bottom border-dark pb-2 px-5">{{getTabelaGeral('titulos','titulo_evento_home')->valor??''}}</h3>
        </div> --}}

 
        <div class="container d-md-none">
            <div id="carrousel_evento_home" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($events_home as $key => $item)
                        <div class="carousel-item @if($key == 0) active @endif text-center">
                            <a href="{{$item->link}}" @if($item->new_tab == 1)target="_blank"@endif><img src="{{$item->url_file}}" class="img-fluid" alt="{{$item->file_name}}" title="{{$item->file_name}}"></a>
                            <div class="text-center mt-3">{!!$item->descricao_curta!!}</div>
                        </div>
                    @endforeach
                    {{-- @foreach (banner_configs('home-page-principal') as $key => $banner)
                        <div class="carousel-item @if($key == 0) active @endif">
                            <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                        </div>
                    @endforeach --}}
                    {{-- <div class="carousel-item">
                        <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                    </div> --}}
                </div>
                <a class="carousel-control-prev" href="#carrousel_evento_home" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carrousel_evento_home" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
@endsection
