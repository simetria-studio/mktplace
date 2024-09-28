@extends('layouts.site')

@section('container')
    <form action="{{route('checkout.service.post')}}" method="post">
        @csrf
        <div class="container my-5">
            @if(\Illuminate\Support\Facades\Session::has('error'))
            <div class="alert alert-danger">
                <ul>
                    <li>{{\Illuminate\Support\Facades\Session::get('error')}}</li>
                </ul>
            </div>
            @endif

            <div class="row">
                {{-- dados de endereço --}}
                <div class="col-12 col-md-6">
                    <div class="row endereco">
                        <div class="form-group col-12">
                            <h2>Endereço da Experiência</h2>
                        </div>

                        @if ($service->address_controller == '0')
                            <div class="form-group col-12">
                                CEP: {{$service->store->post_code ?? ''}}<br>
                                Rua: {{$service->store->address ?? ''}} - Nº {{$service->store->number ?? ''}} / {{$service->store->address2 ?? ''}}<br>
                                Cidade/Estado: {{$service->store->city ?? ''}}/{{$service->store->state ?? ''}}<br>
                                Complemento: {{$service->store->complement ?? ''}}<br>
                            </div>
                        @else
                            <div class="form-group col-12">
                                CEP: {{$service->postal_code ?? ''}}<br>
                                Rua: {{$service->address ?? ''}} - Nº {{$service->number ?? ''}} / {{$service->address2 ?? ''}}<br>
                                Cidade/Estado: {{$service->city ?? ''}}/{{$service->state ?? ''}}<br>
                                Complemento: {{$service->complement ?? ''}}<br>
                            </div>
                        @endif

                        <div class="form-group col-12"><h3>Endereço de Cobrança</h3></div>

                        @if ($addresses->count() > 0)
                            <div class="form-group col-12">
                                <button type="button" class="btn btn-primary btn-new-address">Novo Endereço</button>
                            </div>
                            <div class="form-group col-12">
                                <select name="endereco" class="form-control">
                                    @foreach ($addresses as $value)
                                        <option value="{{$value->id}}" @if($addresses->last()->post_code == $value->post_code) selected @endif data-dados="{{json_encode($value)}}">{{$value->address}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 endereço-pronto">
                                CEP: <span class="_post_code">{{$address->post_code ?? ''}}</span> <br>
                                Rua: <span class="_address">{{$address->address ?? ''}}</span> - Nº <span class="_number">{{$address->number ?? ''}}</span> / <span class="_address2">{{$address->address2 ?? ''}}</span><br>
                                Cidade/Estado: <span class="_city">{{$address->city ?? ''}}</span>/<span class="_state">{{$address->state ?? ''}}</span> <br>
                            </div>
                        @endif

                        <div class="form-group @if(isset($address->post_code)) d-none @endif col-5 col-md-4 spinner-cep">
                            <input type="text" name="post_code" class="form-control" placeholder="00000-000">
                            <div class="spinner-grow spinner-cep-div d-none" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="form-group @if(isset($address->address)) d-none @endif col-md-12">
                            <input type="text" name="address" class="form-control" value="{{$address->address ?? ''}}" placeholder="Endereço/Rua/Avenida">
                        </div>
                        <div class="form-group col-9 @if(isset($address->complement)) d-none @endif">
                            <input type="text" name="complement" class="form-control" value="{{$address->complement ?? ''}}" placeholder="Complemento">
                        </div>
                        <div class="form-group col-3 @if(isset($address->number)) d-none @endif">
                            <input type="text" name="number" class="form-control requerid" value="{{$address->number ?? ''}}" placeholder="Nº 999">
                        </div>
                        <div class="form-group @if(isset($address->address2)) d-none @endif col-12">
                            <input type="text" name="address2" class="form-control" value="{{$address->address2 ?? ''}}" placeholder="Bairro">
                        </div>
                        <div class="form-group @if(isset($address->state)) d-none @endif col-12 col-md-6">
                            <input type="text" name="state" class="form-control" value="{{$address->state ?? ''}}" placeholder="Estado">
                        </div>
                        <div class="form-group @if(isset($address->city)) d-none @endif col-12 col-md-6">
                            <input type="text" name="city" class="form-control" value="{{$address->city ?? ''}}" placeholder="Cidade">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <input type="text" name="phone2" class="form-control requerid" value="{{$address->phone2 ?? ''}}" placeholder="Celular">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <input type="text" name="cnpj_cpf2" class="form-control requerid" value="{{auth()->user()->cnpj_cpf ?? ''}}" placeholder="CPF/CNPJ">
                        </div>
                        {{-- <div class="form-group col-12 col-md-6">
                            <input type="text" name="phone2" class="form-control requerid" placeholder="Celular">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <input type="text" name="cnpj_cpf2" class="form-control requerid" value="{{auth()->user()->cnpj_cpf ?? ''}}" placeholder="CPF/CNPJ">
                        </div> --}}

                        <div class="form-group col-12">
                            Data da Reserva: {{date('d-m-Y', strtotime($cart_session['attributes']['calendar']['date_ini']))}} {{!empty($cart_session['attributes']['calendar']['date_fim']) ? 'Até '.date('d-m-Y', strtotime($cart_session['attributes']['calendar']['date_fim'])) : ''}} {{$cart_session['attributes']['calendar']['hours'] !== '0:0' ? $cart_session['attributes']['calendar']['hours'] : ''}}<br>
                        </div>

                        <div class="form-group col-12">
                            <div class="row justify-content-center mb-2">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-primary ml-2" data-toggle="modal" data-target="#modalCupomAplicar">Aplicar Cupom</button>
                                </div>
                                <div class="col-12 text-center">
                                    <span>So pode ter um cupom aplicado</span>
                                </div>
                                <div class="col-12 text-center">
                                    @if ($coupons->count() > 0)
                                        <b>Cupom Aplicado - {{$coupons[0]['code_coupon']}}</b>
                                        <input type="hidden" class="coupons" value="{{$coupons->toJson()}}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-12 mt-3">
                            <div class="row">
                                <div class="col-12"><strong>{{$cart_session['name']}}:</strong></div>
                                @if ($cart_session['attributes']['hospedagem'] == '1')
                                    <div class="col-12 text-right total">
                                        {{$cart_session['attributes']['diaria']}} Dias x R$ {{number_format($cart_session['price'], 2, ',','.')}}
                                        <br>
                                        {{$cart_session['quantity']}} <span class="text-capitalize">{{$cart_session['attributes']['selecao_hospedagem']}}</span> x R$ {{number_format($cart_session['price']*$cart_session['attributes']['diaria'], 2, ',','.')}}
                                    </div>
                                @else
                                    <div class="col-12 text-right total">
                                        {{$cart_session['quantity']}} x R$ {{number_format($cart_session['price']*$cart_session['attributes']['diaria'], 2, ',','.')}}
                                    </div>
                                @endif
                                @if ((calcCouponService()['dp'] ?? 0) > 0)
                                    <div class="col-6"><strong>Desconto:</strong></div>
                                    <div class="col-6 text-right"><span>R$ -{{number_format(calcCouponService()['dp'], 2, ',', '.')}}</span></div>
                                @endif
                                <div class="col-6 mt-2"><strong>Total a Pagar:</strong></div>
                                <div class="col-6 mt-2 text-right total">
                                    R$ {{number_format(($cart_session['quantity']*($cart_session['price']*$cart_session['attributes']['diaria'])-(calcCouponService()['dp']??0)), 2, ',', '.')}}
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-12 mt-2">
                            <div class="accordion" id="accordionComments">
                                <div class="card">
                                    <div class="card-header" id="seller_{{$cart_session['attributes']['seller_id'] ?? ''}}">
                                        <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse_{{$cart_session['attributes']['seller_id'] ?? ''}}" aria-expanded="true" aria-controls="collapse_{{$cart_session['attributes']['seller_id'] ?? ''}}">
                                            <i class="fas fa-arrow-down"></i> Deixe uma observação para a Loja - {{$service->store->store_name ?? $cart_session['attributes']['seller_id']}} (opcional)
                                        </button>
                                        </h2>
                                    </div>

                                    <div id="collapse_{{$cart_session['attributes']['seller_id'] ?? ''}}" class="collapse" aria-labelledby="seller_{{$cart_session['attributes']['seller_name'] ?? ''}}" data-parent="#accordionComments">
                                        <div class="card-body">
                                            <textarea name="note" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dados do cartão --}}
                <div class="col-12 col-md-6">
                    <div style="position: sticky;top: 12px;">
                        <select id="method_payment" name="method_payment" class="form-control form-group">
                            <option value="boleto">Boleto</option>
                            <option value="credit_card">Cartão de crédito</option>
                            <option value="pix">Pix</option>
                        </select>
                        <div class="row justify-content-center fade d-none" id="campos_cartao_credito">
                            <div class="col-12 form-group">
                                <label for="">Nome do Titular no Cartão</label>
                                <input placeholder="Nome (como escrito no cartão)" class="form-control form-control-sm" type="text" name="card_holder_name"/>
                            </div>
                            <div class="col-12 form-group">
                                <label for="">Número do Cartão</label>
                                <input placeholder="Número do cartão" class="form-control form-control-sm" type="text" name="card_number"/>
                            </div>
                            <div class="col-8 form-group">
                                <label for="">Validade do Cartão MM/AAAA</label>
                                <input placeholder="MM/AAAA" class="form-group form-control form-control-sm col-12 col-md-12" type="text" name="card_expiration_month_year"/>
                            </div>
                            <div class="col-4 form-group">
                                <label for="">CVC</label>
                                <input placeholder="CVC" class="form-group form-control form-control-sm col-12 col-md-12" type="text" name="card_cvv"/>
                            </div>
                            <div class="col-12 form-group">
                                @isset (getTabelaGeral('regra_parcelamento','parcelas')->array_text)
                                    <select name="installments" class="form-control form-control-sm">
                                        @foreach (getTabelaGeral('regra_parcelamento','parcelas')->array_text as $item)
                                            @php
                                                $valor_cal_par = ($cart_session['quantity']*($cart_session['price']*$cart_session['attributes']['diaria'])-(calcCouponService()['dp']??0));
                                            @endphp
                                            @isset ($item['valor'])
                                                @if (str_replace(',','.',$item['valor']) <= $valor_cal_par)
                                                    <option value="{{$item['parcela']}}">{{$item['parcela']}} x R$ {{number_format(($valor_cal_par+(($valor_cal_par*str_replace(',','.',$item['porcentage']))/100))/$item['parcela'], 2, ',', '.')}} = R$ {{number_format($valor_cal_par+(($valor_cal_par*str_replace(',','.',$item['porcentage']))/100), 2, ',', '.')}}</option>
                                                @endif
                                            @endisset
                                        @endforeach
                                    </select>
                                @endisset
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-9 form-group mt-2">
                                <button type="button" class="btn btn-primary btn-block btn-send-payment">Pagar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalFretes" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalFretesLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title" id="modalFretesLabel">
                            <h5>Modalidade de Entrega</h5>
                            <span style="font-size:.8rem;">Selecione uma opção para cada loja</span>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="fretes"></div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="modalCupomAplicar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalCupomAplicarLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title" id="modalCupomAplicarLabel">
                        <h5>Aplicar Cupom</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('aplicarCupomService')}}" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="">Codigo do Cupom</label>
                                <input type="text" class="form-control text-uppercase" name="code_coupon">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary btn-aplicar-cupom" data-dismiss="modal">Aplicar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        let mthod = $('#method_payment');
        mthod.on('change', function (dm) {
            let camposCartaoCredito = $("#campos_cartao_credito");
            camposCartaoCredito.addClass('fade').addClass('d-none');

            let fn = {
                'boleto': () => {
                },
                'credit_card': () => {
                    camposCartaoCredito.removeClass('fade').removeClass('d-none');
                }
            };

            fn[dm.target.value]();

        });

        $(document).ready(function(){
            $(document).on('click', '.btn-aplicar-cupom', function(){
                var btn = $(this);
                Swal.fire({
                    title: 'Aplicando Cupom de Desconto, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: btn.closest('form').attr('action'),
                    type: 'POST',
                    data: new FormData(btn.closest('form')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data)=>{
                        // console.log(data);
                        Swal.fire({
                            icon: 'success',
                            title: 'Cupom Aplicado com sucesso!'
                        }).then((result)=>{
                            window.location.reload();
                        });
                    },
                    error: (err) => {
                        if(err.responseJSON.error){
                            Swal.fire({
                                icon: err.responseJSON.error.icon,
                                title: err.responseJSON.error.msg
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
