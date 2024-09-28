@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Páginas adicionais</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.pages')}}">Páginas</a></li>
                        <li class="breadcrumb-item">@if(isset($id)) Editar @else Nova @endif Página</li>
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
                            <h3 class="card-title">@if(isset($id)) Editar @else Nova @endif Página</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            @if (isset($id))
                                <form action="{{route('admin.page.update')}}" method="post" enctype='multipart/form-data'>
                            @else
                                <form action="{{route('admin.page.store')}}" method="post" enctype='multipart/form-data'>
                            @endif
                                @csrf
                                @isset ($id)
                                    <input type="hidden" name="id" value="{{$id}}">
                                @endisset
                                <div class="row justify-content-center">
                                    <div class="form-group col-12 col-sm-4">
                                        <label for="">Título da Página</label>
                                        <input type="text" name="title" class="form-control" value="{{$page_view->title ?? ''}}">
                                    </div>
                                    <div class="form-group col-12 col-sm-4">
                                        <label for="">Link Permanente</label>
                                        <input type="text" name="link" class="form-control" value="{{isset($page_view->link) ? str_replace('pagina/', '', $page_view->link) : ''}}">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="">Palavras Chaves</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control keywords-adds">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success btn-add-keywords">Adcionar</button>
                                            </div>
                                        </div>
                                        <div class="row mt-2 keywords_adds">
                                            @isset($page_view->keywords)
                                                @foreach ($page_view->keywords as $item)
                                                <div class="col mb-2">
                                                    <div class="keywords-span">
                                                        <input type="hidden" name="keywords[]" value="{{$item}}">
                                                        {{$item}}
                                                        <button type="button" class="btn close btn-remove-keywords">x</button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @endisset
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="" title="Snippet">Descrição do Site</label>
                                        <textarea name="description" class="form-control max-caracteres" data-max_caracteres="255">{{$page_view->description ?? ''}}</textarea>
                                    </div>
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="banner_path" title="Para Miniatura do Site">Imagem</label>
                                        <input type="file" name="banner_path" class="form-control">
                                        <div class="banner_path my-2">
                                            @isset($page_view->banner_path)
                                                <img class="rounded" width="280" src="/storage/{{$page_view->banner_path}}">
                                            @endisset
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label for="body_page">Corpo da Página</label>
                                    <textarea name="body_page" class="form-control textarea_page">{{$page_view->body_page ?? ''}}</textarea>
                                    {{-- <input type="text" name="descricao_completa" class="form-control" placeholder="Descrição completa"> --}}
                                </div>

                                <div class="row">
                                    <div class="col-12 text-right">
                                        <button type="button" class="btn btn-success btn-gravar-page"><i class="fas fa-save"></i> Gravar</button>
                                    </div>
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
            $('.textarea_page').summernote({
                height:800,
                minHeight: null,
                maxHeight: null,
                dialogsInBody: true,
                dialogsFade: false
            });$

            $('form').find('input').on('keyup', function(e){
                e.preventDefault();
                if(e.keyCode == 13){
                    if(!$(this).is('.keywords-add')){
                        $(this).closest('form').submit();
                    }
                }
            });
            $('.btn-gravar-page').on('click', function(){
                $(this).closest('form').submit();
            });
        });
    </script>
@endsection