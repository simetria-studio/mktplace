@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Categorias / {{$type == '0' ? 'Produtos' : 'Serviços'}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">@if ($id) <a href="{{asset('/admin/cadastro/categoria_menu/').($type == '0' ? 'produtos' : 'servicos')}}">Categorias</a> @else Categorias @endif</li>
                        {{-- @if ($id) <li class="breadcrumb-item active">{{$category_name}}</li> @endif --}}
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
                            <h3 class="card-title">Categoria @if ($id) <strong>{{$category_name}}</strong> @else Principal @endif</h3>
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
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#novaCategoria"><i class="fas fa-plus"></i> Nova Categoria</button>
                                    </div>
                                    <div class="col-6 text-right">
                                        @if ($id)
                                            <a class="btn btn-default btn-sm" href="{{asset('admin/cadastro/categoria_menu')}}"><i class="fas fa-arrow-left"></i> Voltar</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="container mt-2 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nº</th>
                                            <th>Ícone</th>
                                            <th>Nome</th>
                                            <th>Slug</th>
                                            <th>Tipo</th>
                                            {{-- @if ($id == null) <th>Sub Categorias</th> @endif --}}
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($categories as $category)
                                            <tr class="tr-id-{{$category->id}}">
                                                <td>{{$category->id}}</td>
                                                <td>
                                                    @php
                                                        // Pegamos somente a primeira imagem a ser a principal
                                                        $image      = Storage::get($category->icon);
                                                        $mime_type  = Storage::mimeType($category->icon);
                                                        $image      = 'data:'.$mime_type.';base64,'.base64_encode($image);
                                                    @endphp
                                                    <img width="45px" src="{{$image}}" alt="">
                                                </td>
                                                <td>{{$category->name}}</td>
                                                <td>{{$category->slug}}</td>
                                                <td>{{$category->type == '0' ? 'Produto' : 'Serviço'}}</td>
                                                {{-- @if ($category->parent_id == null) <td>{{$category->subCategories->count()}}</td> @endif --}}
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="">
                                                        <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarCategoria" data-dados="{{json_encode($category)}}"><i class="fas fa-edit"></i> Alterar</a>

                                                        <a href="#" class="btn btn-danger btn-xs btn-excluir-categoria" data-toggle="modal" data-target="#excluirCategoria" data-dados="{{json_encode($category)}}"><i class="fas fa-trash"></i> Apagar</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <th colspan="6">{{$categories->count()}} Categorias</th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="novaCategoria">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postNovaCategoria">
                    @csrf
                    <input type="hidden" name="parent_id" value="@if($id){{$id}}@endif">
                    <div class="modal-header">
                        <h4 class="modal-title">Nova Categoria</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            @if ($id == null)
                                <input type="hidden" name="type" value="{{$type}}">
                            @endif
                            <div class="form-group col-12">
                                <label for="category_name">Nome da Categoria</label>
                                <input type="text" name="category_name" class="form-control" placeholder="Nome da Categoria">
                            </div>

                            <div class="form-group col-12">
                                <label for="">Título da Página</label>
                                <input type="text" name="title" class="form-control">
                            </div>

                            <div class="form-group col-12">
                                <label for="">Link Permanente</label>
                                <input type="text" name="link" class="form-control">
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
                                            <table class="table table-sm table-striped keywords_adds"></table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label for="">Descrição do Site</label>
                                <textarea name="description" class="form-control max-caracteres" data-max_caracteres="160"></textarea>
                            </div>

                            <div class="form-group col-12 mb-2">
                                {{-- <label for="banner_path">Imagem para Pre-visualização</label> --}}
                                <div class="custom-file">
                                    <input name="banner_path" type="file" class="custom-file-input">
                                    <label class="custom-file-label" for="banner_path">Imagem para Pré-visualização</label>
                                </div>
                                <div class="banner_path mt-2"></div>
                                {{-- <input type="file" name="banner_path" class="form-control"> --}}
                            </div>

                            @if ($id == null)
                                <div class="form-group col-12">
                                    <div class="custom-file">
                                        <input name="img_icon" type="file" class="custom-file-input img_icon">
                                        <label class="custom-file-label" for="img_icon">Imagem Pequena (45x45)</label>
                                    </div>

                                    <div class="my-2 img-icon"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postNovaCategoria" data-save_route="{{route('novaCategoria')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarCategoria">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postEditarCategoria">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Atualizar Categoria</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="category_name">Nome da Categoria</label>
                                <input type="text" name="category_name" class="form-control name" placeholder="Nome da Categoria">
                            </div>

                            <div class="form-group col-12">
                                <label for="">Título da Página</label>
                                <input type="text" name="title" class="form-control">
                            </div>

                            <div class="form-group col-12">
                                <label for="">Link Permanente</label>
                                <input type="text" name="link" class="form-control">
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
                                            <table class="table table-sm table-striped keywords_adds"></table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label for="">Descrição do Site</label>
                                <textarea name="description" class="form-control max-caracteres" data-max_caracteres="160"></textarea>
                            </div>

                            <div class="form-group col-12 mb-2">
                                {{-- <label for="banner_path">Imagem para Pre-visualização</label> --}}
                                <div class="custom-file">
                                    <input name="banner_path" type="file" class="custom-file-input">
                                    <label class="custom-file-label" for="banner_path">Imagem para Pré-visualização</label>
                                </div>
                                <div class="banner_path mt-2"></div>
                                {{-- <input type="file" name="banner_path" class="form-control"> --}}
                            </div>

                            @if ($id == null)
                                <div class="form-group col-12">
                                    <div class="custom-file">
                                        <input name="img_icon" type="file" class="custom-file-input img_icon">
                                        <label class="custom-file-label" for="img_icon">Imagem Pequena (45x45)</label>
                                    </div>

                                    <div class="my-2 img-icon"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postEditarCategoria" data-save_route="{{route('atualizarCategoria')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="excluirCategoria">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postExcluirCategoria">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-danger btn-confirma-exclusao-categoria d-none"><i class="fas fa-save"></i> Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection