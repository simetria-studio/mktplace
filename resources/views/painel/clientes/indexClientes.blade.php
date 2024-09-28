@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Clientes do Sistema</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Clientes</li>
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
                            <h3 class="card-title">Contas de Usuários</h3>
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
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#novoCliente"><i class="fas fa-plus"></i> Novo Cliente</button>
                                    </div>
                                    <div class="col-12">
                                        <form action="" method="get">
                                            <div class="row mt-2">
                                                <div class="col-12"><h4>Filtros para clientes</h4></div>
                                                <div class="col-12 col-md-5 form-group">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control search-filter" name="search_value" placeholder="Pesquisar por..." value="{{$request->search_value ?? ''}}">
                                                        <select name="column_name" class="form-control">
                                                            <option value="name" @if(($request->column_name ?? 'null') == 'name') selected @endif>Nome do Cliente</option>
                                                            <option value="cnpj_cpf" @if(($request->column_name ?? 'null') == 'cnpj_cpf') selected @endif>CNPJ/CPF</option>
                                                            <option value="email" @if(($request->column_name ?? 'null') == 'email') selected @endif>Email</option>
                                                        </select>
                                                    </div>
                                                </div>
    
                                                <div class="col-12 col-md-2 form-group">
                                                    <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                                                </div>
                                            </div>
    
                                            <div class="row mt-2">
                                                <div class="col-12 col-md-4 form-group">
                                                    <label for="">Clientes por Página</label>
                                                    <select name="per_page" class="form-control">
                                                        <option value="20" @if(($request->per_page ?? 'null') == '20') selected @endif>20 por Página</option>
                                                        <option value="30" @if(($request->per_page ?? 'null') == '30') selected @endif>30 por Página</option>
                                                        <option value="50" @if(($request->per_page ?? 'null') == '50') selected @endif>50 por Página</option>
                                                        <option value="100" @if(($request->per_page ?? 'null') == '100') selected @endif>100 por Página</option>
                                                        <option value="500" @if(($request->per_page ?? 'null') == '500') selected @endif>500 por Página</option>
                                                        <option value="1000" @if(($request->per_page ?? 'null') == '1000') selected @endif>1000 por Página</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="container mt-2 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nº</th>
                                            <th>Nome</th>
                                            <th>CNPJ/CPF</th>
                                            <th>Email</th>
                                            <th>Endereços</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($accounts as $account)
                                            <tr class="tr-id-{{$account->id}}">
                                                <td>{{$account->id}}</td>
                                                <td>{{$account->name}}</td>
                                                <td>{{$account->cnpj_cpf}}</td>
                                                <td>{{$account->email}}</td>
                                                <td><a href="{{url('admin/cliente/enderecos-cliente', $account->id)}}" class="btn btn-info btn-sm">({{$account->adresses->count()}}) <i class="fas fa-eye"></i> Visualizar</a></td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="">
                                                        <a href="{{route('admin-logar', ['id' => $account->id, 'user' => 'client'])}}" class="btn btn-success btn-sm">Acessar Conta</a>
                                                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#atualizarSenha" data-dados="{{json_encode($account)}}"><i class="fas fa-edit"></i> Trocar Senha</a>
                                                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarCliente" data-dados="{{json_encode($account)}}"><i class="fas fa-edit"></i> Editar Cliente</a>
                                                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirCliente" data-dados="{{json_encode($account)}}"><i class="fas fa-trash"></i> Apagar Conta</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <th colspan="6">{{$accounts->count()}} Contas</th>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="container mt-2">{{$accounts->links()}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="novoCliente">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postNovoCliente">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Novo Clientes</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="name">Nome Completo</label>
                                <input type="text" name="name" class="form-control" placeholder="Nome do Usuário">
                            </div>
                            <div class="form-group col-12">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email da Conta">
                            </div>
                            <div class="form-group col-12">
                                <label for="cnpj_cpf">CNPJ/CPF</label>
                                <input type="text" name="cnpj_cpf" class="form-control" placeholder="CNPJ ou CPF">
                            </div>
                            <div class="form-group col-12">
                                <label for="password">Senha</label>
                                <input type="password" name="password" class="form-control" placeholder="Senha da Conta">
                            </div>
                            <div class="form-group col-12">
                                <label for="password_confirmation">Comfirmar Senha</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar a senha digitada">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postNovoCliente" data-save_route="{{route('novoCliente')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarCliente">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postEditarCliente">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Atualizar Cliente</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="name">Nome Completo</label>
                                <input type="text" name="name" class="form-control name" placeholder="Nome do Usuário">
                            </div>
                            <div class="form-group col-12">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control name" placeholder="Email da Conta">
                            </div>
                            <div class="form-group col-12">
                                <label for="cnpj_cpf">CNPJ/CPF</label>
                                <input type="text" name="cnpj_cpf" class="form-control" placeholder="CNPJ ou CPF">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postEditarCliente" data-save_route="{{route('atualizarCliente')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="excluirCliente">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postExcluirCliente">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Conta de(a) <span class="_name"></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>Tem certeza que gostaria de apagar essa conta?</h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-danger btn-salvar" data-trash="S" data-save_target="#postExcluirCliente" data-save_route="{{route('excluirCliente')}}"><i class="fas fa-trash"></i> Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="atualizarSenha">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postAtualizarSenha">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Atualizar senha de(a) <span class="_name"></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="password">Nova Senha</label>
                                <input type="password" name="password" class="form-control" placeholder="Nova Senha">
                            </div>
                            <div class="form-group col-12">
                                <label for="password_confirmation">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar Nova Senha">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-save_target="#postAtualizarSenha" data-save_route="{{route('atualizarSenhaCliente')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection