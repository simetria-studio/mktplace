@extends('layouts.painelSman')

@section('container')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Transporte Próprio</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{asset('/vendedor')}}">Dashboard</a></li>
                    <li class="breadcrumb-item">Transporte Próprio</li>
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
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#novoTransporte"><i class="fas fa-plus"></i> Novo Transporte</button>
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
                                        <th>Valor Entrega</th>
                                        <th>Tempo de Entrega</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transportes as $transporte)
                                        <tr class="tr-id-{{$transporte->id}}">
                                            <td>{{$transporte->id}}</td>
                                            <td>{{$transporte->estado}}</td>
                                            <td>{{$transporte->cidade}}</td>
                                            <td>{{$transporte->bairro}}</td>
                                            <td>R$ {{$transporte->valor_entrega}}</td>
                                            <td>{{$transporte->tempo_entrega}} {{$transporte->tempo == 'H' ? 'Horas' : 'Dias'}}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="">
                                                    <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarTransporte" data-dados="{{json_encode($transporte)}}"><i class="fas fa-edit"></i> Editar</a>
                                                    <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirTransporte" data-dados="{{json_encode($transporte)}}"><i class="fas fa-trash"></i> Apagar</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    {{-- <th colspan="6">{{$accounts->count()}} Contas</th> --}}
                                </tfoot>
                            </table>
                        </div>

                        {{-- <div class="container mt-2">{{$accounts->links()}}</div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="novoTransporte">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postNovoTransporte">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Nova Entrega <div class="spinner-border d-none loadCep" role="status"><span class="sr-only">Loading...</span></div></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">Estado</label>
                            <select class="form-control selectpicker" data-size="4" data-live-search="true" title="Escolha o Estado" name="estado">
                                <option value="">- Selecione um Estado -</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 cidade">
                            <label for="">Cidade</label>
                            <select class="form-control selectpicker" data-size="4" data-live-search="true" title="Escolha a Cidade" name="cidade">
                                <option value="">- Selecione uma Cidade -</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 bairro">
                            <label for="">Bairro</label>
                            <select class="form-control bairro_select selectpicker" data-header="Selecione os Bairros" data-size="4" data-actions-box="true" data-live-search="true" title="Escolha os Bairros" multiple="multiple" name="bairro[]">
                                <option value="">- Selecione um Bairro -</option>
                            </select>
                            <input type="text" class="form-control bairro_input d-none" placeholder="Nome do bairro">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Valor</label>
                            <input type="text" class="form-control" id="sellprice" name="valor_frete" placeholder="Valor do transporte">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <div class="icheck-primary">
                                <input type="checkbox" id="em_todas_cidades" name="em_todas_cidades" value="true">
                                <label for="em_todas_cidades">Entrega em todas as Cidades?</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6 toda_cidade">
                            <div class="icheck-primary">
                                <input type="checkbox" id="toda_cidade" name="toda_cidade" value="true">
                                <label for="toda_cidade">Entrega em todos os Bairros?</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">Formato de Entrega</label>
                            <select class="form-control" name="tempo">
                                <option value="">- Selecione o Formato de Entrega -</option>
                                <option value="H">Horas</option>
                                <option value="D">Dias</option>
                                <option value="S">Semanalmente</option>
                                <option value="C">Customizado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Tempo</label>
                            <input type="text" class="form-control" name="tempo_entrega" placeholder="Tempo de entrega">
                        </div>
                        <div class="form-group col-md-12 row justify-content-center semana d-none">
                            <div class="col d-flex flex-column"><label class="mb-0">Dom.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="1"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Seg.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="2"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Ter.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="3"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Qua.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="4"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Qui.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="5"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Sex.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="6"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Sab.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="7"></div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">Descrição</label>
                            <textarea name="descricao" data-max_caracteres="150" class="form-control max-caracteres"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <div class="icheck-primary">
                                <input type="checkbox" id="frete_gratis" name="frete_gratis" value="true">
                                <label for="frete_gratis">Oferece frete grátis?</label>
                            </div>
                        </div>

                        <div class="form-group col-md-6 d-none">
                            <label for="">Pedido mínimo para entrega grátis</label>
                            <input type="number" class="form-control" name="valor_minimo" placeholder="R$">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postNovoTransporte" data-save_route="{{route('seller.novoTransporte')}}"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editarTransporte">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postEditarTransporte">
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
                            <select class="form-control selectpicker" data-size="4" data-live-search="true" title="Escolha o Estado" name="edit_estado">
                                <option value="">- Selecione um Estado -</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 cidade">
                            <label for="">Cidade</label>
                            <select class="form-control selectpicker" data-size="4" data-live-search="true" title="Escolha a Cidade" name="edit_cidade">
                                <option value="">- Selecione uma Cidade -</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 bairro">
                            <label for="">Bairro</label>
                            <select class="form-control bairro_select selectpicker" data-header="Selecione os Bairros" data-size="4" data-actions-box="true" data-live-search="true" title="Escolha os Bairros" multiple="multiple" name="edit_bairro[]">
                                <option value="">- Selecione um Bairro -</option>
                            </select>
                            <input type="text" class="form-control bairro_input d-none" placeholder="Nome do bairro">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Valor</label>
                            <input type="text" class="form-control valor_entrega" id="sellprice" name="valor_frete" placeholder="Valor do transporte">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <div class="icheck-primary">
                                <input type="checkbox" id="edit_em_todas_cidades" name="em_todas_cidades" value="true">
                                <label for="edit_em_todas_cidades">Entrega em todas as Cidades?</label>
                            </div>
                        </div>
                        <div class="form-group col-md-6 toda_cidade">
                            <div class="icheck-primary">
                                <input type="checkbox" id="edit_toda_cidade" name="toda_cidade" value="true">
                                <label for="edit_toda_cidade">Entrega em todos os Bairros?</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">Formato de Entrega</label>
                            <select class="form-control" name="tempo">
                                <option value="">- Selecione o Formato de Entrega -</option>
                                <option value="H">Horas</option>
                                <option value="D">Dias</option>
                                <option value="S">Semanalmente</option>
                                <option value="C">Customizado</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Tempo</label>
                            <input type="text" class="form-control" name="tempo_entrega" placeholder="Tempo de entrega">
                        </div>
                        <div class="form-group col-md-12 row justify-content-center semana d-none">
                            <div class="col d-flex flex-column"><label class="mb-0">Dom.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="1"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Seg.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="2"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Ter.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="3"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Qua.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="4"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Qui.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="5"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Sex.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="6"></div>
                            <div class="col d-flex flex-column"><label class="mb-0">Sab.</label><input type="checkbox" style="width: 20px;" class="form-control" name="semana[]" value="7"></div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">Descrição</label>
                            <textarea name="descricao" data-max_caracteres="150" class="form-control max-caracteres"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <div class="icheck-primary">
                                <input type="checkbox" id="edit_frete_gratis" name="frete_gratis" value="true">
                                <label for="edit_frete_gratis">Oferece frete grátis?</label>
                            </div>
                        </div>

                        <div class="form-group col-md-6 d-none">
                            <label for="">Pedido mínimo para entrega grátis</label>
                            <input type="number" class="form-control" name="valor_minimo" placeholder="R$">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postEditarTransporte" data-save_route="{{route('seller.editarTransporte')}}"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="excluirTransporte">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postExcluirTransporte">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-header">
                    <h4 class="modal-title">Excluir Transporte</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Tem certeza que gostaria de apagar esse transporte?</h5>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-danger btn-salvar" data-trash="S" data-save_target="#postExcluirTransporte" data-save_route="{{route('seller.excluirTransporte')}}"><i class="fas fa-trash"></i> Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection