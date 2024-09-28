@extends('layouts.site')

@section('container')
    <form action="" method="post">
        @csrf
        <input type="hidden" name="affiliate_code" value="{{$affiliate}}">
        <input type="hidden" name="service_id" value="{{$service->id}}">
        <input type="hidden" name="service_title" value="{{$service->service_title}}">
        <input type="hidden" name="service_category" value="{{$service->categories[0]->category->name ?? ''}}">
        <input type="hidden" name="service_price" value="{{$service->preco}}">
        <input type="hidden" name="service_image" value="{{$service->images[0]->caminho ?? ''}}">
        <input type="hidden" name="seller_id" value="{{$service->seller_id}}">
        <input type="hidden" name="seller_name" value="{{$service->seller->name}}">
        <input type="hidden" name="hospedagem_controller" value="{{$service->hospedagem_controller}}">
        <input type="hidden" name="vaga_controller" value="{{$service->vaga_controller}}">
        <input type="hidden" name="vagas" value="{{$service->vaga}}">
        <input type="hidden" name="selecao_hospedagem" value="{{$service->selecao_hospedagem ?? null}}">
        <input type="hidden" name="qty_max_hospedagem" value="{{$service->qty_max_hospedagem ?? null}}">
        <input type="hidden" class="service_reservation" value="{{$service->serviceReservation->where('active', 'S')}}">
        @if ($service->hospedagem_controller == 1)
            <input type="hidden" name="diaria">
        @endif

        <input type="hidden" class="get-installments" value="{{collect(getTabelaGeral('regra_parcelamento','parcelas')->array_text)->toJson()}}">

        <input type="hidden" id="json_desconto_progressivo" value="{{$service->progressiveDiscount}}">
        <div class="container-fluid" style="background-color: #EDEDED;">
            <div class="container py-5">
                <div class="row produto-edit">
                    <div class="col-12 col-md-5 mb-3 mb-md-0">
                        <img class="img-fluid-edit" id="view_img" src="{{$service->images[0]->caminho ?? ''}}" alt="{{$service->images[0]->legenda ?? ''}}">
                    </div>
                    <div class="col-12 d-md-none mb-3 mb-md-0">
                        <div class="row">
                            @php $count_img = 0; @endphp
                            @foreach ($service->images as $image)
                                <div class="col-4"><img class="img-fluid click-img-view @if($count_img == 0) active-img @endif" src="{{$image->caminho}}" alt="{{$image->legenda}}"></div>
                                @php $count_img++; @endphp
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 col-md-7 dados-produto">
                        <div><h3 title="{{$service->title}}">{{$service->service_title}}</h3></div>
                        <div class="link-produtor"><a href="{{isset($service->seller->store->store_slug) ? route('seller.turismo.store',[$service->seller->store->store_slug]) : '#'}}">{{$service->seller->store->store_name ?? $service->seller->name}}</a></div>
                        <div class="avaliacao py-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= number_format(starsService($service->id)['star_media'], 0, '', ''))
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="fas fa-star"></i>
                                @endif
                            @endfor
                            <span>({{number_format(starsService($service->id)['star_media'], 2, '.', '')}})</span>
                        </div>

                        <div class="line"></div>

                        <div>
                            <p>{!!$service->short_description!!}</p>
                        </div>

                        <div>
                            @if ($service->vaga_controller == 'true')
                                @if ($service->vaga)
                                    <div>
                                        <p>Vagas Disponíveis: {{$service->vaga}}</p>
                                    </div>
                                @endif
                            @endif
                            
                            <div class="d-flex values alter-value">
                                <span class="value-2 apartir-value py-1">A partir de R$ {{number_format($service->preco, 2, ',', '.')}}</span>
                            </div>

                            <div class="row">
                                <div class="d-flex align-items-end">
                                    <div><button type="button" class="btn btn-falar-com-estabelecimento"><i class="fas fa-shopping-cart"></i> CLIQUE AQUI PARA FALAR COM O ESTABELICMENTO</button></div>
                                    <div class="ml-3"><a href="#" class="favorite" data-service_id="{{$service->id}}" style="position: relative; top: -5px;"><i class="fas fa-heart sv-cart" style="font-size: 1.8rem; color: #FF8300;"></i></a></div>
                                </div>
                            </div>
                            <div class="d-none d-md-block mt-3">
                                <div class="row">
                                    @php $count_img = 0; @endphp
                                    @foreach ($service->images as $image)
                                        <div class="col-2"><img class="img-fluid click-img-view @if($count_img == 0) active-img @endif" src="{{$image->caminho}}" alt="{{$image->legenda}}"></div>
                                        @php $count_img++; @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 pt-3 compartilhar">
                        COMPARTILHAR PRODUTO: <span class="d-md-none"><br></span>
                        <a target="_blank" href=" https://www.facebook.com/sharer/sharer.php?u={{route('service', $service->service_slug)}}"><i class="fab fa-facebook-f"></i></a>
                        <a target="_blank" href="https://twitter.com/intent/tweet?text={{route('service', $service->service_slug)}}"><i class="fab fa-twitter"></i></a>
                        <a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url={{route('service', $service->service_slug)}}"><i class="fab fa-linkedin"></i></a>
                        <a target="_blank" href="https://api.whatsapp.com/send?text={{route('service', $service->service_slug)}}&hl=pt-br"><i class="fab fa-whatsapp"></i></a>
                        <a class="copy-link" href="{{route('service', $service->service_slug)}}"><i class="fas fa-link"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="container mt-4 service_full_desc">
        {!! $service->full_description !!}
    </div>

    <div class="container my-3">
        <h2 class="d-none d-md-block">AVALIAÇÕES DO SERVIÇO</h2>
        <h5 class="d-md-none">AVALIAÇÕES DO SERVIÇO</h5>
        <div class="row" style="max-height: 300px; overflow-y: auto;">
            @foreach ($service->stars as $star)
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

    <div class="container">
        <div class="my-1 d-flex">
            <h3 class="border-bottom border-dark pb-2 d-none d-md-block">VEJA TAMBÉM</h3>
            <h6 class="border-bottom border-dark pb-2 d-md-none">VEJA TAMBÉM</h3>
        </div>

        <div class="row mb-1">
            @foreach ($servicesReferences as $service)
                @include('components.singleService', ['service' => $service, 'class' => 'col-md-3', 'service_gtag_type' => ['Serviços Proximos ao escolhido', 'servico_proximo_escolhido']])
            @endforeach
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            var whatsapp = `{!! $whatsapp !!}`;
            var text_contact = `{{  $text_contact  }}`;

            if('{{session()->has("error")}}'){
                Swal.fire({
                    icon: 'error',
                    title: '{{session()->get("error")}}'
                });
            }

            gtag('event', 'view_item', {
                currency: 'BRL',
                items: [{
                    item_id: `S_{{$service->id}}`,
                    item_name: `{{$service->service_title}}`,
                    item_brand: 'Biguaçu',
                    item_category: `{{$service->categories[0]->category->name ?? ''}}`,
                    price: `{{$service->preco}}`,
                    currency: 'BRL',
                    quantity: 1
                }],
                value: `{{$service->preco}}`,
            });

            $(window).on('click', function(e){
                if(!$(e.target).is('.div-hm')){
                    $('.div-hm-select').removeClass('open');
                }
            });
            $(document).on('click', '.div-hm', function(){
                $('.div-hm-select').removeClass('open');
                $(this).parent().find('.div-hm-select').addClass('open');
            });
            $(document).on('click', '.select-hm', function(){
                $(this).parent().parent().find('input').val($(this).text());
                $(this).parent().removeClass('open');
            });

            $(document).on('click', '.btn-falar-com-estabelecimento', function(e){
                var btn_text = $(this).html();
                var btn = $(this);

                var data_gtag = {
                    currency: 'BRL',
                    items: [{
                        item_id: `S_${$('[name="service_id"]').val()}`,
                        item_name: `${$('[name="service_name"]').val()}`,
                        item_brand: 'Biguaçu',
                        item_category: `${$('[name="service_category"]').val()}`,
                        price: `${parseFloat($('.get-price-calc').val())}`,
                        currency: 'BRL',
                        quantity: $('[name="quantidade"]').val()
                    }],
                    value: `${parseFloat($('.get-price-calc').val())}`,
                };

                // gtag('event', 'add_to_cart', data_gtag);

                window.open(`https://wa.me/55${whatsapp}?text=${text_contact}`, '_blank');
                // https://wa.me/551234567890?text=Olá%20amigo,%20estou%20interessado%20em%20seu%20produto.%20%0A%0AGostaria%20de%20mais%20informações.
            });
        });
    </script>
@endsection