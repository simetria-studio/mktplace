@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Loja do Vendedor</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{asset('/admin/cliente/vendedores')}}">Vendedores</a></li>
                        <li class="breadcrumb-item">Loja</li>
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
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Dados da Loja de {{$seller->name}}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
                            <div class="container">
                                <div class="row">
                                    <div class="col-6">
                                        @if (!empty($seller->store))
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#atualizarSEO">Atualizar SEO da Loja</button>
                                        @else
                                            <h4 class="text-warining">Vendedor ainda não criou sua loja!</h4>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="container mt-2">
                                <div class="row">
                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Nome da Loja:</strong> {{$seller->store->store_name ?? ''}}
                                    </div>
                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Link da Loja:</strong> <a target="_blank" href="{{isset($seller->store->store_slug) ? route('seller.store',[$seller->store->store_slug]) : '#'}}">{{isset($seller->store->store_slug) ? asset('loja-vendedor/'.$seller->store->store_slug) : 'Sem Link'}}</a>
                                    </div>

                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Endereço:</strong> {{$seller->store->address ?? ''}}, Nº {{$seller->store->number ?? ''}} - {{$seller->store->address2 ?? ''}}
                                    </div>
                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Estado/Cidade:</strong> {{$seller->store->state ?? ''}} / {{$seller->store->city ?? ''}} - {{$seller->store->post_code ?? ''}}
                                    </div>
                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Complemento:</strong> {{$seller->store->complement ?? ''}}
                                    </div>
                                    <div class="col-6 col-sm-3 py-3 px-3 border-bottom">
                                        <strong>Telefone:</strong> {{$seller->store->phone1 ?? ''}}
                                    </div>
                                    <div class="col-6 col-sm-3 py-3 px-3 border-bottom">
                                        <strong>Celular:</strong> {{$seller->store->phone2 ?? ''}}
                                    </div>
                                    <div class="col-12 col-sm-6 py-3 px-3">
                                        <div>
                                            <strong>Logotipo: (260x260)</strong>
                                            <button type="button" class="btn btn-sm btn-success btn-path-update">{{isset($seller->store->logo_path) ? 'Atualizar' : 'Inserir'}} logotipo</button>
                                            <input type="file" name="logo_path" class="d-none file-change" data-url="{{route('atualizarIMGSVendedor')}}" data-wh="[130,130]" data-id="{{$seller->store->id}}">
                                        </div>
                                        <br>
                                        <div class="image">
                                            @isset($seller->store->logo_path)
                                                <img src="{{($seller->store->logo_path ?? null) ? asset('storage/'.$seller->store->logo_path) : asset('site/imgs/logo.png')}}" alt="LOGO" width="130" height="130" class="rounded">
                                            @endisset
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 py-3 px-3">
                                        <div>
                                            <strong>Banner: (760x260)</strong>
                                            <button type="button" class="btn btn-sm btn-success btn-path-update">{{isset($seller->store->banner_path) ? 'Atualizar' : 'Inserir'}} banner</button>
                                            <input type="file" name="banner_path" class="d-none file-change" data-url="{{route('atualizarIMGSVendedor')}}" data-wh="[370,130]" data-id="{{$seller->store->id}}">
                                        </div>
                                        <br>
                                        <div class="image">
                                            @isset($seller->store->banner_path)
                                                <img src="{{($seller->store->logo_path ?? null) ? asset('storage/'.$seller->store->banner_path) : asset('site/imgs/banner-vendedor-faltante.png')}}" alt="LOGO" width="370" height="130" class="rounded">
                                            @endisset
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-12 my-1"><h3>SEO da página do Vendedor</h3></div>

                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Título:</strong> {{$seller->store->title ?? ''}}
                                    </div>
                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Link Permanente:</strong> {{asset(($seller->store->link ?? ''))}}
                                    </div>
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="">Palavras-Chave</label>
                                        <div class="form-group mt-2">
                                            <div class="card collapsed-card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Lista de palavras chave</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                                title="Collapse">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="overflow-auto" style="max-height: 280px;">
                                                        <table class="table table-sm table-striped keywords_adds">
                                                            @foreach (($seller->store->keywords ?? []) as $item)
                                                                <tr>
                                                                    <td class="border-bottom"><input type="hidden" name="keywords[]" value="{{$item}}">  <span class="ml-2">{{$item}}</span></td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Descrição:</strong> <br>
                                        {{$seller->store->description ?? ''}}
                                    </div>
                                    <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                        <strong>Imagem:</strong> <br>
                                        @isset($seller->store->banner_path_two)
                                            <img class="rounded" width="280" src="/storage/{{$seller->store->banner_path_two}}">
                                        @endisset
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="atualizarSEO">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postAtualizarSEO">
                    @csrf
                    <input type="hidden" name="id" value="{{$seller->store->id ?? ''}}">
                    <div class="modal-header">
                        <h4 class="modal-title">Atualizar SEO</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="">Título da Página</label>
                                <input type="text" name="title" class="form-control" value="{{$seller->store->title ?? ''}}">
                            </div>

                            <div class="form-group col-12">
                                <label for="">Link Permanente</label>
                                <input type="text" name="link" class="form-control" value="{{$seller->store->link ?? ''}}">
                            </div>

                            <div class="form-group col-12">
                                <label for="">Palavras-Chave</label>
                                <div class="input-group">
                                    <input type="text" class="form-control keywords-adds" placeholder="Inserir termos separados por ';'">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success btn-add-keywords">Adicionar</button>
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <div class="card collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">Lista de palavras chave</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                        title="Collapse">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="overflow-auto" style="max-height: 280px;">
                                                <table class="table table-sm table-striped keywords_adds">
                                                    @foreach (($seller->store->keywords ?? []) as $item)
                                                        <tr>
                                                            <td class="border-right border-bottom" width="5%"><button type="button" class="btn py-0 btn-remove-keyword">x</button></td>
                                                            <td class="border-bottom"><input type="hidden" name="keywords[]" value="{{$item}}">  <span class="ml-2">{{$item}}</span></td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label for="">Descrição do Site</label>
                                <textarea name="description" class="form-control max-caracteres" data-max_caracteres="160">{{$seller->store->description ?? ''}}</textarea>
                            </div>

                            <div class="form-group col-12 mb-2">
                                {{-- <label for="banner_path">Imagem para Pre-visualização</label> --}}
                                <div class="custom-file">
                                    <input name="banner_path_two" type="file" class="custom-file-input">
                                    <label class="custom-file-label" for="banner_path_two">Imagem para Pré-visualização</label>
                                </div>
                                <div class="banner_path_two mt-2">
                                    @isset($seller->store->banner_path_two)
                                        <img class="rounded" width="280" src="/storage/{{$seller->store->banner_path_two}}">
                                    @endisset
                                </div>
                                {{-- <input type="file" name="banner_path" class="form-control"> --}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-refresh="S" data-save_target="#postAtualizarSEO" data-save_route="{{route('atualizarSEOVendedor')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $(document).on('click', '.btn-path-update',function(){$(this).parent().find('input.file-change').trigger('click');});
            $(document).on('change', '.file-change', function(){
                $(this).parent().parent().find('.image').empty();

                var wh = [100,100];
                if($(this).data('wh')) wh = $(this).data('wh');

                var preview = $(this).parent().parent().find('.image');
                var files   = $(this).prop('files');

                function readAndPreview(file) {
                    // Make sure `file.name` matches our extensions criteria
                    if ( /\.(jpe?g|png|gif)$/i.test(file.name) ) {
                        var reader = new FileReader();

                        reader.addEventListener("load", function () {
                        var image = new Image();
                        image.classList = 'rounded';
                        image.width = parseFloat(wh[0]);
                        image.height = parseFloat(wh[1]);
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

                var formData = new FormData();
                formData.append($(this).attr('name'), $(this).prop('files')[0]);
                formData.append('store_id', $(this).data('id'));
                $.ajax({
                    url: $(this).data('url'),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        // console.log(data);
                    }
                });
            });
        });
    </script>
@endsection