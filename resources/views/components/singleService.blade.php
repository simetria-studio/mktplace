<div class="col-6 {{$class}} d-md-flex flex-column produtos">
    <input type="hidden" class="gtag_select_item" value="
        {{collect([
            'items' => [
                [
                    'item_id' => 'S_'.$service->id,
                    'item_name' => $service->service_title,
                    'item_list_name' => isset($service_gtag_type) ? ($service_gtag_type[0] ?? 'Serviços de Turismo Rural') : 'Serviços de Turismo Rural',
                    'item_list_id' => isset($service_gtag_type) ? ($service_gtag_type[1] ?? 'servicos_tursismo_rural') : 'servicos_tursismo_rural',
                    'item_brand' => 'Biguaçu',
                    'item_category' => $service?->categories[0]->category->name,
                    'price' => $service->preco,
                    'currency' => 'BRL',
                    'quantity' => 1
                ]
        ],
        'item_list_name' => isset($service_gtag_type) ? ($service_gtag_type[0] ?? 'Serviços de Turismo Rural') : 'Serviços de Turismo Rural',
        'item_list_id' => isset($service_gtag_type) ? ($service_gtag_type[1] ?? 'servicos_tursismo_rural') : 'servicos_tursismo_rural'
        ])->toJson()}}
    ">
    <div>
        <div class="div-img"><a href="{{route('service', $service->service_slug)}}" class="click_select_item" title="{{$service->title ?? $service->service_title}}"><img class="img-produto" src="{{isset($service->images[0]) ? $service->images[0]->caminho : asset('site/imgs/logo.png')}}" alt="{{isset($service->images[0]) ? $service->images[0]->legenda : ''}}"></a></div>
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
        <div class="div-btn-detail d-sm-none"><a href="{{route('service', $service->service_slug)}}" class="click_select_item"><i class="fas fa-shopping-cart sv-cart" style="font-size: 1.2rem; color: #59981A;"></i></a></div>
        <div>
            <div class="text-center">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= number_format(starsService($service->id)['star_media'], 0, '', ''))
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
            <a class="link click_select_item" href="{{route('service', $service->service_slug)}}">Ver Mais</a>
        </div>
    </div>
</div>

<script>
    gtag('event', 'view_item_list', {
        items: [{
            item_id: `S_{{$service->id}}`,
            item_name: `{{$service->service_title}}`,
            item_list_name: `{{isset($service_gtag_type) ? ($service_gtag_type[0] ?? 'Serviços de Turismo Rural') : 'Serviços de Turismo Rural'}}`,
            item_list_id: `{{isset($service_gtag_type) ? ($service_gtag_type[1] ?? 'servicos_tursismo_rural') : 'servicos_tursismo_rural'}}`,
            item_brand: 'Biguaçu',
            item_category: `{{$service->categories[0]->category->name}}`,
            price: `{{$service->preco}}`,
            currency: 'BRL',
            quantity: 1
        }],
        item_list_name: `{{isset($service_gtag_type) ? ($service_gtag_type[0] ?? 'Serviços de Turismo Rural') : 'Serviços de Turismo Rural'}}`,
        item_list_id: `{{isset($service_gtag_type) ? ($service_gtag_type[1] ?? 'servicos_tursismo_rural') : 'servicos_tursismo_rural'}}`
    });
</script>