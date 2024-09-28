@extends('layouts.site')

@section('container')
    <form action="{{route('checkout')}}" method="post">
        @csrf
        <div class="container-fluid index-checkout">
            <div class="container-fluid pt-3">
                <div class="d-flex div-checkout">
                    <div class="checkout">
                        <div class="checkout-metodo">
                            <div><b>Selecione a forma de pagamento:</b></div>
                            <div class="payment_method">
                                <div>
                                    <div class="custom-radio">
                                        <input type="radio" id="boleto" value="boleto" name="payment_method">
                                        <label for="boleto"><b>Boleto</b></label>
                                    </div>
                                </div>
                                <div>
                                    <div class="custom-radio">
                                        <input type="radio" id="cartao_credito" value="cartao_credito" name="payment_method">
                                        <label for="cartao_credito"><b>Cartão de crédito</b></label>
                                    </div>
                                </div>
                                <div>
                                    <div class="custom-radio">
                                        <input type="radio" id="pix" value="pix" name="payment_method">
                                        <label for="pix"><b>Pix</b></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="checkout-cartao-credito d-none">
                            <div class="card-titulo">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-6">
                                        <b>Cartão de Crédito</b>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout-credit-card-row row justify-content-center">
                                <div class="col-12 col-md-6">
                                    <div class="row justify-content-around">
                                        <div class="col-12">
                                            <label for="card_holder_name">Nome do titular do cartão</label>
                                            <input type="text" class="form-control" name="card_holder_name">
                                        </div>
                                        <div class="col-12">
                                            <label for="card_number">Número do cartão</label>
                                            <input type="text" class="form-control" name="card_number">
                                        </div>
                                        <div class="col-12 col-md-9">
                                            <label>Validade do cartão MM/AAAA</label>
                                            <div class="input-group">
                                                <select class="form-control" name="card_month">
                                                    <optgroup label="Mês">
                                                        <option value="01" selected="">01</option>
                                                        <option value="02">02</option>
                                                        <option value="03">03</option>
                                                        <option value="04">04</option>
                                                        <option value="05">05</option>
                                                        <option value="06">06</option>
                                                        <option value="07">07</option>
                                                        <option value="08">08</option>
                                                        <option value="09">09</option>
                                                        <option value="10">10</option>
                                                        <option value="11">11</option>
                                                        <option value="12">12</option>
                                                    </optgroup>
                                                </select>
                                                <select class="form-control" name="card_year">
                                                    <optgroup label="Ano">
                                                        <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                                        @for ($i = 1; $i < 20; $i++)
                                                            <option value="{{ date('Y', strtotime("+$i Years")) }}">{{ date('Y', strtotime("+$i Years")) }}</option>
                                                        @endfor
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <label for="card_cvv">CVV</label>
                                            <input type="text" class="form-control" name="card_cvv">
                                        </div>
                                        <div class="col-12 d-none">
                                            <label for="installments">Parcelamento</label>
                                            <input type="text" class="form-control" name="installments" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="address-titulo">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-6">
                                        <b>Endereço de Cobrança</b>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout-address-row row justify-content-center mb-5">
                                <div class="col-12 col-md-6">
                                    <div class="row justify-content-around">
                                        <div class="col-12">
                                            <label for="name">Nome completo</label>
                                            <input type="text" class="form-control" name="name" value="{{ auth('web')->user()->name }}">
                                        </div>
                                        <div class="col-6">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" name="email" value="{{ auth('web')->user()->email }}">
                                        </div>
                                        <div class="col-6">
                                            <label for="cnpj_cpf">CPF/CNPJ</label>
                                            <input type="text" class="form-control" name="cnpj_cpf" value="{{ auth('web')->user()->cnpj_cpf }}">
                                        </div>
                                        <div class="col-3">
                                            <label for="zip_code">CEP</label>
                                            <input type="text" class="form-control" name="zip_code" value="{{ $session_address['zip_code'] }}">
                                        </div>
                                        <div class="col-3">
                                            <label for="address_number">Num. do end.</label>
                                            <input type="text" class="form-control" name="address_number">
                                        </div>
                                        <div class="col-6">
                                            <label for="phone">Celular</label>
                                            <input type="text" class="form-control" name="phone">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="checkout-boleto-pix d-none">
                            //...
                        </div>
                    </div>
                    <div class="checkout-resumo">
                        <div class="">
                            <div class="checkout-titulo"><b>Resumo de compra</b></div>
                            @foreach ($session_cart as $item)
                                <div class="itens-checkout">
                                    <div>{{ $item['name'] }}</div>
                                    <div>{{ $item['quantity'] }} x {{ number_format($item['price'], 2, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex flex-column align-items-center">
                            <div class="sub-checkout">
                                <div>SubTotal</div>
                                <div>{{ number_format(collect($session_cart)->map(function($q){return $q['quantity']*$q['price'];})->sum(), 2, ',', '.') }}</div>
                            </div>
                            <div class="sub-checkout">
                                <div>Frete</div>
                                <div>{{ number_format($total_frete, 2, ',', '.') }}</div>
                            </div>
                            <div class="sub-checkout">
                                <div>Desconto</div>
                                <div>{{ number_format($discount, 2, ',', '.') }}</div>
                            </div>
                            <div class="sub-checkout">
                                <div>Total</div>
                                <div>{{ number_format($total_checkout, 2, ',', '.') }}</div>
                            </div>

                            <div class="col-12 col-md-8">
                                <button type="button" class="btn btn-sm btn-block btn-success btn-finalizar-pagamento">Pagar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script>
        $(document).ready(function(){
            $(`[name="card_number"]`).mask('0000 0000 0000 0000');

            $('input[name="payment_method"]').change(function(){
                if($(this).val() == 'cartao_credito'){
                    $('.checkout-cartao-credito').removeClass('d-none');
                    // $('.checkout-boleto-pix').addClass('d-none');
                }else{
                    $('.checkout-cartao-credito').addClass('d-none');
                    // $('.checkout-boleto-pix').removeClass('d-none');
                }
            });

            $(document).on('click', '.btn-finalizar-pagamento', function(){
                var form_data = $(this).closest('form').serialize();

                Swal.fire({
                    title: 'Gerando pagamento, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: $(this).closest('form').attr('action'),
                    type: 'POST',
                    data: form_data,
                    success: function(data){
                        // console.log(data);

                        if(data.success){
                            var msg = `Pagamento efetuado com sucesso! redirecionando para pagina...`;
                        
                            if($('input[name="payment_method"]:checked').val() == 'boleto'){
                                var msg = `Boleto gerado com sucesso! redirecionando para pagina...`;
                            }else if($('input[name="payment_method"]:checked').val() == 'pix'){
                                var msg
                            }

                            Swal.fire({
                                icon: 'success',
                                title: msg,
                                showConfirmButton: false,
                                timer: 1500
                            }).then((result) => {
                                window.location.href = data.redirect;
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao efetuar pagamento',
                                text: data.msg,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: (err) => {
                        Swal.close();
                    }
                });
            });
        });
    </script>
@endsection
