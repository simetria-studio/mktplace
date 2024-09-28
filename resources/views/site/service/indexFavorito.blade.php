@extends('layouts.site')

@section('category-top')
    @include('components.category', ['bg' => '#ca5812', 'tipo_home' => 'home-rural'])
@endsection
@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    <div class="container my-1 mt-md-4">
        <div><a class="btn btn-c-primary" href="{{route('favorites')}}">Ver Meus Produtos Favoritos</a></div>

        <div class="my-2 d-flex">
            <h3 class="border-bottom border-dark mb-0 pb-2 d-none d-sm-block">SEUS SERVIÇOS FAVORITOS</h3>
            <h6 class="border-bottom border-dark mb-0 pb-2 d-sm-none">SEUS SERVIÇOS FAVORITOS</h6>
        </div>

        <div class="row">
            @foreach ($services as $service)
                <div class="col-6 col-md-3 d-md-flex flex-column produtos">
                    <div>
                        <div class="div-img"><a href="{{route('service', $service->service_slug)}}" title="{{$service->title ?? $service->service_title}}"><img class="img-produto" src="{{isset($service->images[0]) ? $service->images[0]->caminho : asset('site/imgs/logo.png')}}" alt="{{isset($service->images[0]) ? $service->images[0]->legenda : ''}}"></a></div>
                        @php
                            $count_category = 0;
                            $categories = '';
                        @endphp
                        @foreach ($service->categories as $category)
                            @php
                                $count_category++;
                                if(isset($category->category->name)){
                                    $categories .= $category->category->name.($count_category < $service->categories->count() ? ' / ' : '');
                                }
                            @endphp
                        @endforeach
                        <div class="pl-2 pt-2 py-1 t-categoria text-truncate" title="{{$categories}}" data-toggle="tooltip">{{$categories}}</div>
                        <div class="pl-2 short-description">
                            <p class="mb-0 mb-md-1 text-truncate-c" title="{{$service->title ?? $service->service_title}}">{{\Str::upper($service->service_title)}}</p>
                        </div>
                    </div>
                    <div class="mt-md-auto star-rate">
                        <div class="div-btn-detail d-sm-none"><a href="{{route('service', $service->service_slug)}}"><i class="fas fa-shopping-cart sv-cart" style="font-size: 1.2rem; color: #59981A;"></i></a></div>
                        <div>
                            <div class="text-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= number_format(stars($service->id)['star_media'], 0, '', ''))
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="fas fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-center d-flex flex-column values">
                                @if ($service->variations->count() > 0)
                                    @php
                                        $precos = [];
                                        foreach($service->variations as $variation){
                                            $precos[] = $variation->preco;
                                        }
                                    @endphp
                                    {{-- <span class="value-1 pt-1">R$ 29,00</span> --}}
                                    <span class="value-2 py-1">A partir de R$ {{number_format(min($precos), 2, ',', '.')}}</span>
                                @else
                                    {{-- <span class="value-1 pt-1">R$ 29,00</span> --}}
                                    <span class="value-2 py-1">R$ {{number_format($service->preco, 2, ',', '.')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="div-btn-favorite d-sm-none"><a href="#" class="favorite" data-service_id="{{$service->id}}"><i class="fas fa-heart sv-cart" style="font-size: 1.2rem; color: #cb5813;"></i></a></div>
                        <div class="pl-2 div-btn my-2 d-none d-sm-block">
                            <a class="link" href="{{route('service', $service->service_slug)}}">COMPRAR</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mb-4 mt-5 d-flex justify-content-center">
            {{$services->links()}}
        </div>
    </div>
@endsection
