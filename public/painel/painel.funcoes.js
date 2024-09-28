$(document).ready(function () {
    let vendas_realizadas;
    let acessos_produtos_servicos;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.real').mask('000.000,00', { reverse: true });

    $(document).on('click', '.check-custom', function () {
        $(this).toggleClass('active');
    });

    $('.select2').select2();
    $.fn.dataTable.moment('DD/MM/YYYY');

    $("#table_pedido").DataTable({
        "responsive": true,
        "autoWidth": false,
        "columnDefs": [
            // { "visible": false, "targets": 2 },
            // { "visible": false, "targets": 3 },
        ],
        "language": language_pt_br,
        "buttons": ["copy", "csv", "colvis"],
        'order': [[0, 'desc']],
    }).buttons().container().appendTo('.table-options .col-md-6:eq(0)');

    $("#table_assinatura").DataTable({
        "responsive": true,
        "autoWidth": false,
        "columnDefs": [
            // { "visible": false, "targets": 2 },
            // { "visible": false, "targets": 3 },
        ],
        "language": language_pt_br,
        "buttons": ["copy", "csv", "colvis"],
        'order': [[0, 'desc']],
    }).buttons().container().appendTo('.table-options .col-md-6:eq(0)');

    var width = $(window).width();
    if (width < 370) {
        $('.buttons-colvis').parent().addClass('d-none');
    }

    var user_name = $('#user_name').text();
    user_name = user_name.split(' ');
    var intials = user_name[0].charAt(0) + user_name[user_name.length - 1].charAt(0);

    $('[name="postal_code"]').mask('00000-000');
    $('[name="post_code"], .post_code').mask('00000-000');
    $('[name="number"], .number').mask('0000000000');
    $('[name="phone1"]').mask('(00) 0000-0000');
    $('[name="phone2"], .phone2').mask('(00) 00000-0000');
    $('.phone').mask('(00) 00000-0000');

    // Telefone/Celeular
    var behavior_phone = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
        options_fone = {
            onKeyPress: function (val, e, field, options_fone) {
                field.mask(behavior_phone.apply({}, arguments), options_fone);
            }
        };
    $('[name="phone"]').mask(behavior_phone, options_fone);

    // Documentos do tipo CPF e CNPJ
    var behavior_document = function (val) {
        return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-000';
    },
        documento_options = {
            onKeyPress: function (val, e, field, options_fone) {
                field.mask(behavior_document.apply({}, arguments), options_fone);
            }
        };
    $('[name="cnpj_cpf"], .document').mask(behavior_document, documento_options);

    // Aciona a validação ao sair do input
    $('[name="cnpj_cpf"], .document').blur(function () {
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

    $('#image_perfil').text(intials.toUpperCase());

    $('[data-toggle="popover"]').popover();

    // ###################### CARREGA MODAL COMPENENTES ###################### //
        $(document).on('click', '.charge-modal', function () {
            var infos = $(this).data('info') || {};
            var data_url = $(this).data('url');
            var tag_event = $(this).data('tag_event');

            infos['tag_event'] = tag_event;

            if (data_url) {
                Swal.fire({
                    title: 'Carregando Informações...',
                    // allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: data_url,
                    type: 'POST',
                    data: infos,
                    success: (data) => {
                        Swal.close();
                        $('body').append(data); // Inserindo o modal no final do  body
                        $(tag_event).modal('show'); // Ativando o modal

                        $('[name="postal_code"]').mask('00000-000');
                        $('[name="post_code"], .post_code').mask('00000-000');
                        $('[name="number"], .number').mask('0000000000');
                        $('[name="phone1"]').mask('(00) 0000-0000');
                        $('[name="phone2"], .phone2').mask('(00) 00000-0000');
                        $('.phone').mask('(00) 00000-0000');

                        $('[name="cnpj_cpf"], .document').mask(behavior_document, documento_options);

                        busca_state();

                        $('[data-eachfun="true"]').each(function () {
                            if ($(this).data('event') == 'click') {
                                if ($(this).attr('disabled')) {
                                    $(this).prop('disabled', false).trigger($(this).data('event')).prop('disabled', true);
                                } else {
                                    $(this).trigger($(this).data('event'));
                                }
                            } else {
                                $(this).trigger($(this).data('event'));
                            }
                        });

                        $('.select2').select2();

                        $('.max-caracteres').each(function () {
                            var max_caracteres = $(this).data('max_caracteres') || 255;
                            $(this).parent().find('label').append('<span class="ml-2 count-max-caracteres-length">(max. caracteres ' + max_caracteres + ')</span>')
                        });

                        // Removendo o modal quando fechar
                        $(document).on('hidden.bs.modal', tag_event, function () { $(this).remove(); });
                    },
                    error: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Houve um erro na solicitação, contate o administrador!'
                        });
                    }
                });
            }
        });
    // ####################################################################### //

    // ####################################################################### //
    // ###################### FUNÇÕES GERAIS ################################ //
        //#################//
        $(document).on('click', '.btn-delete', function(){
            var table_html = $(this).data('table_html');
            var url = $(this).data('url');
            var id = $(this).data('id');

            var parametros = obterParametrosDaURL();
            parametros['id'] = id;

            Swal.fire({
                title: 'Atenção!',
                text: "Você está prestes a apagar uma informação, ao fazer isso ela sera deletada para sempre e seu dados vinculados a ela e não podera ser recuperada!",
                icon: 'warning',
                showCancelButton: true,
                // confirmButtonColor: '#3085d6',
                // cancelButtonColor: '#d33',
                confirmButtonText: 'Sim',
                cancelButtonText: 'Não',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Apagando informações, aguarde...',
                        allowOutsideClick: false,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: parametros,
                        success: (data) => {
                            var msg = 'Dados Apagados com sucesso!';

                            if(table_html){
                                if(data.table_html){
                                    $(table_html).html(data.table_html);
                                }
                            }

                            Toast.fire({
                                icon: 'success',
                                title: msg
                            });
                        },
                        error: (err) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Houve um erro ao apagar os dados, contate o administrador!'
                            });
                        }
                    });
                }
            });
        });
    // ####################################################################### //

    $('.textarea').summernote({
        height: 300,
        minHeight: null,
        maxHeight: null,
        dialogsInBody: true,
        dialogsFade: false
    });

    $('.date-mask').daterangepicker({
        singleDatePicker: false,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY',
            daysOfWeek: ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sab'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'outubro', 'Novembro', 'Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    });
    $('.date-mask-single').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY',
            daysOfWeek: ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sab'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'outubro', 'Novembro', 'Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    });

    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function (event) {
        $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    });

    // Busca dos estados
    if ($('[name="state"], .state')) busca_state();

    // Busca das cidades/municipios
    $(document).on('change', '[name="state"], .state', function () {
        let sigla_id = $(this).find(':selected').data('sigla_id');
        let select = $(this).parent().parent().find('select[name="city"], .city');
        var city_selected = select.find('option:selected').val();

        $.ajax({
            url: 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/' + sigla_id + '/municipios',
            type: 'GET',
            success: (data) => {
                // console.log(data);
                select.empty();
                select.append('<option value="">::Selecione uma Opção::</option>');
                if (select.is('.entrega')) {
                    select.append('<option value="Toda Região">Toda Região</option>');
                }

                for (var i = 0; data.length > i; i++) {
                    select.append('<option value="' + data[i].nome + '">' + data[i].nome + '</option>');
                }

                select.val(city_selected);
            }
        });
    });

    $(document).on('keyup blur', '[name="postal_code"]', function () {
        if ($(this).val().length == 9) {
            $.ajax({
                url: '/cep/' + $(this).val(),
                type: 'GET',
                success: (data) => {
                    $('[name="address"]').val(data.logradouro);
                    $('[name="address2"]').val(data.bairro);
                    $('[name="state"]').val(data.uf);
                    if (data.uf) {
                        $('[name="state"]').trigger('change');
                    }
                    setTimeout(() => {
                        $('[name="city"]').val(data.localidade);
                        if (data.localidade) {
                            $('[name="city"]').trigger('change');
                        }
                    }, 1000);

                    $('[name="number"]').focus();
                }
            });
        }
    });

    $(document).on('keyup blur', '[name="post_code"], .post_code', function () {
        $(this).parent().parent().find('input, select').attr('readonly', false);
        $(this).parent().parent().find('input, select').trigger('change');

        if ($(this).val().length == 9) {
            $('.loadCep').removeClass('d-none');
            $.ajax({
                url: '/cep/' + $(this).val(),
                type: 'GET',
                success: (data) => {
                    $('[name="address"], .address').val(data.logradouro);
                    $('[name="address2"], .address2').val(data.bairro);
                    $('[name="state"], .state').val(data.uf);
                    if (data.uf) {
                        $('[name="state"], .state').trigger('change');
                    }
                    setTimeout(() => {
                        $('[name="city"], .city').val(data.localidade);
                        if (data.localidade) {
                            $('[name="city"], .city').trigger('change');
                        }
                    }, 1000);

                    $('[name="number"], .number').focus();
                    $('.loadCep').addClass('d-none');
                }
            });
        }
    });

    $(document).on('click', '.apagar-produto', function (e) {
        e.preventDefault();
        var url = $(this).data('href');

        Swal.fire({
            icon: 'error',
            title: 'Apagar Produto?',
            showCancelButton: true,
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
    $(document).on('click', '.apagar-service', function (e) {
        e.preventDefault();
        var url = $(this).data('href');

        Swal.fire({
            icon: 'error',
            title: 'Apagar Serviço?',
            showCancelButton: true,
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    // Summernote se perde quando esta em um Modal então atualziamos o index dos modais e a função do summernote
    $(document).on("show.bs.modal", '#novoProduto, #editarProduto', function (event) {
        // console.log("Global show.bs.modal fire");
        var zIndex = 1050 + (10 * $(".modal:visible").length);
        $(this).css("z-index", zIndex);
        setTimeout(function () {
            $(".modal-backdrop").not(".modal-stack").first().css("z-index", zIndex - 1).addClass("modal-stack");
        }, 0);
    }).on("hidden.bs.modal", '#novoProduto, #editarProduto', function (event) {
        // console.log("Global hidden.bs.modal fire");
        $(".modal:visible").length && $("body").addClass("modal-open");
    });
    $(document).on("show.bs.modal", '#novaCategoria', function (event) {
        // console.log("Global show.bs.modal fire");
        var zIndex = 1051 + (10 * $(".modal:visible").length);
        $(this).css("z-index", zIndex);
        setTimeout(function () {
            $(".modal-backdrop").not(".modal-stack").first().css("z-index", zIndex - 1).addClass("modal-stack");
        }, 0);
    }).on("hidden.bs.modal", '#novaCategoria', function (event) {
        // console.log("Global hidden.bs.modal fire");
        $(".modal:visible").length && $("body").addClass("modal-open");
    });

    // modal para o note-modal -- colcando o index do summernote mais alto so para os popovers
    $(document).on("show.bs.modal", '.note-modal', function (event) {
        // console.log("Global show.bs.modal fire");
        var zIndex = 10000 + (10 * $(".modal:visible").length);
        $(this).css("z-index", zIndex);
        setTimeout(function () {
            $(".modal-backdrop").not(".modal-stack").first().css("z-index", zIndex - 1).addClass("modal-stack");
        }, 0);
    }).on("hidden.bs.modal", '.note-modal', function (event) {
        // console.log("Global hidden.bs.modal fire");
        $(".modal:visible").length && $("body").addClass("modal-open");
    });
    $(document).on('inserted.bs.tooltip', function (event) {
        // console.log("Global show.bs.tooltip fire");
        var zIndex = 100000 + (10 * $(".modal:visible").length);
        var tooltipId = $(event.target).attr("aria-describedby");
        $("#" + tooltipId).css("z-index", zIndex);
    });
    $(document).on('inserted.bs.popover', function (event) {
        // console.log("Global inserted.bs.popover fire");
        var zIndex = 100000 + (10 * $(".modal:visible").length);
        var popoverId = $(event.target).attr("aria-describedby");
        $("#" + popoverId).css("z-index", zIndex);
    });

    // Nomes das Imagens nos inputs
    $('.custom-file input').change(function (e) {
        var files = [];
        for (var i = 0; i < $(this)[0].files.length; i++) {
            files.push($(this)[0].files[i].name);
        }
        $(this).next('.custom-file-label').html(files.join(', '));
    });

    // Funções extras da função de salvamento geral
    // Quando da success no ajax
    function funcaoSuccessExtra(data, target) {
        switch (target) {
            case '#postProductNovaCategoria': // Função do novo/editar Produto para poder criar novas categorias e avisar a view
                var modal_form = '#postEditarProduto'; // Setamos por padrão o modal_form por padrão

                if ($('#novoProduto').is(':visible')) modal_form = '#postNovoProduto'; // Caso o modal do novo produto esteja aberto, seta o novo modal_form

                // Verificamos se a categroias principal esta selecionada para que possa criar uma nova categroia principa
                if ($(modal_form).find('.main_category option:selected').data('new_category') == 'category_id') {
                    $(modal_form).find('.main_category').empty(); // Limpamos o campo da categoria princpal para que possa fazer a busca novamente

                    $(modal_form).find('.main_category').append('<option value="" data-new_category="category_id"> - Nova Categoria - </option>'); // adicionar sempre a opção nova categoria

                    var categories = buscaCategoria({ category_id: 'S' }); // busca especifica das categorias

                    // Lendo os dados recebidos das categorias e setando normalmnte
                    $.each(categories.responseJSON, (key, value) => {
                        $(modal_form).find('.main_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
                // Caso a categoria principal não seja encontrada, chama o proximo para gerar categoria
                if ($(modal_form).find('.main_category option:selected').data('new_category') !== 'category_id') {
                    let subCategories = $(modal_form).find('.sub_category').val();
                    $(modal_form).find('.sub_category').empty(); // Limpamos a sub catgeoria para prencher com as novas

                    var categories = buscaCategoria({ parent_id: $(modal_form).find('.main_category option:selected').val() }); // busca especifica das categorias
                    // Lendo os dados recebidos das categorias e setando normalmnte
                    $.each(categories.responseJSON, (key, value) => {
                        $(modal_form).find('.sub_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });

                    if (subCategories.length > 0) {
                        $(modal_form).find('.sub_category').val(subCategories);
                        $(modal_form).find('.sub_category').trigger('change');
                    }
                }
                break;
            case '#postEditarCategoria':
                // $('.tr-id-'+data.category_id).children().eq(1).text(data.category_name);
                break;
            case '#postEditarProduto':
                $('.tr-id-' + data.product_id).children().eq(1).html('<img width="100px" src="' + data.image_data + '">');
                $('.tr-id-' + data.product_id).children().eq(2).html(data.product_name);
                $('.tr-id-' + data.product_id).children().eq(3).html(data.measured_unit);
                $('.tr-id-' + data.product_id).children().eq(4).html(data.sales_uni);
                $('.tr-id-' + data.product_id).children().eq(5).html(data.value);
                $('.tr-id-' + data.product_id).find('.btn-editar').data('images', data.dados_image);
                $('.tr-id-' + data.product_id).find('.btn-editar').data('dados', data.dados);
                break;
            case '#postEditarPromocao':
                $('.tr-id-' + data.promotion_id).children().eq(2).html(data.value);
                $('.tr-id-' + data.promotion_id).children().eq(3).html(data.start_date);
                $('.tr-id-' + data.promotion_id).children().eq(4).html(data.final_date);
                $('.tr-id-' + data.promotion_id).find('.btn-editar').data('dados', data.dados);
                break;
        }
    }
    // Antes do ajax
    function funcaoEventoExtra(data, target) {
        switch (target) {
            case '#postProductNovaCategoria': // Uma função antes de dar sucesso para que possamos controlar alguma coisa
                var modal_form = '#postEditarProduto'; // Setamos por padrão o modal_form por padrão

                if ($('#novoProduto').is(':visible')) modal_form = '#postNovoProduto'; // Caso o modal do novo produto esteja aberto, seta o novo modal_form

                // Verificando se a categoria principal esta selcionada
                if ($(modal_form).find('.main_category option:selected').data('new_category') == 'category_id') {
                    $(modal_form).find('.sub_category').empty(); // limpando a subcategoria para não haver problemas

                    $(target).find('[name="parent_id"]').val(""); // limpando o parente ainda na nova categroia quando for a principal

                    $(modal_form).find('.sub_category').append('<option value="" data-new_category="parent_id"> - Nova Sub Categoria- </option>');
                }
                break;
            case '#postEditarCategoria':

                break;
            case '#postEditarProduto':

                break;
            case '#postEditarPromocao':

                break;
        }
    }

    // Busca dos estados
    $(function () {
        if ($('[name="estado"]')) {
            $.ajax({
                url: '/buscaEstado',
                type: 'GET',
                success: (data) => {
                    // console.log(data);
                    for (var i = 0; data.length > i; i++) {
                        $('[name="estado"]').append('<option value="' + data[i].sigla + '" data-estado_id="' + data[i].id + '">' + data[i].sigla + ' - ' + data[i].titulo + '</option>').selectpicker('refresh');
                    }
                }
            });
        }
        if ($('[name="edit_estado"]')) {
            $.ajax({
                url: '/buscaEstado',
                type: 'GET',
                success: (data) => {
                    // console.log(data);
                    for (var i = 0; data.length > i; i++) {
                        $('[name="edit_estado"]').append('<option value="' + data[i].sigla + '" data-estado_id="' + data[i].id + '">' + data[i].sigla + ' - ' + data[i].titulo + '</option>').selectpicker('refresh');
                    }
                }
            });
        }
        if ($('[name="estado_id"]')) {
            $.ajax({
                url: '/buscaEstado',
                type: 'GET',
                success: (data) => {
                    // console.log(data);
                    for (var i = 0; data.length > i; i++) {
                        $('[name="estado_id"]').append('<option value="' + data[i].id + '" data-estado_id="' + data[i].id + '">' + data[i].sigla + ' - ' + data[i].titulo + '</option>');
                    }
                }
            });
        }
        if ($('[name="edit_estado_id"]')) {
            $.ajax({
                url: '/buscaEstado',
                type: 'GET',
                success: (data) => {
                    // console.log(data);
                    for (var i = 0; data.length > i; i++) {
                        $('[name="edit_estado_id"]').append('<option value="' + data[i].id + '" data-estado_id="' + data[i].id + '">' + data[i].sigla + ' - ' + data[i].titulo + '</option>');
                    }
                }
            });
        }
    });

    // Busca das cidades/municipios
    $(document).on('change', '[name="estado"]', function () {
        let estado_id = $(this).find(':selected').data('estado_id');
        let select = $(this).parent().parent().parent().find('select[name="cidade"]');

        $.ajax({
            url: '/buscaCidade/' + estado_id,
            type: 'GET',
            success: (data) => {
                // console.log(data);
                select.empty().selectpicker('refresh');
                select.append('<option value="">- Selecione uma Cidade -</option>');

                for (var i = 0; data.length > i; i++) {
                    select.append('<option value="' + data[i].titulo + '" data-cidade_id="' + data[i].id + '">' + data[i].titulo + '</option>');
                }

                select.selectpicker('refresh')
            }
        });
    });

    // Busca das cidades/municipios
    $(document).on('change', '[name="cidade"]', function () {
        let cidade_id = $(this).find(':selected').data('cidade_id');

        $.ajax({
            url: '/buscaBairro/' + cidade_id,
            type: 'GET',
            success: (data) => {
                // console.log(data);
                if (data.length == 0) {
                    $('.bairro_select').addClass('d-none').removeAttr('name');
                    $('.bairro_input').removeClass('d-none').attr('name', 'bairro');
                } else {
                    $('.bairro_select').removeClass('d-none').attr('name', 'bairro[]');
                    $('.bairro_input').addClass('d-none').removeAttr('name');
                }

                $('select.bairro_select').empty().selectpicker('refresh');
                $('select.bairro_select').append('<option value="">- Selecione um Bairro -</option>');

                for (var i = 0; data.length > i; i++) {
                    $('select.bairro_select').append('<option value="' + data[i].titulo + '">' + data[i].titulo + '</option>');
                }

                $('select.bairro_select').selectpicker('refresh');
            }
        });
    });
    // Busca das cidades/municipios
    $(document).on('change', '[name="edit_estado"]', function () {
        let estado_id = $(this).find(':selected').data('estado_id');
        let select = $(this).parent().parent().parent().find('select[name="edit_cidade"]');

        $.ajax({
            url: '/buscaCidade/' + estado_id,
            type: 'GET',
            success: (data) => {
                // console.log(data);
                select.empty().selectpicker('refresh');
                select.append('<option value="">- Selecione uma Cidade -</option>');

                for (var i = 0; data.length > i; i++) {
                    select.append('<option value="' + data[i].titulo + '" data-cidade_id="' + data[i].id + '">' + data[i].titulo + '</option>');
                }

                select.selectpicker('refresh')
            }
        });
    });

    // Busca das cidades/municipios
    $(document).on('change', '[name="edit_cidade"]', function () {
        let cidade_id = $(this).find(':selected').data('cidade_id');
        var thiss = $(this).parent().parent().parent();

        $.ajax({
            url: '/buscaBairro/' + cidade_id,
            type: 'GET',
            success: (data) => {
                // console.log(data);
                if (data.length == 0) {
                    thiss.find('.bairro_select').addClass('d-none').removeAttr('name');
                    thiss.find('.bairro_input').removeClass('d-none').attr('name', 'edit_bairro');
                } else {
                    thiss.find('.bairro_select').removeClass('d-none').attr('name', 'edit_bairro');
                    thiss.find('.bairro_input').addClass('d-none').removeAttr('name');
                }

                thiss.find('select.bairro_select').empty().selectpicker('refresh');
                thiss.find('select.bairro_select').append('<option value="">- Selecione um Bairro -</option>');

                for (var i = 0; data.length > i; i++) {
                    thiss.find('select.bairro_select').append('<option value="' + data[i].titulo + '">' + data[i].titulo + '</option>');
                }
                thiss.find('select.bairro_select').selectpicker('refresh');
            }
        });
    });

    // Busca das cidades/municipios
    $(document).on('change', '[name="estado_id"]', function () {
        let estado_id = $(this).find(':selected').data('estado_id');
        let select = $(this).parent().parent().find('select[name="cidade_id"]');

        $.ajax({
            url: '/buscaCidade/' + estado_id,
            type: 'GET',
            success: (data) => {
                // console.log(data);
                select.empty();
                select.append('<option value="">- Selecione uma Cidade -</option>');

                for (var i = 0; data.length > i; i++) {
                    select.append('<option value="' + data[i].id + '" data-cidade_id="' + data[i].id + '">' + data[i].titulo + '</option>');
                }
            }
        });
    });
    // Busca das cidades/municipios
    $(document).on('change', '[name="edit_estado_id"]', function () {
        let estado_id = $(this).find(':selected').data('estado_id');
        let select = $(this).parent().parent().find('select[name="edit_cidade_id"]');

        $.ajax({
            url: '/buscaCidade/' + estado_id,
            type: 'GET',
            success: (data) => {
                // console.log(data);
                select.empty();
                select.append('<option value="">- Selecione uma Cidade -</option>');

                for (var i = 0; data.length > i; i++) {
                    select.append('<option value="' + data[i].id + '" data-cidade_id="' + data[i].id + '">' + data[i].titulo + '</option>');
                }
            }
        });
    });

    $('form').on('submit', function (e) {
        if ($(this).find('.btn-salvar').length > 0) {
            e.preventDefault();
            $(this).find('.btn-salvar').trigger('click');
        }
    });
    $('form').find('input').on('keyup', function (e) {
        e.preventDefault();
        if (e.keyCode == 13) {
            if (!$(this).is('.keywords-add') && !$(this).is('.address-maps')) {
                $(this).closest('form').find('.btn-salvar').trigger('click');
            }
        }
    });

    $(document).on('click', '.btn-ME-purchase', function () {
        var id = $(this).data('id');

        $('#MEPurchase').find('[name="id"]').val(id);
        $('#MEPurchase').find('.agency').removeClass('d-none');
        $('#MEPurchase').find('#agency').empty();

        $.ajax({
            url: 'melhor_envio/dados/' + id,
            type: 'GET',
            success: (data) => {
                console.log(data);

                if (data.agencies) {
                    for (var i = 0; data.agencies.length > i; i++) {
                        $('#MEPurchase').find('#agency').append('<option value="' + data.agencies[i].id + '" data-dados="' + JSON.stringify(data.agencies[i]) + '">' + data.agencies[i].company_name + ' - ' + data.agencies[i].name + '</option>');
                    }
                } else {
                    $('#MEPurchase').find('.agency').addClass('d-none');
                }

                $('#MEPurchase').find('._name_service').html(data.service.name);
            }
        });
    });

    // Condifgurações
    var Toast = Swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 4000
    });

    let table_one = $("#table_one").DataTable({
        "responsive": false,
        "autoWidth": false,
        "language": language_pt_br,
        "buttons": ["copy", "csv", "colvis"],
        'order': [[1, 'desc']],
    }).buttons().container().appendTo('#table_one_wrapper .col-md-6:eq(0)');

    // Para as variações do atributo
    $('.img_icon').on("change", function () {
        var form_img = $(this).parent().parent();
        form_img.find('.img-icon').empty();

        var preview = form_img.find('.img-icon');
        var files = form_img.find('.img_icon').prop('files');

        function readAndPreview(file) {
            // Make sure `file.name` matches our extensions criteria
            if (/\.(jpe?g|png|gif)$/i.test(file.name)) {
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    var image = new Image();
                    image.classList = 'rounded';
                    image.width = 45;
                    image.title = file.name;
                    image.src = this.result;
                    preview.append(image);
                }, false);

                reader.readAsDataURL(file);
            }
        }

        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    });

    $('[name="logo_path"]').on("change", function () {
        $(this).parent().find('.logo_path').empty();

        var preview = $(this).parent().find('.logo_path');
        var files = $(this).prop('files');

        function readAndPreview(file) {
            // Make sure `file.name` matches our extensions criteria
            if (/\.(jpe?g|png|gif)$/i.test(file.name)) {
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    var image = new Image();
                    image.classList = 'rounded';
                    image.width = 100;
                    image.title = file.name;
                    image.src = this.result;
                    preview.append(image);
                }, false);

                reader.readAsDataURL(file);
            }
        }

        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    });
    $('[name="banner_path"]').on("change", function () {
        if ($(this).is('.custom-file-input')) {
            $(this).parent().parent().find('.banner_path').empty();
        } else {
            $(this).parent().find('.banner_path').empty();
        }

        if ($(this).is('.custom-file-input')) {
            var preview = $(this).parent().parent().find('.banner_path');
        } else {
            var preview = $(this).parent().find('.banner_path');
        }
        var files = $(this).prop('files');

        function readAndPreview(file) {
            // Make sure `file.name` matches our extensions criteria
            if (/\.(jpe?g|png|gif)$/i.test(file.name)) {
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    var image = new Image();
                    image.classList = 'rounded';
                    image.width = 280;
                    image.title = file.name;
                    image.src = this.result;
                    preview.append(image);
                }, false);

                reader.readAsDataURL(file);
            }
        }

        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    });
    $('[name="banner_path_two"]').on("change", function () {
        if ($(this).is('.custom-file-input')) {
            $(this).parent().parent().find('.banner_path_two').empty();
        } else {
            $(this).parent().find('.banner_path_two').empty();
        }

        if ($(this).is('.custom-file-input')) {
            var preview = $(this).parent().parent().find('.banner_path_two');
        } else {
            var preview = $(this).parent().find('.banner_path_two');
        }
        var files = $(this).prop('files');

        function readAndPreview(file) {
            // Make sure `file.name` matches our extensions criteria
            if (/\.(jpe?g|png|gif)$/i.test(file.name)) {
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    var image = new Image();
                    image.classList = 'rounded';
                    image.width = 280;
                    image.title = file.name;
                    image.src = this.result;
                    preview.append(image);
                }, false);

                reader.readAsDataURL(file);
            }
        }

        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    });

    // Limpa toda a area do modal novo
    $(document).on('click', '[data-toggle="modal"]', function () {
        $('.modal-content').find('.overlay').remove();
        $('.modal-content').find('.perecivel').removeClass('d-none');
        $('.modal-content').find('#frete_gratis').prop('checked', true).trigger('click');
        // $('.modal-content').find('.select-attr').val([]).trigger('change');

        // Especifico produto
        $('#postNovoProduto').find('.fatores-negacao').empty().addClass('d-none');
        $('#postNovoProduto').find('.fatores-negacao-nativo').empty().addClass('d-none');
        $('#postNovoProduto').find('.aprovacao').remove();
        $('#postNovoProduto').find('.btns-apro-neg').addClass('d-none');
    });
    // Função salva dados gerais
    $(document).on('click', '.btn-salvar', function () {
        // Pegamos os dados do data
        let save_target = $(this).data('save_target');
        let save_route = $(this).data('save_route');
        let update_table = $(this).data('update_table');
        let table_html = $(this).data('table_html') || null;
        let refresh = $(this).data('refresh');
        let table_trash = $(this).data('trash');

        // Função extra antes de chamar o ajax para resolver antes de entrar aqui
        funcaoEventoExtra($(save_target).serializeArray(), save_target);

        // Por mais que tenha erro, limpamos para os outros que não tenha
        $(save_target).find('input').removeClass('is-invalid');
        $(save_target).find('.invalid-feedback').remove();

        // Pegamos o parente do id para adicionar um modelo de carregamento
        let modal = $(save_target).parent();
        if (modal.is('.modal-content')) modal.append('<div class="overlay d-flex justify-content-center align-items-center"><i class="fas fa-2x fa-sync fa-spin"></i></div>');

        var isValid = true;

        $(save_target).find('.required').each(function () {
            if ($(this).val() == '' || $(this).val() == '0,00') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (isValid) {
            $.ajax({
                url: save_route,
                type: "POST",
                data: new FormData($(save_target)[0]),
                cache: false,
                contentType: false,
                processData: false,
                success: (data) => {
                    // console.log(data);
                    // Procuramos a div adcionada recentemente para removemos e fechamos o modal
                    $(modal).find('.overlay').remove();
                    $(modal).parent().parent().modal('hide');

                    $(save_target).find('input[type="text"]').val('');
                    $(save_target).find('input[type="checkbox"]').prop('checked', false).trigger('change');
                    $(save_target).find('.perecivel').removeClass('d-none');
                    $(save_target).find('select').val('').trigger('change');
                    $(save_target).find('.select2').val([]).trigger('change');
                    $(save_target).find('.selectpicker').val('').trigger('change');
                    $(save_target).find('input[type="number"]').val('');
                    $(save_target).find('input[type="file"]').val('').next('.custom-file-label').html('Imagem Pequena (45x45)');
                    $(save_target).find('.img-icon').empty();
                    $(save_target).find('.btn-remove-image').trigger('click');
                    $(save_target).find('input, select').attr('readonly', false);
                    $(save_target).find('.note-editable').empty();
                    $(save_target).find('.check-variation').prop('checked', false);
                    // $(save_target).find('.select-attr').val('').trigger('change');
                    // $(save_target).find('.select-attr').empty();
                    $(save_target).find('.variacoes').empty();

                    // Especifico produto
                    $('#postNovoProduto').find('.aprovacao').remove();

                    if (update_table == 'S') if (data.table) $('table tbody').append(data.table); // Inserindo novos dados
                    if (update_table == 'S') if (data.tb_up) $('table tbody').find('.tr-id-' + data.tb_id).html(data.tb_up); // Editando dados

                    if (table_html) {
                        if (data.table_html) $(table_html).html(data.table_html);
                    }

                    if (table_trash == 'S') { // Somente quando for apagar
                        if (data.tb_trash) $('table tbody').find('.tr-id-' + data.tb_trash).remove();

                        Toast.fire({
                            icon: 'success',
                            title: 'Os dados foram excluidos com successo!'
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Os dados foram salvos com successo!'
                        });
                    }

                    if (refresh == 'S') setTimeout(() => { window.location.reload(); }, 1200);

                    funcaoSuccessExtra(data, save_target);
                },
                error: (err) => {
                    // console.log(err);
                    $(modal).find('.overlay').remove();

                    console.log(err.status);
                    if(err.status == 500) {
                        Swal.fire({
                            icon: 'error',
                            text: 'Erro interno do servidor, contate o Administrador!',
                        });
                    }

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
        } else {
            $(modal).find('.overlay').remove();
        }
    });

    // Passar os dados nos campos paranão puxar um por e sim recueprar em json em um atributo
    $(document).on('click', '.btn-editar', function () {
        var target = $(this).data('target'); // qual modal ta sendo acessado
        var dados = $(this).data('dados'); // dados que serão passados aos campos
        var images = $(this).data('images'); // dados que serão passados aos campos

        $(target).find('.keywords_add').empty();

        // Fazemos uma leitura dosa campos
        var data = '';
        var estado = null;
        var cidade = null;
        var bairro = null;
        var estado_id = null;
        var cidade_id = null;
        var bairro_name = null;
        var banco = null;
        var conta = null;
        var tipo_conta = null;
        $.each(dados, (key, value) => {
            if (key !== 'icon' && key !== 'banner_path') {
                $(target).find('[name="' + key + '"').val(value); // os campos name são iguais aos das colunas vidna do banco
            }
            // $(target).find('[name="edit_'+key+'"').val(value); // os campos name são iguais aos das colunas vidna do banco
            $(target).find('.' + key).val(value); // quando o campo name por motivos especiais for diferente, pega por class tambem

            $(target).find('._' + key).text(value); // qunado campo for texto

            if (key == 'keywords') {
                for (var i = 0; value.length > i; i++) {
                    $('.keywords_adds').append(`
                        <tr>
                            <td class="border-right border-bottom" width="5%"><button type="button" class="btn py-0 btn-remove-keyword">x</button></td>
                            <td class="border-bottom"><input type="hidden" name="keywords[]" value="${value[i]}">  <span class="ml-2">${value[i]}</span></td>
                        </tr>
                    `);
                }
            }

            if (key == 'banner_path') {
                $(target).find('.banner_path').html('<img class="rounded" width="280" src="/storage/' + value + '">');
            }

            // Especifico para o modal editarProduto
            if (key == 'description') {
                $(target).find('.note-editable').html(value);
            }

            if (key == 'estado') {
                estado = value;
            }
            if (key == 'cidade') {
                cidade = value;
            }
            if (key == 'bairro') {
                bairro = value;
            }
            if (key == 'localidade_estado_id') {
                estado_id = value;
            }
            if (key == 'localidade_municipio_id') {
                cidade_id = value;
            }
            if (key == 'titulo') {
                bairro_name = value;
            }

            if (key == 'toda_cidade') {
                if (value == 1) {
                    $(target).find('[name="' + key + '"').prop('checked', true).trigger('change');
                }
            }
            if (key == 'em_todas_cidades') {
                if (value == 1) {
                    $(target).find('[name="' + key + '"').prop('checked', true).trigger('change');
                }
            }

            if (key == 'tempo') {
                $(target).find('[name="' + key + '"]').trigger('change');
            }
            if (key == 'semana') {
                $.each(value, (key, value) => {
                    $('.semana').find('[value="' + value + '"]').prop('checked', true);
                });
            }

            if (key == 'frete_gratis') {
                if (value == 1) {
                    $(target).find('[name="' + key + '"]').trigger('click');
                }
            }

            if (key == 'price') {
                if (value > 0) {
                    $(target).find('[name="' + key + '"]').val(value.toString().replace(/\./g, ','));
                }
            }

            if (key == 'bank') {
                banco = value;
            }

            if (key == 'price_type') {
                if (value == 'percentage') {
                    $(target).find('.price-0').prop('checked', true);
                }
                else if (value == 'money') {
                    $(target).find('.price-1').prop('checked', true);
                }
            }

            if (key == 'type') {
                if (value == 'checking') {
                    conta = 'conta_corrente';
                }
                else if (value == 'savings') {
                    conta = 'conta_poupanca';
                }
            }

            if (key == 'holder_type') {
                if (value == 'individual') {
                    tipo_conta = '';
                }
                else if (value == 'company') {
                    tipo_conta = '_conjunta';
                }
            }
        });

        setTimeout(() => {
            $(target).find('[name="type"]').val(conta + tipo_conta);
            $(target).find('.bank').selectpicker('val', banco).trigger('change');
        }, 200);

        setTimeout(() => {
            $(target).find('[name="edit_estado"]').selectpicker('val', estado).trigger('change');
            setTimeout(() => {
                $(target).find('[name="edit_cidade"]').selectpicker('val', cidade).trigger('change');
                setTimeout(() => {
                    $(target).find('[name="edit_bairro"]').selectpicker('val', bairro);
                }, 1700);
            }, 1100);
        }, 700);

        setTimeout(() => {
            $(target).find('[name="edit_estado_id"]').val(estado_id).trigger('change');
            setTimeout(() => {
                $(target).find('[name="edit_cidade_id"]').val(cidade_id);
                setTimeout(() => {
                    $(target).find('[name="edit_bairro_name"]').val(bairro_name);
                }, 1200);
            }, 800);
        }, 400);
    });

    // Para produtos e serviços
    $(document).on('click', '[name="stock_controller"]', function (e) {
        if ($(this).prop('checked')) {
            if ($('[name="check_variation"]').prop('checked') !== true) {
                $('.stock').removeClass('d-none');
                $('.vaga').removeClass('d-none');
            }
            $('.stock-attr').removeClass('d-none');
            $('.vaga-attr').removeClass('d-none');
        } else {
            if ($('[name="check_variation"]').prop('checked') !== true) {
                $('.stock').addClass('d-none');
                $('.vaga').addClass('d-none');
                $('.stock').find('input').val('');
                $('.vaga').find('input').val('');
            }
            $('.stock-attr').addClass('d-none');
            $('.vaga-attr').addClass('d-none');
            $('.stock-attr').find('input').val('');
            $('.vaga-attr').find('input').val('');
        }
    });

    // Adicionando imagem
    $(document).on('click', '.btn-add-image', function (e) {
        e.preventDefault();
        $(this).parent().find('.add-image').trigger('click');
    });
    $(document).on('change', '.add-image', function () {
        $(this).removeClass('add-image');

        $(this).parent().find('.btn-add-image').removeClass('btn-success btn-add-image').addClass('btn-danger btn-remove-image').html('x');

        var count_i = 0;
        for (var i = 0; ($(this).parent().parent().find('.imgs-count').length + 1) > i; i++) {
            if (!$(this).parent().parent().find('.imgs-count').is('.img-find-' + i)) {
                count_i = i;
            }
        }

        $(this).parent().parent().prepend(
            '<div class="col-6 col-md-3 imgs-count img-find-' + count_i + '">' +
            '<button type="button" class="btn btn-success btn-add-image">+</button>' +
            '<input type="file" class="d-none add-image" name="imagem[' + count_i + '][img]">' +
            '<input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[' + count_i + '][name]">' +
            '<div class="image my-2"></div>' +
            '</div>'
        );

        var form_img = $(this).parent();

        var preview = form_img.find('.image');
        var file_title = form_img.find('.file-title');
        var files = $(this).prop('files');

        if (!$(this).parent().parent().is('.not-display-input')) {
            $(this).parent().find('.file-title').removeClass('file-title d-none');
        }

        function readAndPreview(file) {
            // Make sure `file.name` matches our extensions criteria
            if (/\.(jpe?g|png|gif)$/i.test(file.name)) {
                var reader = new FileReader();

                file_title.val(file.name);

                reader.addEventListener("load", function () {
                    var image = new Image();
                    image.classList = 'rounded img-fluid img-bordered';
                    // image.height = 180;
                    image.title = file.name;
                    image.src = this.result;
                    preview.append(image);
                }, false);

                reader.readAsDataURL(file);
            }
        }

        if (files) {
            [].forEach.call(files, readAndPreview);
        }
    });
    $(document).on('click', '.btn-remove-image', function () {
        $(this).parent().remove();
    });

    // Apagar a Categoria -- especifico categoria
    $(document).on('click', '.btn-excluir-categoria', function () { // Verificamos os dados passados
        var target = $(this).data('target');
        var dados = $(this).data('dados');

        $(target).find('.modal-title').html('Excluir Categoria <strong>"' + dados.name + '"</strong>'); // Passamos o titulo da categoria
        $(target).find('[name="id"]').val(dados.id); // o id da categoria caso for ser excluida

        $.ajax({ // Busca de produtos que estejam vinculados a categoria ou subcategorias vinculados a categoria
            url: '/admin/cadastro/pesquisa_categoria_produto',
            type: 'POST',
            data: { id: dados.id },
            success: (data) => {
                // console.log(data);
                $(target).find('.modal-body').empty();

                // Verificando se é produto ou subcategoria vinculada
                if (data.tipo == 'produto') {
                    if (data.dados.length > 0) {
                        $(target).find('.modal-footer').find('button').eq(1).addClass('d-none');
                        $(target).find('.modal-body').append('<p>Essa categoria não pode ser excluida porque possui <strong>' + data.dados.length + '</strong> Produtos vinculados!</p>');
                        $(target).find('.modal-body').append('<p>Inative os Produtos vinculados ou altere a Categoria vinculado ao Produto para excluir!</p>');
                    } else {
                        $(target).find('.modal-footer').find('button').eq(1).removeClass('d-none');
                        $(target).find('.modal-body').append('<p>Tem certeza que deseja excluir essa categoria?</p>');
                    }
                } else if (data.tipo == 'categoria') {
                    if (data.dados.length > 0) {
                        $(target).find('.modal-footer').find('button').eq(1).addClass('d-none');
                        $(target).find('.modal-body').append('<p>Essa categoria não pode ser excluida porque possui <strong>' + data.dados.length + '</strong> Sub Categorias vinculados!</p>');
                        $(target).find('.modal-body').append('<p>Exclua as Sub Categorias vinculadas para excluir a Principal!</p>');
                    } else {
                        $(target).find('.modal-footer').find('button').eq(1).removeClass('d-none');
                        $(target).find('.modal-body').append('<p>Tem certeza que deseja excluir essa categoria?</p>');
                    }
                }
            }
        });
    });
    // Fazendo a exclusão da categoria
    $(document).on('click', '.btn-confirma-exclusao-categoria', function () {
        let modal = $('#postExcluirCategoria').parent();
        if (modal.is('.modal-content')) modal.append('<div class="overlay d-flex justify-content-center align-items-center"><i class="fas fa-2x fa-sync fa-spin"></i></div>');

        $.ajax({ // Excluir categoria
            url: '/admin/cadastro/excluir_categoria',
            type: 'POST',
            data: $('#postExcluirCategoria').serialize(),
            success: (data) => {
                $(modal).find('.overlay').remove();
                $(modal).parent().parent().modal('hide');

                $('.tr-id-' + data.category_id).remove();

                Toast.fire({
                    icon: 'success',
                    title: 'Os dados foram apagados com successo!'
                });
            },
            error: (err) => {
                $(modal).find('.overlay').remove();
            }
        });
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
                                '<div class="col-12 col-md-4"><b>Recebido no Total: </b>' + (parseFloat(value[i].net_received_amount) || 0).toFixed(2).toString().replace('.', ',') + '</div>' +
                                '<div class="col-12 col-md-4"><b>Taxa Paga a Operadora: </b>' + value[i].rate_mp + '</div>' +
                                '<div class="col-12 col-md-4"><b>Metodo de Pagamento: </b>' + value[i].payment_method_id + '</div>' +
                                '<div class="col-12 col-md-4"><b>Tipo de Pagamento: </b>' + value[i].payment_type_id + '</div>' +
                                '<div class="col-12 col-md-4"><b>Nome do Pagador: </b>' + value[i].payer_name + '</div>' +
                                '<div class="col-12 col-md-4"><b>CNPJ/CPF do Pagador: </b>' + value[i].payer_cnpj_cpf + '</div>' +
                                '</div>'
                            );
                    }
                    break;
            }
        });
    });

    $(document).on('click', '.codigoAdd', function () {
        var order_number = $(this).data('order_number');
        var url = $(this).data('url');

        Swal.fire({
            title: 'Adicionar Código de Rastreio',
            // input: 'text',
            // inputAttributes: {
            //     autocapitalize: 'off'
            // },
            html:
                '<input id="swal-input1" class="swal2-input" placeholder="Codigo/Texto">' +
                '<input id="swal-input2" class="swal2-input" placeholder="Link">' +
                '<select id="swal-input3" class="swal2-input">' +
                '<option value="proprio">Transporte Próprio</option>' +
                '<option value="https://www.websro.com.br/rastreamento-correios.php?P_COD_UNI={code}">Correios</option>' +
                '<option value="https://www.jadlog.com.br/tracking?cte={code}">Jadlog</option>' +
                '</select>',
            showCancelButton: true,
            confirmButtonText: 'Adicionar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
            preConfirm: () => {
                return [
                    document.getElementById('swal-input1').value,
                    document.getElementById('swal-input2').value,
                    document.getElementById('swal-input3').value
                ]
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Salvando, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { order_number, codigo: result.value[0], link: result.value[1], rastreio_url: result.value[2] },
                    success: (data) => {
                        // console.log(data);
                        window.location.reload();
                    }
                });
            }
        });
    });
    $(document).on('change', '#swal-input3', function () {
        $('#swal-input2').removeClass('d-none');
        if ($(this).val() !== 'proprio') {
            $('#swal-input2').addClass('d-none');
        }
    });

    $(document).on('click', '.alterarStatusOrder', function () {
        var order_number = $(this).data('order_number');
        var url = $(this).data('url');

        Swal.fire({
            icon: 'warning',
            title: 'Alterar Status do Pedido?',
            input: 'select',
            inputOptions: {
                '0': 'Aguardando Pagamento',
                '1': 'Em Andamento',
                '2': 'Finalizado',
                '3': 'Cancelado',
            },
            inputPlaceholder: 'Selecione uma Opção',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                switch (result.value) {
                    case '0':
                        $('.tr-order-id-' + order_number).find('.btn-status-pay').css('background-color', '#fdc300').html('Aguardando Pagamento');
                        break;
                    case '1':
                        $('.tr-order-id-' + order_number).find('.btn-status-pay').css('background-color', '#58bc9a').html('Em Andamento');
                        break;
                    case '2':
                        $('.tr-order-id-' + order_number).find('.btn-status-pay').css({ 'background-color': '#c6d300', 'height': '52px' }).html('Finalizado');
                        break;
                    case '3':
                        $('.tr-order-id-' + order_number).find('.btn-status-pay').css({ 'background-color': '#db5812', 'height': '52px' }).html('Cancelado');
                        break;
                }
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { order_number: order_number, status: result.value },
                    success: (data) => {
                        // console.log(data);
                    }
                });
            }
        });
    });
    $(document).on('click', '.alterarStatusContact', function () {
        var btn = $(this);
        var id = $(this).data('id');
        var url = $(this).data('url');

        Swal.fire({
            icon: 'warning',
            title: 'Alterar Status do Contato?',
            input: 'select',
            inputOptions: {
                '0': 'Aguardando',
                '1': 'Em Andamento',
                '2': 'Já Resolvido',
            },
            inputPlaceholder: 'Selecione uma Opção',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                switch (result.value) {
                    case '0':
                        btn.parent().parent().find('.btn-status-contact').css('background-color', '#fdc300').html('Em Andamento');
                        break;
                    case '1':
                        btn.parent().parent().find('.btn-status-contact').css('background-color', '#58bc9a').html('Já Resolvido');
                        break;
                }
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { id: id, status: result.value },
                    success: (data) => {
                        // console.log(data);
                        Toast.fire({
                            icon: 'success',
                            title: 'Status alterado com sucesso!'
                        });
                    }
                });
            }
        });
    });

    // ###########ALTERAÇÂO DE STATUS E RESPONSAVEL###########
    $(document).on('change', '.selectResponsavel', function () {
        var id = $(this).data('id');
        var url = $(this).data('url');

        $.ajax({
            url: url,
            type: 'POST',
            data: { id: id, responsavel_id: $(this).val() },
            success: (data) => {
                // console.log(data);
                Toast.fire({
                    icon: 'success',
                    title: 'Responsavel Trocado com sucesso!'
                });
            }
        });
    });

    $(document).on('change', '.selectStatus', function () {
        var id = $(this).data('id');
        var url = $(this).data('url');

        $.ajax({
            url: url,
            type: 'POST',
            data: { id: id, status: $(this).val() },
            success: (data) => {
                // console.log(data);
                Toast.fire({
                    icon: 'success',
                    title: 'Status atualizado com sucesso!'
                });
            }
        });
    });
    // ###########ALTERAÇÂO DE STATUS E RESPONSAVEL###########

    $(document).on('click', '.btn-send-admin-star', function () {
        var btn = $(this);
        var comment = btn.parent().parent().parent().find('textarea').val();
        var text_btn = $(this).html();
        $(this).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
        $(this).parent().find('a').prop('disabled', true);

        $.ajax({
            url: btn.data('route'),
            type: 'POST',
            data: { info: btn.data('info'), comment: comment, id: btn.data('id') },
            success: (data) => {
                // console.log(data);

                btn.html(text_btn);
                btn.parent().parent().parent().remove();

                if (btn.data('info') == 1) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Avaliação Aprovada com sucesso!'
                    });
                } else {
                    Toast.fire({
                        icon: 'success',
                        title: 'Avaliação Negada/Excluida!'
                    });
                }
            }
        });
    });

    // Evento para gerar um novo atrr
    $(document).on('click', '.btn-new-attr', function () {
        var seller_id = $('select.seller_id').val() || $('.seller_id').val();
        var idrand = $(this).data('idrand');
        var inputrand = $(this).data('inputrand');

        Swal.fire({
            title: 'Criar Novo Atributo',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Criar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Criando Atributo',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: '/produto-attr/new',
                    type: 'POST',
                    data: { seller_id, attr_name: result.value },
                    success: (data) => {
                        $('select.select-attr').append('<option value="' + data.id + '">' + data.name + '</option>').selectpicker('refresh');
                        var attrs_id = $('select.select-attr').val();
                        attrs_id.push(data.id);
                        $('select.select-attr').val(attrs_id).trigger('change');
                        Swal.fire({
                            icon: 'success',
                            title: 'Atributo Criado',
                        });
                    },
                    error: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro na criação do Atributo!',
                        });
                    }
                });
            }
        });
    });
    $(document).on('click', '.btn-new-attr-value', function () {
        var btn = $(this);
        var attr_id = $(this).parent().parent().find('select').data('attr_id');
        var seller_id = $('select.seller_id').val() || $('.seller_id').val();

        Swal.fire({
            title: 'Novo Variação do Atributo',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Criar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Criando Variação',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: '/produto-attr/new',
                    type: 'POST',
                    data: { seller_id, attr_id, value_name: result.value },
                    success: (data) => {
                        // console.log(data);
                        $('select.attr-variation-' + attr_id).append('<option value="' + data.id + '-' + attr_id + '">' + data.name + '</option>');
                        btn.parent().parent().find('select').val(data.id + '-' + attr_id);
                        Swal.fire({
                            icon: 'success',
                            title: 'Variação Criado',
                        });
                    }
                });
            }
        });
    });

    $(document).on('changed.bs.select', 'select.select-attr', function (e, clickedIndex, isSelected, previousValue) {
        var option_attr_id = $($('select.select-attr').find('option')[clickedIndex]).attr('value');
        var option_attr_add_id = $($('select.select-attr').find('option')[clickedIndex]).attr('value');

        $('.select-variation-' + option_attr_id).remove();

        if (isSelected) {
            $('.variacoes').find('.card').each(function () {
                var varranid = $(this).data('varranid');
                if ($(this).find('.select-variation-' + option_attr_add_id).length == 0) {
                    var thus_ul = $(this).find('ul');
                    $.ajax({
                        url: '/geral/busca-attrs-var',
                        type: 'POST',
                        data: { seller_id: $('[name="seller_id"]').val(), attr_id: option_attr_add_id },
                        // async: false,
                        success: (data) => {
                            // console.log(data);

                            var html_draw = '<li class="list-group-item p-0 pb-3 select-variation-' + data.id + '">';
                            html_draw += '<label for="id_variation' + varranid + 'attributos' + data.id + '">' + data.name + ':</label>';
                            html_draw += '<div class="input-group">';
                            html_draw += '<select class="form-control attr-variation-' + data.id + '" id="id_id_variation' + varranid + 'attributos' + data.id + '" data-attr_id="' + data.id + '" name="variations[' + varranid + '][attributos][]">';
                            html_draw += '<option value="0-' + data.id + '">Qualquer um</option>';
                            for (var i = 0; data.variations.length > i; i++) {
                                html_draw += '<option value="' + data.variations[i].id + '-' + data.id + '">' + data.variations[i].name + '</option>';
                            }
                            html_draw += '</select>';
                            html_draw += '<div class="input-group-append">';
                            html_draw += '<button class="btn btn-success btn-new-attr-value" type="button">Criar Variação</button>';
                            html_draw += '</div>';
                            html_draw += '</div>';
                            html_draw += '</li>';
                            thus_ul.append(html_draw)
                        }
                    });
                }
            });
        }
    });

    // Seleção de controle das variações
    // Função descontinuada, retirar quando "serviços" for alterado
    $(document).on('change', 'select.seller_id', function () {
        var seller_id = $('select.seller_id').val();
        $('select.select-attr').empty().selectpicker('refresh');
        $.ajax({
            url: '/geral/busca-attrs',
            type: 'POST',
            data: { seller_id: seller_id },
            success: (data) => {
                $.each(data, (key, value) => {
                    $('select.select-attr').append('<option value="' + value.id + '">' + value.name + '</option>').selectpicker('refresh');
                });
            }
        });
    });
    $(document).on('click', '.check-variation', function () {
        if ($(this).prop('checked')) {
            var attrs_selected = $('select.select-attr').attr('data-ids_attrs') || null;

            $('.div-variacoes').removeClass('d-none');
            $('.price-variation, .perecivel, .stock, .vaga, .div-desconto').addClass('d-none');
            $('.price-variation, .perecivel, .stock, .vaga, .div-desconto').find('input').val('');
            if ($('[name="hospedagem_controller"]').prop('checked')) $('.vaga').removeClass('d-none').find('input').val('1');
            $('#novoProduto').animate({
                scrollTop: ($('.div-variacoes').offset().top - 180)
            }, 'slow');

            // $('#novoProduto').scrollTop($('.div-variacoes').offset().top);

            $('select.select-attr').val('').trigger('change');
            $('select.select-attr').empty().selectpicker('refresh');
            $.ajax({
                url: '/geral/busca-attrs',
                type: 'POST',
                data: { seller_id: $('[name="seller_id"]').val() },
                success: (data) => {
                    $.each(data, (key, value) => {
                        $('select.select-attr').append('<option value="' + value.id + '">' + value.name + '</option>').selectpicker('refresh');
                    });

                    if (attrs_selected) {
                        $('select.select-attr').val(attrs_selected.split(',')).trigger('change');
                    }
                }
            });
        } else {
            if ($("[name='check_desconto']").prop('checked') !== true) {
                $('.price-variation').removeClass('d-none');
            }
            else {
                $('.div-desconto').removeClass('d-none');
            }

            $('.div-variacoes').addClass('d-none');
            $('select.select-attr').val('').trigger('change');
            $('select.select-attr').empty().selectpicker('refresh');
            $('.select-attr').attr('data-ids_attrs', '');
            $('.seller_id').selectpicker('val', '');
            $('.variacoes').empty();

            if ($('[name="perecivel"]').prop('checked') !== true) {
                $('.perecivel').removeClass('d-none');
            }
        }
    });
    $(document).on('click', '.btn-add-variation', function () {
        var attrs_id = $('select.select-attr').val();
        $.ajax({
            url: '/produto/variations/index',
            type: 'POST',
            data: { seller_id: $('.seller_id').val(), transporte_proprio: ($('[name="perecivel"]').prop('checked') ? 'true' : 'false'), stock_controller: ($('[name="stock_controller"]').prop('checked') ? 'true' : 'false'), attrs_id: attrs_id, check_discount: ($('[name="check_desconto"]').prop('checked') ? 'true' : 'false') },
            success: (data) => {
                // console.log(data);

                $('.variacoes').prepend(data);
                $('[data-toggle="popover"]').popover();
            }
        });
    });

    $(document).on('click', '.btn-analise-product', function () {
        setTimeout(() => {
            if ($('#postNovoProduto').find('.aprovacao').length > 0) {
                $('#postNovoProduto').find('.aprovacao').val('1');
            } else {
                $('#postNovoProduto').append('<input type="hidden" class="aprovacao" name="aprovacao" value="1">');
            }
        }, 200);

        $('#postNovoProduto').find('.btns-apro-neg').removeClass('d-none');
    });
    $(document).on('click', '.btn-negar', function () {
        $('#postNovoProduto').find('.aprovacao').val('0');
        $('#postNovoProduto').find('.fatores-negacao').removeClass('d-none').append(
            '<div>' +
            '<div class="row">' +
            '<div class="col-12 col-md-6 form-group">' +
            '<select name="fields[0][field_name]" class="form-control select-next" data-placeholder="Selecione um campo">' +
            '<option value="">Selecione um campo</option>' +
            '<option value="Nome do Produto">Nome do Produto</option>' +
            '<option value="Preço">Preço</option>' +
            '<option value="Peso">Peso</option>' +
            '<option value="Altura">Altura</option>' +
            '<option value="Largura">Largura</option>' +
            '<option value="Comprimento">Comprimento</option>' +
            '<option value="Categorias">Categorias</option>' +
            '<option value="Descrição Curta">Descrição Curta</option>' +
            '<option value="Imagens">Imagens</option>' +
            '<option value="Descrição Completa">Descrição Completa</option>' +
            '</select>' +
            '</div>' +
            '<div class="col-12 col-md-6 form-group">' +
            '<input type="text" name="fields[0][field_value]" class="form-control" placeholder="Descrição do problema">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="mt-2">' +
            '<textarea class="form-control" name="field_text" placeholder="Mais Informações (Opcional)"></textarea>' +
            '</div>'
        );
    });
    $(document).on('click', '.btn-aprovar', function () {
        $('#postNovoProduto').find('.btns-apro-neg').addClass('d-none');
        $('#postNovoProduto').find('.aprovacao').val('1');
        $('#postNovoProduto').find('.fatores-negacao').addClass('d-none').empty();
        $('#postNovoProduto').find('.btn-salvar').trigger('click');
    });

    // Função descontinuada, retirar quandoa utalizar o "serviços"
    $(document).on('change', '.check-desconto', function () {
        if ($(this).prop('checked')) {
            if ($("[name='check_variation']").prop('checked') !== true) {
                $('.div-desconto').removeClass('d-none');
            } else {
                $('.desconto-card').removeClass('d-none');
            }
        } else {
            if ($("[name='check_variation']").prop('checked') !== true) {
                $('.price-variation').removeClass('d-none');
            }
            $('.div-desconto').addClass('d-none');
            $('.div-row-desconto').empty();
            $('.desconto-card').addClass('d-none');
            $('.div-row-desconto-var').empty();
        }
    });

    $(document).on('change', '.select-next', function () {
        $(this).parent().parent().parent().append(
            '<div class="row">' +
            '<div class="col-12 col-md-6 form-group">' +
            '<select name="fields[' + ($(this).parent().parent().parent().find('.row').length) + '][field_name]" class="form-control select-next" data-placeholder="Selecione um campo">' +
            '<option value="">Selecione um campo</option>' +
            '<option value="Nome do Produto">Nome do Produto</option>' +
            '<option value="Preço">Preço</option>' +
            '<option value="Peso">Peso</option>' +
            '<option value="Altura">Altura</option>' +
            '<option value="Largura">Largura</option>' +
            '<option value="Comprimento">Comprimento</option>' +
            '<option value="Categorias">Categorias</option>' +
            '<option value="Descrição Curta">Descrição Curta</option>' +
            '<option value="Imagens">Imagens</option>' +
            '<option value="Descrição Completa">Descrição Completa</option>' +
            '</select>' +
            '</div>' +
            '<div class="col-12 col-md-6 form-group">' +
            '<input type="text" name="fields[' + ($(this).parent().parent().parent().find('.row').length) + '][field_value]" class="form-control" placeholder="Descrição do problema">' +
            '</div>' +
            '</div>'
        );
        $(this).removeClass('select-next');
    });

    $(document).on('keydown', '[name="descricao_curta"]', function (e) {
        $('.count-max-length').html('(max. caracteres ' + (255 - $(this).val().length) + ')');
        if ($(this).val().length >= 255 && e.keyCode != 8 && e.keyCode != 9) {
            return false;
        }
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
    $(document).on('paste', '.max-caracteres', function (e) {
        setTimeout(() => {
            var max_caracteres = $(this).data('max_caracteres') || 255;
            $(this).parent().find('.count-max-caracteres-length').html('(max. caracteres ' + (max_caracteres - ($(this).val().substring(0, $(this).val().length + (max_caracteres - $(this).val().length))).length) + ')');
            if ($(this).val().length >= max_caracteres && e.keyCode != 8 && e.keyCode != 9) {
                $(this).val($(this).val().substring(0, $(this).val().length + (max_caracteres - $(this).val().length)));
                return false;
            }
        }, 100);
    });

    $(document).on('click', '#frete_gratis', function () {
        if ($(this).prop('checked')) {
            $('#postNovoTransporte').find('[name="valor_minimo"]').parent().removeClass('d-none');
        } else {
            $('#postNovoTransporte').find('[name="valor_minimo"]').parent().addClass('d-none');
        }
    });
    $(document).on('click', '#edit_frete_gratis', function () {
        if ($(this).prop('checked')) {
            $('#postEditarTransporte').find('[name="valor_minimo"]').parent().removeClass('d-none');
        } else {
            $('#postEditarTransporte').find('[name="valor_minimo"]').parent().addClass('d-none');
        }
    });
    $(document).on('change', '#toda_cidade', function () {
        if ($(this).prop('checked')) {
            $('#postNovoTransporte').find('.bairro').addClass('d-none');
        } else {
            $('#postNovoTransporte').find('.bairro').removeClass('d-none');
        }
    });
    $(document).on('change', '#edit_toda_cidade', function () {
        if ($(this).prop('checked')) {
            $('#postEditarTransporte').find('.bairro').addClass('d-none');
        } else {
            $('#postEditarTransporte').find('.bairro').removeClass('d-none');
        }
    });
    $(document).on('change', '[name="tempo"]', function () {
        if ($(this).val() == 'S' || $(this).val() == 'C') {
            $('[name="tempo_entrega"]').parent().addClass('d-none');
            if ($(this).val() == 'S') {
                $('.semana').removeClass('d-none');
            } else {
                $('.semana').addClass('d-none');
            }
        } else {
            $('[name="tempo_entrega"]').parent().removeClass('d-none');
            $('.semana').addClass('d-none');
        }
    });

    $(document).on('change', '#em_todas_cidades', function () {
        if ($(this).prop('checked')) {
            $('#postNovoTransporte').find('.bairro').addClass('d-none');
            $('#postNovoTransporte').find('.toda_cidade').addClass('d-none');
            $('#postNovoTransporte').find('.cidade').addClass('d-none');
        } else {
            $('#postNovoTransporte').find('.bairro').removeClass('d-none');
            $('#postNovoTransporte').find('.toda_cidade').removeClass('d-none');
            $('#postNovoTransporte').find('.cidade').removeClass('d-none');
        }
    });
    $(document).on('change', '#edit_em_todas_cidades', function () {
        if ($(this).prop('checked')) {
            $('#postEditarTransporte').find('.bairro').addClass('d-none');
            $('#postEditarTransporte').find('.toda_cidade').addClass('d-none');
            $('#postEditarTransporte').find('.cidade').addClass('d-none');
        } else {
            $('#postEditarTransporte').find('.bairro').removeClass('d-none');
            $('#postEditarTransporte').find('.toda_cidade').removeClass('d-none');
            $('#postEditarTransporte').find('.cidade').removeClass('d-none');
        }
    });

    // Add keywords
    $(document).on('keydown', '.keywords-adds', function (e) {
        if (e.keyCode == 13 || e.isTrigger == 3) {
            e.preventDefault();
            if ($(this).val().includes(';')) {
                var separator = $(this).val().split(';');
                for (var i = 0; separator.length > i; i++) {
                    if (separator[i]) {
                        $(this).parent().parent().find('table.keywords_adds').append(
                            `<tr><td class="border-right border-bottom" width="5%"><button type="button" class="btn py-0 btn-remove-keyword">x</button></td> <td class="border-bottom"><input type="hidden" name="keywords[]" value="${separator[i]}"> <span class="ml-2">${separator[i]}</span></td></tr>`
                        );
                    }
                }
            } else {
                $(this).parent().parent().find('table.keywords_adds').append(
                    `<tr><td class="border-right border-bottom" width="5%"><button type="button" class="btn py-0 btn-remove-keyword">x</button></td> <td class="border-bottom"><input type="hidden" name="keywords[]" value="${$(this).val()}">  <span class="ml-2">${$(this).val()}</span></td></tr>`
                );
            }

            $(this).val('');
        }
    });
    $(document).on('click', '.btn-add-keywords', function () {
        $('.keywords-adds').trigger('keydown');
    });
    $(document).on('click', '.btn-remove-keyword', function (e) {
        $(this).parent().parent().remove();
    });

    $(document).on('click', '.btn-save-seo', function () {
        $.ajax({
            url: 'configurar-seo/register',
            type: 'POST',
            data: new FormData($('#seoConfig')[0]),
            cache: false,
            contentType: false,
            processData: false,
            success: (data) => {
                console.log(data);
                $('select').selectpicker('val', '');
                $('input').val('');
                $('textarea').val('');
                $('.keywords_adds').empty();
                $('.banner_path').empty();
            }
        });
    });

    $(document).on('change', 'select.select-page', function () {
        if ($(this).val() !== '') {
            $('.btn-save-seo').removeClass('d-none');
        } else {
            $('.btn-save-seo').addClass('d-none');
        }
        $.ajax({
            url: 'configurar-seo/busca-info',
            type: 'GET',
            data: { page: $(this).val() },
            success: (data) => {
                if (isEmpty(data)) {
                    // $('select').selectpicker('val', '');
                    $('input').val('');
                    $('textarea').val('');
                    $('.keywords_adds').empty();
                    $('.banner_path').empty();
                } else {
                    $('[name="id"]').val(data.id);
                    $('[name="page"]').selectpicker('val', data.page);
                    $('[name="title"]').val(data.title);
                    $('[name="link"]').val(data.link);
                    $('[name="description"]').val(data.description);

                    for (var i = 0; data.keywords.length > i; i++) {
                        $('.keywords_adds').append(`
                            <tr>
                                <td class="border-right border-bottom" width="5%"><button type="button" class="btn py-0 btn-remove-keyword">x</button></td>
                                <td class="border-bottom"><input type="hidden" name="keywords[]" value="${data.keywords[i]}">  <span class="ml-2">${data.keywords[i]}</span></td>
                            </tr>
                        `);
                    }

                    $('.banner_path').html('<img class="rounded" width="280" src="/storage/' + data.banner_path + '">');
                }
            }
        });
    });

    $(document).on('click', '.btn-env-code-delete', function () {
        var btn_this = $(this);

        btn_this.prop('disabled', true);
        btn_this.html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');

        $.ajax({
            url: '/vendedor/env-code-delete',
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
            url: '/vendedor/confirm-code-delete',
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

    $(document).on('click', '.notificarVendedor', function () {
        var order_number = $(this).data('order_number');
        var url = $(this).data('url');
        var btn_this = $(this);
        btn_this.html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: { order_number },
            success: (data) => {
                btn_this.html('Notificar').prop('disabled', false);

                Toast.fire({
                    icon: 'success',
                    title: 'Notificação enviado com sucesso!'
                });
            }
        });
    });

    $(document).on('click', '.btn-verifica-cancelamento', function () {
        $('#verificarSolicitacaoCancelamento').find('[name="order_number"]').val($(this).data('order_number'));
        $.ajax({
            url: $(this).data('url'),
            type: 'POST',
            data: { order_number: $(this).data('order_number') },
            success: (data) => {
                // console.log(data);
                if (data.order.payment_method == 'boleto') {
                    $('#verificarSolicitacaoCancelamento').find('.boleto').removeClass('d-none');
                } else {
                    $('#verificarSolicitacaoCancelamento').find('.boleto').addClass('d-none');
                }

                $.each(data.order_cancel, (key, value) => {
                    $('#verificarSolicitacaoCancelamento').find('._' + key).text(value);
                });
            }
        });
    });
    $(document).on('click', '.btn-confirma-cancelamento', function () {
        var btn = $(this);
        Swal.fire({
            title: 'Confirmando Dados, Aguarde!',
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
                    title: 'Pedido cancelado com successo!'
                });
                $('#verificarSolicitacaoCancelamento').modal('hide');
            }
        });
    });

    // ####Funções especificas de serviços/turismo rural#####
    // Função para adiconar data especificas no produto
    $(document).on('click', '.btn-add-variation-servico', function () {
        var attrs_id = $('select.select-attr').val();
        $.ajax({
            url: '/servico/variation/component',
            type: 'POST',
            data: { seller_id: $('.seller_id').val(), stock_controller: ($('[name="stock_controller"]').prop('checked') ? 'true' : 'false'), hospedagem_controller: ($('[name="hospedagem_controller"]').prop('checked') ? 'true' : 'false'), attrs_id: attrs_id, check_discount: ($('[name="check_desconto"]').prop('checked') ? 'true' : 'false') },
            success: (data) => {
                // console.log(data);

                $('.variacoes').prepend(data);
                $('[data-toggle="popover"]').popover();
            }
        });
    });

    $(document).on('click', '.btn-save', function () {
        let modal_target = $(this).data('target');
        let refresh = $(this).data('refresh');

        // Por mais que tenha erro, limpamos para os outros que não tenha
        $(modal_target).find('input').removeClass('is-invalid');
        $(modal_target).find('.invalid-feedback').remove();

        // Pegamos o parente do id para adicionar um modelo de carregamento
        let modal = $(modal_target).find('.modal-content');
        if (modal.is('.modal-content')) modal.append('<div class="overlay d-flex justify-content-center align-items-center"><i class="fas fa-2x fa-sync fa-spin"></i></div>');

        $.ajax({
            url: $(modal_target).find('form').attr('action'),
            type: 'POST',
            data: $(modal_target).find('form').serialize(),
            success: (data) => {
                $(modal_target).find('.overlay').remove();
                $(modal_target).modal('hide');
            },
            error: (err) => {
                $(modal_target).find('.overlay').remove();
            }
        });
    });

    $(document).on('click', '.btn-destroy', function () {
        let url = $(this).data('url');
        let id = $(this).data('id');

        Swal.fire({
            icon: 'warning',
            title: 'Apagar Dados?',
            showCancelButton: true,
            confirmButtonText: 'Apagar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Apagando Dados',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { id },
                    success: (data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dados Apagados',
                        }).then(() => { window.location.reload(); });
                    },
                    error: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao apagar o dado!',
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-desativar-ps', function () {
        let url = $(this).data('href');
        let id = $(this).data('id');
        let ativo = $(this).data('ativo');

        Swal.fire({
            title: (ativo == 'N' ? 'Desativar' : 'Ativar') + ' Dados?',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Desativando, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { id, ativo },
                    success: (data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dados Atualizados!',
                        }).then(() => { window.location.reload(); });
                    },
                    error: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao apagar o dado!',
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-clonar-ps', function () {
        let url = $(this).data('href');
        let id = $(this).data('id');

        Swal.fire({
            title: 'Clonar Dados?',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Clonando, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { id },
                    success: (data) => {
                        $
                        Swal.fire({
                            icon: 'success',
                            title: 'Dados Clonado para ' + data.type + ' #' + data.id + '!',
                        }).then(() => {
                            // window.location.reload();
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                $('.search-filter').val(data.name).closest('form').submit();
                            }
                        });
                    },
                    error: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao atualizar o dado!',
                        });
                    }
                });
            }
        });
    });

    // Adicionar stock
    $(document).on('click', '.btn-add-stock', function () {
        let url = $(this).data('href');
        let id = $(this).data('id');

        Swal.fire({
            title: 'Quantidade a ser Adicionada.',
            input: 'number',
            showCancelButton: true,
            confirmButtonText: 'Adcionar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Gravando, aguarde...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { id, stock: result.value },
                    success: (data) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Estoque adiconado com sucesso!',
                        })
                    },
                    error: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao atualizar os dados!',
                        });
                    }
                });
            }
        });
    });

    $(document).on('change', '.periodo_ini, .periodo_fim', function () {
        getInfoDash();
    });

    // ---------------------------------------------------------
    // ---------------------------------------------------------
    // ---------------------------------------------------------

    $(function () {
        if ($('.table-ajax').length > 0) {
            var url = $('.table-ajax').data('url');
            var table_ajax = $('.table-ajax').data('table');
            var columns = $('.table-ajax').data('columns');

            if (!url.includes('?')) {
                url = url + "?table=" + table_ajax;
            } else {
                url = url + "&table=" + table_ajax;
            }

            $('.table-ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "autoWidth": false,
                "language": language_pt_br,
                "ajax": {
                    url: url
                },
                columns: columns,
                order: [[0, 'desc']]
            });

            $('.table-ajax').on('draw.dt', function () {
                // Esta função será chamada após o carregamento dos dados via AJAX
                $('[data-toggle="tooltip"]').tooltip();
            });
        }
    })

    //............................
    $('.short-textarea').summernote({
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']]
        ],
        callbacks: {
            onKeydown: function (e) {
                var max = $(this).data('max-caracteres');
                var t = e.currentTarget.innerText;
                if (t.length >= max) {
                    //delete key
                    if (e.keyCode != 8)
                        e.preventDefault();
                    // add other keys ...
                }
            },
            onKeyup: function (e) {
                var max = $(this).data('max-caracteres');
                var t = e.currentTarget.innerText;
                $(this).parent().find('.count-max-length').text(`(max. caracteres ${(max - t.length)})`);
            },
            onPaste: function (e) {
                var max = $(this).data('max-caracteres');
                var t = e.currentTarget.innerText;
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                e.preventDefault();
                var all = t + bufferText;
                document.execCommand('insertText', false, all.trim().substring(0, 400));
                $(this).parent().find('.count-max-length').text(`(max. caracteres ${(max - t.length)})`);
            }
        }
    });

    $('[name="per_page"]').on('change', function () {
        $(this).closest('form').submit();
    });
});

