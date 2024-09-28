@extends('layouts.site')

@section('container')
    <form action="{{route('addCart')}}" method="post" id="form_product">
        @csrf
        <input type="hidden" name="affiliate_code" value="{{$affiliate}}">
        <input type="hidden" name="product_id" value="{{$product->id}}">
        <input type="hidden" name="product_name" value="{{$product->nome}}">
        <input type="hidden" name="product_category" value="{{$product->categories[0]->category->name ?? ''}}">
        <input type="hidden" name="product_price" value="{{$product->preco}}">
        <input type="hidden" name="product_image" value="{{$product->images[0]->caminho}}">
        <input type="hidden" name="seller_id" value="{{$product->seller_id}}">
        <input type="hidden" name="seller_name" value="{{$product->seller->name}}">
        <input type="hidden" name="stock_controller" value="{{$product->stock_controller}}">

        <input type="hidden" name="product_weight" value="{{$product->weight}}">
        <input type="hidden" name="product_width" value="{{$product->width}}">
        <input type="hidden" name="product_height" value="{{$product->height}}">
        <input type="hidden" name="product_length" value="{{$product->length}}">

        <input type="hidden" class="get-installments" value="{{collect(getTabelaGeral('regra_parcelamento','parcelas')->array_text ?? [])->toJson()}}">

        <input type="hidden" id="json_desconto_progressivo" value="{{$product->progressiveDiscount}}">
        <div class="container-fluid">
            <div class="container-fluid pb-2 pt-3">
                <div class="row produto-edit">
                    <div class="col-12 mb-3">
                        Feito por Biguaçu > Produtos > {{ mb_convert_case($product->categories[0]->category->name, MB_CASE_TITLE) }} > {{$product->nome}}
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 d-flex flex-column align-items-center mb-3 mb-md-0">
                        <div class="produto-edit-img">
                            @php
                                $product_image = $product->images->where('principal', 1)->first() ?? $product->images->sortBy('position')->first();
                            @endphp
                            <img id="view_img" src="{{$product_image->caminho}}" title="{{$product_image->legenda}}" alt="{{$product_image->texto_alternativo}}">
                        </div>

                        <div class="mt-3">
                            <div class="d-flex">
                                @if ($product->images->where('principal', 1)->first())
                                    <div class="select-div-img click-img-view active-img"><img src="{{$product->images->where('principal', 1)->first()->caminho}}" title="{{$product->images->where('principal', 1)->first()->legenda}}" alt="{{$product->images->where('principal', 1)->first()->texto_alternativo}}"></div>
                                @endif
                                @foreach ($product->images->where('principal', 0)->sortBy('position')->take(3) as $image)
                                    <div class="select-div-img click-img-view @if(($product_image->id == $image->id)) active-img @endif"><img src="{{$image->caminho}}" title="{{$image->texto_alternativo}}" alt="{{$image->legenda}}"></div>
                                @endforeach
                                @if ($product->images->where('principal', 0)->count() > 3)
                                    <div class="click-div-img" id="open-gallery"><i class="fas fa-plus"></i>{{ $product->images->where('principal', 0)->count() - 3 }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4 dados-produto">
                        <div><h3 title="{{$product->title}}">{{$product->nome}}</h3></div>
                        <div class="link-produtor"><a href="{{isset($product->seller->store->store_slug) ? route('seller.store',[$product->seller->store->store_slug]) : '#'}}">{{$product->seller->store->store_name ?? $product->seller->name}} - Ver Loja</a></div>
                        <div class="avaliacao py-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= number_format(stars($product->id)['star_media'], 0, '', ''))
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="fas fa-star"></i>
                                @endif
                            @endfor
                            <span>({{number_format(stars($product->id)['star_media'], 2, '.', '')}})</span>
                        </div>

                        {{-- <div class="line"></div> --}}

                        <div>
                            <p>{!!$product->descricao_curta!!}</p>
                        </div>

                        <div>
                            @if ($product->stock_controller == 'true')
                                @if ($product->stock)
                                    <div style="font-size: 12px">
                                        Estoque Disponível: {{$product->stock ?? 0}}
                                    </div>
                                @endif
                            @endif

                            @if (($product->stock_controller == 'true' && $product->stock == 0 && $product->variations->count() == 0))
                                <div>
                                    <p style="font-size: 1.3rem; color: #3D550C;">PRODUTO INDISPONÍVEL</p>
                                    <div class="row {{auth()->guard('web')->check() ? 'd-none' : ''}}">
                                        <div class="col-12 col-sm-6 mb-2">
                                            <input type="text" class="form-control form-control-sm name-avise-me-qd" value="{{auth()->guard('web')->user()->name ?? ''}}" placeholder="Nome">
                                        </div>
                                    </div>
                                    <div class="row {{auth()->guard('web')->check() ? 'd-none' : ''}}">
                                        <div class="col-12 col-sm-6 mb-2">
                                            <input type="email" class="form-control form-control-sm email-avise-me-qd" value="{{auth()->guard('web')->user()->email ?? ''}}" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-8 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" id="newsletter-check-avise-me-qd" class="form-check-input" value="true" checked>
                                                <label class="form-check-label" for="newsletter-check-avise-me-qd">Me inscrever newsletter</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary btn-avise-me-qd">Avise-me quando disponivel!</button>
                                    </div>
                                </div>
                            @else
                                <div class="values alter-value">
                                    @if ($product->variations->count() > 0)
                                        <span class="value-2 apartir-value py-1">A partir de R$ {{number_format($product->preco, 2, ',', '.')}}</span>
                                    @else
                                        @if ($product->planPurchases->count() > 0)
                                            <span class="value-2 apartir-value py-1 w-100">Planos a partir de R$ {{number_format($product->preco, 2, ',', '.')}}</span>
                                        @else
                                            <div>
                                                <span class="value-2 py-1">R$ {{number_format($product->preco, 2, ',', '.')}}</span>
                                                <br>
                                                <span style="font-size: 12px;">(Preço por unidade)</span>
                                            </div>

                                            <input type="hidden" class="get-price-calc" value="{{$product->preco}}">
                                        @endif
                                    @endif
                                </div>

                                @if($product->progressiveDiscount->count() > 0)
                                    <div class="d-flex flex-column values mb-1">
                                        @foreach ($product->progressiveDiscount as $productDiscount)
                                            <span class="value-4 py-1 w-100" style="font-size: .8rem;line-height: .8;">Acima de <span class="value-3" style="font-size: .8rem;">{{$productDiscount->discount_quantity}}</span> unidades <span class="value-3" style="font-size: .8rem;">R$ {{number_format($productDiscount->discount_value, 2, ',', '.')}}</span></span>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($product->variations->count() > 0)
                                    <input type="hidden" class="variation_ids" value="{{json_encode($variation_ids)}}">
                                    @php
                                        $count_attr = 0;
                                    @endphp
                                    @foreach ($attributes as $attribute)
                                        @php
                                            $attr_var = verificar_attrs([$attribute->attribute->id, $variation_ids]);
                                            $count_attr++;
                                        @endphp
                                        <div class="row">
                                            <div class="form-group col-12 col-md-6">
                                                <label for="">{{$attribute->attribute->name}}</label>
                                                <select name="atributo_valor[]" class="form-control select-attributes" data-attr_id="{{$attribute->attribute->id}}">
                                                    <option value="">Selecione uma Opção</option>
                                                    @if ($count_attr == 1)
                                                        @if ($attr_var['all_options'])
                                                            @foreach ($attribute->attribute->variations as $values)
                                                                <option value="{{$values->id}}">{{$values->name}}</option>
                                                            @endforeach
                                                        @else
                                                            @foreach ($attr_var['options'] as $val_id)
                                                                @php
                                                                    $attr_val_var = $attribute->attribute->variations->filter(function ($query) use ($val_id){
                                                                        return $query->id == $val_id;
                                                                    })->first();
                                                                @endphp
                                                                @if (!empty($attr_val_var))
                                                                    <option value="{{$val_id}}">{{$attr_val_var->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @elseif ($count_attr > 1)
                                                        @if ($attr_var['all_options'])
                                                            @foreach ($attribute->attribute->variations as $values)
                                                                <option value="{{$values->id}}" @if($count_attr > 1) class="d-none0" @endif>{{$values->name}}</option>
                                                            @endforeach
                                                        @else
                                                            @foreach ($attr_var['options'] as $val_id)
                                                                @php
                                                                    $attr_val_var = $attribute->attribute->variations->filter(function ($query) use ($val_id){
                                                                        return $query->id == $val_id;
                                                                    })->first();
                                                                @endphp
                                                                @if (!empty($attr_val_var))
                                                                    <option value="{{$val_id}}" @if($count_attr > 1) class="d-none0" @endif>{{$attr_val_var->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        @foreach ($attribute->attribute->variations as $values)
                                            <input type="hidden" name="atributo[{{$values->id}}]" value="{{json_encode($values)}}">
                                        @endforeach
                                    @endforeach

                                    <div class="d-flex flex-wrap" id="Variations"></div>
                                @endif

                                @if ($product->planPurchases->count() > 0)
                                    <button type="button" class="btn btn-ver-plano w-25 mb-2" data-toggle="modal" data-target="#modalPlan"><i class="fas fa-credit-card"></i> VER PLANOS</button>
                                @else
                                    <div class="row">
                                        <div class="col-12 col-md-8">
                                            <div class="d-flex flex-column qty-btnPurchase mb-3 mt-4">
                                                <div class="field-qty-btnPurchase">
                                                    <span>Quantidade: </span>
                                                    <input type="text" class="form-control type-number" name="quantidade" value="1">
                                                </div>
        
                                                <div class="get-installments {{ $product->variations->count() > 0 ? 'd-none' : '' }}">
                                                    <div class="dropdown mt-3">
                                                        <button class="btn dropdown-toggle" type="button" id="getInstallmentsDropdown" data-toggle="dropdown" aria-expanded="false">
                                                            Ver Parcelas Disponíveis
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="getInstallmentsDropdown">
                                                            @foreach ((getTabelaGeral('regra_parcelamento','parcelas')->array_text ?? []) as $item)
                                                                @isset ($item['valor'])
                                                                    @if (str_replace(',','.',$item['valor']) <= $product->preco)
                                                                        <a class="dropdown-item">{{$item['parcela']}} x R$ {{number_format(($product->preco+(($product->preco*str_replace(',','.',$item['porcentage']))/100))/$item['parcela'], 2, ',', '.')}} = R$ {{number_format(($product->preco+(($product->preco*str_replace(',','.',$item['porcentage']))/100)), 2, ',', '.')}} {{str_replace(',','.',$item['porcentage']) > 0 ? 'Com Juros' : 'Sem Juros'}}</a>
                                                                    @endif
                                                                @endisset
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
        
                                                <div class="d-flex flex-column align-items-end mt-5">
                                                    @if ($product->status == 1)
                                                        <button type="button" class="btn btn-comprar-direct" >Comprar este produto</button>
                                                        <button type="button" class="btn btn-comprar-cart" >Adicionar no carrinho</button>
                                                    @endif
                                                </div>
        
                                                {{-- <div class="d-flex align-items-end">
                                                    @if ($product->status == 1)
                                                        <div><button type="button" class="btn btn-comprar" ><i class="fas fa-shopping-cart"></i> COMPRAR</button></div>
                                                    @endif
                                                    <div class="ml-3"><a href="#" class="favorite" data-product_id="{{$product->id}}" style="position: relative; top: -5px;"><i class="fas fa-heart sv-cart" style="font-size: 1.8rem; color: #FF8300;"></i></a></div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- <div class="values d-none div-price-calc">
                                    <span class="value-2 pb-1">TOTAL R$ <span class="price_calc"></span></span>
                                </div> --}}
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card-b fretes">
                            <div class="card-b-header">
                                <strong>Modalidade de entrega: </strong>
                            </div>
                            <div class="card-b-subtitle alter-value">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <div class="row">
                                            @if (auth('web')->check())
                                                <div class="col-12 col-md-8">
                                                    <select name="endereco_f" class="form-control">
                                                        <option value="new_address">Consultar novo cep</option>
                                                        @foreach ($addresses as $value)
                                                            <option value="{{$value->post_code}}" @if(session()->get('zip_code') == $value->post_code) selected @endif>{{$value->address}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="col-12 col-md-4 spinner-cep">
                                                <input type="text" name="zip_code_f" class="form-control @if(auth('web')->check()) d-none @endif _post_code @if(session()->has('zip_code')) zip-code-trigger @endif" value="@if(session()->has('zip_code')){{session()->get('zip_code')}}@endif" placeholder="00000-000">
                                                <div class="spinner-grow spinner-cep-div d-none" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <div>{{$product->nome}}</div>
                                            <div>
                                                <span class="c-qty">1</span>
                                                <span>X</span>
                                                <span class="value-2">R$ {{ number_format(($product->progressiveDiscount->count() == 0 ? $product->preco : 0), 2, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-b-body">
                                <div class="fretes"></div>
                            </div>
                            <div class="card-b-footer alter-value">
                                <div class="d-flex justify-content-between">
                                    <div>Sub total (sem frete)</div>
                                    <div class="c-value-total value-2 price_calc">R$ {{ number_format(($product->progressiveDiscount->count() == 0 ? $product->preco : 0), 2, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 pt-3 compartilhar">
                        COMPARTILHAR PRODUTO: <span class="d-md-none"><br></span>
                        <a target="_blank" href=" https://www.facebook.com/sharer/sharer.php?u={{route('product', $product->slug)}}"><i class="fab fa-facebook-f"></i></a>
                        <a target="_blank" href="https://twitter.com/intent/tweet?text={{route('product', $product->slug)}}"><i class="fab fa-twitter"></i></a>
                        <a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url={{route('product', $product->slug)}}"><i class="fab fa-linkedin"></i></a>
                        <a target="_blank" href="https://api.whatsapp.com/send?text={{route('product', $product->slug)}}&hl=pt-br"><i class="fab fa-whatsapp"></i></a>
                        <a class="copy-link" href="{{route('product', $product->slug)}}"><i class="fas fa-link"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="container-fluid mt-4 product_full_desc">
        <div class="container-fluid">
            <div class="border my-3"></div>
            <div class="mb-3"><h3>Descrição</h3></div>
            {!! $product->descricao_completa !!}
        </div>
    </div>

    <div class="container-fluid mt-4 mb-3">
        <div class="container-fluid">
            <h2 class="d-none d-md-block">AVALIAÇÕES DO PRODUTO</h2>
            <h5 class="d-md-none">AVALIAÇÕES DO PRODUTO</h5>
            <div class="row" style="max-height: 300px; overflow-y: auto;">
                @foreach ($product->stars as $star)
                    @if (date('Y-m-d', strtotime($star->created_at)) > date('Y-m-d', strtotime('-1 Years')))
                        @if ($star->status == 1)
                            <div class="col-12 col-md-6">
                                <div class="border rounded px-2 my-2">
                                    <p>
                                        <b>{{$star->user->name}}</b> <i>{{date('d-m-Y', strtotime($star->created_at))}}</i>
                                        <br>
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $star->star)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="fas fa-star"></i>
                                            @endif
                                        @endfor
                                        <span>({{$star->star}})</span>
                                        <br>
                                        {{$star->comment}}
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="container-fluid">
            <div class="my-1 d-flex">
                <h3 class="border-bottom border-dark pb-2 d-none d-md-block">VEJA TAMBÉM</h3>
                <h6 class="border-bottom border-dark pb-2 d-md-none">VEJA TAMBÉM</h3>
            </div>

            <div class="row justify-content-center mb-1">
                @foreach ($produtosNovos as $produto_novo)
                @include('components.singleProduct', ['produto' => $produto_novo, 'class' => 'col-md-2', 'product_gtag_type' => 'Produtos do mesmo Vendedor'])
                    {{-- <div class="col-6 col-md-3 d-md-flex flex-column produtos">
                        <input type="hidden" class="gtag_select_item" value="
                            {{collect([
                                'items' => [
                                    [
                                        'item_id' => 'P_'.$produto_novo->id,
                                        'item_name' => $produto_novo->nome,
                                        'item_list_name' => 'Produtos do mesmo Vendedor',
                                        'item_list_id' => 'produto_mesmo_vendedor',
                                        'item_brand' => 'Biguaçu',
                                        'item_category' => $produto_novo->categories[0]->category->name ?? '',
                                        'price' => $produto_novo->preco,
                                        'currency' => 'BRL',
                                        'quantity' => 1
                                    ]
                            ],
                            'item_list_name' => 'Produtos do mesmo Vendedor',
                            'item_list_id' => 'produto_mesmo_vendedor'
                            ])->toJson()}}
                        ">
                        <div>
                            <div class="div-img"><a href="{{route('product', $produto_novo->slug)}}" class="click_select_item" title="{{$produto_novo->title ?? $produto_novo->nome}}"><img class="img-produto" src="{{isset($produto_novo->images[0]) ? $produto_novo->images[0]->caminho : asset('site/imgs/icone-logo.png')}}" alt="{{isset($produto_novo->images[0]) ? $produto_novo->images[0]->legenda : ''}}"></a></div>
                            @php
                                $count_category = 0;
                                $categories = '';
                            @endphp
                            @foreach ($produto_novo->categories as $category)
                                @php
                                    $count_category++;
                                    if(isset($category->category->name)){
                                        $categories .= $category->category->name.($count_category < $produto_novo->categories->count() ? ' / ' : '');
                                    }
                                @endphp
                            @endforeach
                            <div class="pl-2 pt-2 py-1 t-categoria text-truncate" title="{{$categories}}" data-toggle="tooltip">{{$categories}}</div>
                            <div class="pl-2 short-description">
                                <p class="mb-0 mb-md-1 text-truncate-c" title="{{$produto_novo->title ?? $produto_novo->nome}}">{{\Str::upper($produto_novo->nome)}}</p>
                            </div>
                        </div>
                        <div class="mt-md-auto star-rate">
                            <div class="div-btn-detail d-sm-none"><a href="{{route('product', $produto_novo->slug)}}" class="click_select_item"><i class="fas fa-shopping-cart sv-cart" style="font-size: 1.2rem; color: #59981A;"></i></a></div>
                            <div>
                                <div class="text-center">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= number_format(stars($produto_novo->id)['star_media'], 0, '', ''))
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="fas fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="text-center d-flex flex-column values">
                                    @if ($produto_novo->variations->count() > 0)
                                        @php
                                            $precos = [];
                                            foreach($produto_novo->variations as $variation){
                                                $precos[] = $variation->preco;
                                            }
                                        @endphp
                                        <span class="value-2 py-1">A partir de R$ {{number_format(min($precos), 2, ',', '.')}}</span>
                                    @else
                                        <span class="value-2 py-1">R$ {{number_format($produto_novo->preco, 2, ',', '.')}}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="div-btn-favorite d-sm-none"><a href="#" class="favorite" data-product_id="{{$produto_novo->id}}"><i class="fas fa-heart sv-cart" style="font-size: 1.2rem; color: #FF8300;"></i></a></div>
                            <div class="pl-2 div-btn my-2 d-none d-sm-block">
                                <a class="link click_select_item {{$produto_novo->planPurchases->count() > 0 ? 'btn-plano' : ''}}" href="{{route('product', $produto_novo->slug)}}">{{$produto_novo->planPurchases->count() > 0 ? 'VER PLANOS' : 'COMPRAR'}}</a>
                            </div>
                        </div>
                    </div> --}}
    
                    {{-- <script>
                        gtag('event', 'view_item_list', {
                            items: [{
                                item_id: `P_{{$produto_novo->id}}`,
                                item_name: `{{$produto_novo->nome}}`,
                                item_list_name: 'Produtos do mesmo Vendedor',
                                item_list_id: 'produto_mesmo_vendedor',
                                item_brand: 'Biguaçu',
                                item_category: `{{$produto_novo->categories[0]->category->name ?? ''}}`,
                                price: `{{$produto_novo->preco}}`,
                                currency: 'BRL',
                                quantity: 1
                            }],
                            item_list_name: 'Produtos do mesmo Vendedor',
                            item_list_id: 'produto_mesmo_vendedor'
                        });
                    </script> --}}
                @endforeach
            </div>
        </div>
    </div>

    <!-- Container da galeria -->
    <div class="gallery-container">
        <span class="close-gallery">&times;</span>
        <div class="gallery">
            @foreach ($product->images->sortBy('position')->take(3) as $image)
                <img src="{{$image->caminho}}" alt="{{$image->texto_alternativo}}" class="gallery-image">
            @endforeach
        </div>
        <button class="prev-gallery">&#10094;</button>
        <button class="next-gallery">&#10095;</button>
    </div>

    <div class="modal fade" id="modalPlan" data-keyboard="false" tabindex="-1" aria-labelledby="modalPlanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title" id="modalPlanLabel">
                        <h5>Selecione o melhor plano que te atende!</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        @foreach ($product->planPurchases as $plan)
                            <div class="col-12 col-sm-5 mb-1">
                                <div class="card">
                                    <div class="card-body">
                                        <div><h3>{{$plan->plan_title}}</h3></div>
                                        <div class="mb-1">{!!planCobranca($plan->select_interval)!!}</div>
                                        <div class="mb-1">Duração do Plano: {{$plan->duration_plan}} Mês(ses)</div>
                                        <div class="mb-1">Valor do Plano: R$ {{number_format($plan->plan_value, 2, ',', '')}} + Frete</div>
                                        <div class="mb-1">A entrega é feita {{$plan->select_entrega}}</div>
                                        <div class="my-2">{{$plan->descption_plan}}</div>
                                        <div class="mt-2 text-center">
                                            <button type="button" class="btn btn-c-orange btn-purchase-plan" data-plan_id="{{$plan->id}}">Contratar Plano</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            // $('.zip-code-trigger').trigger('blur');

            gtag('event', 'view_item', {
                currency: 'BRL',
                items: [{
                    item_id: `P_{{$product->id}}`,
                    item_name: `{{$product->nome}}`,
                    item_brand: 'Biguaçu',
                    item_category: `{{$product->categories[0]->category->name ?? ''}}`,
                    price: `{{$product->preco}}`,
                    currency: 'BRL',
                    quantity: 1
                }],
                value: `{{$product->preco}}`,
            });

            if('{{session()->has("error")}}'){
                Swal.fire({
                    icon: 'error',
                    title: '{{session()->get("error")}}'
                });
            }

            $(document).on('click', '.btn-purchase-plan', function(){
                var form_geral = $('#form_product').serialize();
                form_geral = form_geral+'&plan_id='+$(this).data('plan_id');
                var btn_text = $(this).html();
                $(this).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>').prop('disabled', true);

                if($('.select-attributes').length > 0){
                    var isValid = true;
                    $('.select-attributes').each(function() {
                        if($(this).val() == '')  isValid = false;
                    });

                    if(isValid){
                        formPlanAjax(form_geral);
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'É necessário que todos atributos estejam selecionados!'
                        });
                        $(this).html(btn_text).prop('disabled', false);
                    }
                }else{
                    formPlanAjax(form_geral);
                }
            });

            $(document).on('click', '.btn-avise-me-qd', function(){
                var data = {
                    'id': $('[name="product_id"]').val()+"-P",
                    'name': $('.name-avise-me-qd').val(),
                    'email': $('.email-avise-me-qd').val(),
                    'newsletter': $('#newsletter-check-avise-me-qd').prop('checked'),
                };

                Swal.fire({
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `{{route('aviseMeRegister')}}`,
                    type: 'POST',
                    data: data,
                    success: (data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registrado, quando o produto estiver disponível avisaremos você por e-mail.'
                        });
                    }
                });
            });

            $('[name="zip_code_f"]').mask('00000-000');

            $('[name="zip_code_f"]').on('keyup', function () {
                if ($(this).val().length == 9) {
                    var data_frete_calc = {
                        'cep_consulta': $(this).val(),
                        'quantidade': $('[name="quantidade"]').val(),
                        'product_id': $('[name="product_id"]').val(),
                        'variacoes': JSON.parse($('[name^="variacao"]').val() || '[]'),
                    };

                    $('.spinner-cep-div').removeClass('d-none');
                    $.ajax({
                        url: '/freteCheckoutProduto',
                        type: 'POST',
                        data: data_frete_calc,
                        success: (data) => {
                            console.log(data);
                            var transportadoras = data.fretes_seller.transportadoras;
                            var transporte_proprio = data.fretes_seller.transporte_proprio;
                            var locais_retirada = data.fretes_seller.locais_retirada;

                            $('.card-b-body .fretes').empty();
                            var div = '';
                            div = '<div class="row frete pb-3">';

                            div += '<div class="col-12">';
                            for (var i = 0; i < transportadoras.length; i++) {
                                if (!transportadoras[i].error) {
                                    div += `
                                        <div style="margin-bottom: 12px;box-shadow: 1px 6px 8px 1px #c5c5c552;padding: 4px 10px;border-radius: .5rem;" class="d-flex justify-content-between align-items-center">
                                            <div style="width: 30%">
                                                <label class="m-0" style="font-size:.9rem;">${transportadoras[i].name}</label>
                                            </div>
                                            <div>
                                                Entrega até ${(transportadoras[i].custom_delivery_time + 1)} Dias úteis.
                                            </div>
                                            <div>R$ ${parseFloat(transportadoras[i].custom_price).toFixed(2).replace('.', ',')}</div>
                                        </div>
                                    `;
                                }
                            }
                            div += '</div>';

                            // Tranporte proprio
                            div += '<div class="col-12">';
                            for (var i = 0; i < transporte_proprio.length; i++) {
                                var valor_entrega = transporte_proprio[i].valor_entrega;
                                if (transporte_proprio[i].frete_gratis == 1) {
                                    if (sub_total > transporte_proprio[i].valor_minimo) valor_entrega = 0;
                                }
                                var entrega = 'Entrega Até ' + (transporte_proprio[i].tempo_entrega) + (transporte_proprio[i].tempo == 'H' ? ' Horas' : ' Dias Uteis') + '.';
                                if (transporte_proprio[i].tempo == 'S') entrega = 'Entrega nos dias da semana <br>' + (diaSemana(transporte_proprio[i].semana)).join(' - ');
                                if (transporte_proprio[i].tempo == 'C') entrega = '';

                                div += `
                                    <div style="margin-bottom: 12px;box-shadow: 1px 6px 8px 1px #c5c5c552;padding: 4px 10px;border-radius: .5rem;" class="d-flex justify-content-between align-items-center">
                                        <div style="width: 30%">
                                            <label class="m-0" style="font-size:.9rem;">Transporte Próprio</label>
                                            <span>${(transporte_proprio[i].descricao ? 'Obs: ' + transporte_proprio[i].descricao : '')}</span>
                                        </div>
                                        <div>
                                            ${entrega}
                                        </div>
                                        <div>${((valor_entrega > 0 ? 'R$ ' : '') + (valor_entrega > 0 ? parseFloat(valor_entrega).toFixed(2).replace('.', ',') : 'Frete Grátis'))}</div>
                                    </div>
                                `;
                            }
                            div += '</div>';

                            div += '<div class="col-12 my-2"><label>Locais de retirada:</label></div>';

                            // Tranporte proprio
                            div += '<div class="col-12">';
                            for (var i = 0; i < locais_retirada.length; i++) {
                                div += `
                                    <div style="margin-bottom: 12px;box-shadow: 1px 6px 8px 1px #c5c5c552;padding: 4px 10px;border-radius: .5rem;" class="d-flex justify-content-between align-items-center">
                                        <div style="width: 40%">
                                            <label class="m-0" style="font-size:.9rem;">${locais_retirada[i].localidade.title}</label>
                                            <span>${locais_retirada[i].localidade.description || ''}</span>
                                        </div>
                                        <div>
                                            ${locais_retirada[i].localidade.address}, ${locais_retirada[i].localidade.number} - ${locais_retirada[i].localidade.district} - ${locais_retirada[i].localidade.city}/${locais_retirada[i].localidade.state}
                                        </div>
                                    </div>
                                `;
                            }
                            div += '</div>';

                            div += '</div>';

                            $('.card-b-body .fretes').append(div);

                            $('.spinner-cep-div').addClass('d-none');
                        },
                        error: (err) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'insira o cep correto do seu endereço'
                            });

                            $('.btn-frete').removeClass('d-none');

                            $('.spinner-cep-div').addClass('d-none');
                        }
                    });
                }
            });

            $('[name="endereco_f"]').on('change', function(){
                if($(this).val() == 'new_address'){
                    $('.spinner-cep').removeClass('d-none');
                    $('.spinner-cep-div').addClass('d-none');
                    $('[name="zip_code_f"]').val('').removeClass('d-none');
                }else{
                    $('.spinner-cep').addClass('d-none');
                    $('.spinner-cep-div').removeClass('d-none');
                    $('[name="zip_code_f"]').val($(this).val());
                    $('[name="zip_code_f"]').trigger('keyup');
                }
            });

            setTimeout(() => {
                $('[name="endereco_f"]').trigger('change');
            }, 200);

            let currentIndex = 0;
            const images = $('.gallery-image');
            const totalImages = images.length;

            // Função para mostrar a imagem atual
            function showImage(index) {
                images.removeClass('active');
                images.eq(index).addClass('active');
            }

            // Iniciar a galeria na primeira imagem
            showImage(currentIndex);

            // Navegação para a próxima imagem
            $('.next-gallery').click(function() {
                currentIndex = (currentIndex + 1) % totalImages;
                showImage(currentIndex);
            });

            // Navegação para a imagem anterior
            $('.prev-gallery').click(function() {
                currentIndex = (currentIndex - 1 + totalImages) % totalImages;
                showImage(currentIndex);
            });

            // Mostrar a galeria ao clicar no botão
            $('#open-gallery').click(function() {
                $('body').addClass('no-scroll'); // Bloqueia a rolagem do body
                $('.gallery-container').show();
            });

            // Fechar a galeria
            $('.close-gallery').click(function() {
                $('.gallery-container').hide();
                $('body').removeClass('no-scroll'); // Libera a rolagem do body
            });

            // if(`{{session()->has('success_alert')}}`){
            //     Toast.fire({
            //         icon: 'success',
            //         title: `{{session()->get('success_alert')}}`
            //     });
            // }

            $(document).on('click', '.btn-comprar-direct', function(){
                $('#form_product').append(`<input type="hidden" name="criar_sessao_cart" value="S">`);
                $('#form_product').submit();
                // window.location.href = '{{ route("checkout.modalidade") }}';
            });
        });

        function formPlanAjax(form){
            $.ajax({
                url: `{{route('cartSessionPlan')}}`,
                type: 'POST',
                data: form,
                success: (data) => {
                    window.location.href = `{{route('checkoutSessionPlan')}}`;
                }
            });
        }
    </script>
@endsection