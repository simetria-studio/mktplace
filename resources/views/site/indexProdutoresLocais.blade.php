@extends('layouts.site')

@section('category-top')
    @include('components.category', ['bg' => '#3D550C', 'tipo_home' => 'home'])
@endsection
@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    {{-- Banner --}}
    {{-- <div class="d-none d-sm-block">
        <img src="{{asset('site/imgs/banner-desktop-produtos-locais.png')}}" class="d-block w-100" alt="...">
    </div>
    <div class="d-sm-none">
        <img src="{{asset('site/imgs/banner-mobile-produtos-locais.png')}}" class="d-block w-100" alt="...">
    </div> --}}

    <div class="banner d-none d-sm-block">
        <div id="carousel_banner" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @for ($i = 0; $i < banner_configs('produtor-local-principal')->count(); $i++)
                    <li data-target="#carousel_banner" data-slide-to="{{$i}}" @if($i == 0)class="active"@endif></li>
                @endfor
            </ol>
            <div class="carousel-inner">
                @foreach (banner_configs('produtor-local-principal') as $key => $banner)
                    <div class="carousel-item @if($key == 0) active @endif">
                        <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                    </div>
                @endforeach
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

    <div class="container my-5">
        {{-- Filtros --}}
        <div class="filters border rounded px-2 py-1 mb-5 mt-3 mt-sm-0">
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
                    <div class="form-group col-5 col-sm-3 col-md-4">
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

        <div class="d-flex">
            <h3 class="border-bottom border-dark pb-2">PRODUTORES LOCAIS</h3>
        </div>
        @if ($endereço_procurado)
            <div class="mb-5">
                {{$endereço_procurado->localidade ?? $endereço_procurado->city}}/{{$endereço_procurado->uf ?? $endereço_procurado->state}}
            </div>
        @endif

        @if ($produtos->count() > 0)
            <div class="row">
                @foreach ($produtos as $produto)
                    @include('components.singleProduct', ['produto' => $produto, 'class' => 'col-md-3', 'product_gtag_type' => 'Produtos Locais'])
                @endforeach
            </div>

            <div class="links mt-3">{{$produtos->appends(['filters' => $_GET['filters'] ?? null])->links()}}</div>
        @else
            <div class="container mb-1">
                <div id="carousel_banner_two" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        @foreach (banner_configs('produtor-local-nao-encontrado') as $key => $banner)
                            <div class="carousel-item @if($key == 0) active @endif">
                                <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                            </div>
                        @endforeach
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
            </div>
        @endif
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function(){
            if(parseInt(`{{$endereço_procurado->id ?? 0}}`) == 0){
                $(function(){
                    setTimeout(() => {
                        if(!getCookie('cep_consulta')){
                            $('#ModalCepSession').removeClass('d-none');
                            setTimeout(() => {
                                $('#ModalCepSession').css({
                                    'top': '0',
                                    'transition': 'top .8s'
                                });
                            }, 100);
                        }
                    }, 1000);
                });
            }
        });
    </script>
@endsection