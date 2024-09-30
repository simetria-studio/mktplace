@extends('layouts.site')

@section('container')
    <div class="container-fluid index-modalidade">
        <div class="container-fluid pt-3">
            <div class="mb-5 mt-4">
                <h3>Modalidade de entrega</h3>
            </div>

            <div class="row justify-content-center mb-2">
                <div class="col-12 text-center">
                    <button type="button" class="btn swal-aplicar-cupom" style="color: #59981A;font-size: 1.4rem;font-weight: bold;">Aplicar Cupom</button>
                </div>
                <div class="col-12 text-center">
                    <span>Só pode ter um cupom aplicado</span>
                </div>
                <div class="col-12 text-center">
                    @if ($coupons)
                        <b>Cupom Aplicado - {{$coupons['code_coupon']}}</b>
                        <input type="hidden" class="coupons" value="{{collect($coupons)->toJson()}}">
                    @endif
                </div>
            </div>

            <div class="row justify-content-center mb-2">
                <div class="col-auto">
                    Entregar neste endereço
                </div>
                <div class="col-auto col-md-4">
                    <select name="endereco" class="form-control">
                        <option value="new_address">Consultar novo cep</option>
                        @foreach ($addresses as $value)
                            <option selected value="{{$value->post_code}}" data-address_json='{!! $value !!}'>{{$value->address}}, {{$value->number}} - {{$value->address2}} - {{$value->city}}/{{$value->state}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto col-md-2 spinner-cep">
                    <input type="text" name="zip_code" class="form-control" value="@if(session()->has('zip_code')){{session()->get('zip_code')}}@endif" placeholder="00000-000">
                    <div class="spinner-grow spinner-cep-div d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col-auto col-md-3">
                    <button class="btn btn-c-outline-success btn-block btn-consultar-cep">Calcular frete</button>
                </div>
            </div>

            <div class="address mb-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6">
                        <div class="row">
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
                                <input type="text" name="phone" class="form-control" placeholder="Telefone/Celular">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5 div-modalidades">
                @foreach ($sellers as $seller_id => $seller)
                    @php
                        $store = App\Models\Store::where('user_id',$seller_id)->first();
                    @endphp
                    <div class="mb-2 modalidade-seller" id="seller-{{ $seller_id }}">
                        <div class="header">
                            <h4>{{ $store->store_name }}</h4>
                        </div>
                        <div class="body">
                            <div class="body-modalidades"></div>
                            <div class="body-produtos">
                                <div class="produtos">
                                    @foreach ($seller as $item)
                                        <div style="margin-bottom: 12px;">
                                            <div style="margin-bottom: 6px">
                                                {{ $item['name'] }}
                                            </div>
                                            <div class="d-flex">
                                                <div class="col-4"></div>
                                                <div class="col-4">{{ $item['quantity'] }} x R$ {{ number_format($item['price'], 2, ',', '.') }}</div>
                                                <div class="col-4">R$ {{ number_format($item['quantity']*$item['price'], 2, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="footer">
                                    <div class="d-flex">
                                        <div class="col-4">Sub Total:</div>
                                        <div class="col-4"></div>
                                        <div class="col-4 subtotal">R$ {{ number_format(collect($seller)->map(function($query){return $query['quantity']*$query['price'];})->sum(), 2, ',', '.') }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-4">Frete:</div>
                                        <div class="col-4"></div>
                                        <div class="col-4 frete">R$ 0,00</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="col-4">Total:</div>
                                        <div class="col-4"></div>
                                        <div class="col-4 total">R$ {{ number_format(collect($seller)->map(function($query){return $query['quantity']*$query['price'];})->sum(), 2, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="div-modalidade-line d-flex align-items-center justify-content-between div-total">
                <div class="col-auto d-flex align-items-center">
                    Produtos selecionados: <span class="ml-2 quantity-total">{{ cart_show()->quantidade }}</span>
                </div>
                <div class="col-auto subtotal">
                    Subtotal: R$ {{number_format($session_cart->map(function($query){return $query['quantity']*$query['price'];})->sum() , 2, ',', '.')}}
                </div>
                <div class="col-auto frete">
                    Frete: R$ 0,00
                </div>
                <div class="col-auto desconto">
                    Desconto: R$ 0,00
                </div>
                <div class="col-auto total">
                    Total: R$ {{number_format($session_cart->map(function($query){return $query['quantity']*$query['price'];})->sum() , 2, ',', '.')}}
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <div style="width: 20%;">
                    <a href="{{ route('cart') }}" class="btn btn-c-outline-success">Voltar</a>
                </div>
                <div style="width: 20%;">
                    <button type="button" class="btn btn-c-outline-success btn-criar-modalidade">Continuar</button>
                </div>
            </div>
       </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            var session_coupon = {};

            setTimeout(() => {
                $('[name="endereco"]').trigger('change');
                setTimeout(() => {
                    $('.btn-consultar-cep').trigger('click');

                    window.session_coupon = JSON.parse(`{!! $cupon_calc !!}`);
                    setCoupons(JSON.parse(`{!! $cupon_calc !!}`));
                }, 100);
            }, 100);

            $('[name="endereco"]').on('change', function(){
                if($(this).val() == 'new_address'){
                    $('[name="zip_code"]').val('').removeClass('d-none');

                    $('[name="address"]').val('');
                    $('[name="complement"]').val('');
                    $('[name="number"]').val('');
                    $('[name="address2"]').val('');
                    $('[name="state"]').val('');
                    $('[name="city"]').val('');
                    $('[name="phone"]').val('');
                }else{
                    let address = $(this).find('option:selected').data('address_json');
                    $('[name="zip_code"]').val($(this).val());

                    $('[name="address"]').val(address.address);
                    $('[name="complement"]').val(address.complement);
                    $('[name="number"]').val(address.number);
                    $('[name="address2"]').val(address.address2);
                    $('[name="state"]').val(address.state);
                    $('[name="city"]').val(address.city);
                    $('[name="phone"]').val(address.phone2);
                }
            });

            $('.btn-consultar-cep').on('click', function(){
                let zip_code = $('[name="zip_code"]').val();
                if(zip_code.length == 9){
                    $('.spinner-cep-div').removeClass('d-none');
                    $.ajax({
                        url: "{{ route('freteCheckout') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            zip_code: zip_code
                        },
                        success: function(data){
                            $('.spinner-cep-div').addClass('d-none');
                            if(data.success){
                                for(i in data.html){
                                    
                                    $('#seller-'+i+' .body-modalidades').html(data.html[i]);
                                }

                                if($('[name="endereco"]').val() == 'new_address'){
                                    $('[name="address"]').val(data.cep_consulta.logradouro);
                                    $('[name="complement"]').val('');
                                    $('[name="number"]').val('');
                                    $('[name="address2"]').val(data.cep_consulta.bairro);
                                    $('[name="state"]').val(data.cep_consulta.uf);
                                    $('[name="city"]').val(data.cep_consulta.localidade);
                                    $('[name="phone"]').val('');
                                }
                                // $('.div-modalidades').html(data.html);
                            }else{
                                alert('CEP não encontrado');
                            }
                        }
                    });
                }else{
                    alert('CEP inválido');
                }
            });

            $(document).on('click', '.swal-aplicar-cupom', function(){
                Swal.fire({
                    title: 'Aplicar Cupom',
                    html: '<input type="text" class="form-control text-uppercase" id="swal-cupom" placeholder="Código do Cupom">',
                    showCancelButton: true,
                    confirmButtonText: 'Aplicar',
                    cancelButtonText: 'Fechar',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch('{{ route("aplicarCupom") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                code_coupon: $('#swal-cupom').val()
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText)
                            }
                            return response.json()
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error}`
                            )
                        })
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    var response = result.value;
                    if (result.isConfirmed) {
                        if(result.value.success){
                            Swal.fire({
                                icon: 'success',
                                title: 'Cupom Aplicado com sucesso!'
                            }).then((result)=>{
                                var discount_total = 0;
                                var coupon = response.coupon;

                                window.session_coupon = coupon;

                               setCoupons(coupon);
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: result.value.statusText || 'Cupom inválido!'
                            });
                        }
                    }
                });
            });

            $(document).on('change', '.check-fretes', function(){
                let seller_id = $(this).data('seller_id');
                let transporte = $(this).data('transporte');
                let total = 0;
                let frete = 0;
                let subtotal = 0;
                let total_geral = 0;

                if($(this).is(':checked')){
                    $('.check-fretes').each(function(){
                        if($(this).data('seller_id') == seller_id){
                            $(this).prop('checked', false);
                        }
                    });
                    $(this).prop('checked', true);
                    frete = parseFloat($(this).parent().parent().parent().find('.price').text().replace('R$ ','').replace('.','').replace(',','.') || 0);
                    subtotal = parseFloat($('#seller-'+seller_id+' .footer .subtotal').text().replace('R$ ','').replace('.','').replace(',','.'));
                    total = frete + subtotal;
                    $('#seller-'+seller_id+' .footer .frete').text('R$ '+frete.toFixed(2).replace('.',','));
                    $('#seller-'+seller_id+' .footer .total').text('R$ '+total.toFixed(2).replace('.',','));
                }else{
                    $('#seller-'+seller_id+' .footer .frete').text('R$ 0,00');
                    $('#seller-'+seller_id+' .footer .total').text('R$ '+subtotal.toFixed(2).replace('.',','));
                }

                $('.div-modalidades').attr('data-check', 'true');

                var total_fretes = 0;
                $('.modalidade-seller .body-modalidades .div-fretes .price').each(function(){
                    if($(this).parent().find('.check-fretes').is(':checked')){
                        total_fretes += parseFloat($(this).text().replace('R$ ','').replace('.','').replace(',','.') || 0);
                    }
                });
                $('.div-total .frete').text('Frete: R$ '+total_fretes.toFixed(2).replace('.',','));

                total_geral = parseFloat($('.div-total .subtotal').text().replace('Subtotal: R$ ','').replace('.','').replace(',','.')) + total_fretes;
                $('.div-total .total').text('R$ '+total_geral.toFixed(2).replace('.',','));

                var div_modalidades = 0;
                $('.div-modalidades').each(function(){
                    if($(this).attr('data-check') == 'true'){
                        div_modalidades++;
                    }
                });

                if(div_modalidades == $('.div-modalidades').length){
                    $('.btn-criar-modalidade').removeClass('btn-c-outline-success').addClass('btn-c-success');
                }else{
                    $('.btn-criar-modalidade').removeClass('btn-c-success').addClass('btn-c-outline-success');
                }

                setCoupons(window.session_coupon);
            });

            $(document).on('click', '.btn-criar-modalidade', function(){
                var div_modalidades = 0;
                $('.div-modalidades').each(function(){
                    if($(this).attr('data-check') == 'true'){
                        div_modalidades++;
                    }
                });

                if(div_modalidades < $('.div-modalidades').length){
                    alert('Selecione um frete');
                    return false;
                }

                var fretes = {};
                $('.modalidade-seller .body-modalidades .div-fretes .check-fretes').each(function(){
                    if($(this).is(':checked')){
                        fretes[$(this).data('seller_id')] = {
                            'tipo': $(this).data('tipo'),
                            'frete': $(this).data('transporte')
                        };
                    }
                });

                var address = {
                    'zip_code': $('[name="zip_code"]').val(),
                    'address': $('[name="address"]').val(),
                    'complement': $('[name="complement"]').val(),
                    'number': $('[name="number"]').val(),
                    'address2': $('[name="address2"]').val(),
                    'state': $('[name="state"]').val(),
                    'city': $('[name="city"]').val(),
                    'phone2': $('[name="phone"]').val(),
                };

                $.ajax({
                    url: "{{ route('checkout.createSessionModalidade') }}",
                    type: 'POST',
                    data: {
                        fretes: fretes,
                        address: address,
                    },
                    success: function(data){
                        if(data.success){
                            window.location.href = "{{ route('checkout') }}";
                        }else{
                            alert('Erro ao criar modalidade');
                        }
                    }
                });
            });
        });

        function setCoupons(coupon){
            console.log(coupon);
            if((coupon.ftv || 'p') == 'free'){
                var frete = $(`#seller-${coupon.seller_id}`).find('.body-produtos').find('.frete').text();
                frete = parseFloat(frete.replace('R$ ','').replace('.','').replace(',','.'));

                $('.div-total .desconto').text('Desconto: R$ '+parseFloat(frete).toFixed(2).replace('.',','));

                var frete_total = parseFloat($('.div-total .frete').text().replace('Frete: R$ ','').replace('.','').replace(',','.')) - frete;

                var total_geral = parseFloat($('.div-total .subtotal').text().replace('Subtotal: R$ ','').replace('.','').replace(',','.')) + frete_total;
                $('.div-total .total').text('R$ '+total_geral.toFixed(2).replace('.',','));
            }else if((coupon.ftv || 'p') == 'discount'){
                var frete = $(`#seller-${coupon.seller_id}`).find('.body-produtos').find('.frete').text();
                frete = parseFloat(frete.replace('R$ ','').replace('.','').replace(',','.'));

                var discount = 0;
                if(coupon.ftc == 'porcentage'){
                    discount = (frete * coupon.ftd / 100);
                }else if(coupon.ftc == 'money'){
                    discount = coupon.ftd;
                }

                $('.div-total .desconto').text('Desconto: R$ '+parseFloat(discount).toFixed(2).replace('.',','));

                var frete_total = parseFloat($('.div-total .frete').text().replace('Frete: R$ ','').replace('.','').replace(',','.')) - discount;

                var total_geral = parseFloat($('.div-total .subtotal').text().replace('Subtotal: R$ ','').replace('.','').replace(',','.')) + frete_total;
                $('.div-total .total').text('R$ '+total_geral.toFixed(2).replace('.',','));
            }else{
                if(coupon.dp  || null){
                    $('.div-total .desconto').text('Desconto: R$ '+parseFloat(coupon.dp).toFixed(2).replace('.',','));

                    var total_geral = parseFloat($('.div-total .subtotal').text().replace('Subtotal: R$ ','').replace('.','').replace(',','.')) - coupon.dp;
                    $('.div-total .total').text('R$ '+total_geral.toFixed(2).replace('.',','));
                }
            }
        }
    </script>
@endsection