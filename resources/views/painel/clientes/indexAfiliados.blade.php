@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Afiliados do Sistema</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Afiliados</li>
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
                            <h3 class="card-title">Contas de Afiliados</h3>
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
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#novoCliente"><i class="fas fa-plus"></i> Novo Afiliado</button>
                                    </div>

                                    <div class="col-12">
                                        <form action="" method="get">
                                            <div class="row mt-2">
                                                <div class="col-12"><h4>Filtros para afiliados</h4></div>
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
                                                    <label for="">Afiliados por Página</label>
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
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($afiliados as $afiliado)
                                            <tr class="tr-id-{{$afiliado->id}}">
                                                <td>{{$afiliado->id}}</td>
                                                <td>{{$afiliado->users->name}}</td>
                                                <td>{{$afiliado->users->cnpj_cpf}}</td>
                                                <td>{{$afiliado->users->email}}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="">
                                                        @if($afiliado->status == 1)
                                                            <a href="{{route('pedidos.afiliados', $afiliado->user_id)}}" target="_blank" class="btn btn-info btn-sm"><i class="fas fa-eye"></i>Resumo de Pedidos</a>
                                                        @endif
                                                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarCliente" data-dados="{{json_encode($afiliado)}}"><i class="fas fa-edit"></i> @if($afiliado->status == 0) Aprovar Afiliado @else Editar Afiliado @endif</a>
                                                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirCliente" data-dados="{{json_encode($afiliado)}}"><i class="fas fa-trash"></i> Apagar Afiliado</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <th colspan="5">{{$afiliados->count()}} Afiliados</th>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="container mt-2">{{$afiliados->links()}}</div>
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
                        <h4 class="modal-title">Novo Afiliado</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="user_id">Usuário</label>
                                <select name="user_id" class="form-control select2">
                                    <option value="">::Selecione uma Opção::</option>
                                    @foreach ($users as $user)
                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="bank_code_id">Banco</label>
                                <select id="bank_code_id" name="bank_code" class="form-control select2">
                                    {!! collect(bancos())->map(function ($bancoNome, $bancoCode){ return "<option value='{$bancoCode}'>{$bancoCode} - {$bancoNome}</option>";})->join(' ') !!}
                                </select>
                            </div>
                            <div class="form-group col-8">
                                <label for="store_name">Agência</label>
                                <input name="agencia" class="form-control" value="">
                            </div>
                            <div class="form-group col-4">
                                <label for="agencia_dv_id">{{--Agência DV--}}Dígito Verificador</label>
                                <input id="agencia_dv_id" name="agencia_dv" class="form-control" value="">
                            </div>
                            <div class="form-group col-8">
                                <label for="conta_id">Conta</label>
                                <input id="conta_id" name="conta" class="form-control" value="">
                            </div>
                            <div class="form-group col-4">
                                <label for="conta_dv_id">{{--Conta DV--}}Dígito Verificador</label>
                                <input id="conta_dv_id" name="conta_dv" class="form-control" value="">
                            </div>
                            <div class="form-group col-12">
                                <label for="store_name">Tipo da conta</label>
                                <select name="type" class="form-control">
                                    <option value="conta_corrente">Conta corrente</option>
                                    <option value="conta_poupanca">Conta poupança</option>
                                    <option value="conta_corrente_conjunta">Conta corrente conjunta</option>
                                    <option value="conta_poupanca_conjunta">Conta poupança conjunta</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="document_number_id">CPF/CNPJ atrelado à conta</label>
                                <input id="document_number_id" name="document_number" class="form-control" value="">
                            </div>
                            <div class="form-group col-12">
                                <label for="legal_name_id">Nome/Razão social do dono da conta</label>
                                <input id="legal_name_id" name="legal_name" class="form-control max-caracteres" data-max_caracteres="28" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postNovoCliente" data-save_route="{{route('perfil.salvarAfiliado')}}"><i class="fas fa-save"></i> Salvar</button>
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
                    <input name="user_id" type="hidden">
                    <input name="status" type="hidden" value="1">
                    <div class="modal-header">
                        <h4 class="modal-title">Atualizar Cliente</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="bank_code_id">Banco</label>
                                <select id="bank_code_id" name="bank_code" class="form-control select2 bank">
                                    {!! collect(bancos())->map(function ($bancoNome, $bancoCode){ return "<option value='{$bancoCode}'>{$bancoCode} - {$bancoNome}</option>";})->join(' ') !!}
                                </select>
                            </div>
                            <div class="form-group col-8">
                                <label for="agencia">Agência</label>
                                <input name="agencia" class="form-control branch_number" value="">
                            </div>
                            <div class="form-group col-4">
                                <label for="agencia_dv_id">{{--Agência DV--}}Dígito Verificador</label>
                                <input id="agencia_dv_id" name="agencia_dv" class="form-control branch_check_digit" value="">
                            </div>
                            <div class="form-group col-8">
                                <label for="conta_id">Conta</label>
                                <input id="conta_id" name="conta" class="form-control account_number" value="">
                            </div>
                            <div class="form-group col-4">
                                <label for="conta_dv_id">{{--Conta DV--}}Dígito Verificador</label>
                                <input id="conta_dv_id" name="conta_dv" class="form-control account_check_digit" value="">
                            </div>
                            <div class="form-group col-12">
                                <label for="type">Tipo da conta</label>
                                <select name="type" class="form-control">
                                    <option value="conta_corrente">Conta corrente</option>
                                    <option value="conta_poupanca">Conta poupança</option>
                                    <option value="conta_corrente_conjunta">Conta corrente conjunta</option>
                                    <option value="conta_poupanca_conjunta">Conta poupança conjunta</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label for="document_number_id">CPF/CNPJ atrelado à conta</label>
                                <input id="document_number_id" name="document_number" class="form-control holder_document" value="">
                            </div>
                            <div class="form-group col-12">
                                <label for="legal_name_id">Nome/Razão social do dono da conta</label>
                                <input id="legal_name_id" name="legal_name" class="form-control max-caracteres holder_name" data-max_caracteres="28" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-refresh="S" data-update_table="S" data-save_target="#postEditarCliente" data-save_route="{{route('admin.salvarAfiliado')}}"><i class="fas fa-save"></i> Salvar</button>
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
                        <h4 class="modal-title">Afiliado(a) <span class="_name"></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>Tem certeza que gostaria de apagar esse afiliado?</h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-danger btn-salvar" data-refresh="S" data-trash="S" data-save_target="#postExcluirCliente" data-save_route="{{route('admin.excluirAfiliado')}}"><i class="fas fa-trash"></i> Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection