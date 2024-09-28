@extends('layouts.painelAdm')

@section('container')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Novo Bairro</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                    <li class="breadcrumb-item">Novo Bairro</li>
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
                        <h3 class="card-title">Dados do transporte</h3>
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
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#novoBairro"><i class="fas fa-plus"></i> Novo Bairro</button>
                                </div>
                            </div>
                        </div>

                        <div class="container mt-2 table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>Estado</th>
                                        <th>Cidade</th>
                                        <th>Bairro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bairros as $bairro)
                                        <tr class="tr-id-{{$bairro->id}}">
                                            <td>{{$bairro->id}}</td>
                                            <td>{{$bairro->estado->titulo}}</td>
                                            <td>{{$bairro->cidade->titulo}}</td>
                                            <td>{{$bairro->titulo}}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="">
                                                    <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarBairro" data-dados="{{json_encode($bairro)}}"><i class="fas fa-edit"></i> Editar</a>
                                                    <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirBairro" data-dados="{{json_encode($bairro)}}"><i class="fas fa-trash"></i> Apagar</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <th colspan="6">{{$bairros->count()}} Contas</th>
                                </tfoot>
                            </table>
                        </div>

                        <div class="container mt-2">{{$bairros->links()}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="novoBairro">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postNovoBairro">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Novo Bairro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">Estado</label>
                            <select class="form-control" name="estado_id">
                                <option value="">- Selecione um Estado -</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Cidade</label>
                            <select class="form-control" name="cidade_id">
                                <option value="">- Selecione uma Cidade -</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Bairro</label>
                            <input type="text" name="bairro_name" class="form-control" placeholder="Nome do bairro">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar" data-update_table="N" data-save_target="#postNovoBairro" data-save_route="{{route('admin.bairro.store')}}"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editarBairro">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postEditarBairro">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-header">
                    <h4 class="modal-title">Atualizar Endereço</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">Estado</label>
                            <select class="form-control estado" name="edit_estado_id">
                                <option value="">- Selecione um Estado -</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Cidade</label>
                            <select class="form-control" name="edit_cidade_id">
                                <option value="">- Selecione uma Cidade -</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Bairro</label>
                            <input type="text" name="edit_bairro_name" class="form-control" placeholder="Nome do bairro">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postEditarBairro" data-save_route="{{route('admin.bairro.edit')}}"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="excluirBairro">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postExcluirBairro">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-header">
                    <h4 class="modal-title">Excluir Bairro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Tem certeza que gostaria de apagar esse Bairro?</h5>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-danger btn-salvar" data-trash="S" data-save_target="#postExcluirBairro" data-save_route="{{route('admin.bairro.destroy')}}"><i class="fas fa-trash"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection