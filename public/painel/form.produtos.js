$(document).ready(function(){
    // Carregando select customizado
    $('.check-select').each(function(){
        var title = $(this).data('title');
        var btn_save = $(this).data('btn_save');
        var html = $(this).html();

        $(this).html(`
            <div class="check-select-button">
                <div class="pt-1 title-selects">${title}</div>
                <span class="arrow-down"></span>
            </div>
            <div class="check-select-modal d-none">
                <div class="check-select-header">
                    <div class="check-select-btns">
                        <button type="button" class="btn bg-white btn-sm check-select-btn-mark-off border">Desmarcar Todos</button>
                        <button type="button" class="btn btn-sm btn-info check-select-btn-save">${btn_save}</button>
                    </div>
                </div>
                <div class="check-select-list">
                    ${html}
                </div>
            </div>
        `);

        $(this).css('display', 'block');
        $('[data-toggle="tooltip"]').tooltip();
    });
    // Select customizado
    $(document).on('click', '.check-select-button', function(){
        if($(this).closest('.check-select').find('.check-select-modal').is('.d-none')) $('.check-select-modal').addClass('d-none');
        $(this).closest('.check-select').find('.check-select-modal').toggleClass('d-none');
        $('body').toggleClass('check-select-show');
    });
    $(document).on('click', '.check-select-show', function(e){
        if($(e.target).closest('.check-select').length == 0){
            $('.check-select-modal').addClass('d-none');
            $('body').toggleClass('check-select-show');
            $('.check-select').each(function(){
                var selects = [];
                $(this).find('.check-select-list input[type="checkbox"]').each(function(){
                    if($(this).prop('checked')){
                        selects.push($(this).parent().attr('title'));
                    }
                });
                if(selects.length > 0){
                    if(selects.length > 3){
                        $(this).find('.title-selects').text(selects.length+' Selecionados');
                    }else{
                        $(this).find('.title-selects').text(selects.join(', '));
                    }
                }else{
                    $(this).find('.title-selects').text($(this).data('title'));
                }
            });
        }
    });
    $(document).on('click', '.check-select-btn-save', function(){
        $(this).closest('.check-select-modal').addClass('d-none');
        $('body').toggleClass('check-select-show');
        var selects = [];
        $(this).closest('.check-select-modal').find('.check-select-list input[type="checkbox"]').each(function(){
            if($(this).prop('checked')){
                selects.push($(this).parent().attr('title'));
            }
        });
        if(selects.length > 0){
            if(selects.length > 3){
                $(this).closest('.check-select').find('.title-selects').text(selects.length+' Selecionados');
            }else{
                $(this).closest('.check-select').find('.title-selects').text(selects.join(', '));
            }
        }else{
            $(this).closest('.check-select').find('.title-selects').text($(this).closest('.check-select').data('title'));
        }
    });
    $(document).on('click', '.check-select-btn-mark-off', function(){
        $(this).closest('.check-select-modal').find('input[type="checkbox"]').prop('checked', false);
    });
    $('.check-select').each(function(){
        setTimeout(() => {

            var selects = [];
            $(this).find('.check-select-list input[type="checkbox"]').each(function(){
                if($(this).prop('checked')){
                    selects.push($(this).parent().attr('title'));
                }
            });
            console.log(selects);
            if(selects.length > 0){
                if(selects.length > 3){
                    $(this).find('.title-selects').text(selects.length+' Selecionados');
                }else{
                    $(this).find('.title-selects').text(selects.join(', '));
                }
            }else{
                $(this).find('.title-selects').text($(this).data('title'));
            }
        }, 1000);
    });

    // Click customizado
    $(document).on('change', '.check-custom', function(e){
        var thiss = $(this);

        $('.check-custom').each(function(){
            if(thiss.parent().index() !== $(this).parent().index()){
                $(this).prop('checked', false);
                $(this).parent().find('label').removeClass('active');
                $(this).parent().css('pointer-events', 'auto');
            }
            checkProdutoVariavel($(this));
            checkPlanoAssinatura($(this));
        });


        if($(this).prop('checked')){
            $(this).parent().find('label').addClass('active');
            $(this).parent().css('pointer-events', 'none');
        }
    });
    $('.check-custom').each(function(){
        if($(this).prop('checked')){
            $(this).parent().css('pointer-events', 'none');
            checkProdutoVariavel($(this));
            checkPlanoAssinatura($(this));
        }
    });

    // Função para verificar o desconto progressivo
    $(document).on('change', '[name="check_desconto"]', function(){
        if($(this).prop('checked')){
            if ($("[name='produto_variavel']").prop('checked') !== true)
            {
                $('.desconto-progressivo').removeClass('d-none');
            }else{
                $('.desconto-card').removeClass('d-none');
            }
        }else{
            if ($("[name='produto_variavel']").prop('checked') !== true)
            {
                $('.price-variation').removeClass('d-none');
            }
            $('.desconto-progressivo').addClass('d-none');
            $('.div-row-desconto').empty();
            $('.desconto-card').addClass('d-none');
            $('.div-row-desconto-var').empty();
        }
    });

    // Trocando a foto
    $(document).on('click', '.div-image', function(){
        $('.div-image').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // Sortables das Fotos
    $('.div-image-registers').sortable({
        // axis: 'y',
        placeholder: "ui-state-highlight",
        cursor: "move",
        // forcePlaceholderSize: true,
        beforeStop: function( event, ui ) {
            // var tag = $(ui.item[0]);
            var positions = [];
            $('.div-image-registers > div').each(function(index){
                if($(this).attr('data-id')){
                    positions.push({
                        foto_id: $(this).attr('data-id'),
                        position: index+1,
                    });
                }
            });

            $.ajax({
                url: url_geral_save,
                type: 'POST',
                data: {positions: positions, postProduct: 'true', postType: 'postUpdatePositionFotos'},
                success: (data) => {}
            });
        },
        // containment: "document",
    }).disableSelection();

    // Upload de Fotos
    $(document).on('click', '.btn-upload-fotos', () => $('.upload-fotos').trigger('click')).on('change', '.upload-fotos', function(){
        Swal.fire({
            title: 'Carregando imagens, aguarde...',
            allowOutsideClick: false,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var files = $(this).prop('files');

        var form_images = new FormData();
        for(i in files){
            if(files[i].name) form_images.append('images[]', files[i]);
        }
        form_images.append('postProduct', 'true');
        form_images.append('postType', 'postAddFotos');
        form_images.append('product_id', $('#product_id').val());

        $.ajax({
            url: url_geral_save,
            type: 'POST',
            data: form_images,
            cache: false,
            contentType: false,
            processData: false,
            success: (data) => {
                // window.location.reload();
                $('.div-image-registers').append(data);
                $(this).val('');
                Swal.close();
            },
            error: (err) => {
                $(this).val('');
                Swal.fire({
                    icon: 'error',
                    title: 'Houve um erro na gravação, contate o administrador!'
                });
            }
        });
    });
    // Apagando Foto
    $(document).on('click', '.btn-delete-foto', function(){
        Swal.fire({
            icon: 'warning',
            title: 'Deseja apagar essa foto?',
            showCancelButton: true,
            confirmButtonText: 'Sim Apagar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: $(this).data('url'),
                    type: 'POST',
                    data: {postRemoveFotos: 'true',foto_id: $(this).data('id')},
                    success: (data) => {
                        $(this).parent().remove();
                    }
                });
            }
        });
    });

    $(document).on('click', '[name="perecivel"]', function(e) {
        if($(this).prop('checked')){
            $('.perecivel').addClass('d-none');
            $('.perecivel-attr').addClass('d-none');
        }else{
            if($('[name="check_variation"]').prop('checked') !== true){
                $('.perecivel').removeClass('d-none');
                $('.perecivel').find('input').val('');
            }
            $('.perecivel-attr').removeClass('d-none');
        }
    });

    // Nova função de variação
    $(document).on('change', 'select.seller_id', function(){
        var seller_id = $('select.seller_id').val();
        $('.check-select.attribute-select .check-select-list').empty();
        $.ajax({
            url: '/geral/busca-attrs',
            type: 'POST',
            data: {seller_id: seller_id},
            success: (data)=>{
                $.each(data, (key, value)=>{
                    $('.check-select.attribute-select .check-select-list').append(`
                        <div class="check-select-div-input col-12 col-sm-3" title="${value.name}" data-toggle="tooltip">
                            <input type="checkbox" id="attribute-${value.id}" name="attrs[]" value="${value.id}">
                            <label class="text-truncate" for="attribute-${value.id}">${value.name}</label>
                        </div>
                    `);
                });

                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });
    $(document).on('click', '.btn-plus-variation', function(e){
        var attrs_id = [];
        $('.check-select.attribute-select .check-select-list').find('input[type="checkbox"]:checked').each(function(){attrs_id.push($(this).val())});
        $.ajax({
            url: '/produto/variations/index',
            type: 'POST',
            data: {seller_id: $('.seller_id').val(), transporte_proprio: ($('[name="perecivel"]').prop('checked') ? 'true' : 'false'), stock_controller: ($('[name="stock_controller"]').prop('checked') ? 'true' : 'false'), attrs_id: attrs_id, check_discount: ($('[name="check_desconto"]').prop('checked') ? 'true' : 'false')},
            success: (data)=>{
                // console.log(data);

                $('.variacoes').prepend(data);
            }
        });
    });
    // Nova função de troca de attributos
    $(document).on('change', '.check-select.attribute-select .check-select-list input[type="checkbox"]', function(e){
        var option_attr_id = $(this).val();

        $('.select-variation-'+option_attr_id).remove();

        if($(this).prop('checked')){
            $('.variacoes').find('.card').each(function(){
                var varranid = $(this).data('varranid');
                if($(this).find('.select-variation-'+option_attr_id).length == 0){
                    var thus_ul = $(this).find('.card-div-var');
                    $.ajax({
                        url: '/geral/busca-attrs-var',
                        type: 'POST',
                        data: {seller_id: $('[name="seller_id"]').val(), attr_id: option_attr_id},
                        // async: false,
                        success: (data) => {
                            // console.log(data);

                            thus_ul.append(`
                                <div class="row mb-2 select-variation-${data.id}">
                                    <div class="col-12">
                                        <label style="font-size: .8rem;" for="id_${varranid}attributos${data.id}">${data.name}:</label>
                                    </div>
                                    <div class="col-5 pr-0">
                                        <select class="form-control form-control-sm attr-variation-${data.id}" id="id_${varranid}attributos${data.id}" data-attr_id="${data.id}" name="variations[${varranid}][attributos][]">
                                            <option value="0-${data.id}">Qualquer um</option>
                                            ${data.variations.map(function(query){return `<option value="${query.id}-${data.id}">${query.name}</option>`;}).join('')}
                                        </select>
                                    </div>
                                    <div class="col-7 pl-1 d-flex align-items-end">
                                        <div class="pr-1"><b>OU</b></div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-success btn-new-attribute-value" style="font-size: .7rem;" type="button">CRIAR VARIAÇÃO</button>
                                        </div>
                                    </div>
                                </div>
                            `);
                        }
                    });
                }
            });
        }
    });
    // Evento para gerar um novo atrr
    $(document).on('click', '.btn-new-attribute', function(){
        var seller_id = $('select.seller_id').val() || $('.seller_id').val();

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
            if(result.isConfirmed){
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
                    data: {seller_id, attr_name: result.value},
                    success: (data) => {
                        $('.check-select.attribute-select .check-select-list').append(`
                            <div class="check-select-div-input col-12 col-sm-3" title="${data.name}" data-toggle="tooltip">
                                <input type="checkbox" id="attribute-${data.id}" name="attrs[]" value="${data.id}">
                                <label class="text-truncate" for="attribute-${data.id}">${data.name}</label>
                            </div>
                        `);
                        $('.check-select.attribute-select').find(`#attribute-${data.id}`).trigger('click');
                        var selects = [];
                        $('.check-select.attribute-select').find('.check-select-list input[type="checkbox"]').each(function(){
                            if($(this).prop('checked')){
                                selects.push($(this).parent().attr('title'));
                            }
                        });
                        console.log(selects);
                        if(selects.length > 0){
                            if(selects.length > 3){
                                $('.check-select.attribute-select').find('.title-selects').text(selects.length+' Selecionados');
                            }else{
                                $('.check-select.attribute-select').find('.title-selects').text(selects.join(', '));
                            }
                        }else{
                            $('.check-select.attribute-select').find('.title-selects').text($('.check-select.attribute-select').data('title'));
                        }
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
    $(document).on('click', '.btn-new-attribute-value', function(){
        var btn = $(this);
        var attr_id = $(this).closest('.row').find('select').data('attr_id');
        var seller_id = $('select.seller_id').val() || $('.seller_id').val();

        Swal.fire({
            title: 'Nova Variação do Atributo',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Criar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if(result.isConfirmed){
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
                    data: {seller_id, attr_id, value_name: result.value},
                    success: (data) => {
                        // console.log(data);
                        $('select.attr-variation-'+attr_id).append('<option value="'+data.id+'-'+attr_id+'">'+data.name+'</option>');
                        btn.parent().parent().find('select').val(data.id+'-'+attr_id);
                        Swal.fire({
                            icon: 'success',
                            title: 'Variação Criado',
                        });
                    }
                });
            }
        });
    });

    // Adicionando Planos
    $(document).on('click', '.btn-add-campo-plano', function(){
        var qty_planos = $('.div-row-planos').find('.row').length;
        for(var i=0;i<=qty_planos;i++){
            if($('.div-row-planos').find('.plan-'+i).length == 0){
                $('.div-row-planos').append(`
                    <div class="col-12 col-sm-3 plan-${i}">
                        <div class="card">
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="form-group col-12">
                                        <label for="">Título do Plano</label>
                                        <input type="text" class="form-control form-control-sm" name="plan[${i}][plan_title]">
                                    </div>
                                    <div class="col-12"><label for="">Realizar Cobrança a cada</label></div>
                                    <div class="form-group col-12">
                                        <select class="form-control form-control-sm" name="plan[${i}][select_interval]">
                                            <option value="4-week">4 Semanas</option>
                                            <option value="1-month">1 Mês</option>
                                            <option value="3-month">3 Meses</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="">Duração do Plano (Mês)</label>
                                        <input type="text" class="form-control form-control-sm" name="plan[${i}][duration_plan]">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="">Valor da Assinatura</label>
                                        <input type="text" class="form-control form-control-sm real" name="plan[${i}][plan_value]">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="">Quantidade de Entregas</label>
                                        <select class="form-control form-control-sm" name="plan[${i}][select_entrega]">
                                            <option value="semanal">Semanal</option>
                                            <option value="quinzenal">Quinzenal</option>
                                            <option value="mensal">Mensal</option>
                                            <option value="trimestral">Trimestral</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12 perecivel ${$('[name="perecivel"]').prop('checked') ? 'd-none' : ''}">
                                        <label for="">Peso KG</label>
                                        <input type="text" class="form-control form-control-sm" name="plan[${i}][peso]">
                                    </div>
                                    <div class="form-group col-12 perecivel ${$('[name="perecivel"]').prop('checked') ? 'd-none' : ''}">
                                        <label for="">Altura CM</label>
                                        <input type="text" class="form-control form-control-sm cm" name="plan[${i}][dimensoes_A]">
                                    </div>
                                    <div class="form-group col-12 perecivel ${$('[name="perecivel"]').prop('checked') ? 'd-none' : ''}">
                                        <label for="">Comprimento CM</label>
                                        <input type="text" class="form-control form-control-sm cm" name="plan[${i}][dimensoes_C]">
                                    </div>
                                    <div class="form-group col-12 perecivel ${$('[name="perecivel"]').prop('checked') ? 'd-none' : ''}">
                                        <label for="">Largura CM</label>
                                        <input type="text" class="form-control form-control-sm cm" name="plan[${i}][dimensoes_L]">
                                    </div>
                                    <div class="form-group col-12 perecivel">
                                        <label for="">Descrição</label>
                                        <textarea class="form-control form-control-sm" name="plan[${i}][descption_plan]"></textarea>
                                    </div>

                                    <div class="col-6 mt-3">
                                        <button type="button" class="btn btn-block btn-sm btn-danger btn-remove-campo-plano"><i class="fas fa-times"></i> remover</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }
        }
        $('.real').mask('000.000,00', {reverse: true});
        $('.kg').mask('000,000', {reverse: true});
        $('.cm').mask('000', {reverse: true});
    });
    $(document).on('click', '.btn-remove-campo-plano', function(){
        $(this).closest('.card').parent().remove();
    });

    // Adicionando cmapos de desontos gerais
    $(document).on('click', '.btn-add-campo-desconto', function(){
        var thiss = $(this).closest('.card');
        var qty_desconto = thiss.find('.div-row-desconto').find('.row').length;
        for(var i=0;i<=qty_desconto;i++){
            if(thiss.find('.div-row-desconto').find('.discount-'+i).length == 0){
                thiss.find('.div-row-desconto').append(`
                    <div class="col-12 discount-${i}">
                        <div class="row mb-3" style="font-size: .8rem;">
                            <div class="col-9 d-flex justify-content-center">
                                <label class="ml-2 mt-1" for="">Acima de</label>
                                <input type="number" class="form-control form-control-sm mx-2" name="discount[${i}][discount_quantity]" style="width:15%; height: 23px; font-size: .8rem;">
                                <label class="mt-1" for=""> unidades </label>
                                <label class="mt-1 ml-2" for=""> o preço é </label>
                                <input type="text" class="form-control form-control-sm real mx-2" placeholder="R$" name="discount[${i}][discount_value]" style="width:20%; height: 23px; font-size: .8rem;">
                            </div>
                            <div class="col-3 text-center"><button type="button" class="btn btn-danger btn-remove-campo-desconto" style="padding: 2px 4px; font-size: .8rem;"><i class="fas fa-times"></i> remover</button></div>
                        </div>
                    </div>
                `);
            }
        }
        $('.real').mask('000.000,00', {reverse: true});
    });
    $(document).on('click', '.btn-remove-campo-desconto', function(){
        $(this).closest('.row').parent().remove();
    });

    // Adicionadno descontos em variações
    $(document).on('click', '.btn-add-desconto-card-variacao', function(){
        var desconto_card_var = $(this).closest('.card');
        var qty_desconto = desconto_card_var.find('.div-row-desconto-var').find('.row').length;
        var data_rand = $(this).data('rand');
        for(var i=0;i<=qty_desconto;i++){
            if(desconto_card_var.find('.div-row-desconto-var').find('.discount-var-'+i).length == 0){
                desconto_card_var.find('.div-row-desconto-var').append(`
                    <div class="col-12 discount-var-${i}">
                        <div class="card">
                            <div class="card-body p-2">
                                <div class="row justify-content-center" style="font-size: .8rem;">
                                    <div class="col-12 d-flex justify-content-center">
                                        <label for="">Acima de</label>
                                        <input type="number" class="form-control form-control-sm mx-2" name="${data_rand}[discount][${i}][discount_quantity]" style="font-size: .8rem; width: 20%; height: 23px;">
                                        <label for=""> unidades </label>
                                    </div>
                                    <div class="d-flex justify-content-center col-12">
                                        <label for=""> o preço é </label>
                                        <input type="text" class="form-control form-control-sm mx-2 real" name="${data_rand}[discount][${i}][discount_value]" placeholder="R$" style="font-size: .8rem; width: 30%; height: 23px;">
                                    </div>

                                    <div class="col-12 text-center mt-1">
                                        <button type="button" class="btn btn-sm btn-primary btn-remove-desconto-card" style="font-size: .8rem; padding: 0 8px;"><i class="fas fa-times"></i> remover</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }
        }
        $('.real').mask('000.000,00', {reverse: true});
    });
    $(document).on('click', '.btn-remove-desconto-card', function(){
        $(this).closest('.card').parent().remove();
    });

    var next_element_valid = true;
    $(document).on('click', '.btn-continuar-save', function(){
        postProductStore($('.pills-active').val(), true);
        var tab_select = $(`#pills-${$('.pills-active').val()}-tab`);
        var next_element = tab_select.parent().next().children();
        while(true){
            if(!next_element.is('.d-none')) break;
            next_element = next_element.parent().next().children();
        }
        if(next_element_valid){
            var pills_active = next_element.attr('id').split('-')[1];
            $('.pills-active').val(pills_active);
            next_element.removeClass('inactive').trigger('click');

            if(pills_active !== 'inicio') $('.btn-voltar').removeClass('d-none');
        }
        if(next_element.parent().next().children().length == 0){
            next_element_valid = false;
            $(this).html(texto_btn);
        }

        currentStep(pills_active);
    });
    $(document).on('click', '.btn-voltar', function(){
        next_element_valid = true;
        var tab_select = $(`#pills-${$('.pills-active').val()}-tab`);
        var prev_element = tab_select.parent().prev().children();
        while(true){
            if(!prev_element.is('.d-none')) break;
            prev_element = prev_element.parent().prev().children();
        }
        if(next_element_valid){
            var pills_active = prev_element.attr('id').split('-')[1];
            $('.pills-active').val(pills_active);
            prev_element.removeClass('inactive').trigger('click');

            if(pills_active == 'inicio') $('.btn-voltar').addClass('d-none');
        }
        $('.btn-continuar-save').html('SALVAR E CONTINUAR');
        currentStep(pills_active);
    });
    $(document).on('click', '#pills-tab [data-toggle="pill"]', function(e){
        if(e.originalEvent){
            postProductStore($('.pills-active').val(), false);
            next_element_valid = true;
            var pills_active = $(this).attr('id').split('-')[1];
            $('.pills-active').val(pills_active);
            if(pills_active == 'inicio'){
                $('.btn-voltar').addClass('d-none');
            }else{
                $('.btn-voltar').removeClass('d-none');
            }
            $('.btn-continuar-save').html('SALVAR E CONTINUAR');
            currentStep(pills_active);
            if($(this).parent().next().children().length == 0){
                next_element_valid = false;
                $('.btn-continuar-save').html(texto_btn);
            }
        }
    });

    // Aprovando a analise do produto
    $(document).on('click', '.btn-aprovar-analise', function(){
        Swal.fire({
            icon: 'warning',
            title: 'Liberar produto?',
            showCancelButton: true,
            confirmButtonText: 'Sim, Liberar',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if(result.isConfirmed){
                Swal.fire({
                    title: 'Liberando produto...',
                    allowOutsideClick: false,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var form_data = new FormData();
                form_data.append('postProduct', 'true');
                form_data.append('postType', 'postLiberarProduto');
                form_data.append('id', $('#product_id').val());

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: form_data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        if(data.redirect_url) {
                            Swal.fire({
                                icon: 'success',
                                title: data.msg
                            }).then((result)=>{
                                if(result.isConfirmed){
                                    if(data.redirect_url) window.location.href = data.redirect_url;
                                    Swal.close();
                                }
                            });
                        }
                    }
                });
            }
        });
    });
    $(document).on('click', '.btn-salvar-campos-rejeicao', function(){
        Swal.fire({
            title: 'Salvando, aguarde...',
            allowOutsideClick: false,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var form_data = new FormData($(this).closest('form')[0]);
        form_data.append('postProduct', 'true');
        form_data.append('postType', 'postNegarProduto');
        form_data.append('id', $('#product_id').val());

        $.ajax({
            url: '',
            type: 'POST',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: (data) => {
                if(data.redirect_url) {
                    $(this).closest('.modal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Dados salvos, ',
                        showCancelButton: true,
                        confirmButtonText: 'Ir para listagem',
                        cancelButtonText: 'Ficar na pagina',
                    }).then((result)=>{
                        if(result.isConfirmed) window.location.href = data.redirect_url;
                    });
                }
            }
        });
    });

    $(function(){
        var inactive = true;
        $('#pills-tab [data-toggle="pill"]').each(function(){
            if(inactive){
                $(this).removeClass('inactive');
                if($(this).is(`#pills-${$('.pills-active').val()}-tab`)){
                    inactive = false;
                    $(this).trigger('click');
                }
                if($('.pills-active').val() !== 'inicio') $('.btn-voltar').removeClass('d-none');
            }
        });

        $('.event-time-click').each(function(){
            $(this).trigger('click');
            $(this).trigger('change');
        });
    });
});

function postProductStore(pills_active, finaliza){
    var form_data = new FormData($(`#form-${pills_active}`)[0]);
    form_data.append('postProduct', 'true');
    if($('#product_id').length > 0) form_data.append('id', $('#product_id').val());

    $.ajax({
        url: '',
        type: 'POST',
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: (data) => {
            console.log(data);
            if($('#product_id').length == 0){
                $('.content-wrapper').prepend(`<input type="hidden" id="product_id" value="${data.product_id}">`);
            }else{
                if((parseInt($('#product_id').val()) || 0) == 0) $('#product_id').val(data.product_id);
            }
            if(data.seo){
                if(data.seo.title) $('#form-seo').find('input[name="title"]').val(data.seo.title);
                if(data.seo.link) $('#form-seo').find('input[name="link"]').val(data.seo.link);
                if(data.seo.banner_path) $('#form-seo').find('.banner_path').html(`<img width="280px" src="${data.seo.banner_path}">`);
            }

            if(data.redirect_url && finaliza) {
                var next_element = $(`#pills-${pills_active}-tab`).parent().next().children();
                while(true){
                    if(!next_element.is('.d-none')) break;
                    next_element = next_element.parent().next().children();
                }
                if(next_element.parent().next().children().length == 0){
                    Swal.fire({
                        icon: 'success',
                        title: ((pills_active == 'seo') ? 'Produto cadastrado com sucesso!' : 'Seu produto foi cadastrado com sucesso e está em analise!')
                    }).then((result)=>{
                        if(result.isConfirmed){
                            window.location.href = data.redirect_url;
                        }
                    });
                }
            }
        }
    });
}

function currentStep(pills_active){
    var full_url = get_full_url;
    full_url = (full_url.search('[?]')) > 0 ? full_url.replaceAll('&amp;','&').split('?') : [full_url.replaceAll('&amp;','&'), ''];

    var new_url_get = '?';
    var url_get = full_url[1] ? full_url[1].split('&') : [];
    var count_order = 0;
    for(i in url_get){
        if(url_get[i].search('step') < 0){
            new_url_get += (count_order > 0 ? '&' : '')+url_get[i];
            count_order++;
        }
    }

    var url_insert = full_url[0]+new_url_get+`${(count_order >= 1 ? '&' : '')}step=${(pills_active ? pills_active : '')}`;
    window.history.pushState({url: url_insert}, $('title').text(), url_insert);
}

function checkProdutoVariavel(thiss){
    if(thiss.attr('id') === 'check_produto_variavel'){
        if(thiss.prop('checked')){
            $('#pills-variacoes-tab').removeClass('d-none');
            $('.preco-dimensao-geral, .desconto-progressivo').addClass('d-none');
            $('.preco-dimensao-geral, .desconto-progressivo').find('input').val('');

            var seller_id = $('select.seller_id').val() || $('.seller_id').val();
            $('.check-select.attribute-select .check-select-list').empty();
            var attrs_selected = $('.check-select.attribute-select').attr('data-attrs_selected') || [];
            attrs_selected = JSON.parse(attrs_selected);
            $.ajax({
                url: '/geral/busca-attrs',
                type: 'POST',
                data: {seller_id: seller_id},
                success: (data)=>{
                    $.each(data, (key, value)=>{
                        $('.check-select.attribute-select .check-select-list').append(`
                            <div class="check-select-div-input col-12 col-sm-3" title="${value.name}" data-toggle="tooltip">
                                <input type="checkbox" id="attribute-${value.id}" name="attrs[]"  ${$.inArray(value.id, attrs_selected) > -1 ? 'checked' : ''} value="${value.id}">
                                <label class="text-truncate" for="attribute-${value.id}">${value.name}</label>
                            </div>
                        `);
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }else{
            if(!$("[name='check_desconto']").prop('checked')){
                $('.preco-dimensao-geral').removeClass('d-none');
            }else{
                $('.desconto-progressivo').removeClass('d-none');
            }

            $('#pills-variacoes-tab').addClass('d-none');
            $('.check-select.attribute-select .check-select-list').empty();
            $('.variacoes').empty();

            if(!$('[name="perecivel"]').prop('checked')){
                $('.perecivel').removeClass('d-none');
            }
        }
    }
}

function checkPlanoAssinatura(thiss){
    if(thiss.attr('id') === 'check_plano_assinatura'){
        if(thiss.prop('checked')){
            $('#pills-assinatura-tab').removeClass('d-none');
            $('.preco-dimensao-geral, #pills-variacoes-tab').addClass('d-none');
            $('#check_stock_controller').parent().parent().addClass('d-none');
        }else{
            $('#pills-assinatura-tab').addClass('d-none');
            if(!$('#check_produto_variavel').prop('checked')) $('.preco-dimensao-geral').removeClass('d-none');
            $('#check_stock_controller').parent().parent().removeClass('d-none');
        }
    }
}