function getInfoDash() {
    $.ajax({
        url: '/getInfoDash',
        type: 'POST',
        data: { date_ini: $('.periodo_ini').val(), date_fim: $('.periodo_fim').val() },
        success: (data) => {
            // console.log(data);
            $("#info_vendas").html(data.vendas);
            $("#qty_produtos_servicos").html(data.qty_produtos_servicos);
            $("#qty_acessos").html(data.qty_acessos);
            $('#table_pedidos').empty().html(data.pedidos.trs);
            // $('#table_view_products').empty().html(data.view_clicks.view_products_tr);
            // $('#table_view_services').empty().html(data.view_clicks.view_services_tr);
            // $('#table_click_services').empty().html(data.view_clicks.clicks_services_tr);
            if (data.pedidos.total < 7) {
                for (var i = 1; i <= (7 - data.pedidos.total); i++) { $('#table_pedidos').append('<tr><td style="font-size: 12px;">&nbsp;</td><td></td><td></td><td></td></tr>'); }
            }
            // if (data.view_clicks.total_vp < 7) {
            //     for (var i = 1; i <= (7 - data.view_clicks.total_vp); i++) { $('#table_view_products').append('<tr><td style="font-size: 12px;">&nbsp;</td><td></td></tr>'); }
            // }
            // if (data.view_clicks.total_vs < 7) {
            //     for (var i = 1; i <= (7 - data.view_clicks.total_vs); i++) { $('#table_view_services').append('<tr><td style="font-size: 12px;">&nbsp;</td><td></td></tr>'); }
            // }
            // if (data.view_clicks.total_cs < 7) {
            //     for (var i = 1; i <= (7 - data.view_clicks.total_cs); i++) { $('#table_click_services').append('<tr><td style="font-size: 12px;">&nbsp;</td><td></td></tr>'); }
            // }
            vendas_realizadas.updateSeries([{
                name: 'Vendas Realizadas',
                data: data.venda_graf.series,
            }]);
            vendas_realizadas.updateOptions({
                xaxis: {
                    categories: data.venda_graf.category,
                }
            });
            acessos_produtos_servicos.updateSeries([{
                name: 'Produtos/Serviços',
                data: data.acessos_graf.series,
            }]);
            acessos_produtos_servicos.updateOptions({
                xaxis: {
                    categories: data.acessos_graf.category,
                }
            });

            if (data.crescimento) {
                $.each(data.crescimento, (key, value) => {
                    $('.crescimento').find(`[data-target="${key}"]`).text(value);
                });
            }
            if (data.ranking) {
                $.each(data.ranking, (key, value) => {
                    $('.ranking').find(`[data-target="${key}"]`).html(value);
                });
            }
        }
    });
}

