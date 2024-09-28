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
                                @if ($service->variations->count() > 0)
                                    <span class="value-2 apartir-value py-1">A partir de R$ {{number_format($service->preco, 2, ',', '.')}}</span>
                                @else
                                    <span class="value-2 py-1 ">R$ {{number_format($service->preco, 2, ',', '.')}}</span>

                                    <div class="get-installments ml-2">
                                        <div class="dropdown mt-2">
                                            <button class="btn dropdown-toggle" type="button" id="getInstallmentsDropdown" data-toggle="dropdown" aria-expanded="false">
                                                Ver Parcelas Disponíveis
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="getInstallmentsDropdown">
                                                @foreach (getTabelaGeral('regra_parcelamento','parcelas')->array_text as $item)
                                                    @isset ($item['valor'])
                                                        @if (str_replace(',','.',$item['valor']) <= $service->preco)
                                                            <a class="dropdown-item">{{$item['parcela']}} x R$ {{number_format(($service->preco+(($service->preco*str_replace(',','.',$item['porcentage']))/100))/$item['parcela'], 2, ',', '.')}} = R$ {{number_format(($service->preco+(($service->preco*str_replace(',','.',$item['porcentage']))/100)), 2, ',', '.')}} {{str_replace(',','.',$item['porcentage']) > 0 ? 'Com Juros' : 'Sem Juros'}}</a>
                                                        @endif
                                                    @endisset
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" class="get-price-calc" value="{{$service->preco}}">
                                @endif
                            </div>

                            @if($service->progressiveDiscount->count() > 0)
                                <div class="d-flex flex-column values mb-2">
                                    @foreach ($service->progressiveDiscount as $serviceDiscount)
                                        <span class="value-4 py-1 w-100" style="font-size: .8rem;line-height: .8;">Acima de <span class="value-3">{{$serviceDiscount->discount_quantity}}</span> vagas <span class="value-3">R$ {{number_format($serviceDiscount->discount_value, 2, ',', '.')}}</span></span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($service->variations->count() > 0)
                                <input type="hidden" class="variation_ids" value="{{json_encode($variation_ids)}}">
                                @php
                                    $count_attr = 0;
                                @endphp
                                @foreach ($attributes as $attribute)
                                    @php
                                        $attr_var = verificar_attrs_service([$attribute->attribute->id, $variation_ids]);
                                        $count_attr++;
                                    @endphp
                                    <div class="row">
                                        <div class="form-group col-12 col-md-6">
                                            <label for="">{{$attribute->attribute->name}}</label>
                                            <select name="atributo_valor[]" class="form-control select-service-attributes" data-attr_id="{{$attribute->attribute->id}}">
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

                                <div class="d-flex" id="Variations"></div>
                            @endif
                            <div class="row @if($service->variations->count() > 0) d-none @endif" id="calendar">
                                <div class="col-8 col-sm-6">
                                    <label for="">Selecionar Data @if($service->hospedagem_controller == 1) de Inicio & Fim @endif</label>
                                    <div class="row calendar-custom">
                                        <div class="col-12 col-sm-5 d-flex align-items-end @if($service->hospedagem_controller == 1) border-right border-dark @endif">
                                            <input type="text" class="form-control date-calendar d-none" name="calendar_ini" data-calendar="{{collect($service->calendars)->toJson()}}">
                                            <span class="mx-2 span-calendar">__/__/____</span>
                                        </div>
                                        @if ($service->hospedagem_controller == 1)
                                            <div class="col-12 col-sm-5 d-flex align-items-end border-left border-dark">
                                                <input type="text" class="form-control date-calendar-verif d-none" disabled name="calendar_fim" data-calendar="{{collect($service->calendars)->toJson()}}">
                                                <span class="mx-2 span-calendar">__/__/____</span>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- <div class="input-group">
                                        @if ($service->hospedagem_controller == 1)
                                            <input type="text" class="form-control date-calendar-verif" disabled name="calendar_fim" data-calendar="{{collect($service->calendars)->toJson()}}">
                                        @endif
                                    </div> --}}
                                </div>
                                <div class="col-4 col-sm-4 reserva-hora d-none">
                                    <label for="">Selecionar Hora</label>
                                    <select name="hours" class="form-control"></select>
                                </div>
                            </div>
                            <div class="row d-none" id="calendar-var"></div>
                            <div class="row @if($service->variations->count() == 0) d-none @endif" id="calendar-var-verif" style="cursor: pointer;">
                                <div class="col-12 col-sm-5 d-flex align-items-end @if($service->hospedagem_controller == 1) border-right border-dark @endif">
                                    <i class="far fa-calendar-alt" style="font-size: 1.6rem;"></i>
                                    <span class="mx-2">__/__/____</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    {{-- <label for="" style="font-size: .8rem;">
                                        Quantidade
                                        @if ($service->hospedagem_controller == 1)
                                            de <span class="text-capitalize">{{$service->selecao_hospedagem}}</span> 
                                            @if ($service->selecao_hospedagem !== 'quartos')
                                                <span style="font-size: .7rem;">(max: {{$service->qty_max_hospedagem}})</span>
                                            @else
                                                <span style="font-size: .7rem;">(max: {{$service->vaga}})</span>
                                            @endif
                                        @else
                                            de Vagas
                                        @endif
                                    </label> --}}
                                    <input type="text" class="form-control text-center type-number" name="quantidade" value="1">
                                </div>
                                <div class="d-flex align-items-end">
                                    @if ($service->status == 1)
                                        <div><button type="button" class="btn btn-comprar-servico"><i class="fas fa-shopping-cart"></i> COMPRAR</button></div>
                                    @endif
                                    <div class="ml-3"><a href="#" class="favorite" data-service_id="{{$service->id}}" style="position: relative; top: -5px;"><i class="fas fa-heart sv-cart" style="font-size: 1.8rem; color: #cb5813;"></i></a></div>
                                </div>
                            </div>
                            <div class="values d-none div-price-calc">
                                <span class="value-2 pb-1">TOTAL R$ <span class="price_calc"></span></span>
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
        <h2 class="d-none d-md-block">AVALIAÇÕES DO PRODUTO</h2>
        <h5 class="d-md-none">AVALIAÇÕES DO PRODUTO</h5>
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

            $(document).on('click', '.span-calendar', function(){$(this).parent().find('.ui-datepicker-trigger').trigger('click');});
            $(document).on('click', '#calendar-var-verif', function(){
                var selecao = 0;
                var attributes_value = [];
                $('.select-service-attributes').each(function(){
                    if($(this).val() !== '') {
                        selecao++;
                        attributes_value.push($(this).val());
                    }
                });

                if(selecao !== $('.select-service-attributes').length){
                    Swal.fire({
                        icon: 'warning',
                        title: 'É necessário que todos atributos estejam selecionados!'
                    });
                }
            })

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

            calendar_start('.date-calendar');

            $(document).on('change', '.select-service-attributes', function(){
                $('#Variations').empty();
                var service_id = $('[name="service_id"]').val();
                var vaga_controller = $('[name="vaga_controller"]').val();

                var selecao = 0;
                var attributes_value = [];
                $('.select-service-attributes').each(function(){
                    if($(this).val() !== '') {
                        selecao++;
                        attributes_value.push($(this).val());
                    }
                });

                if(selecao == $('.select-service-attributes').length){
                    $.ajax({
                        url: '/geral/select-attrs-service-variations',
                        type: 'POST',
                        data: {attributes_value, service_id},
                        success: (data) => {
                            // console.log(data);
                            $('#calendar-var-verif').addClass('d-none');
                            $('#json_desconto_progressivo').val(JSON.stringify(data.progressive_discount));

                            $('.apartir-value').addClass('d-none');

                            var estoque = '';
                            if(parseInt(vaga_controller)) {
                                $('[name="vagas"]').val(data.vaga);
                                estoque = '<p>Vagas Disponíveis: '+data.vaga+'</p>';
                            }

                            var variation = {
                                'var_id': data.id,
                                'preco': data.preco,
                                'vaga': data.vaga,
                                'relation': attributes_value
                            };

                            $('#Variations').html(
                                '<div class="values alter-value" id="variacao-'+attributes_value.join('-')+'">'+
                                    estoque+
                                    '<span class="value-2 py-1">R$ '+data.preco.toFixed(2).replace('.',',')+'</span>'+

                                    '<input type="hidden" name="variacao['+attributes_value.join('-')+']" value=\''+JSON.stringify(variation)+'\'>'+
                                    '<input type="hidden" class="get-price-calc" value="'+data.preco+'">'+
                                '</div>'+
                                `<div class="get-installments mt-auto">
                                    <div class="dropdown mt-2">
                                        <button class="btn dropdown-toggle" type="button" id="getInstallmentsDropdown" data-toggle="dropdown" aria-expanded="false">
                                            Ver Parcelas Disponíveis
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="getInstallmentsDropdown"></div>
                                    </div>
                                </div>`
                            );

                            $('[name="quantidade"]').trigger('keyup');

                            $('#calendar-var').empty();
                            if(data.calendars.length > 0){
                                var date_verif = data.hospedagem_controller == 1 ? `
                                    <div class="col-12 col-sm-5 d-flex align-items-end border-left border-dark">
                                        <input type="text" class="form-control date-calendar-verif d-none" disabled name="calendar_var_fim" data-calendar="${JSON.stringify(data.calendars)}">
                                        <span class="mx-2 span-calendar">__/__/____</span>
                                    </div>
                                ` : '';
                                $('#calendar-var').append(`
                                    <div class="col-6">
                                        <label for="">Selecionar Data</label>
                                        <div class="row calendar-custom">
                                            <div class="col-12 col-sm-5 d-flex align-items-end @if($service->hospedagem_controller == 1) border-right border-dark @endif">
                                                <input type="text" class="form-control date-calendar d-none" name="calendar_var_ini" data-calendar="${JSON.stringify(data.calendars)}">
                                                <span class="mx-2 span-calendar">__/__/____</span>
                                            </div>
                                            ${date_verif}
                                        </div>
                                    </div>
                                    <div class="col-4 col-sm-4 reserva-hora d-none">
                                        <label for="">Selecionar Hora</label>
                                        <select name="hours" class="form-control"></select>
                                    </div>
                                `);
                                $('#calendar').addClass('d-none');
                                $('#calendar-var').removeClass('d-none');
                            }else{
                                $('#calendar').removeClass('d-none');
                                $('#calendar-var').addClass('d-none');
                            }
                            calendar_start('.date-calendar');
                        }
                    });
                }

                if($(this).val() == ''){
                    $('.apartir-value').removeClass('d-none');
                    $('#calendar').addClass('d-none');
                    $('#calendar-var').empty();
                    $('#calendar-var-verif').removeClass('d-none');
                }
            });

            $(document).on('click', '.btn-comprar-servico', function(e){
                var btn_text = $(this).html();
                var btn = $(this);
                $(this).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>').prop('disabled', true);

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

                if($('.select-service-attributes').length > 0){
                    var isValid = true;
                    $('.select-service-attributes').each(function() {
                        if($(this).val() == '')  isValid = false;
                    });

                    if(isValid){
                        gtag('event', 'add_to_cart', data_gtag);
                        // $(this).closest('form').submit();
                        $.ajax({
                            url: `{{route('service.session')}}`,
                            type: 'POST',
                            data: $(this).closest('form').serialize(),
                            success: (data) => {
                                // console.log(data);
                                window.location.href = `{{route('checkout.service')}}`;
                            },
                            error: (err) => {
                                btn.html(btn_text).prop('disabled', false);
                                var err = err.responseJSON;
                            if(err.erro_custom){
                                Swal.fire({
                                    icon: err.icon,
                                    title: err.msg,
                                });
                            }
                            }
                        });
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'É necessário que todos atributos estejam selecionados!'
                        });
                        $(this).html(btn_text).prop('disabled', false);
                    }
                }else{
                    gtag('event', 'add_to_cart', data_gtag);
                    // $(this).closest('form').submit();
                    $.ajax({
                        url: `{{route('service.session')}}`,
                        type: 'POST',
                        data: $(this).closest('form').serialize(),
                        success: (data) => {
                            // console.log(data);
                            window.location.href = `{{route('checkout.service')}}`;
                        },
                        error: (err) => {
                            btn.html(btn_text).prop('disabled', false);
                            var err = err.responseJSON;
                            if(err.erro_custom){
                                Swal.fire({
                                    icon: err.icon,
                                    title: err.msg,
                                });
                            }
                        }
                    });
                }
            });

            let semana_dia = {
                0: 'domingo',
                1: 'segunda',
                2: 'terca',
                3: 'quarta',
                4: 'quinta',
                5: 'sexta',
                6: 'sabado',
            };
            var select_date_fim = false;
            $('.date-calendar-verif').datepicker({
                showOn: 'button',
                buttonImage: "/site/imgs/calendar.png",
                buttonImageOnly: true,
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
                dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                changeMonth: true,
                changeYear: true,
                minDate: new Date(), // em teste
                beforeShow: function(){
                    select_date_fim = false;
                },
                onChangeMonthYear: function(){
                    select_date_fim = false;
                },
                beforeShowDay: function(date){
                    var calendar = $(this).data('calendar');
                    var date_select = $(this).parent().parent().find('.date-calendar').val().split('/');
                    date_select = new Date(`${date_select[2]}/${date_select[1]}/${date_select[0]}`);
                    if(select_date_fim) return [false, ''];
                    if(date > date_select){
                        if(filterServiceReservationDate(date)) {
                            select_date_fim = true;
                            return [false, ''];
                        }
                        for(var i=0; calendar.length>i; i++){ // lendo todos os dados do calendario registrado
                            if(calendar[i].select_termino == "data_fim" && date <= new Date(calendar[i].data_fim) ){
                                if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)){
                                    select_date_fim = true;
                                    return [false, ''];
                                }
                                if(calendar[i].semana){
                                    var semana_number = semanaNumber(calendar[i].semana);
                                    for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                        if(date.getDay() == semana_number[semana_i]) {
                                            return [true, ''];
                                        }
                                    }
                                }else{
                                    return [true, ''];
                                }
                            }else if(calendar[i].select_termino == "ocorrencia"){
                                var date_inicial_ocorrencia = new Date(calendar[i].data_inicial.replace('-','/'));
                                var dias = (calendar[i].ocorrencia*7)-(date_inicial_ocorrencia.getDay()+1);
                                date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias);
                                if(date <= date_inicial_ocorrencia){
                                    if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                    if(calendar[i].semana){
                                        var semana_number = semanaNumber(calendar[i].semana);
                                        for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                            if(date.getDay() == semana_number[semana_i]) {
                                                return [true, ''];
                                            }
                                        }
                                    }else{
                                        return [true, ''];
                                    }
                                }
                            }else if(calendar[i].select_termino == "nunca"){
                                if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]] || [];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2);
                                        if(filterServiceReservationHour(date, semana_horario)){
                                            select_date_fim = true;
                                            return [false, ''];
                                        }
                                    }
                                    var semana_number = semanaNumber(calendar[i].semana);
                                    for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                        if(date.getDay() == semana_number[semana_i]) {
                                            return [true, ''];
                                        }
                                    }
                                }else{
                                    return [true, ''];
                                }
                            }
                        }
                    }else{
                        return [false, ''];
                    }
                    select_date_fim = true;
                    return [false, ''];
                },
                onSelect: function(date){
                    $(this).parent().find('span').html(date);
                    var date_ini = $('[name="calendar_ini"]').val() || null;
                    date_ini = date_ini ? date_ini.split('/') : null;
                    var date_fim = date.split('/');
                    var date1 = new Date(`${date_ini[2]}/${date_ini[1]}/${date_ini[0]}`);
                    var date2 = new Date(`${date_fim[2]}/${date_fim[1]}/${date_fim[0]}`);
                    var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                    var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    $('[name="diaria"]').val(diffDays);

                    $('[name="quantidade"]').trigger('keyup');
                }
            });
        });

        function semanaNumber(semana){
            var semana_number = [];
            $.each(semana, (key_s, value_s)=>{
                switch(key_s){
                    case 'domingo':
                        semana_number.push(0);
                        break;
                    case 'segunda':
                        semana_number.push(1);
                        break;
                    case 'terca':
                        semana_number.push(2);
                        break;
                    case 'quarta':
                        semana_number.push(3);
                        break;
                    case 'quinta':
                        semana_number.push(4);
                        break;
                    case 'sexta':
                        semana_number.push(5);
                        break;
                    case 'sabado':
                        semana_number.push(6);
                        break;
                }
            });

            return semana_number;
        }

        function calendar_repetir(calendar){
            var date_inicio = new Date(calendar.data_inicial.replace('-','/'));

            switch(calendar.select_control){
                case 'semana':
                    if(calendar.select_termino == "data_fim"){
                        var date_fim = new Date(calendar.data_fim.replace('-','/'));
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var semana = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(semana == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            if(6 == date_atualizada.getDay()) semana++;
                            if(semana == calendar.number_select) semana = 0;
                        }
                    }else if(calendar.select_termino == "ocorrencia"){
                        var date_inicial_ocorrencia = new Date(calendar.data_inicial.replace('-','/'));
                        var dias = (calendar.ocorrencia*7);
                        date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias)
                        var diff = Math.abs(date_inicial_ocorrencia.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var semana = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(semana == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            if(6 == date_atualizada.getDay()) semana++;
                            if(semana == calendar.number_select) semana = 0;
                        }
                    }else if(calendar.select_termino == "nunca"){
                        var date_fim = new Date(`${(new Date().getFullYear()+5)}/12/31`);
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var semana = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(semana == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            if(6 == date_atualizada.getDay()) semana++;
                            if(semana == calendar.number_select) semana = 0;
                        }
                    }
                    break;
                case 'mes':
                    if(calendar.select_termino == "data_fim"){
                        var date_fim = new Date(calendar.data_fim.replace('-','/'));
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var mes = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(mes == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            mes++;
                            if(mes == calendar.number_select) mes = 0;
                        }
                    }else if(calendar.select_termino == "ocorrencia"){
                        var date_inicial_ocorrencia = new Date(calendar.data_inicial.replace('-','/'));
                        var dias = (calendar.ocorrencia*7);
                        date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias)
                        var diff = Math.abs(date_inicial_ocorrencia.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var mes = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(mes == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            mes++;
                            if(mes == calendar.number_select) mes = 0;
                        }
                    }else if(calendar.select_termino == "nunca"){
                        var date_fim = new Date(`${(new Date().getFullYear()+5)}/12/31`);
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var mes = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(mes == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            mes++;
                            if(mes == calendar.number_select) mes = 0;
                        }
                    }
                    break;
                case 'ano':
                    if(calendar.select_termino == "data_fim"){
                        var date_fim = new Date(calendar.data_fim.replace('-','/'));
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var anual = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(anual == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            anual++;
                            if(anual == calendar.number_select) anual = 0;
                        }
                    }else if(calendar.select_termino == "ocorrencia"){
                        var date_inicial_ocorrencia = new Date(calendar.data_inicial.replace('-','/'));
                        var dias = (calendar.ocorrencia*7);
                        date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias)
                        var diff = Math.abs(date_inicial_ocorrencia.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var anual = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(anual == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            anual++;
                            if(anual == calendar.number_select) anual = 0;
                        }
                    }else if(calendar.select_termino == "nunca"){
                        var date_fim = new Date(`${(new Date().getFullYear()+5)}/12/31`);
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var anual = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(anual == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            anual++;
                            if(anual == calendar.number_select) anual = 0;
                        }
                    }
                    break;
            }

            return data;
        }

        function arrayChunk(array, perChunk){
            return array.reduce((resultArray, item, index) => { 
                const chunkIndex = Math.floor(index/perChunk)

                if(!resultArray[chunkIndex]) {
                    resultArray[chunkIndex] = [] // start a new chunk
                }

                resultArray[chunkIndex].push(item)

                return resultArray
            }, []);
        }

        function filterServiceReservationDate(date){
            date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,'0')+'-'+date.getDate().toString().padStart(2,'0');
            var service_reservation = JSON.parse($('.service_reservation').val());
            // console.log([date,service_reservation]);
            var status = false;
            service = service_reservation.filter(function(service) {
                if(service.date_reservation_fim){
                    if(date >= service.date_reservation_ini){
                        if(date <= service.date_reservation_fim){
                            if(service.status) status = true;
                            return service;
                        }
                    }
                }else if(!service.hour_reservation){
                    if(service.date_reservation_ini == date) {
                        if(service.status) status = true;
                        return service;
                    }
                }
            });

            if(status) return true;

            if($('[name="vaga_controller"]').val() == 1){
                if(service.length >= $('[name="vagas"]').val()){
                    return true;
                }
            }
            return false;
        }

        function filterServiceReservationHour(date, hour){
            date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,'0')+'-'+date.getDate().toString().padStart(2,'0');
            var service_reservation = JSON.parse($('.service_reservation').val());

            var count_horario = 0;
            var horarios = hour.map(function(value){
                return value.join(' - ');
            });
            var service = service_reservation.map(function(service) {
                if(service.date_reservation_fim){
                    if(date >= service.date_reservation_ini){
                        if(date <= service.date_reservation_fim){
                            for(var i=0;horarios.length>i;i++){
                                if(horarios[i] == service.hour_reservation){
                                    count_horario++;
                                }
                            }
                        }
                    }
                }else if(service.date_reservation_ini == date){
                    for(var i=0;horarios.length>i;i++){
                        if(horarios[i] == service.hour_reservation){
                            count_horario++;
                        }
                    }
                }
            });

            if($('[name="vaga_controller"]').val() == 1){
                if(count_horario >= (horarios.length * $('[name="vagas"]').val())){
                    return true;
                }
            }else{
                if(count_horario == hour.length){
                    return true;
                }
            }
            return false;
        }

        function filterServiceReservationHourSelected(date, hour){
            var hour_old_new = hour;
            date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,'0')+'-'+date.getDate().toString().padStart(2,'0');
            var service_reservation = JSON.parse($('.service_reservation').val());

            var count_horario = 0;
            var service_hours = [];
            service_reservation.filter(function(service) {
                if(service.date_reservation_fim){
                    if(date >= service.date_reservation_ini){
                        if(date <= service.date_reservation_fim){
                            return true;
                        }
                    }
                }else if(service.date_reservation_ini == date){
                    return true;
                }
            }).map(function(service){
                service_hours[service.hour_reservation] = service_hours[service.hour_reservation] ? service_hours[service.hour_reservation]+1 : 1;
            });

            for(var i=0;hour.length>i;i++){
                if(service_hours[hour[i]]){
                    if(service_hours[hour[i]] >= $('[name="vagas"]').val()){
                        hour_old_new.splice(i,1);
                    }
                }
                // console.log(service_hours[hour[i]]);
            }
            return hour_old_new;
        }

        function calendar_start(identifier){
            let semana_dia = {
                0: 'domingo',
                1: 'segunda',
                2: 'terca',
                3: 'quarta',
                4: 'quinta',
                5: 'sexta',
                6: 'sabado',
            };

            $(`${identifier}`).datepicker({
                showOn: 'button',
                buttonImage: "/site/imgs/calendar.png",
                buttonImageOnly: true,
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
                dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                changeMonth: true,
                changeYear: true,
                minDate: new Date(), // em teste
                beforeShowDay: function(date){
                    var calendar = $(this).data('calendar');
                    // console.log(filterServiceReservationDate(date));
                    if(filterServiceReservationDate(date)) return [false, ''];
                    for(var i=0; calendar.length>i; i++){ // lendo todos os dados do calendario registrado
                        var date_calendar = new Date(calendar[i].data_inicial); // setamos a dat inicial
                        if(new Date() > date_calendar) date_calendar = new Date(); // serve para bloquear as datas anteriores
                        date_calendar.setDate((date_calendar.getDate()+(calendar[i].antecedencia))-1); // adicionamos antecendencia caso para bloquear as datas
                        date_calendar.setHours('00', '00', '00');
                        if(date >= date_calendar){ // entra na função para liberar as datas caso
                            if(calendar[i].select_termino == "data_fim" && date <= new Date(calendar[i].data_fim) ){
                                if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]] || [];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2);
                                        if(filterServiceReservationHour(date, semana_horario)) return [false, ''];
                                    }
                                    var semana_number = semanaNumber(calendar[i].semana);
                                    for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                        if(date.getDay() == semana_number[semana_i]) {
                                            return [true, ''];
                                        }
                                    }
                                }else{
                                    return [true, ''];
                                }
                            }else if(calendar[i].select_termino == "ocorrencia"){
                                var date_inicial_ocorrencia = new Date(calendar[i].data_inicial.replace('-','/'));
                                var dias = (calendar[i].ocorrencia*7)-(date_inicial_ocorrencia.getDay()+1);
                                date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias);
                                if(date <= date_inicial_ocorrencia){
                                    if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                    if(calendar[i].semana){
                                        var semana_select = calendar[i].semana[semana_dia[date.getDay()]] || [];
                                        if(semana_select['horario']){
                                            var semana_horario = arrayChunk(semana_select['horario'],2);
                                            if(filterServiceReservationHour(date, semana_horario)) return [false, ''];
                                        }
                                        var semana_number = semanaNumber(calendar[i].semana);
                                        for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                            if(date.getDay() == semana_number[semana_i]) {
                                                return [true, ''];
                                            }
                                        }
                                    }else{
                                        return [true, ''];
                                    }
                                }
                            }else if(calendar[i].select_termino == "nunca"){
                                if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]] || [];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2);
                                        if(filterServiceReservationHour(date, semana_horario)) return [false, ''];
                                    }
                                    var semana_number = semanaNumber(calendar[i].semana);
                                    for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                        if(date.getDay() == semana_number[semana_i]) {
                                            return [true, ''];
                                        }
                                    }
                                }else{
                                    return [true, ''];
                                }
                            }
                        }
                    }
                    return [false, ''];
                },
                onSelect: function(date){
                    $(this).parent().find('span').html(date);
                    var calendar = $(this).data('calendar');
                    var date = date.split('/');
                    date = `${date[2]}/${date[1]}/${date[0]}`;
                    date = new Date(date);
                    for(var i=0; calendar.length>i; i++){ // lendo todos os dados do calendario registrado
                        var date_calendar = new Date(calendar[i].data_inicial.replace('-','/')); // setamos a dat inicial
                        if(date >= date_calendar){ // entra na função para liberar as datas caso
                            if(calendar[i].select_termino == "data_fim" && date <= new Date(calendar[i].data_fim.replace('-','/')) ){
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2).map(function(value){
                                            return `${value[0]} - ${value[1]}`;
                                        });
                                        semana_horario = filterServiceReservationHourSelected(date,semana_horario);

                                        $('[name="hours"]').empty();
                                        $('[name="hours"]').append(semana_horario.map(function(value){
                                            return `<option value="${value}">${value}</option>`;
                                        }).join(''));
                                        $('.reserva-hora').removeClass('d-none');
                                    }else{
                                        $('.reserva-hora').addClass('d-none');
                                    }
                                }
                            }else if(calendar[i].select_termino == "ocorrencia"){
                                var date_inicial_ocorrencia = new Date(calendar[i].data_inicial.replace('-','/'));
                                var dias = (calendar[i].ocorrencia*7)-(date_inicial_ocorrencia.getDay()+1);
                                date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias)
                                if(date <= date_inicial_ocorrencia){
                                    if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2).map(function(value){
                                            return `${value[0]} - ${value[1]}`;
                                        });
                                        semana_horario = filterServiceReservationHourSelected(date,semana_horario);

                                        $('[name="hours"]').empty();
                                        $('[name="hours"]').append(semana_horario.map(function(value){
                                            return `<option value="${value}">${value}</option>`;
                                        }).join(''));
                                        $('.reserva-hora').removeClass('d-none');
                                    }else{
                                        $('.reserva-hora').addClass('d-none');
                                    }
                                }
                                }
                            }else if(calendar[i].select_termino == "nunca"){
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2).map(function(value){
                                            return `${value[0]} - ${value[1]}`;
                                        });
                                        semana_horario = filterServiceReservationHourSelected(date,semana_horario);

                                        $('[name="hours"]').empty();
                                        $('[name="hours"]').append(semana_horario.map(function(value){
                                            return `<option value="${value}">${value}</option>`;
                                        }).join(''));
                                        $('.reserva-hora').removeClass('d-none');
                                    }else{
                                        $('.reserva-hora').addClass('d-none');
                                    }
                                }
                            }
                        }
                    }
                    $('.date-calendar-verif').prop('disabled', false).val('');
                    select_date_fim = false;
                }
            });
        }
    </script>
@endsection