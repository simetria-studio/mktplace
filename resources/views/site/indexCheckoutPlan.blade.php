@extends('layouts.site')

@section('container')
    <form action="{{route('checkoutSessionPlan.post')}}" method="post">
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
                    <div class="row">
                        <div class="col-12 mb-1"><b>Selecione um endereço cadastrado ou cadastre um novo</b></div>
                        <div class="form-group col-7 col-md-8">
                            <select name="address_id" class="form-control">
                                <option value="newAddress">Novo Endereço</option>
                                @foreach ($addresses as $value)
                                    <option value="{{$value->id}}">{{$value->address}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-5 col-md-4 spinner-cep">
                            <input type="text" name="postal_code" class="form-control" placeholder="00000-000">
                            <div class="spinner-grow spinner-cep-div d-none" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <input type="text" name="address" class="form-control" placeholder="Endereço/Rua/Avenida">
                        </div>
                        <div class="form-group col-9">
                            <input type="text" name="complement" class="form-control" placeholder="Complemento">
                        </div>
                        <div class="form-group col-3">
                            <input type="text" name="number" class="form-control requerid" placeholder="Nº 999">
                        </div>
                        <div class="form-group col-12">
                            <input type="text" name="address2" class="form-control" placeholder="Bairro">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <input type="text" name="state" class="form-control" placeholder="Estado">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <input type="text" name="city" class="form-control" placeholder="Cidade">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <input type="text" name="phone2" class="form-control requerid" placeholder="Celular">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <input type="text" name="cnpj_cpf2" class="form-control requerid" value="{{auth()->user()->cnpj_cpf ?? ''}}" placeholder="CPF/CNPJ">
                        </div>

                        <div class="form-group col-12 mt-3">
                            <div class="row mb-2 btn-frete d-none">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#modalFretes">Selecionar Modalidade de Entrega
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6"><strong>Sub Total:</strong></div>
                                <div class="col-6 text-right sub-total-cart">
                                    R$ {{number_format($cart_session['plan_value'], 2, ',', '.')}}</div>
                                <div class="col-6"><strong>Frete:</strong></div>
                                <div class="col-6 text-right total-frete">
                                    R$ {{number_format(0, 2, ',', '.')}}
                                </div>
                                <div class="col-6"><strong>Total:</strong></div>
                                <div class="col-6 text-right total">
                                    R$ {{number_format($cart_session['plan_value']+0, 2, ',', '.')}}</div>
                            </div>
                        </div>

                        <div class="form-group col-12 mt-2">
                            <label for="">Observação (opcional)</label>
                            <textarea name="note" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Dados do cartão --}}
                <div class="col-12 col-md-6">
                    <div style="position: sticky;top: 12px;">
                        <div class="row justify-content-center">
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
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-9 form-group mt-2">
                                <button type="button" class="btn btn-primary btn-block btn-send-payment">Assinar</button>
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
@endsection
@section('js')
    <script>
        $(document).ready(function(){
            $(document).on('change', '[name="address_id"]', function(){
                $('.spinner-cep-div').removeClass('d-none');
                getFretes({address_id: $(this).val()});
            });
            $(document).on('keyup', '[name="postal_code"]', function(){
                if($(this).val().length == 9){
                    $('.spinner-cep-div').removeClass('d-none');
                    getFretes({cep: $(this).val()});
                }
            });

            $(document).on('click', '.click-frete2', function(){
                var radio = $(this);
                var frete_id = $(this).data('frete_id');
                $('.input-frete.frete-'+frete_id).prop('disabled', true);
                radio.parent().find('.input-frete').prop('disabled', false);

                var sub_price = parseFloat(radio.parent().find('.price').val()) || 0;
                radio.closest('.frete').find('.sub-total').html('R$ '+(sub_price + parseFloat(radio.closest('.frete').find('.sub_total').val())).toFixed(2).replace('.',','));

                var total_price = 0;
                $('.frete').find('.price').each(function(){
                    if(!$(this).is(':disabled')){
                        total_price += parseFloat($(this).val()) || 0;
                    }
                });

                $('.total-frete').html('R$ '+sub_price.toFixed(2).replace(".",","));
                $('.total').html('R$ '+(sub_price + parseFloat(radio.closest('.frete').find('.sub_total').val())).toFixed(2).replace('.',','));
            });
        });

        function getFretes(dataVals){
            $.ajax({
                url: `{{route('freteCheckoutPlano')}}`,
                type: 'POST',
                data: dataVals,
                success: (data) => {
                    console.log(data);

                    $('.btn-frete').removeClass('d-none');
                    $('.fretes').empty();
                    var div = '';
                    $('[name="postal_code"]').val(data.addresses.cep ?? data.addresses.post_code);
                    $('[name="number"]').val(data.addresses.number ?? '');
                    $('[name="address"]').val(data.addresses.logradouro ?? data.addresses.address);
                    if(data.addresses.logradouro ?? data.addresses.address) $('[name="address"]').prop('readonly', true);
                    $('[name="address2"]').val(data.addresses.bairro ?? data.addresses.address2);
                    if(data.addresses.bairro ?? data.addresses.address2) $('[name="address2"]').prop('readonly', true);
                    $('[name="state"]').val(data.addresses.uf ?? data.addresses.state);
                    if(data.addresses.uf ?? data.addresses.state) $('[name="state"]').prop('readonly', true);
                    $('[name="city"]').val(data.addresses.localidade ?? data.addresses.city);
                    if(data.addresses.localidade ?? data.addresses.city) $('[name="city"]').prop('readonly', true);
                    $('[name="phone2"]').val(data.addresses.phone2 ?? '');

                    $('[name="number"]').focus();

                    var sub_total = data.cart_session.plan_value;
                    div = '<div class="row frete border-bottom border-dark pb-3">';
                        div += '<input type="hidden" class="sub_total" value="'+sub_total+'">';

                        div += '<div class="col-12">';
                            div += '<div class="row border mx-2 py-3 rounded">';
                                // Tranporte proprio
                                for(var i=0; i<data.transporte_proprio.length; i++) {
                                    var valor_entrega = data.transporte_proprio[i].valor_entrega;
                                    if(data.transporte_proprio[i].frete_gratis == 1){
                                        if(sub_total > data.transporte_proprio[i].valor_minimo) valor_entrega = 0;
                                    }
                                    var entrega = 'Entrega Até '+(data.transporte_proprio[i].tempo_entrega)+(data.transporte_proprio[i].tempo == 'H' ? ' Horas' : ' Dias Uteis')+'.';
                                    if(data.transporte_proprio[i].tempo == 'S') entrega = 'Entrega nos dias da semana <br>'+(diaSemana(data.transporte_proprio[i].semana)).join(' - ');
                                    if(data.transporte_proprio[i].tempo == 'C') entrega = '';
                                    div += 
                                        '<div class="col-5 my-1">'+
                                            '<input type="hidden" class="sub_total" value="'+sub_total+'">'+
                                            '<input type="radio" class="click-frete2 radio" data-frete_id="'+data.cart_session.id+'" name="frete[type]" value="proprio"> '+
                                            '<input disabled class="input-frete frete-'+data.cart_session.id+'" type="hidden" name="frete[service_id]" value="'+data.transporte_proprio[i].id+'"> '+
                                            '<input disabled class="price input-frete frete-'+data.cart_session.id+'" type="hidden" name="frete[price]" value="'+(data.total_entregas*valor_entrega)+'">'+
                                            '<input disabled class="input-frete frete-'+data.cart_session.id+'" type="hidden" name="frete[dados_gerais]" value=\''+JSON.stringify(data.transporte_proprio[i])+'\'> <label><b style="font-size:.9rem;">Transporte Próprio</b></label>'+
                                        '</div>'+
                                        '<div class="col-4 my-1" style="font-size: .8rem;">'+entrega+'</div>'+
                                        '<div class="col-3 my-1">'+(valor_entrega > 0 ? 'R$ ' : '')+(valor_entrega > 0 ? parseFloat((data.total_entregas*valor_entrega)).toFixed(2).replace('.',',') : 'Frete Grátis')+'</div>'+
                                        '<div class="col-12 border-bottom pb-2">'+(data.transporte_proprio[i].descricao ? 'Obs: '+data.transporte_proprio[i].descricao : '')+'</div>'
                                    ;
                                }
                                // if(data.store.retirada){
                                //     if(data.store.retirada == 'true'){
                                //         div += 
                                //             '<div class="col-7 my-1">'+
                                //                 '<input type="hidden" class="sub_total" value="'+sub_total+'">'+
                                //                 '<input type="radio" class="click-frete2 radio" data-frete_id="'+data.cart_session.id+'" name="frete[type]" value="retirada"> '+
                                //                 '<input disabled class="input-frete frete-'+data.cart_session.id+'" type="hidden" name="frete[service_id]" value="rsa-retirada"> '+
                                //                 '<input disabled class="price input-frete frete-'+data.cart_session.id+'" type="hidden" name="frete[price]" value="0">'+
                                //                 '<input disabled class="input-frete frete-'+data.cart_session.id+'" type="hidden" name="frete[dados_gerais]" value="'+btoa(JSON.stringify(data.store))+'"> <label><b style="font-size:.9rem;">Retirar na Loja</b></label>'+
                                //             '</div>'+
                                //             '<div class="col-5 my-1">Grátis</div>'+
                                //             '<div class="col-12 border-bottom pb-2">Obs: '+(data.store.ob_retirada)+'</div>'
                                //         ;
                                //     }
                                // }

                                for(var i=0; i<data.transportadoras.length; i++) {
                                    if(!data.transportadoras[i].error){
                                        div += 
                                            '<div class="col-12 col-md-3 my-1"><img class="img-fluid" src="'+data.transportadoras[i].company.picture+'"></div>'+
                                            '<div class="col-4 col-md-3 my-1">'+
                                                '<input type="hidden" class="sub_total" value="'+sub_total+'">'+
                                                '<input type="radio" class="click-frete2 radio" name="frete[type]" value="correios"> '+
                                                '<input disabled class="input-frete frete-'+data.cart_session.id+'" type="hidden" name="frete[transport_name]" value="'+data.transportadoras[i].name+'"> '+
                                                '<input disabled class="input-frete" type="hidden" name="frete[company_id]" value="'+data.transportadoras[i].company.id+'"> '+
                                                '<input disabled class="input-frete" type="hidden" name="frete[service_id]" value="'+data.transportadoras[i].id+'"> '+
                                                '<input disabled class="input-frete" type="hidden" name="frete[packages]" value="'+btoa(JSON.stringify(data.transportadoras[i].packages))+'"> '+
                                                '<input disabled class="price input-frete" type="hidden" name="frete[price]" value="'+(data.total_entregas*data.transportadoras[i].custom_price)+'"> '+
                                                '<input disabled class="input-frete" type="hidden" name="frete[dados_gerais]" value=\''+JSON.stringify(data.transportadoras[i])+'\'>'+
                                                '<label style="font-size:.9rem;">'+data.transportadoras[i].name+'</label>'+
                                            '</div>'+
                                            '<div class="col-4 col-md-3 my-1" style="font-size: .8rem;">Entrega Até '+(data.transportadoras[i].custom_delivery_time+1)+' Dias Uteis.</div>'+
                                            '<div class="col-4 col-md-3 my-1">R$ '+parseFloat((data.total_entregas*data.transportadoras[i].custom_price)).toFixed(2).replace('.',',')+'</div>'
                                        ;
                                    }
                                }
                            div += '</div>';
                        div += '</div>';
                        div += '<div class="col-6 my-2">Total</div>';
                        div += '<div class="col-6 my-2 text-right sub-total">R$ '+sub_total.toFixed(2).replace('.',',')+'</div>';
                    div += '</div>';

                    $('.fretes').append(div);

                    $('.spinner-cep-div').addClass('d-none');
                },
                error: (err)=>{
                    Swal.fire({
                        icon: 'error',
                        title: 'insira o cep correto do seu endereço'
                    });

                    $('.btn-frete').removeClass('d-none');
                    $('.fretes').empty();

                    $('.spinner-cep-div').addClass('d-none');
                }
            });
        }
    </script>
@endsection