function isEmpty(obj) {
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            return false;
        }
    }
    return JSON.stringify(obj) === JSON.stringify({});
}

function busca_state() {
    var state_selected = $('[name="state"]').find('option:selected').val();
    $('[name="state"], .state').empty();
    $('[name="state"], .state').append('<option value="">::Selecione uma Opção::</option>');
    $.ajax({
        url: 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/',
        type: 'GET',
        success: (data) => {
            // console.log(data);
            for (var i = 0; data.length > i; i++) {
                $('[name="state"], .state').append('<option value="' + data[i].sigla + '" data-sigla_id="' + data[i].id + '">' + data[i].sigla + ' - ' + data[i].nome + '</option>');
            }

            $('[name="state"], .state').val(state_selected).trigger('change');
        }
    });
}

var language_pt_br = {
    "emptyTable": "Nenhum registro encontrado",
    "info": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
    "infoEmpty": "Mostrando 0 até 0 de 0 registros",
    "infoFiltered": "(Filtrados de _MAX_ registros)",
    "infoThousands": ".",
    "loadingRecords": "Carregando...",
    "processing": "Processando...",
    "zeroRecords": "Nenhum registro encontrado",
    "search": "Pesquisar",
    "paginate": {
        "next": "Próximo",
        "previous": "Anterior",
        "first": "Primeiro",
        "last": "Último"
    },
    "aria": {
        "sortAscending": ": Ordenar colunas de forma ascendente",
        "sortDescending": ": Ordenar colunas de forma descendente"
    },
    "select": {
        "rows": {
            "_": "Selecionado %d linhas",
            "1": "Selecionado 1 linha"
        },
        "cells": {
            "1": "1 célula selecionada",
            "_": "%d células selecionadas"
        },
        "columns": {
            "1": "1 coluna selecionada",
            "_": "%d colunas selecionadas"
        }
    },
    "buttons": {
        "copySuccess": {
            "1": "Uma linha copiada com sucesso",
            "_": "%d linhas copiadas com sucesso"
        },
        "collection": "Coleção  <span class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"><\/span>",
        "colvis": "Visibilidade da Coluna",
        "colvisRestore": "Restaurar Visibilidade",
        "copy": "Copiar",
        "copyKeys": "Pressione ctrl ou u2318 + C para copiar os dados da tabela para a área de transferência do sistema. Para cancelar, clique nesta mensagem ou pressione Esc..",
        "copyTitle": "Copiar para a Área de Transferência",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "-1": "Mostrar todos os registros",
            "_": "Mostrar %d registros"
        },
        "pdf": "PDF",
        "print": "Imprimir"
    },
    "autoFill": {
        "cancel": "Cancelar",
        "fill": "Preencher todas as células com",
        "fillHorizontal": "Preencher células horizontalmente",
        "fillVertical": "Preencher células verticalmente"
    },
    "lengthMenu": "Exibir _MENU_ resultados por página",
    "searchBuilder": {
        "add": "Adicionar Condição",
        "button": {
            "0": "Construtor de Pesquisa",
            "_": "Construtor de Pesquisa (%d)"
        },
        "clearAll": "Limpar Tudo",
        "condition": "Condição",
        "conditions": {
            "date": {
                "after": "Depois",
                "before": "Antes",
                "between": "Entre",
                "empty": "Vazio",
                "equals": "Igual",
                "not": "Não",
                "notBetween": "Não Entre",
                "notEmpty": "Não Vazio"
            },
            "number": {
                "between": "Entre",
                "empty": "Vazio",
                "equals": "Igual",
                "gt": "Maior Que",
                "gte": "Maior ou Igual a",
                "lt": "Menor Que",
                "lte": "Menor ou Igual a",
                "not": "Não",
                "notBetween": "Não Entre",
                "notEmpty": "Não Vazio"
            },
            "string": {
                "contains": "Contém",
                "empty": "Vazio",
                "endsWith": "Termina Com",
                "equals": "Igual",
                "not": "Não",
                "notEmpty": "Não Vazio",
                "startsWith": "Começa Com"
            },
            "array": {
                "contains": "Contém",
                "empty": "Vazio",
                "equals": "Igual à",
                "not": "Não",
                "notEmpty": "Não vazio",
                "without": "Não possui"
            }
        },
        "data": "Data",
        "deleteTitle": "Excluir regra de filtragem",
        "logicAnd": "E",
        "logicOr": "Ou",
        "title": {
            "0": "Construtor de Pesquisa",
            "_": "Construtor de Pesquisa (%d)"
        },
        "value": "Valor",
        "leftTitle": "Critérios Externos",
        "rightTitle": "Critérios Internos"
    },
    "searchPanes": {
        "clearMessage": "Limpar Tudo",
        "collapse": {
            "0": "Painéis de Pesquisa",
            "_": "Painéis de Pesquisa (%d)"
        },
        "count": "{total}",
        "countFiltered": "{shown} ({total})",
        "emptyPanes": "Nenhum Painel de Pesquisa",
        "loadMessage": "Carregando Painéis de Pesquisa...",
        "title": "Filtros Ativos"
    },
    "thousands": ".",
    "datetime": {
        "previous": "Anterior",
        "next": "Próximo",
        "hours": "Hora",
        "minutes": "Minuto",
        "seconds": "Segundo",
        "amPm": [
            "am",
            "pm"
        ],
        "unknown": "-",
        "months": {
            "0": "Janeiro",
            "1": "Fevereiro",
            "10": "Novembro",
            "11": "Dezembro",
            "2": "Março",
            "3": "Abril",
            "4": "Maio",
            "5": "Junho",
            "6": "Julho",
            "7": "Agosto",
            "8": "Setembro",
            "9": "Outubro"
        },
        "weekdays": [
            "Domingo",
            "Segunda-feira",
            "Terça-feira",
            "Quarta-feira",
            "Quinte-feira",
            "Sexta-feira",
            "Sábado"
        ]
    },
    "editor": {
        "close": "Fechar",
        "create": {
            "button": "Novo",
            "submit": "Criar",
            "title": "Criar novo registro"
        },
        "edit": {
            "button": "Editar",
            "submit": "Atualizar",
            "title": "Editar registro"
        },
        "error": {
            "system": "Ocorreu um erro no sistema (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">Mais informações<\/a>)."
        },
        "multi": {
            "noMulti": "Essa entrada pode ser editada individualmente, mas não como parte do grupo",
            "restore": "Desfazer alterações",
            "title": "Multiplos valores",
            "info": "Os itens selecionados contêm valores diferentes para esta entrada. Para editar e definir todos os itens para esta entrada com o mesmo valor, clique ou toque aqui, caso contrário, eles manterão seus valores individuais."
        },
        "remove": {
            "button": "Remover",
            "confirm": {
                "_": "Tem certeza que quer deletar %d linhas?",
                "1": "Tem certeza que quer deletar 1 linha?"
            },
            "submit": "Remover",
            "title": "Remover registro"
        }
    },
    "decimal": ","
};

