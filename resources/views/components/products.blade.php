@foreach ($produtos as $produto)
    <div class="col-6 @if(isset($class_css)) {{$class_css}} @else col-md-3 @endif d-md-flex flex-column produtos">
        <div>
            <div class="div-img">
                <a href="{{route('product', $produto->slug)}}" class="click_select_item" title="{{$produto->title ?? $produto->nome}}">
                    @if (!empty($produto->images->where('principal', 1)->first()))
                        <img class="img-produto" src="{{$produto->images->where('principal', 1)->first()->caminho}}" title="{{$produto->images->where('principal', 1)->first()->legenda}}" alt="{{$produto->images->where('principal', 1)->first()->texto_alternativo}}">
                    @else
                        <img class="img-produto" src="{{$produto->images->sortBy('position')->first()->caminho}}" title="{{$produto->images->sortBy('position')->first()->legenda}}" alt="{{$produto->images->sortBy('position')->first()->texto_alternativo}}">
                    @endif
                </a>
            </div>
            @php
                $count_category = 0;
                $categories = '';
            @endphp
            @foreach ($produto->categories as $category)
                @php
                    $count_category++;
                    if(isset($category->category->name)){
                        $categories .= $category->category->name.($count_category < $produto->categories->count() ? ' / ' : '');
                    }
                @endphp
            @endforeach
            <div class="pl-2 pt-2 py-1 t-categoria text-truncate" title="{{$categories}}" data-toggle="tooltip">{{$categories}}</div>
            <div class="pl-2 short-description">
                <p class="mb-0 mb-md-1 text-truncate-c">{{\Str::upper($produto->nome)}}</p>
            </div>
        </div>
        <div class="mt-md-auto star-rate">
            <div class="div-btn-detail d-sm-none"><a href="{{route('product', $produto->slug)}}"><i class="fas fa-shopping-cart sv-cart" style="font-size: 1.2rem; color: #59981A;"></i></a></div>
            <div>
                <div class="text-center">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= number_format(stars($produto->id)['star_media'], 0, '', ''))
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="fas fa-star"></i>
                        @endif
                    @endfor
                </div>
                <div class="text-center d-flex flex-column values">
                    @if ($produto->variations->count() > 0)
                        @php
                            $precos = [];
                            foreach($produto->variations as $variation){
                                $precos[] = $variation->preco;
                            }
                        @endphp
                        {{-- <span class="value-1 pt-1">R$ 29,00</span> --}}
                        <span class="value-2 py-1">A partir de R$ {{number_format(min($precos), 2, ',', '.')}}</span>
                    @else
                        {{-- <span class="value-1 pt-1">R$ 29,00</span> --}}
                        <span class="value-2 py-1">R$ {{number_format($produto->preco, 2, ',', '.')}}</span>
                    @endif
                </div>
            </div>
            <div class="div-btn-favorite d-sm-none"><a href="#"><i class="fas fa-heart sv-cart" style="font-size: 1.2rem; color: #cb5813;"></i></a></div>
            <div class="pl-2 div-btn my-2 d-none d-sm-block">
                <a class="link" href="{{route('product', $produto->slug)}}">COMPRAR</a>
            </div>
        </div>
    </div>
@endforeach