$(document).ready(function(){
    $('.date-mask-custom').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        // parentEl:'#novoServico',
        locale: {
            format: 'DD/MM/YYYY',
            daysOfWeek: ['dom','seg','ter','qua','qui','sex','sab'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','outubro','Novembro','Dezembro'],
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar'
        }
    });
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
            if(thiss.parent().index() !== $(this).parent().index() && $(this).prop('tagName') !== 'LABEL'){
                $(this).prop('checked', false);
                $(this).parent().find('label').removeClass('active');
                $(this).parent().css('pointer-events', 'auto');
            }
        });


        if($(this).prop('checked') && $(this).prop('tagName') !== 'LABEL'){
            $(this).parent().find('label').addClass('active');
            $(this).parent().css('pointer-events', 'none');
        }
    });
    $('.check-custom').each(function(){
        console.log($(this).prop('tagName'));
        if($(this).prop('checked') && $(this).prop('tagName') !== 'LABEL'){
            $(this).parent().css('pointer-events', 'none');
        }
    });

    // Função para verificar o desconto progressivo
    $(document).on('change', '[name="check_desconto"]', function(){
        if($(this).prop('checked')){
            if ($("[name='service_variations']").prop('checked') !== true)
            {
                $('.desconto-progressivo').removeClass('d-none');
            }else{
                $('.desconto-card').removeClass('d-none');
                $('.btn-add-desconto-card-variacao').removeClass('d-none');
            }
        }else{
            if ($("[name='service_variations']").prop('checked') !== true)
            {
                $('.price-variation').removeClass('d-none');
            }
            $('.btn-add-desconto-card-variacao').addClass('d-none');
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
                data: {positions: positions, postService: 'true', postType: 'postUpdatePositionFotos'},
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
        form_images.append('postService', 'true');
        form_images.append('postType', 'postAddFotos');
        form_images.append('service_id', $('#service_id').val());

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
            url: '/servico/variation/component',
            type: 'POST',
            data: {seller_id: $('.seller_id').val(), transporte_proprio: ($('[name="perecivel"]').prop('checked') ? 'true' : 'false'), stock_controller: ($('[name="stock_controller"]').prop('checked') ? 'true' : 'false'), attrs_id: attrs_id, check_discount: ($('[name="check_desconto"]').prop('checked') ? 'true' : 'false')},
            success: (data)=>{
                // console.log(data);

                $('.variacoes').prepend(data);
                $('.real').mask('000.000,00', {reverse: true});
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
        postServiceStore($('.pills-active').val(), true);
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
            postServiceStore($('.pills-active').val(), false);
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
                form_data.append('postService', 'true');
                form_data.append('postType', 'postLiberarService');
                form_data.append('id', $('#service_id').val());

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
        form_data.append('postService', 'true');
        form_data.append('postType', 'postNegarProduto');
        form_data.append('id', $('#service_id').val());

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

    // Endereço personalizado
    $(document).on('change', '[name="address_controller"]', function(){
        if($('[name="seller_id"]').val() !== ''){
            if($(this).prop('checked')){
                $('.mapa-controller').removeClass('d-none');
                $.ajax({
                    url: '/buscaAddressSeller',
                    type: 'POST',
                    data: {seller_id: $('[name="seller_id"]').val()},
                    success: (data) => {
                        // console.log(data);

                        if(data){
                            $('[name="number"]').val(data.number);
                            $('[name="complement"]').val(data.complement);
                            $('[name="phone"]').val(data.phone2);

                            $('[name="postal_code"]').val(data.post_code);
                            $('[name="address"]').val(data.address);
                            $('[name="address2"]').val(data.address2);
                            setTimeout(() => {
                                $('[name="state"]').val(data.state).trigger('change');
                            }, 600);
                            $('[name="city"]').html(`<option value="${data.city}">${data.city}</option>`);
                        }

                        $('[name="latitude"]').val(data.lat ? data.lat : '');
                        $('[name="longitude"]').val(data.lng ? data.lng : '');

                        if(data.lat && data.lng){
                            geocode({location:{lat: parseFloat(data.lat), lng: parseFloat(data.lng)}});
                        }
                    }
                });
            }else{
                $('.mapa-controller').addClass('d-none');
            }
        }else{
            $(this).prop('checked', false);
            Swal.fire({
                icon: 'error',
                title: 'Vendedor não selecionado'
            });
        }
    });

    // Buscar no mapa pelo cep infomado
    $(document).on('blur', '[name="postal_code"]', function(){
        Swal.fire({
            icon: 'info',
            title: 'Deseja buscar no mapa o cep informado?',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            focusConfirm: false,
        }).then((result) => {
            if(result.isConfirmed){
                geocode({address: $(this).val()});
            }
        });
    });

    // Configuração do temino
    $(document).on('click', '.select_termino', function(){
        $(this).closest('.card-body').find('.date-fim, .date-ocorrencia').addClass('d-none');
        switch($(this).val()){
            case 'data_fim':
                $(this).closest('.card-body').find('.date-fim').removeClass('d-none');
                break;
            case 'ocorrencia':
                $(this).closest('.card-body').find('.date-ocorrencia').removeClass('d-none');
                break;
        }
    });

    // Abrindo o campo de horas na seman
    $(document).on('change', '.check-semana', function(){
        if($(this).prop('checked')){
            $(this).closest('.card-body').find('.card-semana-'+$(this).data('card')).removeClass('d-none');
        }else{
            $(this).closest('.card-body').find('.card-semana-'+$(this).data('card')).addClass('d-none');
        }
    });

    // Adcionando horas no campo
    $(document).on('click', '.btn-qty-add-time', function(){
        var x = $(this).data('x');
        var y = $(this).data('y');
        var btn = $(this).parent().clone();
        var btn_parent = $(this).closest('.input-time');
        $(this).parent().html(`
            <button type="button" class="close btn-remove-time">x</button>
            <input type="time" class="form-control form-control-sm mr-1" name="${y}[${x}][semana][${$(this).data('semana')}][horario][]">
            <input type="time" class="form-control form-control-sm ml-1" name="${y}[${x}][semana][${$(this).data('semana')}][horario][]">
        `);
        btn_parent.append(btn);
    });
    $(document).on('click', '.btn-remove-time', function(e){
        $(this).parent().remove();
    });

    // Inserindo Datas no Card
    $(document).on('click', '.btn-add-date-card', function(){
        let x = Math.floor((Math.random() * 500) + 10);
        addDateCard(x, 'calendar', '3', 'date-card');
    });
    $(document).on('click', '.btn-add-date-card-variacao', function(){
        let x = $(this).data('rand');
        let x2 = Math.floor((Math.random() * 500) + 10);
        addDateCard(x2, 'variations['+x+'][calendar]', '12', 'date-card-var-'+x);
    });
    $(document).on('click', '.btn-remove-data-card', function(e) {
        e.preventDefault();
        $('#data-card-rand-'+$(this).data('id')).remove();
    });

    // Verificando hospedagem
    $(document).on('change', '.check-custom', function(){
        if($(this).attr('id') == 'check_hospedagem' || $(this).attr('id') == 'check_horarios'){
            if($(this).prop('checked') && $(this).attr('id') == 'check_hospedagem'){
                $('.preco-label').addClass('d-none');
                $('.preco-label-select').removeClass('d-none');
            }else{
                $('.preco-label').removeClass('d-none');
                $('.preco-label-select').addClass('d-none');
            }
        }
    });

    // Verificando variações
    $(document).on('change', '#check_service_variations', function(){checkServicoVariavel($(this));});

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

        setTimeout(()=>{
            checkServicoVariavel($('#check_service_variations'));
        },200);
    });
});