// Função para deixar elementos do mesmo tamanho
(function ($) {
    $.fn.matchDimensions = function (dimension) {
        var itemsToMatch = $(this),
            maxHeight = 0,
            maxWidth = 0;
        if (itemsToMatch.length > 0) {
            switch (dimension) {
                case "height":
                    itemsToMatch.css("height", "auto").each(function () {
                        maxHeight = Math.max(maxHeight, $(this).height());
                    }).height(maxHeight);
                    break;
                case "width":
                    itemsToMatch.css("width", "auto").each(function () {
                        maxWidth = Math.max(maxWidth, $(this).width());
                    }).width(maxWidth);
                    break;
                default:
                    itemsToMatch.each(function () {
                        var thisItem = $(this);
                        maxHeight = Math.max(maxHeight, thisItem.height());
                        maxWidth = Math.max(maxWidth, thisItem.width());
                    });
                    itemsToMatch
                        .css({
                            "width": "auto",
                            "height": "auto"
                        })
                        .height(maxHeight)
                        .width(maxWidth);
                    break;
            }
        }
        return itemsToMatch;
    };
})(jQuery);

function obterParametrosDaURL() {
    // Obtém a query string da URL
    var queryString = window.location.search.substring(1);

    // Divide a query string em pares chave=valor
    var pares = queryString.split("&");

    // Objeto para armazenar os parâmetros
    var parametros = {};

    // Itera sobre os pares e os adiciona ao objeto
    for (var i = 0; i < pares.length; i++) {
        var par = pares[i].split("=");
        var chave = decodeURIComponent(par[0]);
        var valor = decodeURIComponent(par[1]);
        parametros[chave] = valor;
    }

    return parametros;
}