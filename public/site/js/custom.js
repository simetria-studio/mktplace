$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('[data-toggle="popover"]').popover();

    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', '.click-img-view', function () {
        $('.click-img-view').removeClass('active-img');
        $(this).addClass('active-img');
        var src_img = $(this).find('img').attr('src');
        var alt_img = $(this).find('img').attr('alt');
        $('#view_img').attr('src', src_img);
        $('#view_img').attr('alt', alt_img);
    });

    $(document).on('keydown', '.type-number', function (e) {
        if (e.keyCode == 38) {
            $(this).val(parseInt($(this).val()) + 1);
        } else if (e.keyCode == 40) {
            if (parseInt($(this).val()) > 1) {
                $(this).val(parseInt($(this).val()) - 1);
            }
        }
    });

    $('.real').mask('000.000,00', { reverse: true });

    $('[name="quantidade"]').mask('0000');

    $('[name="phone1"]').mask('(00) 0000-0000');
    $('[name="phone2"]').mask('(00) 00000-0000');
    $('[name="birth_date"]').mask('00/00/0000');
    $('[name="postal_code"]').mask('00000-000');
    $('[name="post_code"]').mask('00000-000');
    $('[name="zip_code"]').mask('00000-000');
    $('.select2').select2();

    $('[name="card_number"]').mask('0000 0000 0000 0000');
    $('[name="card_expiration_month_year"]').mask('00/0000');
    $('[name="card_cvv"]').mask('000');

    var options = {
        onKeyPress: function (cpf, ev, el, op) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            $('[name="cnpj_cpf"]').mask((cpf.length > 14) ? masks[1] : masks[0], op);
        }
    }
    var options2 = {
        onKeyPress: function (cpf, ev, el, op) {
            var masks = ['000000000000', '00000000000000'];
            $('[name="cnpj_cpf2"]').mask((cpf.length > 11) ? masks[1] : masks[0], op);
        }
    }
    $('[name="cnpj_cpf"]').length > 11 ? $('[name="cnpj_cpf"]').mask('00.000.000/0000-00', options) : $('[name="cnpj_cpf"]').mask('000.000.000-00#', options);
    $('[name="cnpj_cpf2"]').length > 11 ? $('[name="cnpj_cpf2"]').mask('00000000000000', options2) : $('[name="cnpj_cpf2"]').mask('00000000000#', options2);
    $('[name="cpf"]').mask('000.000.000-00');

    // Aciona a validação ao sair do input
    $('[name="cnpj_cpf"], [name="cnpj_cpf2"], [name="cpf"]').blur(function () {
        var thiss = $(this);

        // O CPF ou CNPJ
        var cpf_cnpj = $(this).val();

        if (cpf_cnpj) {
            // Testa a validação
            if (valida_cpf_cnpj(cpf_cnpj)) {

            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'CNPJ/CPF informado invalido!',
                }).then((result) => {
                    // thiss.focus();
                });
            }
        }
    });

    // Telefone/Celeular
    var behavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
        options = {
            onKeyPress: function (val, e, field, options) {
                field.mask(behavior.apply({}, arguments), options);
            }
        };
    $('[name="phone"]').mask(behavior, options);

    // Busca dos estados
    $(function () {
        if ($('[name="state"]')) {
            $.ajax({
                url: 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/',
                type: 'GET',
                success: (data) => {
                    // console.log(data);
                    for (var i = 0; data.length > i; i++) {
                        $('[name="state"]').append('<option value="' + data[i].sigla + '" data-sigla_id="' + data[i].id + '">' + data[i].sigla + ' - ' + data[i].nome + '</option>');
                    }
                }
            });
        }
    });

    // // Busca das cidades/municipios
    $(document).on('change', '[name="state"]', function () {
        let sigla_id = $(this).find(':selected').data('sigla_id');
        let select = $(this).parent().parent().find('select[name="city"]');

        $.ajax({
            url: 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/' + sigla_id + '/municipios',
            type: 'GET',
            success: (data) => {
                // console.log(data);
                select.empty();
                select.append('<option value="">::Selecione uma Opção::</option>');

                for (var i = 0; data.length > i; i++) {
                    select.append('<option value="' + data[i].nome + '">' + data[i].nome + '</option>');
                }
            }
        });
    });

    $('[name="post_code"]').on('keyup blur', function () {
        $(this).parent().parent().find('input, select').attr('readonly', false);
        $(this).parent().parent().find('input, select').trigger('change');

        if ($(this).val().length == 9) {
            $('.loadCep').removeClass('d-none');
            $.ajax({
                url: '/cep/' + $(this).val(),
                type: 'GET',
                success: (data) => {
                    $('[name="address"]').val(data.logradouro);
                    if (data.logradouro) $('[name="address"]').prop('readonly', true);
                    $('[name="address2"]').val(data.bairro);
                    if (data.bairro) $('[name="address2"]').prop('readonly', true);
                    $('[name="state"]').val(data.uf);
                    if (data.uf) {
                        $('[name="state"]').attr('readonly', true);
                        $('[name="state"]').trigger('change');
                    }
                    setTimeout(() => {
                        $('[name="city"]').val(data.localidade);
                        if (data.localidade) {
                            $('[name="city"]').attr('readonly', true);
                            $('[name="city"]').trigger('change');
                        }
                    }, 800);

                    $('[name="number"]').focus();
                    $('.loadCep').addClass('d-none');
                }
            });
        }
    });

    // $('[name="zip_code"]').on('keyup blur', function () {
    //     if ($(this).val().length == 9) {
    //         $('.spinner-cep-div').removeClass('d-none');
    //         $.ajax({
    //             url: '/freteCheckout/' + $(this).val(),
    //             type: 'GET',
    //             success: (data) => {
    //                 console.log(data);

    //                 $('.btn-frete').removeClass('d-none');
    //                 $('.fretes').empty();
    //                 var div = '';
    //                 var total_value = 0;
    //                 $.each(data, (keyOne, valueOne) => {
    //                     $('[name="address"]').val(valueOne.ceps.logradouro);
    //                     if (valueOne.ceps.logradouro) $('[name="address"]').prop('readonly', true);
    //                     $('[name="address2"]').val(valueOne.ceps.bairro);
    //                     if (valueOne.ceps.bairro) $('[name="address2"]').prop('readonly', true);
    //                     $('[name="state"]').val(valueOne.ceps.uf);
    //                     if (valueOne.ceps.uf) $('[name="state"]').prop('readonly', true);
    //                     $('[name="city"]').val(valueOne.ceps.localidade);
    //                     if (valueOne.ceps.localidade) $('[name="city"]').prop('readonly', true);

    //                     $('[name="number"]').focus();

    //                     var sub_total = 0;
    //                     div = '<div class="row frete border-bottom border-dark pb-3">';
    //                     for (var i = 0; i < valueOne.itens.length; i++) {
    //                         sub_total += (parseInt(valueOne.itens[i].quantity) * parseFloat(valueOne.itens[i].price));
    //                         div +=
    //                             '<div class="col-8" style="font-size:.8rem;">' + valueOne.itens[i].name + '</div>' +
    //                             '<div class="col-4 text-right" style="font-size:.8rem;">' + valueOne.itens[i].quantity + ' x R$ ' + (parseFloat(valueOne.itens[i].price).toFixed(2).replace('.', ',')) + '</div>'
    //                             ;
    //                     }

    //                     div += '<input type="hidden" class="sub_total" value="' + sub_total + '">';

    //                     div += '<div class="col-12">';
    //                     div += '<div class="row border mx-2 py-3 rounded">';
    //                     // Tranporte proprio
    //                     for (var i = 0; i < valueOne.transporte_proprio.length; i++) {
    //                         var valor_entrega = valueOne.transporte_proprio[i].valor_entrega;
    //                         if (valueOne.transporte_proprio[i].frete_gratis == 1) {
    //                             if (sub_total > valueOne.transporte_proprio[i].valor_minimo) valor_entrega = 0;
    //                         }
    //                         var entrega = 'Entrega Até ' + (valueOne.transporte_proprio[i].tempo_entrega) + (valueOne.transporte_proprio[i].tempo == 'H' ? ' Horas' : ' Dias Uteis') + '.';
    //                         if (valueOne.transporte_proprio[i].tempo == 'S') entrega = 'Entrega nos dias da semana <br>' + (diaSemana(valueOne.transporte_proprio[i].semana)).join(' - ');
    //                         if (valueOne.transporte_proprio[i].tempo == 'C') entrega = '';
    //                         div +=
    //                             '<div class="col-5 my-1">' +
    //                             '<input type="hidden" class="sub_total" value="' + sub_total + '">' +
    //                             '<input type="radio" class="click-frete radio" data-frete_id="' + keyOne + '" name="frete[' + keyOne + '][type]" value="proprio"> ' +
    //                             '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][service_id]" value="' + valueOne.transporte_proprio[i].id + '"> ' +
    //                             '<input disabled class="price input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][price]" value="' + valor_entrega + '">' +
    //                             '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][dados_gerais]" value=\'' + JSON.stringify(valueOne.transporte_proprio[i]) + '\'> <label><b style="font-size:.9rem;">Transporte Próprio</b></label>' +
    //                             '</div>' +
    //                             '<div class="col-4 my-1" style="font-size: .8rem;">' + entrega + '</div>' +
    //                             '<div class="col-3 my-1">' + (valor_entrega > 0 ? 'R$ ' : '') + (valor_entrega > 0 ? parseFloat(valor_entrega).toFixed(2).replace('.', ',') : 'Frete Grátis') + '</div>' +
    //                             '<div class="col-12 border-bottom pb-2">' + (valueOne.transporte_proprio[i].descricao ? 'Obs: ' + valueOne.transporte_proprio[i].descricao : '') + '</div>'
    //                             ;
    //                     }
    //                     if (valueOne.store.retirada) {
    //                         if (valueOne.store.retirada == 'true') {
    //                             div +=
    //                                 '<div class="col-7 my-1">' +
    //                                 '<input type="hidden" class="sub_total" value="' + sub_total + '">' +
    //                                 '<input type="radio" class="click-frete radio" data-frete_id="' + keyOne + '" name="frete[' + keyOne + '][type]" value="retirada"> ' +
    //                                 '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][service_id]" value="rsa-retirada"> ' +
    //                                 '<input disabled class="price input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][price]" value="0">' +
    //                                 '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][dados_gerais]" value=\'' + (JSON.stringify(valueOne.store)) + '\'> <label><b style="font-size:.9rem;">Retirar na Loja</b></label>' +
    //                                 '</div>' +
    //                                 '<div class="col-5 my-1">Grátis</div>' +
    //                                 '<div class="col-12 border-bottom pb-2">Obs: ' + (valueOne.store.ob_retirada) + '</div>'
    //                                 ;
    //                         }
    //                     }

    //                     for (var i = 0; i < valueOne.transportadoras.length; i++) {
    //                         if (!valueOne.transportadoras[i].error) {
    //                             div +=
    //                                 '<div class="col-12 col-md-3 my-1"><img class="img-fluid" src="' + valueOne.transportadoras[i].company.picture + '"></div>' +
    //                                 '<div class="col-4 col-md-3 my-1">' +
    //                                 '<input type="hidden" class="sub_total" value="' + sub_total + '">' +
    //                                 '<input type="radio" class="click-frete radio" name="frete[' + keyOne + '][type]" value="correios"> ' +
    //                                 '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][transport_name]" value="' + valueOne.transportadoras[i].name + '"> ' +
    //                                 '<input disabled class="input-frete" type="hidden" name="frete[' + keyOne + '][company_id]" value="' + valueOne.transportadoras[i].company.id + '"> ' +
    //                                 '<input disabled class="input-frete" type="hidden" name="frete[' + keyOne + '][service_id]" value="' + valueOne.transportadoras[i].id + '"> ' +
    //                                 '<input disabled class="input-frete" type="hidden" name="frete[' + keyOne + '][packages]" value="' + btoa(JSON.stringify(valueOne.transportadoras[i].packages)) + '"> ' +
    //                                 // '<input disabled class="input-frete" type="hidden" name="frete['+keyOne+'][height]" value="'+valueOne.transportadoras[i].packages[0].dimensions.height+'"> '+
    //                                 // '<input disabled class="input-frete" type="hidden" name="frete['+keyOne+'][width]" value="'+valueOne.transportadoras[i].packages[0].dimensions.width+'"> '+
    //                                 // '<input disabled class="input-frete" type="hidden" name="frete['+keyOne+'][length]" value="'+valueOne.transportadoras[i].packages[0].dimensions.length+'"> '+
    //                                 // '<input disabled class="input-frete" type="hidden" name="frete['+keyOne+'][weight]" value="'+valueOne.transportadoras[i].packages[0].weight+'"> '+
    //                                 '<input disabled class="price input-frete" type="hidden" name="frete[' + keyOne + '][price]" value="' + valueOne.transportadoras[i].custom_price + '"> ' +
    //                                 '<input disabled class="input-frete" type="hidden" name="frete[' + keyOne + '][dados_gerais]" value=\'' + (JSON.stringify(valueOne.transportadoras[i])) + '\'>' +
    //                                 '<label style="font-size:.9rem;">' + valueOne.transportadoras[i].name + '</label>' +
    //                                 '</div>' +
    //                                 '<div class="col-4 col-md-3 my-1" style="font-size: .8rem;">Entrega até ' + (valueOne.transportadoras[i].custom_delivery_time + 1) + ' Dias úteis.</div>' +
    //                                 '<div class="col-4 col-md-3 my-1">R$ ' + parseFloat(valueOne.transportadoras[i].custom_price).toFixed(2).replace('.', ',') + '</div>'
    //                                 ;
    //                         }
    //                     }
    //                     div += '</div>';
    //                     div += '</div>';
    //                     div += '<div class="col-6 my-2">Sub Total</div>';
    //                     div += '<div class="col-6 my-2 text-right sub-total">R$ ' + sub_total.toFixed(2).replace('.', ',') + '</div>';
    //                     div += '</div>';

    //                     $('.fretes').append(div);
    //                     total_value += sub_total;
    //                 });

    //                 $('.fretes').append(
    //                     '<div class="row my-2">' +
    //                     '<input type="hidden" class="total_value" value="' + total_value + '">' +
    //                     '<div class="col-6 my-2">Total</div>' +
    //                     '<div class="col-6 my-2 text-right total-value">R$ ' + total_value.toFixed(2).replace('.', ',') + '</div>' +
    //                     '</div>'
    //                 );

    //                 $('.spinner-cep-div').addClass('d-none');
    //             },
    //             error: (err) => {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'insira o cep correto do seu endereço'
    //                 });

    //                 $('.btn-frete').removeClass('d-none');
    //                 $('.fretes').empty();
    //                 var total_value = 0;

    //                 $('.fretes').append(
    //                     '<div class="row my-2">' +
    //                     '<input type="hidden" class="total_value" value="' + total_value + '">' +
    //                     '<div class="col-6 my-2">Total</div>' +
    //                     '<div class="col-6 my-2 text-right total-value">R$ ' + total_value.toFixed(2).replace('.', ',') + '</div>' +
    //                     '</div>'
    //                 );

    //                 $('.spinner-cep-div').addClass('d-none');
    //             }
    //         });
    //     }
    // });

    // $('[name="endereco"]').on('change', function () {
    //     var cep = null;
    //     $('.endereco').find('.endereço-pronto').removeClass('d-none');
    //     $('.btn-frete').addClass('d-none');
    //     $('.total-frete').html('R$ 0,00');
    //     $('.total').html($('.sub-total-cart').html());

    //     $('.endereco').find('[name="endereco"]').find('option[value="newAddress"]').remove();

    //     $.each($(this).find('option:selected').data('dados'), (key, value) => {
    //         $('.endereco').find('[name="' + key + '"]').val(value);
    //         $('.endereco').find('.' + key).val(value);
    //         $('.endereco').find('._' + key).val(value);
    //         $('.endereco').find('._' + key).html(value);

    //         if (value) {
    //             if (key !== 'phone1' && key !== 'phone2') {
    //                 $('.endereco').find('.' + key).parent().addClass('d-none');
    //                 $('.endereco').find('[name="' + key + '"]').parent().addClass('d-none');
    //             }
    //         } else {
    //             $('.endereco').find('[name="' + key + '"]').parent().removeClass('d-none');
    //         }

    //         if (key == 'post_code') cep = value;
    //     });
    //     $('.spinner-cep-div').removeClass('d-none');

    //     if ($('.zip-code-trigger').length > 0) {
    //         if ($('.zip-code-trigger').val() == cep) cep = null;
    //     }

    //     if (cep) {
    //         $.ajax({
    //             url: '/freteCheckout/' + cep,
    //             type: 'GET',
    //             success: (data) => {
    //                 // console.log(data);

    //                 $('.btn-frete').removeClass('d-none');
    //                 $('.fretes').empty();
    //                 var div = '';
    //                 var total_value = 0;
    //                 $.each(data, (keyOne, valueOne) => {
    //                     var sub_total = 0;
    //                     div = '<div class="row frete border-bottom border-dark pb-3">';
    //                     for (var i = 0; i < valueOne.itens.length; i++) {
    //                         sub_total += (parseInt(valueOne.itens[i].quantity) * parseFloat(valueOne.itens[i].price));
    //                         div +=
    //                             '<div class="col-8" style="font-size:.8rem;">' + valueOne.itens[i].name + '</div>' +
    //                             '<div class="col-4 text-right" style="font-size:.8rem;">' + valueOne.itens[i].quantity + ' x R$ ' + (parseFloat(valueOne.itens[i].price).toFixed(2).replace('.', ',')) + '</div>'
    //                             ;
    //                     }

    //                     div += '<input type="hidden" class="sub_total" value="' + sub_total + '">';

    //                     div += '<div class="col-12">';
    //                     div += '<div class="row border mx-2 py-3 rounded">';
    //                     // Tranporte proprio
    //                     for (var i = 0; i < valueOne.transporte_proprio.length; i++) {
    //                         var valor_entrega = valueOne.transporte_proprio[i].valor_entrega;
    //                         if (valueOne.transporte_proprio[i].frete_gratis == 1) {
    //                             if (sub_total > valueOne.transporte_proprio[i].valor_minimo) valor_entrega = 0;
    //                         }
    //                         var entrega = 'Entrega Até ' + (valueOne.transporte_proprio[i].tempo_entrega) + (valueOne.transporte_proprio[i].tempo == 'H' ? ' Horas' : ' Dias Uteis') + '.';
    //                         if (valueOne.transporte_proprio[i].tempo == 'S') entrega = 'Entrega nos dias da semana <br>' + (diaSemana(valueOne.transporte_proprio[i].semana)).join(' - ');
    //                         if (valueOne.transporte_proprio[i].tempo == 'C') entrega = '';
    //                         div +=
    //                             '<div class="col-5 my-1">' +
    //                             '<input type="hidden" class="sub_total" value="' + sub_total + '">' +
    //                             '<input type="radio" class="click-frete radio" data-frete_id="' + keyOne + '" name="frete[' + keyOne + '][type]" value="proprio"> ' +
    //                             '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][service_id]" value="' + valueOne.transporte_proprio[i].id + '"> ' +
    //                             '<input disabled class="price input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][price]" value="' + valor_entrega + '">' +
    //                             '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][dados_gerais]" value=\'' + JSON.stringify(valueOne.transporte_proprio[i]) + '\'> <label><b style="font-size:.9rem;">Transporte Próprio</b></label>' +
    //                             '</div>' +
    //                             '<div class="col-4 my-1" style="font-size: .8rem;">' + entrega + '</div>' +
    //                             '<div class="col-3 my-1">' + (valor_entrega > 0 ? 'R$ ' : '') + (valor_entrega > 0 ? parseFloat(valor_entrega).toFixed(2).replace('.', ',') : 'Frete Grátis') + '</div>' +
    //                             '<div class="col-12 border-bottom pb-2">' + (valueOne.transporte_proprio[i].descricao ? 'Obs: ' + valueOne.transporte_proprio[i].descricao : '') + '</div>'
    //                             ;
    //                     }
    //                     if (valueOne.store.retirada) {
    //                         if (valueOne.store.retirada == 'true') {
    //                             div +=
    //                                 '<div class="col-7 my-1">' +
    //                                 '<input type="hidden" class="sub_total" value="' + sub_total + '">' +
    //                                 '<input type="radio" class="click-frete radio" data-frete_id="' + keyOne + '" name="frete[' + keyOne + '][type]" value="retirada"> ' +
    //                                 '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][service_id]" value="rsa-retirada"> ' +
    //                                 '<input disabled class="price input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][price]" value="0">' +
    //                                 '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][dados_gerais]" value=\'' + (JSON.stringify(valueOne.store)) + '\'> <label><b style="font-size:.9rem;">Retirar na Loja</b></label>' +
    //                                 '</div>' +
    //                                 '<div class="col-5 my-1">Grátis</div>' +
    //                                 '<div class="col-12 border-bottom pb-2">Obs: ' + (valueOne.store.ob_retirada) + '</div>'
    //                                 ;
    //                         }
    //                     }

    //                     for (var i = 0; i < valueOne.transportadoras.length; i++) {
    //                         if (!valueOne.transportadoras[i].error) {
    //                             div +=
    //                                 '<div class="col-12 col-md-3 my-1"><img class="img-fluid" src="' + valueOne.transportadoras[i].company.picture + '"></div>' +
    //                                 '<div class="col-4 col-md-3 my-1">' +
    //                                 '<input type="hidden" class="sub_total" value="' + sub_total + '">' +
    //                                 '<input type="radio" class="click-frete radio" name="frete[' + keyOne + '][type]" value="correios"> ' +
    //                                 '<input disabled class="input-frete frete-' + keyOne + '" type="hidden" name="frete[' + keyOne + '][transport_name]" value="' + valueOne.transportadoras[i].name + '"> ' +
    //                                 '<input disabled class="input-frete" type="hidden" name="frete[' + keyOne + '][company_id]" value="' + valueOne.transportadoras[i].company.id + '"> ' +
    //                                 '<input disabled class="input-frete" type="hidden" name="frete[' + keyOne + '][service_id]" value="' + valueOne.transportadoras[i].id + '"> ' +
    //                                 '<input disabled class="input-frete" type="hidden" name="frete[' + keyOne + '][packages]" value="' + btoa(JSON.stringify(valueOne.transportadoras[i].packages)) + '"> ' +
    //                                 // '<input disabled class="input-frete" type="hidden" name="frete['+keyOne+'][height]" value="'+valueOne.transportadoras[i].packages[0].dimensions.height+'"> '+
    //                                 // '<input disabled class="input-frete" type="hidden" name="frete['+keyOne+'][width]" value="'+valueOne.transportadoras[i].packages[0].dimensions.width+'"> '+
    //                                 // '<input disabled class="input-frete" type="hidden" name="frete['+keyOne+'][length]" value="'+valueOne.transportadoras[i].packages[0].dimensions.length+'"> '+
    //                                 // '<input disabled class="input-frete" type="hidden" name="frete['+keyOne+'][weight]" value="'+valueOne.transportadoras[i].packages[0].weight+'"> '+
    //                                 '<input disabled class="price input-frete" type="hidden" name="frete[' + keyOne + '][price]" value="' + valueOne.transportadoras[i].custom_price + '"> ' +
    //                                 '<input disabled class="input-frete" type="hidden" name="frete[' + keyOne + '][dados_gerais]" value=\'' + (JSON.stringify(valueOne.transportadoras[i])) + '\'>' +
    //                                 '<label style="font-size:.9rem;">' + valueOne.transportadoras[i].name + '</label>' +
    //                                 '</div>' +
    //                                 '<div class="col-4 col-md-3 my-1" style="font-size: .8rem;">Entrega Até ' + (valueOne.transportadoras[i].custom_delivery_time + 1) + ' Dias Uteis.</div>' +
    //                                 '<div class="col-4 col-md-3 my-1">R$ ' + parseFloat(valueOne.transportadoras[i].custom_price).toFixed(2).replace('.', ',') + '</div>'
    //                                 ;
    //                         }
    //                     }
    //                     div += '</div>';
    //                     div += '</div>';
    //                     div += '<div class="col-6 my-2">Sub Total</div>';
    //                     div += '<div class="col-6 my-2 text-right sub-total">R$ ' + sub_total.toFixed(2).replace('.', ',') + '</div>';
    //                     div += '</div>';

    //                     $('.fretes').append(div);
    //                     total_value += sub_total;
    //                 });

    //                 $('.fretes').append(
    //                     '<div class="row my-2">' +
    //                     '<input type="hidden" class="total_value" value="' + total_value + '">' +
    //                     '<div class="col-6 my-2">Total</div>' +
    //                     '<div class="col-6 my-2 text-right total-value">R$ ' + total_value.toFixed(2).replace('.', ',') + '</div>' +
    //                     '</div>'
    //                 );

    //                 $('.spinner-cep-div').addClass('d-none');
    //             }
    //         });
    //     }

    //     setTimeout(function () { $('.spinner-cep-div').addClass('d-none'); }, 5000)
    // });
    // $(document).on('click', '.click-frete', function () {
    //     var discount_product = parseFloat($('.discount_product').val()) || 0;
    //     var discount_frete = parseFloat($('.discount_frete').val()) || 0;
    //     var discount_config = $('.discount_frete').data('discount_config');
    //     var discount_frete_total = $('.discount_frete').data('discount_frete');
    //     console.log(discount_frete_total);
    //     var radio = $(this);
    //     var frete_id = $(this).data('frete_id');
    //     $(this).closest('.frete').find('.input-frete').prop('disabled', true);
    //     radio.parent().find('.input-frete').prop('disabled', false);

    //     var sub_price = parseFloat(radio.parent().find('.price').val()) || 0;
    //     radio.closest('.frete').find('.sub-total').html('R$ ' + (sub_price + parseFloat(radio.closest('.frete').find('.sub_total').val())).toFixed(2).replace('.', ','));

    //     var total_price = 0;
    //     $('.frete').find('.price').each(function () {
    //         if (!$(this).is(':disabled')) {
    //             total_price += parseFloat($(this).val()) || 0;
    //         }
    //     });

    //     if (discount_frete_total) discount_frete = total_price;
    //     if (discount_config == 'porcentage') {
    //         discount_frete = (total_price * discount_frete) / 100;
    //     }

    //     $('.total-frete').html('R$ ' + total_price.toFixed(2).replace(".", ","));
    //     $('.total-frete-desconto').html('R$ ' + discount_frete.toFixed(2).replace(".", ","));
    //     $('.total').html('R$ ' + (((total_price + parseFloat(radio.closest('.fretes').find('.total_value').val()) - discount_product) - discount_frete)).toFixed(2).replace('.', ','));
    //     $('.total-value').html('R$ ' + (((total_price + parseFloat(radio.closest('.fretes').find('.total_value').val()) - discount_product) - discount_frete)).toFixed(2).replace('.', ','));
    // });

    $('.popover-dismiss').popover({ trigger: 'focus' });

    $(document).on('click', '[data-target="#enderecos"]', function () {
        $('#enderecos').find('[name="id"]').val('');
        $('#enderecos').find('input[type="text"]').val('');
        $('#enderecos').find('input').attr('readonly', false);
        $('#enderecos').find('select').val('');
        $('#enderecos').find('select').attr('readonly', false);

        var dados = $(this).data('dados'); // dados que serão passados aos campos
        $.each(dados, (key, value) => {
            $('#enderecos').find('[name="' + key + '"').val(value); // os campos name são iguais aos das colunas vidna do banco
        });

        $('#enderecos').find('[name="post_code"]').trigger('keyup');
        $('#enderecos').find('select').trigger('change');
    });

    $(document).on('click', '.btn-excluir-address', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var url = $(this).data('url');

        Swal.fire({
            icon: 'error',
            title: 'Apagar Endereço?',
            showCancelButton: true,
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url + '/' + id;
            }
        });
    });

    $('.menu-categorias').slick({
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        centerMode: false,
        variableWidth: true
    });

    // ###############################################################################
    // ##################### Controloando o produto n carrinho #######################
    // $(document).on('click', '#comprar_produto', function () {
    //     var data = {
    //         originalId: $('#originalId').val(),
    //         originalValue: $('#originalValue').val(),
    //         originalName: $('#originalName').val(),
    //         hasPreparation: $('#hasPreparation').val(),
    //         preparationTime: $('#preparationTime').val(),
    //         productImage: $('#productImage').val(),
    //         productWeight: $('#productWeight').val(),
    //         productHeight: $('#productHeight').val(),
    //         productWidth: $('#productWidth').val(),
    //         ProductLength: $('#ProductLength').val(),
    //         originalSalesUnit: $('#originalSalesUnit').val(),
    //         customProjectValue: $('#customProjectValue').val(),
    //         customProjectWidth: $('#customProjectWidth').val(),
    //         customProjectHeight: $('#customProjectHeight').val(),
    //         customProjectMeters: $('#customProjectMeters').val(),
    //         customValue: $('#customValue').val(),
    //         attributes_aux: [],
    //         project: [],
    //         qty_total: $('.qty_total').val(),
    //         note: $('#product_ob').val(),
    //     };

    //     $('.select-data-attribute.attr-selecionado').each(function () {
    //         data['attributes_aux'].push($(this).val());
    //     });

    //     $('.customModuloProject').each(function () {
    //         data['project'].push($(this).val());
    //     });

    //     $(this).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');

    //     $.ajax({
    //         url: '/carrinhoAdd',
    //         type: 'POST',
    //         data: data,
    //         success: (data) => {
    //             // console.log(data);
    //             window.location.href = '/carrinho';
    //         }
    //     });
    // });

    $(document).on('click', '.btn-delete-product', function () {
        var this_var = $(this);
        var row_id = $(this).data('row_id');
        var repagina = $(this).data('repagina');
        var json_gtag = $(this).data('json_gtag');

        var data_gtag = {
            currency: 'BRL',
            items: [{
                item_id: `P_${json_gtag.attributes.product_id}`,
                item_name: `${json_gtag.name}`,
                item_brand: 'Biguaçu',
                item_category: `Biguaçu`,
                price: `${parseFloat(json_gtag.price)}`,
                currency: 'BRL',
                quantity: json_gtag.quantity
            }],
            value: `${parseFloat(json_gtag.price)}`,
        };

        gtag('event', 'remove_from_cart', data_gtag);

        $.ajax({
            url: '/remove-cart',
            type: 'POST',
            data: { row_id: row_id },
            success: (data) => {
                if (repagina == 'sim') {
                    location.reload();
                } else {
                    this_var.parent().parent().remove();
                }
            }
        });
    });
    // ###############################################################################
    // ###############################################################################

    // Função para selecionar os atributos do produto
    $(document).on('click', '.select-attribute-bg', function () {
        var this_var = $(this);
        var custom_value = parseFloat($('#customValue').val()) || 0;
        var attr_value = this_var.parent().parent().parent().find('.attr-selecionado');


        if (attr_value.length > 0) {
            attr_value = attr_value.val().split('|');
            attr_value = parseFloat(attr_value[2]) || 0;
            $('#customValue').val((custom_value - attr_value).toFixed(2));
        }

        this_var.parent().parent().parent().find('.select-attribute').removeClass('active');
        this_var.parent().parent().parent().find('.attr-selecionado').removeClass('attr-selecionado');

        this_var.parent().parent().find('.select-data-attribute').addClass('attr-selecionado');
        this_var.parent().addClass('active');

        var custom_value = parseFloat($('#customValue').val()) || 0;
        var attr_value = (this_var.parent().parent().find('.select-data-attribute').val()).split('|');
        attr_value = parseFloat(attr_value[2]) || 0;

        $('#customValue').val((custom_value + attr_value).toFixed(2));

        var custom_project_value = parseFloat($('#customProjectValue').val()) || 0;

        var total_attributes = $('.attributes').length;
        var total_attr_selecionado = $('.attributes').find('.attr-selecionado').length;
        $('#comprar_produto').prop('disabled', true);
        if (total_attributes == total_attr_selecionado) {
            if (custom_project_value !== 0) {
                $('.valor-final').text('R$ ' + ((attr_value + custom_value)).toFixed(2).toString().replace('.', ','));
                $('#comprar_produto').prop('disabled', false);
            }
        }
    });

    $(document).on('click', '.btn-visualizar', function () {
        var target = $(this).data('target'); // qual modal ta sendo acessado
        var dados = $(this).data('dados'); // dados que serão passados aos campos

        console.log(dados);

        $.each(dados, (key, value) => {
            $(target).find('._' + key).text(value); // quando o campo for texto

            if (key == 'product_value' || key == 'cost_freight' || key == 'total_value') {
                $(target).find('._' + key).text('R$ ' + (parseFloat(value) || 0).toFixed(2).toString().replace('.', ','));
            }

            switch (key) {
                case 'birth_date':
                    var date = value.split('-');
                    $(target).find('._' + key).text(date[2] + '/' + date[1] + '/' + date[0]);
                    break;
                case 'order_products':
                    $(target).find('.produtos_pedido').empty();
                    for (var i = 0; i < value.length; i++) {
                        var attributes = '';
                        for (var ii = 0; ii < value[i].attributes.length; ii++) {
                            var attr = value[i].attributes[ii].split('|');
                            attributes += '<div class="col-12">' + attr[1] + ' - ' + (parseFloat(attr[2] || 0).toFixed(2).toString().replace('.', ',')) + '</div>';
                        }
                        var project = '';
                        for (var ii = 0; ii < value[i].project.length; ii++) {
                            var attr = value[i].project[ii].split('|');
                            project += '<div class="col-12">L: ' + attr[0] + ' - A: ' + attr[1] + ' - M²: ' + attr[2] + '</div>';
                        }
                        $(target).find('.produtos_pedido').append(
                            '<div class="row border-bottom my-2">' +
                            '<div class="col-12 col-md-4"><b>Codigo do Produto: </b>' + value[i].product_code + '</div>' +
                            '<div class="col-12 col-md-4"><b>Nome do Produto: </b>' + value[i].product_name + '</div>' +
                            '<div class="col-12 col-md-4"><b>Quantidade: </b>' + value[i].quantity + '</div>' +
                            '<div class="col-12 col-md-4"><b>Tipo de Venda: </b>' + value[i].product_sales_unit + '</div>' +
                            '<div class="col-12 col-md-4"><b>Largura Padrão: </b>' + value[i].product_width + ' cm</div>' +
                            '<div class="col-12 col-md-4"><b>Altura Padrão: </b>' + value[i].product_height + ' cm</div>' +
                            '<div class="col-12 col-md-4"><b>Comprimento Padrão: </b>' + value[i].product_length + ' cm</div>' +
                            '<div class="col-12 col-md-4"><b>Peso Padrão: </b>' + value[i].product_weight + ' kg</div>' +
                            '<div class="col-12"><h5>Calculo do projeto</h5></div>' +
                            '<div class="col-12 col-md-4"><b>Largura do Projeto: </b>' + value[i].project_width + '</div>' +
                            '<div class="col-12 col-md-4"><b>Altura do Projeto: </b>' + value[i].project_height + '</div>' +
                            '<div class="col-12 col-md-4"><b>M² do Projeto: </b>' + value[i].project_meters + '</div>' +
                            '<div class="col-12 my-2"><h5>Atributos</h5></div>' +
                            '<div class="col-12 col-md-4"><div class="row">' + attributes + '</div></div>' +
                            '<div class="col-12 my-2"><h5>Projeto</h5></div>' +
                            '<div class="col-12 col-md-4"><div class="row">' + project + '</div></div>' +
                            '</div>'
                        );
                    }
                    break;
                case 'shipping_customer':
                    $(target).find('.entrega_pedido').empty();

                    $(target).find('.entrega_pedido').append(
                        '<div class="row border-bottom my-2">' +
                        '<div class="col-12 col-md-6">' + value[0].address + ', Nª ' + value[0].number + ' - ' + value[0].address2 + '</div>' +
                        '<div class="col-12 col-md-6">' + value[0].city + ' / ' + value[0].state + ' - ' + value[0].post_code + '</div>' +
                        '<div class="col-12">' + value[0].phone1 + ' / ' + value[0].phone2 + '</div>' +
                        '<div class="col-12 col-md-4"><b>Transportadora: </b>' + value[0].transport + ' - <b>Data Estimada: </b> ' + value[0].time + ' dias</div>' +
                        '</div>'
                    );
                    break;
                case 'payment_order':
                    // console.log(value);
                    $(target).find('.pagamento_pedido').empty();
                    for (var i = 0; i < value.length; i++) {
                        if (value[i].status == 'approved')
                            $(target).find('.pagamento_pedido').append(
                                '<div class="row border-bottom my-2">' +
                                '<div class="col-12 col-md-4"><b>Total Pago: </b>' + (parseFloat(value[i].total_paid_amount) || 0).toFixed(2).toString().replace('.', ',') + '</div>' +
                                '<div class="col-12 col-md-4"><b>Parcelamento: </b>' + value[i].installments + ' x ' + (parseFloat(value[i].installment_amount) || 0).toFixed(2).toString().replace('.', ',') + '</div>' +
                                '<div class="col-12 col-md-4"><b>Metodo de Pagamento: </b>' + value[i].payment_method_id + '</div>' +
                                '<div class="col-12 col-md-4"><b>Nome do Pagador: </b>' + value[i].payer_name + '</div>' +
                                '<div class="col-12 col-md-4"><b>CNPJ/CPF do Pagador: </b>' + value[i].payer_cnpj_cpf + '</div>' +
                                '</div>'
                            );
                    }
                    break;
            }
        });
    });

    $(document).on('change', '.select-attributes', function () {
        $('#Variations').empty();
        var product_id = $('[name="product_id"]').val();
        var stock_controller = $('[name="stock_controller"]').val();

        var selecao = 0;
        var attributes_value = [];
        $('.select-attributes').each(function () {
            if ($(this).val() !== '') {
                selecao++;
                attributes_value.push($(this).val());
            }
        });

        // if(selecao == $('.select-attributes').length) $('.apartir-value').addClass('d-none');
        if (selecao == $('.select-attributes').length) {
            $.ajax({
                url: '/geral/select-attrs-variations',
                type: 'POST',
                data: { attributes_value, product_id },
                success: (data) => {
                    // console.log(data);
                    $('#json_desconto_progressivo').val(JSON.stringify(data.progressive_discount));
                    $('.apartir-value').addClass('d-none');

                    var estoque = '';
                    if (stock_controller) estoque = '<p>Estoque Disponível: ' + data.stock + '</p>'

                    var variation = {
                        'var_id': data.id,
                        'preco': data.preco,
                        'stock': data.stock,
                        'peso': data.peso,
                        'dimensoes_C': data.dimensoes_C,
                        'dimensoes_L': data.dimensoes_L,
                        'dimensoes_A': data.dimensoes_A,
                        'relation': attributes_value
                    };

                    if (data.stock == 0) {
                        $('#Variations').html(`
                            <div class="w-100"><p style="font-size: 1.3rem; color: #3D550C;">VARIAÇÃO INDISPONÍVEL</p></div>
                        `);
                        $('.qty-btnPurchase').addClass('d-none');
                    } else {
                        $('#Variations').html(
                            '<div class="w-100">' + estoque + '</div>' +
                            '<div class="values alter-value" id="variacao-' + attributes_value.join('-') + '">' +
                            `
                                <div>
                                    <span class="value-2 py-1">R$ ${data.preco.toFixed(2).replace('.', ',')}</span>
                                    <br>
                                    <span style="font-size: 12px;">(Preço por unidade)</span>
                                </div>
                            `+

                            '<input type="hidden" name="variacao[' + attributes_value.join('-') + ']" value=\'' + JSON.stringify(variation) + '\'>' +
                            '<input type="hidden" class="get-price-calc" value="' + data.preco + '">' +
                            '</div>'

                        );
                        $('.get-installments').removeClass('d-none');
                        $('.qty-btnPurchase').removeClass('d-none');

                        $('[name="quantidade"]').trigger('keyup');
                    }
                }
            });
        }

        // $('#variacao-'+attributes.join('-')).removeClass('d-none');
    });

    $(document).on('click', '.finalizarOrder', function () {
        var order_number = $(this).data('order_number');

        Swal.fire({
            icon: 'warning',
            title: 'Gostaria de Finalizar o Pedido?',
            showCancelButton: true,
            confirmButtonText: 'Finzalizar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/finalizarOrder',
                    type: 'POST',
                    data: { order_number },
                    success: (data) => {
                        // console.log(data);
                        window.location.reload();
                    }
                });
            }
        });
    });

    // Função para avaliação de etsrelas
    $(document).on('click', '.estrela_click', function () {
        var radio = $(this).parent().find('#' + $(this).attr('for'));
        $('input[name="estrela"]').removeAttr('checked');
        radio.attr('checked', true);
        $(this).parent().find('.estrela_click').removeClass('text-warning');

        for (var i = 1; parseInt(radio.val()) >= i; i++) {
            $(this).parent().find('label[for="estrela_' + i + '"]').addClass('text-warning');
        }
    });
    $(document).on('click', '.btn-env-comment', function () {
        var form = $(this).closest('form');
        var btn = $(this);
        var text_btn = $(this).html();
        $(this).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>').prop('disabled', true);

        form_height = form.height();
        form.css('height', form_height + 'px');

        if (form.find('input[type="radio"]').is(':checked') && form.find('textarea') !== '') {
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: (data) => {
                    // console.log(data);

                    btn.html(text_btn);
                    form.css({
                        'height': '0',
                        'transition': '.6s height',
                        'overflow': 'hidden',
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Avaliação registrado com sucesso!'
                    });
                }
            });
        } else {
            btn.html(text_btn).prop('disabled', false);
            Toast.fire({
                icon: 'error',
                title: 'Campos não preenchidos, verificar!'
            });
        }
    });

    // Evento para enviar contato
    $(document).on('click', '.btn-send-contactus', function () {
        var form_contact = $('#form_contact');
        var btn = $(this);
        btn.html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>').prop('disabled', true);
        form_contact.find('input').removeClass('is-invalid');
        form_contact.find('.invalid-feedback').remove();

        GRecaptchaFun($(this).closest('form'));
        setTimeout(() => {
            $.ajax({
                url: form_contact.attr('action'),
                type: 'POST',
                data: form_contact.serialize(),
                success: (data) => {
                    console.log(data);
                    form_contact.find('input').val('');
                    form_contact.find('select').val('');
                    form_contact.find('textarea').val('');
                    btn.html('Enviar').prop('disabled', false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Email enviado com sucesso!'
                    });
                },
                error: (err) => {
                    btn.html('Enviar').prop('disabled', false);

                    // Adicionamos os erros numa variavel
                    let erro_tags = err.responseJSON.errors;
                    // console.log(erro_tags);

                    $.each(erro_tags, (key, value) => {
                        let tag = form_contact.find('[name="' + key + '"]');
                        tag.addClass('is-invalid');

                        tag.parent().append('<div class="invalid-feedback">' + value[0] + '</div>');
                    });
                }
            });
        }, 1000);
    });

    // Evento para novo registro de newsletter
    $(document).on('click', '.btn-newsletter', function () {
        var form_newsletter = $('#form_newsletter');
        var btn = $(this);
        btn.html('<div class="spinner-border" role="status"><span class="sr-only">Carregando...</span></div>').prop('disabled', true);
        form_newsletter.find('input').removeClass('is-invalid');
        form_newsletter.find('.invalid-feedback').remove();

        $.ajax({
            url: form_newsletter.attr('action'),
            type: 'POST',
            data: form_newsletter.serialize(),
            success: (data) => {
                console.log(data);
                form_newsletter.find('input').val('');
                btn.html('Enviar').prop('disabled', false);
                Swal.fire({
                    icon: 'success',
                    title: 'Inscrição realizada com sucesso!'
                });
            },
            error: (err) => {
                btn.html('Enviar').prop('disabled', false);

                // Adicionamos os erros numa variavel
                let erro_tags = err.responseJSON.errors;
                // console.log(erro_tags);

                $.each(erro_tags, (key, value) => {
                    let tag = form_newsletter.find('[name="' + key + '"]');
                    tag.addClass('is-invalid');

                    tag.parent().append('<div class="invalid-feedback" style="color: #000">' + value[0] + '</div>');
                });
            }
        });
    });

    // // Evento de confirmar o frete
    // $(document).on('click', '.btn-cofirma-frete', function () {
    //     var zip_code = $('[name="zip_code"]').val();
    //     var form = $(this).parent().prev('div').find('form');
    //     var data = form.serialize() + '&zip_code=' + zip_code;
    //     $.ajax({
    //         url: form.attr('action'),
    //         type: 'POST',
    //         data: data,
    //         success: (data) => {
    //             console.log(data);
    //             if (data == 'true') $('.td-finalizar').removeClass('notClick');
    //         }
    //     });
    // });
    // $(document).on('click', '.btn-new-address', function () {
    //     if ($('.endereco').find('[name="endereco"]').val() !== 'newAddress') {
    //         $('.endereco').find('[name="endereco"]').append('<option value="newAddress">Novo Endereço</option>');
    //         $('.endereco').find('[name="endereco"]').val('newAddress');

    //         $('.endereco').find('.form-group').removeClass('d-none');
    //         $('.endereco').find('input').val('');
    //         $('.btn-frete').addClass('d-none');
    //         $('.total-frete').html('R$ 0,00');
    //         $('.total').html($('.sub-total-cart').html());
    //         $('.endereco').find('.endereço-pronto').addClass('d-none');
    //     }
    // });

    // Eventos do payment_order
    $(document).on('click', '.btn-send-payment', function () {
        var form = $(this).closest('form');
        $(this).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>').prop('disabled', true);

        var isValid = true;
        $('.requerid').each(function () {
            if ($(this).val() == '') {
                isValid = false;
                $(this).addClass('is-invalid');
                $(this).parent().append('<span class="invalid-feedback">Campo Obrigatorio</span>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).parent().find('.invalid-feedback').remove();
            }
        });

        if (isValid) {
            form.submit();
        } else {
            $(this).html('Pagar').prop('disabled', false);
        }
    });

    $(document).on('click', '.favorite', function () {
        var product_id = $(this).data('product_id');
        var url = $(this).data('product_id') ? '/add-favoritos/' + product_id : '/add-services-favoritos/' + $(this).data('service_id');

        $.ajax({
            url: url,
            type: 'GET',
            success: (data) => {
                console.log(data);

                Swal.fire({
                    icon: 'success',
                    title: data.msg
                });
            },
            error: (err) => {
                console.log(err);

                if (err.responseJSON.error_login) {
                    Swal.fire({
                        icon: 'error',
                        title: err.responseJSON.error_login
                    });
                }
            }
        });
    });

    $(document).on('click', '.btn-env-code-delete', function () {
        var btn_this = $(this);

        btn_this.prop('disabled', true);
        btn_this.html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');

        $.ajax({
            url: '/perfil/env-code-delete',
            type: 'GET',
            success: (data) => {
                btn_this.prop('disabled', false);
                btn_this.html('Enviar Codigo');

                $('.btn-submit-code-delete').parent().removeClass('d-none');
                $('.info-env-code').removeClass('d-none');
                setTimeout(() => { $('.info-env-code').addClass('d-none'); }, 5000);
            }
        });
    });
    $(document).on('click', '.btn-submit-code-delete', function () {
        var btn_this = $(this);

        btn_this.prop('disabled', true);
        btn_this.html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');

        $.ajax({
            url: '/perfil/confirm-code-delete',
            type: 'POST',
            data: { code_delete: $('[name="code_delete"]').val() },
            success: (data) => {
                btn_this.prop('disabled', false);
                btn_this.html('Apagar Conta');

                Swal.fire({
                    icon: 'success',
                    title: 'Conta apagada com successo!',
                });

                setTimeout(() => { window.location.reload() }, 6000);
            },
            error: (err) => {
                btn_this.prop('disabled', false);
                btn_this.html('Apagar Conta');

                if (err.responseJSON.error_msg) {
                    Swal.fire({
                        icon: 'error',
                        title: err.responseJSON.error_msg
                    });
                }
            }
        });
    });

    $(document).on('click', '.btn-input-pedido', function () { $('[name="order_number"]').val($(this).data('order_number')); });
    $(document).on('click', '.btn-cancelar-pedido', function () {
        var btn = $(this);
        Swal.fire({
            title: 'Salvando Dados, Aguarde!',
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
                Swal.fire({
                    icon: 'success',
                    title: data.msg_10
                });
                $('#solicitarCancelamentoPedido').modal('hide');
            }
        });
    });

    $(document).on('click', '.btn-rastreio-melhor-envio', function (e) {
        e.preventDefault();
        $('#rastreioMelhorEnvio').modal('show');
        $('#rastreioMelhorEnvio').find('.modal-body').html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');

        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: (data) => {
                console.log(data);
                $('#rastreioMelhorEnvio').find('.modal-body').html(JSON.stringify(data));
            }
        });
    });

    $(function () {
        $('.max-caracteres').each(function () {
            var max_caracteres = $(this).data('max_caracteres') || 255;
            $(this).parent().find('label').append('<span class="ml-2 count-max-caracteres-length">(max. caracteres ' + max_caracteres + ')</span>')
        });
    });
    $(document).on('keydown', '.max-caracteres', function (e) {
        var max_caracteres = $(this).data('max_caracteres') || 255;
        $(this).parent().find('.count-max-caracteres-length').html('(max. caracteres ' + (max_caracteres - $(this).val().length) + ')');
        if ($(this).val().length >= max_caracteres && e.keyCode != 8 && e.keyCode != 9) {
            return false;
        }
    });

    // Especifico na pagina de produtos, quando descer adciona mais produtos na pagina
    var laze_skip = sleep_ajax = stop_laze = 0;
    $(window).on('scroll', function () {
        var laze_load = $('.laze_load').height();
        var scrollHeight = $(this).scrollTop() + 300;
        if (stop_laze == 0) {
            if (scrollHeight > laze_load) {
                if (sleep_ajax == 0) {
                    $('.loading').addClass('d-flex').removeClass('d-none');
                    $('.loading').find('.spinner-border').removeClass('d-none');
                    sleep_ajax = 1;
                    laze_skip += 20;
                    filters = $('.filters').find('form').serialize();

                    $.ajax({
                        url: '?return=true&skip=' + laze_skip + '&' + filters,
                        type: 'GET',
                        success: (data) => {
                            // console.log(data);
                            $('.loading').removeClass('d-flex').addClass('d-none');
                            $('.loading').find('.spinner-border').addClass('d-none');

                            $('.laze_load').append(data);

                            sleep_ajax = 0;
                            if (data == '') stop_laze = 1;
                        }
                    });
                }
            }
        } else {
            $('.loading').removeClass('d-flex').addClass('d-none');
            $('.loading').find('.spinner-border').removeClass('d-none');
        }
    });

    $(window).on('scroll', function () {
        var window_height = $(window).height();
        var scrollHeight = $(this).scrollTop() + 300;
        if (scrollHeight > window_height) {
            $('.btn-scroll-top').removeClass('d-none');
        } else {
            $('.btn-scroll-top').addClass('d-none');
        }
    });
    $(document).on('click', '.btn-scroll-top', function () {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    $('.copy-link').on('click', function (e) {
        e.preventDefault();
        navigator.clipboard.writeText($(this).attr('href'));

        $(this).css({
            'color': 'green',
            'border-color': 'green'
        });
        setTimeout(() => {
            $(this).css({
                'color': '#000',
                'border-color': '#000',
                'transition': '0.8s ease'
            });
        }, 5000);
    });

    $(document).on('keyup', '[name="quantidade"]', function (e) {
        var json_desconto_progressivo = JSON.parse($('#json_desconto_progressivo').val());
        var diaria = 1;
        if ($('[name="hospedagem_controller"]').val() == '1') diaria = parseFloat($('[name="diaria"]').val()) || 1;
        var price_calc = parseFloat($('.get-price-calc').val()) || 0;
        var qty_calc = parseFloat($(this).val()) || 0;

        $('.alter-value').find('.c-qty').text(qty_calc);
        $('.alter-value').find('.value-2').html(`R$ ${price_calc.toFixed(2).toString().replace('.', ',')}`);
        if (json_desconto_progressivo.length > 0) {
            var return_json_dp = json_desconto_progressivo.filter(function (query) {
                return qty_calc >= query.discount_quantity;
            });
            if (return_json_dp[return_json_dp.length - 1]) {
                price_calc = return_json_dp[return_json_dp.length - 1].discount_value;
                $('.alter-value').find('.value-2').html(`R$ ${price_calc.toFixed(2).toString().replace('.', ',')}`);
            }
        }

        // $('.div-price-calc').removeClass('d-none');
        $('.price_calc').text('R$ '+((qty_calc * price_calc) * diaria).toFixed(2).replace('.', ','));

        var installments = JSON.parse($('.get-installments').val());
        $('[aria-labelledby="getInstallmentsDropdown"]').empty();
        for (i in installments) {
            var value = ((qty_calc * price_calc) * diaria);
            var installments_valor = parseFloat(installments[i].valor).toFixed(2).toString();
            console.log();
            if (installments_valor.replace(',', '.') <= value) {
                $('[aria-labelledby="getInstallmentsDropdown"]').append(`<a class="dropdown-item">${installments[i].parcela} x ${(((value + (value * installments[i].porcentage.replace(',', '.')) / 100)) / installments[i].parcela).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })} = ${(value + ((value * installments[i].porcentage.replace(',', '.')) / 100)).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })} ${installments[i].porcentage.replace(',', '.') > 0 ? 'Com Juros' : 'Sem Juros'}</a>`);
            }
        }
    });

    $(document).on('click', '.btn-comprar-cart', function () {
        var btn_text = $(this).html();
        $(this).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>').prop('disabled', true);

        var data_gtag = {
            currency: 'BRL',
            items: [{
                item_id: `P_${$('[name="product_id"]').val()}`,
                item_name: `${$('[name="product_name"]').val()}`,
                item_brand: 'Biguaçu',
                item_category: `${$('[name="product_category"]').val()}`,
                price: `${parseFloat($('.get-price-calc').val())}`,
                currency: 'BRL',
                quantity: $('[name="quantidade"]').val()
            }],
            value: `${parseFloat($('.get-price-calc').val())}`,
        };

        gtag('event', 'add_to_cart', data_gtag);

        if ($('.select-attributes').length > 0) {
            var isValid = true;
            $('.select-attributes').each(function () {
                if ($(this).val() == '') isValid = false;
            });

            if (isValid) {
                setTimeout(() => { $(this).closest('form').submit(); }, 100);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'É necessário que todos atributos estejam selecionados!'
                });
                $(this).html(btn_text).prop('disabled', false);
            }
        } else {
            setTimeout(() => { $(this).closest('form').submit(); }, 100);
        }
    });

    // Cookies-PrivacyPolicy
    $('.btn-yes-cookie').on('click', function () {
        $('.modal-cookie').css({
            'height': '0',
            'transition': '.6s height',
        });
        setLocalStorage('cookie_privacy_policy', 'accept', '30');
    });

    $(function () {
        setTimeout(() => {
            var height = '100px';
            if ($(window).width() < 1200) height = '120px';
            if ($(window).width() < 1000) height = '140px';
            if ($(window).width() < 800) height = '180px';
            if ($(window).width() < 600) height = '220px';
            if ($(window).width() < 400) height = '240px';

            if (!getLocalStorage('cookie_privacy_policy')) {
                $('.modal-cookie').css('height', height);
            } else {
                if (new Date(getLocalStorage('cookie_privacy_policy', true)) < new Date()) {
                    deleteLocalStorage('cookie_privacy_policy', true);
                    $('.modal-cookie').css('height', height);
                }
            }
        }, 1000);
    });

    //Salvar Form Afiliado
    $(document).on('click', '#atualizarAfiliado .btn-salvar', function () {
        // Pegamos os dados do data
        let save_target = $(this).data('save_target');
        let save_route = $(this).data('save_route');
        let refresh = $(this).data('refresh');
        let formData = new FormData($(save_target)[0]);

        // Por mais que tenha erro, limpamos para os outros que não tenha
        $(save_target).find('input').removeClass('is-invalid');
        $(save_target).find('.invalid-feedback').remove();

        // Pegamos o parente do id para adicionar um modelo de carregamento
        let modal = $(save_target).parent();
        if (modal.is('.modal-content')) modal.append('<div class="overlay d-flex justify-content-center align-items-center"><i class="fas fa-2x fa-sync fa-spin"></i></div>');

        $.ajax({
            url: save_route,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: (data) => {
                // console.log(data);

                Toast.fire({
                    icon: 'success',
                    title: 'Os dados foram salvos com successo!'
                });

                if (refresh == 'S') setTimeout(() => { window.location.reload(); }, 1200);
            },
            error: (err) => {
                // console.log(err);
                $(modal).find('.overlay').remove();

                // Adicionamos os erros numa variavel
                let erro_tags = err.responseJSON.errors;
                // console.log(erro_tags);

                $.each(erro_tags, (key, value) => {
                    let tag = $(save_target).find('[name="' + key + '"]');
                    tag.addClass('is-invalid');

                    tag.parent().append('<div class="invalid-feedback">' + value[0] + '</div>');
                });

                if (err.responseJSON.msg_alert) {
                    Swal.fire({
                        icon: err.responseJSON.icon_alert,
                        text: err.responseJSON.msg_alert,
                    });
                }
            }
        });
    });

    $(document).on('click', '.btn-gerar-link', function () {
        var url = $(this).data('url');
        var reference_id = $(this).data('reference_id');
        var affiliate_id = $(this).data('affiliate_id');
        var affiliate_item = $(this).data('affiliate_item');

        Swal.fire({
            title: 'Deseja gerar link para este item?',
            text: "Após confirmação, você receberá um link personalizado para vender o item " + reference_id + ".",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Gerando link, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { reference_id, affiliate_id, affiliate_item },
                    success: (data) => {
                        // console.log(data);
                        Swal.fire({
                            icon: 'success',
                            title: 'Link gerado com successo!',
                            html: `<a href="${data}" target="_blank">${data}</a>`
                        });
                    }
                });
            }
        })
    });

    $(document).on('click', '.btn-copiar-link', function (e) {
        e.preventDefault();
        var text = $(this).html();
        $(this).html('Link Copiado <i class="fas fa-check"></i>');
        navigator.clipboard.writeText($(this).attr('href'));

        setTimeout(() => {
            $(this).html(text);
        }, 1600);
    });

    $(document).on('blur', '.requerid', function () {
        if ($(this).val().length !== 0) {
            if ($(this).is('.is-invalid')) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        }
    });

    // Validação de campo do numero do cartão de credito
    $(document).on('blur', '[name="card_number"]', function () {
        $(this).removeClass('is-invalid');
        $(this).parent().find('.invalid-feedback').remove();
        $('.btn-send-payment').prop('disabled', false);
        if ($(this).val().length == 19) {
            $(this).addClass('is-valid');
            $('.btn-send-payment').prop('disabled', false);
        } else {
            $('.btn-send-payment').prop('disabled', true);
            $(this).parent().append(`<span class="invalid-feedback">Cartão Invalido!</span>`);
            $(this).addClass('is-invalid');
            $(this).removeClass('is-valid');
        }
    });

    // Validação de campo do mes do cartão de credito
    $(document).on('blur', '[name="card_expiration_month_year"]', function () {
        $(this).removeClass('is-invalid');
        $(this).parent().find('.invalid-feedback').remove();
        $('.btn-send-payment').prop('disabled', false);
        if ($(this).val().length == 7) {
            $(this).addClass('is-valid');
            $('.btn-send-payment').prop('disabled', false);
        } else {
            $('.btn-send-payment').prop('disabled', true);
            $(this).parent().append(`<span class="invalid-feedback">Data Invalida!</span>`);
            $(this).addClass('is-invalid');
            $(this).removeClass('is-valid');
        }
    });

    // Validação de campo do codigo de segurança do cartão de credito
    $(document).on('blur', '[name="card_cvv"]', function () {
        $(this).removeClass('is-invalid');
        $(this).parent().find('.invalid-feedback').remove();
        $('.btn-send-payment').prop('disabled', false);
        if ($(this).val().length == 3) {
            $(this).addClass('is-valid');
            $('.btn-send-payment').prop('disabled', false);
        } else {
            $('.btn-send-payment').prop('disabled', true);
            $(this).parent().append(`<span class="invalid-feedback">CVV Invalido!</span>`);
            $(this).addClass('is-invalid');
            $(this).removeClass('is-valid');
        }
    });

    // Esse evento faz com mande para o gtag as info e depois vai par ao produto
    $(document).on('click', '.click_select_item', function (e) {
        e.preventDefault();
        var data = JSON.parse($(this).closest('.produtos').find('.gtag_select_item').val());
        gtag('event', 'select_item', data);
        setTimeout(() => { window.location.href = $(this).attr('href'); }, 100);
    })
});

function diaSemana(semana) {
    let semana_dia = {
        1: 'Dom.',
        2: 'Seg.',
        3: 'Ter.',
        4: 'Qua.',
        5: 'Qui.',
        6: 'Sex.',
        7: 'Sab.',
    };


    var return_semana = [];
    for (var i = 0; semana.length > i; i++) {
        if (semana_dia[semana[i]]) return_semana.push(semana_dia[semana[i]]);
    }

    return return_semana;
}

function setLocalStorage(name, value, duration) {
    localStorage.setItem(name, value);
    if (duration) {
        var data = new Date();
        data.setDate(data.getDate() + parseInt(duration));
        localStorage.setItem(name + '_timer', data);
    }
}

function getLocalStorage(name, timer) {
    if (timer) {
        return localStorage.getItem(name + '_timer');
    }
    return localStorage.getItem(name);
}

function deleteLocalStorage(name, timer) {
    localStorage.removeItem(name);
    if (timer) {
        localStorage.removeItem(name + '_timer');
    }
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}