function postServiceStore(pills_active, finaliza){
    var form_data = new FormData($(`#form-${pills_active}`)[0]);
    form_data.append('postService', 'true');
    if($('#service_id').length > 0) form_data.append('id', $('#service_id').val());

    $.ajax({
        url: '',
        type: 'POST',
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: (data) => {
            console.log(data);
            if($('#service_id').length == 0){
                $('.content-wrapper').prepend(`<input type="hidden" id="service_id" value="${data.service_id}">`);
            }else{
                if((parseInt($('#service_id').val()) || 0) == 0) $('#service_id').val(data.service_id);
            }
            if(data.seo){
                if(data.seo.title) $('#form-seo').find('input[name="title"]').val(data.seo.title);
                if(data.seo.link) $('#form-seo').find('input[name="link"]').val(data.seo.link);
                if(data.seo.description) $('#form-seo').find('textarea[name="description"]').val(data.seo.description);
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
                        title: (pills_active == 'seo' ? 'Serviço cadastrado com sucesso!' : 'Seu serviço foi cadastrado com sucesso e está em analise!')
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

function checkServicoVariavel(thiss){
    if(thiss.prop('checked')){
        $('#pills-variacoes-tab').removeClass('d-none');
        $('.preco-geral-info').removeClass('d-none');
        $('.preco-geral, .desconto-progressivo').addClass('d-none');
        $('.preco-geral, .desconto-progressivo').find('input').val('');

        var seller_id = $('select.seller_id').val() || $('.seller_id').val();
        var attrs_selected = $('.check-select.attribute-select').attr('data-attrs_selected') || [];
        attrs_selected = JSON.parse(attrs_selected);
        console.log(attrs_selected);
        setTimeout(() => {
            $.ajax({
                url: '/geral/busca-attrs',
                type: 'POST',
                data: {seller_id: seller_id},
                success: (data)=>{
                    $('.check-select.attribute-select .check-select-list').empty();
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
        }, 600);
    }else{
        $('.preco-geral').removeClass('d-none');
        $('.preco-geral-info').addClass('d-none');
        if($("[name='check_desconto']").prop('checked')){
            $('.desconto-progressivo').removeClass('d-none');
        }

        $('#pills-variacoes-tab').addClass('d-none');
        $('.check-select.attribute-select .check-select-list').empty();
        $('.variacoes').empty();
    }
}

function addDateCard(x,y,z, append, data_x) {
    $.ajax({
        url: '/servico/date-card/component',
        type: 'POST',
        data: {x,y,z, data_x},
        success: (data)=>{
            $('.'+append).prepend(data);

            $('.date-mask-custom').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                // parentEl:'#novoServico',
                locale: {
                    format: 'DD/MM/YYYY',
                    daysOfWeek: ['dom','seg','ter','qua','qui','sex','sab'],
                    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','outubro','Novembro','Dezembro'],
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar'
                }
            });
        }
    });
    
}