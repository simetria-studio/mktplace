@extends('layouts.site')

@section('container')
    <div class="container-fluid index-cart">
       <div class="container-fluid pt-3">
            <div style="margin-bottom: 20px;font-size: ">Seu carrinho</div>

            <div style="margin-bottom: 20px;width: 20%;">
                <a href="{{route('clearCart')}}" class="btn btn-c-outline-danger">Limpar Carrinho</a>
            </div>

            <div class="div-cart-line d-flex align-items-center">
                <div class="d-flex align-items-center" style="width: 50%;">
                    <div class="custom-checkbox">
                        <input type="checkbox" id="checkTodosProdutos">
                    </div>
                    <span class="ml-3">Produtos</span>
                </div>
                <div style="width: 15%;">Quantidade</div>
                <div style="width: 25%;">Preço</div>
                <div style="width: 10%;">Ações</div>
            </div>

            @foreach (cart_show()->content as $cart_content)
                <div class="div-cart-line-2 d-flex align-items-center">
                    <div style="width: 50%;">
                        <div class="custom-checkbox" style="top: -45px">
                            <input type="checkbox" class="check-produtos" data-row_id="{{auth('web')->check() ? $cart_content->row_id : $cart_content->id}}" id="checkProduto{{ $cart_content->id }}">
                        </div>
                        <div class="ml-3" style="height: 120px;display: inline-block;">
                            <img style="width: 120px;height: 100%;object-fit: cover;" class="img-fluid" src="{{$cart_content->attributes->product_image}}" alt="">
                        </div>
                        <span class="ml-3">{{$cart_content->name}}</span>
                    </div>
                    <div style="width: 15%;">
                        <div class="number-input">
                            <input class="quantity qty_new" min="0" name="quantity" data-price="{{$cart_content->price}}" data-var_id="{{$cart_content->attributes->var_id ?? 0}}" data-product_id="{{$cart_content->attributes->product_id}}" data-row_id="{{$cart_content->id}}" value="{{$cart_content->quantity}}" type="number">
                            <div class="number-input-controls">
                                <button class="number-input-button up btn-qty-plus">
                                    <i class="fas fa-angle-up"></i>
                                </button>
                                <button class="number-input-button down btn-qty-minus">
                                    <i class="fas fa-angle-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div style="width: 25%;" class="subtotal"><strong>R$ {{number_format(($cart_content->quantity * $cart_content->price) , 2, ',', '.')}}</strong></div>
                    <div style="width: 10%;">
                        <div class="btn-delete-product" data-json_gtag="{{collect($cart_content)->toJson()}}" data-row_id="{{auth('web')->check() ? $cart_content->row_id : $cart_content->id}}" data-repagina="sim">Excluir</div>
                    </div>
                </div>
            @endforeach

            <div class="div-cart-line d-flex align-items-center">
                <div class="d-flex align-items-center" style="width: 65%;">
                    Produtos selecionados: <span class="ml-2 quantity-total">{{ cart_show()->quantidade }}</span>
                </div>
                <div style="width: 35%;">
                    Subtotal: <span class="sub-total">R$ {{number_format(cart_show()->total , 2, ',', '.')}}</span>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <div style="width: 20%;">
                    <a href="{{ route('home') }}" class="btn btn-c-outline-success">Voltar</a>
                </div>
                <div style="width: 20%;">
                    <button type="button" style="width: 100%;" class="btn btn-c-outline-success btn-criar-sessao-cart" data-auth_check="{{auth()->guard('web')->check() ? 'true' : 'false'}}">Continuar</button>
                </div>
            </div>
       </div>
    </div>

    <div class="modal fade" id="modalLoginRegister" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalLoginRegisterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title" id="modalLoginRegisterLabel">
                        <h5>Fazer Login/Registro</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#formModalLogin" aria-expanded="false" aria-controls="formModalLogin">
                            Fazer Login
                        </button>
                        <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#formModalRegister" aria-expanded="false" aria-controls="formModalRegister">
                            Criar Conta
                        </button>
                    </p>
                    <form action="{{route('login')}}" method="post" class="collapse show" id="formModalLogin">
                        <div class="row justify-content-center">
                            <div class="col-12 mb-5 text-center">
                                <h2>Login do Comprador</h2>
                            </div>

                            <div class="form-group col-10">
                                <input type="email" name="email" class="form-control" placeholder="Email do Usuário">
                            </div>
                            <div class="form-group col-10">
                                <input type="password" name="password" class="form-control" placeholder="Senha do Usuário">
                            </div>

                            <div class="form-group col-10 text-center">
                                Esqueceu a senha? <a class="link" href="{{route('password.request')}}">Clique aqui!</a>
                            </div>

                            <div class="form-group col-10 text-center">
                                <button type="button" class="btn btn-c-primary btn-login-register">ENTRAR</button>
                            </div>
                        </div>
                    </form>
                    <form action="{{route('register')}}" method="post" class="collapse" id="formModalRegister">
                        <div class="row justify-content-center">
                            <div class="col-12 mb-5 text-center">
                                <h2>Registro do Comprador</h2>
                            </div>
                            <div class="form-group col-10">
                                <input type="text" name="name" class="form-control" placeholder="Nome Completo">
                            </div>
                            <div class="form-group col-10">
                                <input type="email" name="email" class="form-control" placeholder="Email de Usuário">
                            </div>
                            <div class="form-group col-10">
                                <input type="text" name="cnpj_cpf" class="form-control" placeholder="CNPJ/CPF">
                            </div>
                            <div class="form-group col-10">
                                <span class="text-muted">A senha deve conter no mínimo 8 caracteres</span>
                                <input type="password" name="password" class="form-control" placeholder="Senha de Usuário">
                            </div>
                            <div class="form-group col-10">
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar Senha">
                            </div>

                            <div class="form-group col-10">
                                <input type="checkbox" class="@error('terms') is-invalid @enderror" name="terms" id="terms">
                                <label for="terms"><a href="{{route('privacypolicy')}}">Política de Privacidade</a> e <a href="{{route('termsofuse')}}">Termos de Uso</a></label>
                                @error('terms')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-10 text-center">
                                <button type="button" class="btn btn-c-primary btn-login-register">REGISTRAR</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            setTimeout(() => {
                $('#checkTodosProdutos').trigger('click');
            }, 100);

            $(document).on('change', '#checkTodosProdutos', function(){
                if($(this).is(':checked')){
                    $('.check-produtos').prop('checked', true).trigger('change');
                }else{
                    $('.check-produtos').prop('checked', false).trigger('change');
                }
            });

            $(document).on('change', '.check-produtos', function(){
                var count = 0;
                $('.check-produtos').each(function(){
                    if($(this).is(':checked')){
                        count++;
                    }
                });

                if(count === 0){
                    $('.btn-criar-sessao-cart').removeClass('btn-c-success').addClass('btn-c-outline-success');
                }
                if(count > 0){
                    $('.btn-criar-sessao-cart').removeClass('btn-c-outline-success').addClass('btn-c-success');
                }
            });

            $(document).on('click', '.btn-criar-sessao-cart', function(){
                var row_id = [];
                $('.check-produtos').each(function(){
                    if($(this).is(':checked')){
                        row_id.push($(this).data('row_id'));
                    }
                });

                if($(this).attr('data-auth_check') === 'false'){
                    $('#modalLoginRegister').modal('show');
                    return;
                }

                console.log('aaaaaaaaaaaa');

                if(row_id.length === 0){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selecione ao menos um produto'
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route("createSessionCart") }}',
                    type: 'POST',
                    data: {row_id},
                    success: (data) => {
                        window.location.href = '{{ route("checkout.modalidade") }}';
                    },
                    error: (err) => {
                        console.log(err);

                        Swal.fire({
                            icon: 'error',
                            title: err.responseJSON.error
                        });
                    }
                });
            });

            $(document).on('click', '.btn-login-register', function () {
                var btn = $(this);
                Swal.fire({
                    title: 'Carregando dados, aguarde...',
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
                    success: (data) => {
                        // console.log(data);
                        // window.location.href = $('.td-finalizar').attr('href');
                        // window.location.reload();
                        $('#modalLoginRegister').modal('hide');
                        $('.btn-criar-sessao-cart').attr('data-auth_check', 'true');
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login efetuado com sucesso!'
                            });
                            $('.btn-criar-sessao-cart').trigger('click');
                        }, 1000);
                    },
                    error: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Parece que houve um problema ao carregar os dados, tente de novo!'
                        });
                    }
                });
            });

            // Controle de quantidade
            var count_time_out_qty = setTimeout(() => { }, 600);
            $(document).on('keyup', '.qty_new', function () {
                var subtotal = $(this).closest('.div-cart-line-2').find('.subtotal');
                var qty_new = parseInt($(this).val()) || 0;
                var row_id = $(this).data('row_id');
                var product_id = $(this).data('product_id');
                var var_id = $(this).data('var_id');
                var price = parseFloat($(this).data('price')) || 0;
                clearTimeout(count_time_out_qty);

                count_time_out_qty = setTimeout(() => {
                    $.ajax({
                        url: "/add-cart-qty",
                        type: 'POST',
                        data: { qty_new, row_id, product_id, var_id },
                        success: (data) => {
                            $(this).parent().parent().find('td').eq(2).html(`R$ ${parseFloat(data.new_value).toFixed(2).toString().replace('.', ',')}`);
                            subtotal.html(`<strong>R$ ${(qty_new * parseFloat(data.new_value)).toFixed(2).toString().replace('.', ',')}</strong>`);
                            $('.total-frete').html('<strong>R$ 0,00</strong>');

                            var subtotal_v = 0;
                            $('.subtotal').each(function () {
                                subtotal_v += parseFloat($(this).text().replace(',', '.').replace('R$ ', '')) || 0;
                            });

                            var qty = 0;
                            $('.quantity').each(function () {
                                qty += parseInt($(this).val() || 0);
                            });

                            $('.quantity-total').html(`${qty}`);
                            $('.sub-total').html(`R$ ${subtotal_v.toFixed(2).toString().replace('.', ',')}`);
                        },
                        error: (err) => {
                            console.log(err);

                            Swal.fire({
                                icon: 'error',
                                title: err.responseJSON.error
                            });
                        }
                    });
                }, 600);
            });
            $(document).on('click', '.btn-qty-plus', function () {
                var number = parseFloat($(this).closest('.number-input').find('[type="number"]').val());
                $(this).closest('.number-input').find('[type="number"]').val(number + 1).trigger('keyup');
            });
            $(document).on('click', '.btn-qty-minus', function () {
                var number = parseFloat($(this).closest('.number-input').find('[type="number"]').val());
                if (number > 1) {
                    $(this).closest('.number-input').find('[type="number"]').val(number - 1).trigger('keyup');
                }
            });
        });
    </script>
@endsection