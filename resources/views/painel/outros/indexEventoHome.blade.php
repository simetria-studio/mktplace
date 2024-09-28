@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Banner</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Banners</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-sm-8 input-group">
                                        <input type="text" class="form-control" name="titulo_evento_home" value="{{getTabelaGeral('titulos','titulo_evento_home')->valor??''}}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-success btn-save-title">Atualizar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Eventos Ativos (Largura=120xAltura=120)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="wmax" value="120">
                                <input type="hidden" name="hmax" value="120">
                                <div class="row">
                                    <div class="col-12 col-sm-6 col-md-4 imgs-count img-find-0">
                                        <div class="card">
                                            <div class="card-body">
                                                <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                                <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                                <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                                <div class="input-group input-group-sm mt-1 link d-none">
                                                    <input type="text" class="form-control" value="Posição" disabled>
                                                    <input type="text" class="form-control" name="imagem[0][posicao]" value="0">
                                                </div>
                                                <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                                <div class="form-check link d-none">
                                                    <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                                    <label for="">Abrir em Nova Aba?</label>
                                                </div>
                                                <div class="form-check link d-none">
                                                    <input type="checkbox" class="form-check-input" name="imagem[0][status]" checked>
                                                    <label for="">Evento Ativo?</label>
                                                </div>
                                                <div class="form-group link d-none">
                                                    <label for="name">Descrição curta <span class="count-max-length ml-2" style="font-size: .7rem;">(max. caracteres 255)</span></label>
                                                    <textarea name="imagem[0][descricao_curta]" class="form-control short-textarea" data-max-caracteres="255"></textarea>
                                                </div>
                                                <div class="image my-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach ($event_home_a as $event)
                                        <div class="col-12 col-sm-6 col-md-4 imgs-count img-find-{{$event->id}}">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input type="checkbox" id="destroy-image-{{$event->id}}" class="form-check-input" name="imagem_delete[]" value={{$event->id}}>
                                                        <label for="destroy-image-{{$event->id}}">Excluir Imagem?</label>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$event->id}}][name]" value="{{$event->file_name}}">
                                                    <div class="input-group input-group-sm mt-1">
                                                        <input type="text" class="form-control" value="Posição" disabled>
                                                        <input type="text" class="form-control" name="imagem_update[{{$event->id}}][posicao]" value="{{$event->posicao}}">
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$event->id}}][link]" value="{{$event->link}}" placeholder="Link Direto">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="imagem_update[{{$event->id}}][new_tab]" @if($event->new_tab == 1) checked @endif>
                                                        <label for="">Abrir em Nova Aba?</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="imagem_update[{{$event->id}}][status]" checked>
                                                        <label for="">Evento Ativo?</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Descrição curta <span class="count-max-length ml-2" style="font-size: .7rem;">(max. caracteres 255)</span></label>
                                                        <textarea name="imagem_update[{{$event->id}}][descricao_curta]" class="form-control short-textarea" data-max-caracteres="255">{{$event->descricao_curta}}</textarea>
                                                    </div>
                                                    <div class="image my-2">
                                                        <img src="{{$event->url_file}}" alt="{{$event->file_name}}" title="{{$event->file_name}}" class="rounded img-fluid img-bordered">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Eventos Inativos</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <div class="row">
                                    @foreach ($event_home_i as $event)
                                        <div class="col-12 col-sm-6 col-md-4 imgs-count img-find-{{$event->id}}">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-check">
                                                        <input type="checkbox" id="destroy-image-{{$event->id}}" class="form-check-input" name="imagem_delete[]" value={{$event->id}}>
                                                        <label for="destroy-image-{{$event->id}}">Excluir Imagem?</label>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$event->id}}][name]" value="{{$event->file_name}}">
                                                    <div class="input-group input-group-sm mt-1">
                                                        <input type="text" class="form-control" value="Posição" disabled>
                                                        <input type="text" class="form-control" name="imagem_update[{{$event->id}}][posicao]" value="{{$event->posicao}}">
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$event->id}}][link]" value="{{$event->link}}" placeholder="Link Direto">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="imagem_update[{{$event->id}}][new_tab]" @if($event->new_tab == 1) checked @endif>
                                                        <label for="">Abrir em Nova Aba?</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="imagem_update[{{$event->id}}][status]">
                                                        <label for="">Evento Ativo?</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name">Descrição curta <span class="count-max-length ml-2" style="font-size: .7rem;">(max. caracteres 255)</span></label>
                                                        <textarea name="imagem_update[{{$event->id}}][descricao_curta]" class="form-control short-textarea" data-max-caracteres="255">{{$event->descricao_curta}}</textarea>
                                                    </div>
                                                    <div class="image my-2">
                                                        <img src="{{$event->url_file}}" alt="{{$event->file_name}}" title="{{$event->file_name}}" class="rounded img-fluid img-bordered">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $(document).on('click', '.btn-save-local', function(){
                // Pegamos os dados do data
                let btn = $(this);
                btn.prop('disabled', true).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');

                $.ajax({
                    url: `{{route('admin.eventoHome.store')}}`,
                    type: "POST",
                    data: new FormData(btn.closest('form')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        btn.prop('disabled', false).html('Atualizar');
                        window.location.reload();
                    },
                    error: (err) => {
                        // console.log(err);
                        btn.prop('disabled', false).html('Atualizar');

                        if(err.responseJSON.msg_alert){
                            Swal.fire({
                                icon: err.responseJSON.icon_alert,
                                text: err.responseJSON.msg_alert,
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.btn-save-title', function(){
                // Pegamos os dados do data
                let btn = $(this);
                btn.prop('disabled', true).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
                var data = {
                    tabela: 'titulos',
                    coluna: 'titulo_evento_home',
                    valor: btn.closest('form').find('[name="titulo_evento_home"]').val(),
                };

                $.ajax({
                    url: `{{route('admin.tabelaGeral')}}`,
                    type: "POST",
                    data: data,
                    success: (data) => {
                        btn.prop('disabled', false).html('Atualizar');
                        Swal.fire({
                            icon: 'success',
                            text: 'Atualizado com successo!',
                        });
                    },
                    error: (err) => {
                        // console.log(err);
                        btn.prop('disabled', false).html('Atualizar');

                        if(err.responseJSON.msg_alert){
                            Swal.fire({
                                icon: err.responseJSON.icon_alert,
                                text: err.responseJSON.msg_alert,
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.btn-add-c-image', function(e){
                e.preventDefault();
                $(this).parent().find('.add-c-image').trigger('click');
            });
            $(document).on('change', '.add-c-image', function(){
                $(this).removeClass('add-c-image');

                $(this).parent().find('.btn-add-c-image').removeClass('btn-success btn-add-c-image').addClass('btn-danger btn-c-remove-image').html('x');

                var count_i = 0;
                for(var i=0; ($(this).parent().parent().parent().parent().find('.imgs-count').length+1)>i; i++){
                    if(!$(this).parent().parent().parent().parent().find('.imgs-count').is('.img-find-'+i)){
                        count_i = i;
                    }
                }

                $(this).parent().parent().parent().parent().prepend(`
                    <div class="col-12 col-sm-6 col-md-4 imgs-count img-find-${count_i}">
                        <div class="card">
                            <div class="card-body">
                                <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                <input type="file" class="d-none add-c-image" name="imagem[${count_i}][img]">
                                <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[${count_i}][name]">
                                <div class="input-group input-group-sm mt-1 link d-none">
                                    <input type="text" class="form-control" value="Posição" disabled>
                                    <input type="text" class="form-control" name="imagem[${count_i}][posicao]" value="${count_i}">
                                </div>
                                <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[${count_i}][link]" placeholder="Link Direto">
                                <div class="form-check link d-none">
                                    <input type="checkbox" class="form-check-input" name="imagem[${count_i}][new_tab]">
                                    <label for="">Abrir em Nova Aba?</label>
                                </div>
                                <div class="form-check link d-none">
                                    <input type="checkbox" class="form-check-input" name="imagem[${count_i}][status]" checked>
                                    <label for="">Evento Ativo?</label>
                                </div>
                                <div class="form-group link d-none">
                                    <label for="name">Descrição curta <span class="count-max-length ml-2" style="font-size: .7rem;">(max. caracteres 255)</span></label>
                                    <textarea name="imagem[${count_i}][descricao_curta]" class="form-control short-textarea" data-max-caracteres="255"></textarea>
                                </div>
                                <div class="image my-2"></div>
                            </div>
                        </div>
                    </div>
                `);

                $('.short-textarea').summernote({
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['color', ['color']]
                    ],
                    callbacks: {
                        onKeydown: function(e) {
                            var max = $(this).data('max-caracteres');
                            var t = e.currentTarget.innerText;
                            if (t.length >= max) {
                                //delete key
                                if (e.keyCode != 8)
                                e.preventDefault();
                                // add other keys ...
                            }
                        },
                        onKeyup: function(e) {
                            var max = $(this).data('max-caracteres');
                            var t = e.currentTarget.innerText;
                            $(this).parent().find('.count-max-length').text(`(max. caracteres ${(max - t.length)})`);
                        },
                        onPaste: function(e) {
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

                var form_img = $(this).parent();

                var preview = form_img.find('.image');
                var file_title = form_img.find('.file-title');
                var files   = $(this).prop('files');

                if(!$(this).parent().parent().parent().parent().is('.not-display-input')){
                    $(this).parent().find('.file-title').removeClass('file-title d-none');
                }
                $(this).parent().find('.link').removeClass('d-none');

                function readAndPreview(file) {
                    // Make sure `file.name` matches our extensions criteria
                    if ( /\.(jpe?g|png|gif)$/i.test(file.name) ) {
                        var reader = new FileReader();

                        file_title.val(file.name);

                        reader.addEventListener("load", function () {
                        var image = new Image();
                        image.classList = 'rounded img-fluid img-bordered';
                        // image.height = 180;
                        image.title = file.name;
                        image.src = this.result;
                        preview.append( image );
                        }, false);

                        reader.readAsDataURL(file);
                    }
                }

                if (files) {
                    [].forEach.call(files, readAndPreview);
                }
            });
            $(document).on('click', '.btn-c-remove-image', function(){
                $(this).parent().parent().parent().remove();
            });
        });
    </script>
@endsection