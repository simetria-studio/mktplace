@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Vendedores do Sistema</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Vendedores</li>
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
                            <h3 class="card-title">Contas de Vendedores</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#novoVendedor"><i class="fas fa-plus"></i> Novo Vendedor</button>
                                    </div>

                                    <div class="col-12">
                                        <form action="" method="get">
                                            <div class="row mt-2">
                                                <div class="col-12 col-md-5 form-group">
                                                    <label for="">Filtros para vendedores</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control search-filter" name="search_value" placeholder="Pesquisar por..." value="{{$request->search_value ?? ''}}">
                                                        <select name="column_name" class="form-control">
                                                            <option value="name" @if(($request->column_name ?? 'null') == 'name') selected @endif>Nome do Vendedor</option>
                                                            <option value="cnpj_cpf" @if(($request->column_name ?? 'null') == 'cnpj_cpf') selected @endif>CNPJ/CPF</option>
                                                            <option value="email" @if(($request->column_name ?? 'null') == 'email') selected @endif>Email</option>
                                                        </select>
                                                    </div>
                                                </div>
    
                                                <div class="col-12 col-md-2 form-group d-flex">
                                                    <div class="mt-auto w-75">
                                                        <button type="submit" class="btn btn-primary btn-sm btn-block">Buscar</button>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-4 form-group">
                                                    <label for="">Vendedores por Página</label>
                                                    <select name="per_page" class="form-control form-control-sm">
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

                            <div class="container-fluid mt-2 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nº</th>
                                            <th>Nome</th>
                                            <th>CNPJ/CPF</th>
                                            <th>Email</th>
                                            <th>Reponsável</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @each('components.tableVendedores', $accounts, 'account')
                                    </tbody>
                                </table>
                            </div>

                            <div class="container mt-2">{{$accounts->links()}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="novoVendedor">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postNovoVendedor">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Novo Vendedor</h4>
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
                                <label for="phone">Telefone/Celular</label>
                                <input type="text" name="phone" class="form-control" placeholder="Telefone ou Celular">
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
                        <button type="button" class="btn btn-success btn-salvar" data-refresh="S" data-save_target="#postNovoVendedor" data-save_route="{{route('novoVendedor')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarVendedor">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postEditarVendedor">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Atualizar Vendedor</h4>
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
                            <div class="form-group col-12">
                                <label for="phone">Telefone/Celular</label>
                                <input type="text" name="phone" class="form-control" placeholder="Telefone ou Celular">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-refresh="S" data-save_target="#postEditarVendedor" data-save_route="{{route('atualizarVendedor')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="excluirVendedor">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postExcluirVendedor">
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
                        <button type="button" class="btn btn-danger btn-salvar" data-refres="S" data-save_target="#postExcluirVendedor" data-save_route="{{route('excluirVendedor')}}"><i class="fas fa-trash"></i> Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="atualizarSenhaVendedor">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postAtualizarSenhaVendedor">
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
                        <button type="button" class="btn btn-success btn-salvar" data-save_target="#postAtualizarSenhaVendedor" data-save_route="{{route('atualizarSenhaVendedor')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection