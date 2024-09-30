<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta Tags -->
    <title>{{ $seo->title ?? 'MarktPlace vapu-vapu' }} - VAPU VAPU</title>
    <meta name="title" content="BIGUAÇU - {{ $seo->title ?? 'MarktPlace Biguaçu' }}">
    <meta name="description" content="{{ $seo->description ?? 'MarktPlace Biguaçu' }}">
    <meta name="image"
        content="{{ isset($seo->banner_path) ? asset('storage/' . $seo->banner_path) : asset('site/imgs/logo.png') }}">
    <link rel="canonical" href="https://feitoporbiguacu.com/{{ $seo->link ?? '' }}">
    <meta name="keywords" content="{{ isset($seo->keywords) ? implode(',', $seo->keywords) : '' }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://feitoporbiguacu.com/{{ $seo->link ?? '' }}">
    <meta property="og:title" content="BIGUAÇU - {{ $seo->title ?? '' }}">
    <meta property="og:description" content="{{ $seo->description ?? 'MarktPlace Biguaçu' }}">

    @php
        if (isset($products)) {
            $product = null;
        }
    @endphp

    @isset($product)
        <meta property="product:brand" content="Biguaçu">
        @if (empty($product->stock_controller))
            <meta property="product:availability" content="in stock">
        @else
            <meta property="product:availability"
                content="{{ $product->stock_controller == 'true' ? (!empty($product->stock) ? ($product->stock > 0 ? 'in stock' : 'out of stock') : 'available for order') : 'available for order' }}">
        @endif
        <meta property="product:condition" content="new">
        <meta property="product:price:amount" content="{{ $product->preco ?? 0 }}">
        <meta property="product:price:currency" content="BRL">
        <meta property="product:retailer_item_id" content="{{ $product->id }}">
        <meta property="og:image"
            content="{{ isset($seo->banner_path) ? asset('storage/' . $seo->banner_path) : $product->images->sortBy('position')->first()->caminho ?? '' }}">
    @else
        <meta property="og:image"
            content="{{ isset($seo->banner_path) ? asset('storage/' . $seo->banner_path) : asset('site/imgs/logo.png') }}">
    @endisset

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://feitoporbiguacu.com/{{ $seo->link ?? '' }}">
    <meta property="twitter:title" content="BIGUAÇU - {{ $seo->title ?? '' }}">
    <meta property="twitter:description" content="{{ $seo->description ?? 'MarktPlace Biguaçu' }}">
    <meta property="twitter:image"
        content="{{ isset($seo->banner_path) ? asset('storage/' . $seo->banner_path) : asset('site/imgs/logo.png') }}">

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-N9CSN44N');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-N9CSN44N"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-N9CSN44N');
    </script>

    <!-- Global site tag (gtag.js) - Google Ads -->
    {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=AW-661991335"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'AW-661991335');
    </script> --}}

    <!-- Facebook Pixel Code -->
    {{-- <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '2695463584094059');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=2695463584094059&ev=PageView&noscript=1" /></noscript> --}}
    <!-- End Facebook Pixel Code -->

    <meta name="facebook-domain-verification" content="k1cr68qtd1ujy9ozo06kezok4ndg4x" />

    <meta name="grecaptcha-key" content="{{ config('recaptcha.v3.public_key') }}">
    @yield('meta_tags')

    <link rel="shortcut icon" href="{{ asset('site/imgs/favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    {{-- Slick --}}
    <link rel="stylesheet" href="{{ asset('plugin/slick-1.8.1/slick.css') }}" />
    <link rel="stylesheet" href="{{ asset('plugin/slick-1.8.1/slick-theme.css') }}" />

    <link rel="stylesheet" href="{{ asset('plugin/bootstrap-4.6.1/css/bootstrap.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('plugin/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugin/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('plugin/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- DateRangerPicker -->
    <link rel="stylesheet" href="{{ asset('plugin/daterangepicker/daterangepicker.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugin/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugin/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugin/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugin/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    {{-- Jquery-ui --}}
    <link rel="stylesheet" href="{{ asset('plugin/jquery-ui-1.13.1/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugin/jquery-ui-1.13.1/jquery-ui.theme.min.css') }}">
    <!-- Custom css -->
    <link rel="stylesheet" href="{{ asset('site/css/custom.min.css') }}">
    <link rel="stylesheet" href="{{ asset('site/css/custom.menu.min.css') }}">

    <style>
        select[readonly].select2-hidden-accessible+.select2-container {
            pointer-events: none;
            touch-action: none;
        }

        select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
            background: #eee;
            box-shadow: none;
        }

        select[readonly].select2-hidden-accessible+.select2-container .select2-selection__arrow,
        select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear {
            display: none;
        }
    </style>
    @laravelPWA
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N9CSN44N" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

    <header>
        {{-- Topo --}}
        <section class="container-fluid d-none d-md-block mb-1 top-header">
            <div class="container-fluid">
                <h6 class="d-flex justify-content-center"><span class="d-none d-md-block pr-1">Que tal conferir os
                        produtos que estão pertinho de você? </span></h6>
            </div>
        </section>

        {{-- Header Desktop --}}
        <section class="container-fluid d-none d-md-block my-1 my-md-2 top-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-c1-12 col-7 col-md-3 mb-2 mb-md-0 text-center text-sm-left"><a
                            href="{{ route('home') }}"><img class="" width="50%"
                                src="{{ asset('site/imgs/logo.png') }}" alt=""></a></div>

                    <div
                        class="col-c1-12 col-5 col-md-5 col-lg-4 col-xl-3 mb-2 mb-md-0 d-flex flex-column justify-content-center">
                        {{-- <div class="row">
                            <div class="col-c1-6 col-12 col-md-6 mb-1 mb-md-0 d-flex align-items-center px-1"><a href="{{route('home')}}" class="btn btn-df1">Produtos</a></div>
                            <div class="col-c1-6 col-12 col-md-6 px-1"><a href="{{route('rural_tourism')}}" class="btn btn-df2">Turismo</a></div>
                        </div> --}}
                    </div>

                    <div
                        class="col-6 col-sm-1 col-md-1 col-lg-2 d-none d-sm-flex align-items-center justify-content-center">
                        <a class="link" href="{{ route('contactus') }}"><i
                                class="fas fa-comment mx-1 d-block d-lg-none"></i></a>
                        <div class="d-lg-flex d-none align-items-center">
                            <i class="fas fa-comment mx-1"></i> <a class="link"
                                href="{{ route('contactus') }}">Fale Conosco</a>
                        </div>
                    </div>

                    <div class="col-6 col-sm-2 col-md-1 col-xl-2 d-flex align-items-center justify-content-center">
                        <div class="btn-group d-none d-sm-block d-xl-none">
                            <button type="button" class="btn btn-light cust dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user "></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="{{ auth()->check() ? route('perfil') : route('login') }}">{{ auth()->check() ? 'Perfil' : 'Entrar' }}</a>
                                <a class="dropdown-item"
                                    @if (auth()->check()) onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="#" @else href="{{ route('register') }}" @endif>{{ auth()->check() ? 'Sair' : 'Cadastrar' }}</a>
                            </div>
                        </div>
                        <div class="d-none d-xl-flex">
                            <i class="fas fa-user user-icon"></i> <a class="link mx-1"
                                href="{{ auth()->check() ? route('perfil') : route('login') }}">{{ auth()->check() ? 'Perfil' : 'Entrar' }}</a>
                            <span class="divider"></span> <a class="link mx-1"
                                @if (auth()->check()) onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="#" @else href="{{ route('register') }}" @endif>{{ auth()->check() ? 'Sair' : 'Cadastrar' }}</a>
                        </div>
                    </div>

                    <div class="col-sm-2 col-md-2 d-none d-sm-flex align-items-center justify-content-end ">
                        <a href="{{ route('favorites') }}" class="cart-buttons">
                            <i class="fas fa-heart ml-3 ml-lg-0 mr-0 mr-lg-2 sv-cart" style="color: #FF8300;"></i>
                            {{-- <img style="width: 36px;" src="{{ asset('site/imgs/heart.png') }}" alt=""> --}}
                        </a>
                        <a href="#" class="button-cart position-relative cart-buttons">
                            @if (cart_show()->quantidade > 0)
                                <span class="cart-badges">{{ cart_show()->quantidade }}</span>
                            @endif
                            <i class="fas fa-shopping-cart ml-lg-2 ml-1 sv-cart" style="color: #cf1a13;"></i>
                            {{-- <img style="width: 36px;" src="{{ asset('site/imgs/shopping-cart.png') }}" alt=""> --}}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Header Mobile --}}
        <section class="container-fluid d-md-none top-body-mobile">
            <div class="d-flex align-items-center h-100">
                <div class="t1">
                    <button class="btn" id="openMenu"><i class="fas fa-bars"></i></button>
                </div>
                <div class="t2">
                    <a href="{{ asset('/') }}"><img class="logo"
                            src="{{ asset('site/imgs/logo.png') }}" alt=""></a>
                </div>

                <div class="t3 d-flex">
                    <div>
                        <button class="btn btn-open-moda-busca-rapida" aria-open="false">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z"
                                    stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M21 20.9999L16.65 16.6499" stroke="white" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <button class="btn button-cart position-relative">
                            @if (cart_show()->quantidade > 0)
                                <span class="cart-badges">{{ cart_show()->quantidade }}</span>
                            @endif
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z"
                                    stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z"
                                    stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6"
                                    stroke="white" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        @yield('category-top')
    </header>

    {{-- Modal de pesquisa simples --}}
    <div class="position-fixed" id="modalSearchSP"
        style="top: -300px;left: 50%;transform: translate(-50%, 0);z-index: 99;width: 74%;">
        <div class="modal-dialog">
            <div class="modal-content" style="border: none;background-color: transparent;">
                <div class="pt-1">
                    <form action="{{ route('search') }}" method="get">
                        <div class="input-group">
                            <input type="search" name="q"
                                value="@isset($_GET['q']){{ $_GET['q'] }}@endisset"
                                class="form-control form-control-sm" placeholder="Busca rapida!">

                            <div class="input-group-append">
                                <button class="btn btn-sm btn-dark"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Carrinho --}}
    <section id="cart_shop" class="close-cart">
        <div class="cart-box">
            <div class="cart-content">
                @if (count(cart_show()->content ?? []) > 0)
                    <a href="{{ route('clearCart') }}" class="m-2 cart-produto-apagar">Limpar Carrinho</a>
                @endif

                <button type="button" class="m-3 close close-cart">X</button>

                <div class="container my-5">
                    {{-- {{print_r(cart_show())}} --}}

                    @if (count(cart_show()->content ?? []) > 0)
                        @foreach (cart_show()->content as $modal_cart)
                            <div class="cart-produto">
                                <div class="cart-image"><img width="120px"
                                        src="{{ $modal_cart->attributes->product_image }}" alt=""></div>
                                <div class="cart-produto-body d-flex flex-column justify-content-center">
                                    <p class="cart-produto-nome">{{ $modal_cart->name }}</p>
                                    <p class="cart-produto-valor">R$ {{ $modal_cart->quantity }} x R$
                                        {{ number_format($modal_cart->price, 2, ',', '') }}</p>
                                </div>
                                <div class="cart-produto-button d-flex flex-column justify-content-center"><a
                                        href="#" class="btn btn-danger btn-delete-product"
                                        data-json_gtag="{{ collect(cart_show()->content)->toJson() }}"
                                        data-repagina="sim" data-row_id="{{ $modal_cart->id }}"><i
                                            class="fa fa-trash"></i></a></div>
                            </div>
                        @endforeach

                        <div class="my-2 sub-total d-flex justify-content-center">
                            <div class="row w-75">
                                <div class="col-6 title"><b>SUBTOTAL:</b></div>
                                <div class="col-6 text-right price"><b>R$
                                        {{ number_format(cart_show()->total, 2, ',', '.') }}</b></div>
                            </div>
                        </div>

                        <div class="cart-button">
                            <div class="my-2"><a href="{{ route('cart') }}" class="btn btn-default">FINALIZAR
                                    COMPRA</a></div>
                            <div class="my-2"><a href="{{ route('home') }}" class="btn btn-default">CONTINUAR
                                    COMPRANDO</a></div>
                            <div class="my-2"><a href="{{ route('cart') }}" class="btn btn-default">IR PARA O
                                    CARRINHO</a></div>
                        </div>
                    @else
                        <div class="my-2"> NENHUM PRODUTO NO CARRINHO</div>
                        <div class="cart-button">
                            <div class="my-2"><a href="{{ route('home') }}" class="btn btn-default">IR PARA A
                                    LOJA</a></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Menu lateral --}}
    <section id="menu_lateral" class="close-menu">
        <div class="menu-box">
            <div class="menu-content">
                <button type="button" class="m-3 close close-menu">X</button>

                <div class="container my-2">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <i class="fas fa-user mx-1"></i> <a class="link mx-1"
                                href="{{ auth()->check() ? route('perfil') : route('login') }}">{{ auth()->check() ? 'Perfil' : 'Entrar' }}</a>
                            | <a class="link mx-1"
                                @if (auth()->check()) onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="#" @else href="{{ route('register') }}" @endif>{{ auth()->check() ? 'Sair' : 'Cadastrar' }}</a>
                        </div>
                        <div class="col-12 mb-2">
                            <i class="fas fa-comment mx-1"></i> <a class="link"
                                href="{{ route('contactus') }}">Fale Conosco</a>
                        </div>

                        <div class="col-12 mb-2">
                            <div style="text-align: center; font-size: .9rem;">Que tal conferir os produtos que estão
                                pertinho de você?</div>
                            <form action="{{ route('ModalCepSession') }}" method="get">
                                <div class="modal-body">
                                    <div class="input-group">
                                        <input type="text" name="post_code"
                                            value="{{ $_COOKIE['cep_consulta'] ?? '' }}"
                                            class="form-control form-control-sm" placeholder="Insira seu cep">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-sm btn-dark"><i
                                                    class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-12 my-2">
                            <h5 class="w-100 border-bottom border-dark">PESQUISA</h6>
                        </div>

                        <div class="col-12 mb-2">
                            <form action="{{ route('search') }}" method="get">
                                <div class="row">
                                    <div class="col-12 form-group">
                                        <div class="input-group">
                                            <input type="search" name="q"
                                                value="@isset($_GET['q']){{ $_GET['q'] }}@endisset"
                                                class="form-control form-control-sm"
                                                placeholder="O que você procura hoje?">

                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-dark"><i
                                                        class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group">
                                        <select name="c" class="form-control form-control-sm">
                                            <option value="">Todas as Categorias</option>
                                            @foreach (getCategories($category_type ?? 0) as $category)
                                                <option value="{{ $category->slug }}"
                                                    @isset($_GET['c']) @if ($_GET['c'] == $category->slug) selected @endif @endisset>
                                                    {{ mb_convert_case($category->name, MB_CASE_TITLE) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-12 my-2">
                            <h5 class="w-100 border-bottom border-dark">CATEGORIAS</h6>
                        </div>

                        <div class="col-12">
                            <ul class="menu-lateral-categorias">
                                @foreach (getCategories($category_type ?? 0) as $category)
                                    @php
                                        $image = Storage::get($category->icon);
                                        $mime_type = Storage::mimeType($category->icon);
                                        $image = 'data:' . $mime_type . ';base64,' . base64_encode($image);
                                    @endphp
                                    <li class="lateral-categoria"
                                        title="{{ mb_convert_case($category->name, MB_CASE_TITLE) }}">
                                        <a href="{{ route('category', $category->slug) }}" class="link">
                                            <span class="img"><img width="30%" src="{{ $image }}"
                                                    alt=""></span>
                                            <span
                                                class="title">{{ mb_convert_case($category->name, MB_CASE_TITLE) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="container-fluid px-0">
        @if (!\Request::is('carrinho') && !\Request::is('login') && !\Request::is('vendedor/login'))
            <div class="mt-3 mb-2 d-flex d-md-none header-site">
                <div class="col-12 px-1">
                    <a href="{{ route('home') }}" class="btn btn btn-p-success btn-block">Produtos</a>
                </div>
                {{-- <div class="col-6 px-1">
                    <a href="{{ route('rural_tourism') }}" class="btn btn-s-warning btn-block">Turismo</a>
                </div> --}}
            </div>
        @endif

        @yield('container')
    </main>

    <footer class="footer">
        @yield('receiver-news')

        <div class="container-fluid pt-5 footer-body">
            <div class="row">
                <div class="col-12 col-sm-4 d-flex justify-content-center">
                    <div>
                        <h5>SOBRE NÓS</h5>
                        <img class="img-fluid" width="200" src="{{ asset('site/imgs/logo.png') }}"
                            alt="">


                    </div>
                </div>

                <div class="col-12 col-sm-8">
                    <div class="row" style="height: 100%;">
                        <div class="col-12 col-md-3 mb-2 mb-md-0">
                            <h5>MAPA DO SITE</h5>

                            <ul class="nav flex-column">
                                {{-- <li class="nav-item"><a class="nav-link" href="{{ route('whoweare') }}">Quem
                                        Somos</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('contactus') }}">Fale
                                        Conosco</a></li> --}}
                                {{-- <li class="nav-item"><a class="nav-link" href="https://raeasy.com/blog/">Blog</a></li> --}}
                                <li class="nav-item"><a class="nav-link" href="{{ route('perfil') }}">Minha
                                        Conta</a></li>
                                {{-- <li class="nav-item"><a class="nav-link" href="{{ route('favorites') }}">Linha de
                                        Desejos</a></li> --}}
                            </ul>
                        </div>

                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <h5>AJUDA E SUPORTE</h5>

                            {{-- <ul class="nav flex-column">
                                <li class="nav-item"><a class="nav-link" href="{{ route('termsofuse') }}">Termos de
                                        Uso</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('faq') }}">Perguntas
                                        Frequentes</a></li>
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ route('exchangereturns') }}">Trocas e Devoluções</a></li>
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ route('privacypolicy') }}">Política de Privacidade</a></li>
                            </ul> --}}
                        </div>

                        <div class="col-12 col-md-5  mb-4 mb-md-0">
                            <h5>PAGAMENTO E SEGURANÇA</h5>

                            <img src="{{ asset('site/imgs/pagamentos.png') }}" alt="">
                        </div>

                        <div class="col-12 col-md-10 mb-3 d-flex footer-espcial-btn">
                            <div class="row mt-auto">
                                <div class="col-10 d-flex mb-3 mb-md-0">
                                    <a class="mt-auto btn-vendedor mr-1 mr-md-2"
                                        href="{{ route('seller.register') }}">SEJA UM VENDEDOR</a>
                                    <a class="mt-auto btn-vendedor" href="{{ route('register') }}">SEJA UM
                                        AFILIADO</a>
                                </div>
    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-footer">
            <div class="container py-4">
                <div class="row">
                    <div class="col-12 col-md-9 footer-r mb-3 mb-md-0">Copyright {{ date('Y') }} &COPY; VAPU-VAPU LTDA</div>
                    <div class="col-12 col-md-3 footer-r text-md-right text-left">CNPJ: 00.000.000/0000</div>
                </div>
            </div>
        </div>
    </footer>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    {{-- Modal para realizar o consulta de cep --}}
    {{-- <div class="position-fixed d-none" id="ModalCepSession">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="close close-modal-cep m-1">
                    <span aria-hidden="true">&times;</span>
                </button>
                <form action="{{ route('ModalCepSession') }}" method="get">
                    <div class="row mr-0 ml-0 mt-5 mt-md-3">
                        <div class="col-3 p-0 position-relative">
                            <img src="{{ asset('site/imgs/avatar-mulher.png') }}" class="avatar"
                                alt="Macote da Biguaçu">
                        </div>
                        <div class="col-9">
                            <h6 class="text-center" style="font-weight: bold;">QUAL O SEU CEP?</h6>
                            <div class="m-0 d-flex justify-content-center">
                                <div class="position-relative">
                                    <input type="text" name="post_code"
                                        style="border: 1px solid #E7E6E6;border-radius: 2rem;padding: 6px 10px;"
                                        value="{{ $_COOKIE['cep_consulta'] ?? '' }}"
                                        placeholder="Digite aqui o CEP">
                                    <button type="submit" class="btn btn-info">OK</button>
                                </div>
                            </div>
                            <div class="text-center mt-3" style="font-weight: bold;font-size: .8rem;">
                                Com essa informação iremos mostrar os produtos que estão pertinho de você!
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <div class="modal-cookie">
        <div class="body-cookie container">
            <div class="row pb-5">
                <div class="col-12 col-sm-9 text-center" style="font-size: 1rem;">
                    Utilizamos cookies para melhorar a sua experiência.
                    Ao continuar, você concorda com a nossa
                    <a class="btn-pp" target="_blank" href="{{ route('privacypolicy') }}">Política de
                        Privacidade</a>. Ao continuar no aplicativo, você concorda com essa Política.
                </div>
                <div class="col-12 col-sm-3">
                    <button type="button" class="btn btn-block btn-c-primary btn-yes-cookie">Aceitar cookies</button>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-light btn-scroll-top d-none"><i class="fas fa-arrow-up"></i></button>

    <!-- jQuery -->
    <script src="{{ asset('plugin/jquery-3.6.0.min.js') }}"></script>
    {{-- Slick --}}
    <script src="{{ asset('plugin/slick-1.8.1/slick.min.js') }}"></script>
    <!-- MaskJquery -->
    <script src="{{ asset('plugin/mask.jquery.js') }}"></script>
    <script src="{{ asset('plugin/mask.money.js') }}"></script>
    <!-- ValidaCnpjCpf -->
    <script src="{{ asset('plugin/valida_cpf_cnpj.js') }}"></script>
    <!-- bootstrap-4.6.1 -->
    <script src="{{ asset('plugin/bootstrap-4.6.1/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('plugin/select2/js/select2.full.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('plugin/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Moment -->
    <script src="{{ asset('plugin/moment/moment.min.js') }}"></script>
    <!-- fontawesome -->
    <script src="{{ asset('plugin/fontawesome-free/js/all.min.js') }}"></script>
    <!-- DateRangerPicker -->
    <script src="{{ asset('plugin/daterangepicker/daterangepicker.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('plugin/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('plugin/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('plugin/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    {{-- Smartmenus --}}
    <script src="{{ asset('site/js/jquery.smartmenus.min.js') }}"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    {{-- Jquert-ui --}}
    <script src="{{ asset('plugin/jquery-ui-1.13.1/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('plugin/jquery.ui.touch-punch.min.js') }}"></script>

    <script>
        function GRecaptchaFun(thiss) {
            let grecaptchaKeyMeta = $("meta[name='grecaptcha-key']");
            let grecaptchaKey = grecaptchaKeyMeta.attr("content");

            grecaptcha.ready(function() {
                let forms = thiss;

                grecaptcha.execute(grecaptchaKey, {
                    action: $(this).attr('grecaptcha-action')
                }).then((token) => {
                    if (forms.find('[name="grecaptcha"]').length > 0) {
                        forms.find('[name="grecaptcha"]').val(token);
                    } else {
                        forms.prepend(`<input type="hidden" name="grecaptcha" value="${token}">`);
                    }
                    return true;
                });
            });

            return false;
        }
    </script>

    <script src="{{ asset('site/js/custom.min.js') }}"></script>

    <script>
        // Apaga o cache do navegador
        // caches.keys().then(function(names) {
        //     for (let name of names)
        //         caches.delete(name);
        // });
        // fecha - apaga o cache do navegador

        $(document).ready(function() {
            // Condifgurações
            var Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 5000
            });

            if (`{{ session()->has('success_alert') }}`) {
                Toast.fire({
                    icon: 'success',
                    title: `{{ session()->get('success_alert') }}`
                });
            }

            // Botão menu mobile
            $(document).on("click", "#openMenu", function(e) {
                e.preventDefault();
                $("#menu_lateral").addClass('menu-body');
                $("#menu_lateral").find(".menu-box").addClass('menu-box-size');

                $("body").addClass("modal-open");
            });

            $(document).on("click", ".close-menu", function(e) {
                let close_box = $(e.target).is("#menu_lateral") ? true : false;
                let close_button = e.target == document.querySelector("button.close-menu") ? true : false;

                if (close_box || close_button) {
                    $("#menu_lateral").find(".menu-box").removeClass("menu-box-size");
                    setTimeout(function() {
                        $("#menu_lateral").removeClass("menu-body");

                        $("body").removeClass("modal-open");
                    }, 398);
                }
            });
            // Botão menu mobile

            // Botão open cart
            $(document).on("click", ".button-cart", function(e) {
                e.preventDefault();
                $("#cart_shop").addClass('cart-body');
                $("#cart_shop").find(".cart-box").addClass('cart-box-size');

                $("body").addClass("modal-open");
            });

            $(document).on("click", ".close-cart", function(e) {
                let close_box = $(e.target).is("#cart_shop") ? true : false;
                let close_button = e.target == document.querySelector("button.close-cart") ? true : false;

                if (close_box || close_button) {
                    $("#cart_shop").find(".cart-box").removeClass("cart-box-size");
                    setTimeout(function() {
                        $("#cart_shop").removeClass("cart-body");

                        $("body").removeClass("modal-open");
                    }, 398);
                }
            });
            // Botão open cart

            $(document).on('click', '#ModalCepSession .btn.btn-info', function() {
                $(this).html(
                    `<div class="spinner-border" style="width: 1.5rem; height: 1.5rem;" role="status"><span class="sr-only">Loading...</span></div>`
                );
            });

            $(document).on("click", '.close-modal-cep', function() {
                var date_cookie = new Date();
                date_cookie.setDate(date_cookie.getDate() + 1)
                document.cookie = "cep_modal_close=true; expires=" + (date_cookie.toUTCString());
                $('#ModalCepSession').css({
                    'top': '-300px',
                    'transition': 'top 1s'
                });
                setTimeout(() => {
                    $('#ModalCepSession').addClass('d-none');
                }, 1000);
            });

            $(document).on("click", '[data-toggle="custom_modal_cep"]', function(e) {
                e.preventDefault();
                $('#ModalCepSession').removeClass('d-none');
                setTimeout(() => {
                    $('#ModalCepSession').css({
                        'top': '0',
                        'transition': 'top .8s'
                    });
                }, 100);
            });

            $(document).on("click", ".btn-open-moda-busca-rapida", function(e) {
                e.preventDefault();
                var aria_open = $(this).attr('aria-open');

                if (aria_open == 'false') {
                    $(this).attr('aria-open', 'true');
                    $("#modalSearchSP").css({
                        'top': '65px',
                        'transition': 'top .4s'
                    });
                } else {
                    $(this).attr('aria-open', 'false');
                    $("#modalSearchSP").css({
                        'top': '-300px',
                        'transition': 'top .8s'
                    });
                }
            });

            $(function() {
                setTimeout(() => {
                    if (!getCookie('cep_consulta')) {
                        if (!getCookie('cep_modal_close')) {
                            $('#ModalCepSession').removeClass('d-none');
                            setTimeout(() => {
                                var tbm = 16;
                                if ($('header').find('.top-body-mobile').css('display') ==
                                    'block') tbm = 36;

                                $('#ModalCepSession').css({
                                    'top': tbm + 'px',
                                    'transition': 'top .8s'
                                });
                            }, 100);
                        }
                    }
                }, 1000);
            });
        });
    </script>
    {{-- <script>
        $(document).ready(function(){
            setInterval(() => {
                $.ajax({
                    url: `{{route('getToken')}}`,
                    type: "GET",
                    data: {},
                    success: (data)=>{
                        // console.log(data);
                    }
                });
            }, 1800000);
        });
    </script> --}}

    @yield('js')
</body>

</html>
