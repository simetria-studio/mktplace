@extends('layouts.site')

@section('container')
    <div class="container-fluid" style="background-color: #f1f0f0;">
        <div class="container py-3">
            {{-- @if(session()->has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{{seesion()->get('success')}}</li>
                    </ul>
                </div>
            @endif --}}

            <div class="row">
                <div class="col-12">
                    <nav class="nav nav-pills flex-column flex-sm-row my-5" id="pills-tab" role="tablist">
                        <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mr-sm-1 @if(!isset($_GET['produtos']) && !isset($_GET['servicos'])) active @endif" id="pills-perfil-tab" data-toggle="pill" href="#pills-perfil" role="tab" aria-controls="pills-perfil" aria-selected="true">Perfil</a>
                        <div class="dropdown flex-sm-fill text-sm-center mb-2 mb-sm-0 mx-sm-1">
                            <a class="dropdown-toggle flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mx-sm-1" id="pills-pedido-drop-tab" data-toggle="dropdown" href="#" role="button" aria-expanded="false">Pedidos</a>
                            <div class="dropdown-menu" id="pedido-dm">
                                <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mx-sm-1 @isset($_GET['produtos']) active @endif" id="pills-compras-tab" data-toggle="pill" href="#pills-compras" role="tab" aria-controls="pills-compras" aria-selected="true">Compras</a>
                                {{-- <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mx-sm-1 @isset($_GET['servicos']) active @endif" id="pills-servicos-tab" data-toggle="pill" href="#pills-servicos" role="tab" aria-controls="pills-servicos" aria-selected="true">Serviços Adquiridos</a> --}}
                            </div>
                        </div>
                        <div class="dropdown flex-sm-fill text-sm-center mb-2 mb-sm-0 mx-sm-1">
                            <a class="dropdown-toggle flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mx-sm-1" id="pills-rate-drop-tab" data-toggle="dropdown" href="#" role="button" aria-expanded="false">Avaliação</a>
                            <div class="dropdown-menu" id="rate-dm">
                                <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 ml-sm-1" id="pills-rate-tab" href="{{route('perfil.rateProduct')}}">Avaliar Produtos Comprados</a>
                                {{-- <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 ml-sm-1" id="pills-rate-tab" href="{{route('perfil.rateService')}}">Avaliar Serviços Adquiridos</a> --}}
                            </div>
                        </div>
                        {{-- <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 ml-sm-1" id="pills-assinatura-tab" href="{{route('perfil.assinatura')}}">Assinatura</a> --}}
                        {{-- <div class="dropdown flex-sm-fill text-sm-center mb-2 mb-sm-0 mx-sm-1">
                            <a class="dropdown-toggle flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mx-sm-1" id="pills-afiliado-drop-tab" data-toggle="dropdown" href="#" role="button" aria-expanded="false">Afiliado</a>
                            <div class="dropdown-menu" id="afiliado-dm">
                                <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mx-sm-1" id="pills-conta-tab" data-toggle="pill" href="#pills-conta" role="tab" aria-controls="pills-conta" aria-selected="true">Conta Bancária</a>
                                <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mx-sm-1" id="pills-ps-afiliado-tab" data-toggle="pill" href="#pills-ps-afiliado" role="tab" aria-controls="pills-ps-afiliado" aria-selected="true">Produtos/Serviços Afiliados</a>
                                <a class="flex-sm-fill text-sm-center nav-link mb-2 mb-sm-0 mx-sm-1 @isset($_GET['resumo_pedidos_aff']) active @endif" id="pills-ps-afiliado-pedido-tab" data-toggle="pill" href="#pills-ps-afiliado-pedido" role="tab" aria-controls="pills-ps-afiliado-pedido" aria-selected="true">Resumo de Pedidos</a>
                            </div>
                        </div> --}}
                    </nav>
                </div>
            </div>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade @if(!isset($_GET['produtos']) && !isset($_GET['servicos'])) show active @endif" id="pills-perfil" role="tabpanel" aria-labelledby="pills-perfil-tab">
                    <div class="row my-5 justify-content-center">
                        <div class="col-12">
                            @if (session()->has('success'))
                                <div class="alert alert-success text-center">
                                    {{session()->get('success')}}
                                </div>
                            @endif

                            @if (session()->has('destroy'))
                                <div class="alert alert-danger text-center">
                                    {{session()->get('destroy')}}
                                </div>
                            @endif
                        </div>

                        <div class="col-12 col-md-8 col-lg-4">
                            <h3 class="mb-3">Dados Pessoais</h3>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Nome: {{auth()->user()->name}}</li>
                                <li class="list-group-item">Email: {{auth()->user()->email}}</li>
                                <li class="list-group-item">CNPJ/CPF: {{auth()->user()->cnpj_cpf}}</li>
                                <li class="list-group-item">Data de Nascimento: {{date("d/m/Y", strtotime(str_replace('-','/', auth()->user()->birth_date)))}}</li>
                                <li class="list-group-item">
                                    <a href="#" class="btn btn-block btn-c-primary" data-toggle="modal" data-target="#alterarDados"><i class="fa fa-user-cog"></i> Alterar Dados</a>
                                    <a href="#" class="btn btn-block btn-c-danger" data-toggle="modal" data-target="#alterarSenha"><i class="fa fa-user-lock"></i> Alterar Senha</a>
                                </li>
                                <li class="list-group-item">
                                    <div class="row justify-content-center">
                                        <div class="col-12 text-center"><h4>APAGAR CONTA</h4></div>
                                        <div class="col-12 my-1">Ao apagar sua conta você perderá acesso ao sistema e todos seus dados no sistema serão apagados.</div>
                                        <div class="col-12 text-center"><button type="button" class="btn btn-block btn-c-primary btn-env-code-delete">Enviar Código</button></div>
                                        <div class="col-12 d-none info-env-code text-center mb-1"><p>Código enviado ao seu email.</p></div>
                                        <div class="col-12">
                                            <label for="">Informe o código enviado ao seu email.</label>
                                            <input type="text" name="code_delete" class="form-control">
                                        </div>
                                        <div class="col-12 mt-2 text-center d-none"><button type="button" class="btn btn-block btn-c-primary btn-submit-code-delete">Apagar Conta</button></div>
                                    </div>
                                </li>
                                @if(!empty($newsletter))
                                    <li class="list-group-item">
                                        <div class="row justify-content-center">
                                            <div class="col-12 text-center"><h4>CANCELAR NEWSLETTER</h4></div>
                                            <div class="col-12 my-1">Ao cancelar a inscrição da newsletter, você não receberá mais e-mails com novidades e promoções.</div>
                                            <div class="col-12 mt-2 text-center"><a href="{{route('cancelNewsletter', 't='.base64_encode($newsletter->token))}}" class="btn btn-block btn-c-primary btn-cancel-newsletter" data-token="{{base64_encode($newsletter->token)}}">Cancelar Inscrição</a></div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
    
                        @foreach ($addresses as $address)
                            <div class="col-12 col-md-8 col-lg-4">
                                <h3 class="mb-3">Endereço</h3>
    
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Cep: {{$address->post_code}}</li>
                                    <li class="list-group-item">Cidade: {{$address->city}} - UF: {{$address->state}}</li>
                                    <li class="list-group-item">Bairro: {{$address->address2}}</li>
                                    <li class="list-group-item">{{$address->address}} - Nº {{$address->number}}</li>
                                    <li class="list-group-item">Complemento: {{$address->complement}}</li>
                                    <li class="list-group-item">Telefone: {{$address->phone1}}</li>
                                    <li class="list-group-item">Celular: {{$address->phone2}}</li>
                                    <li class="list-group-item">
                                        <a href="#" class="btn btn-block btn-c-primary" data-toggle="modal" data-target="#enderecos" data-dados="{{json_encode($address)}}"><i class="fa fa-user-cog"></i> Alterar Endereço</a>
                                        <a href="#" class="btn btn-block btn-c-danger btn-excluir-address" data-id="{{$address->id}}" data-url="{{asset('apagarEndereco')}}"><i class="fa fa-user-lock"></i> Apagar Endereço</a>
                                    </li>
                                </ul>
                            </div>
                        @endforeach
    
                        @if ($addresses->count() <= 1)
                            <div class="col-12 col-md-8 col-lg-4"><a href="#" class="btn btn-block btn-primary" data-toggle="modal" data-target="#enderecos"><i class="fa fa-map-marker-alt"></i> Adicionar Novo Endereço</a></div>
                        @endif
                    </div>
                </div>
                <div class="tab-pane fade @isset($_GET['produtos']) show active @endif" id="pills-compras" role="tabpanel" aria-labelledby="pills-compras-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Número do Pedido</th>
                                    <th>Data do Pedido</th>
                                    <th>Valor Total</th>
                                    <th>Método de Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td>{{$order->order_number}}</td>
                                        <td>{{date('d/m/Y', strtotime($order->created_at))}}</td>
                                        <td>R$ {{number_format($order->total_value-$order->discount, 2 , ',', '.')}}</td>
                                        <td>{!! __($order->payment_method) !!}</td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="">
                                                <a href="{{route('perfil.pedido', $order->order_number)}}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Visualizar Pedido</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{$orders->links()}}
                </div>
                {{-- <div class="tab-pane fade @isset($_GET['servicos']) show active @endif" id="pills-servicos" role="tabpanel" aria-labelledby="pills-servicos-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Número do Pedido</th>
                                    <th>Data do Pedido</th>
                                    <th>Valor do Serviço</th>
                                    <th>Método de Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orderServices as $order)
                                    <tr>
                                        <td>{{$order->order_number}}</td>
                                        <td>{{date('d/m/Y', strtotime($order->created_at))}}</td>
                                        <td>R$ {{number_format($order->service_value, 2 , ',', '.')}}</td>
                                        <td>{!! __($order->payment_method) !!}</td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="">
                                                <a href="{{route('perfil.servico.pedido', $order->order_number)}}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Visualizar Pedido</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{$orderServices->links()}}
                </div> --}}
                {{-- <div class="tab-pane fade" id="pills-afiliado" role="tabpanel" aria-labelledby="pills-afiliado-tab">
                    <div class="row my-3 justify-content-center">
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Dados da conta bancária para recebimento</h3>
                                </div>
                                <div class="card-body pad table-responsive">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                        data-target="#atualizarAfiliado"><i
                                                        class="nav-icon fas fa-money-bill-wave"></i> Conta bancária
                                                </button>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="container mt-5">
                                    </div>
        
                                    <div class="mt-3">
                                        Saques Para contas Bradesco São Gratuitos.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="tab-pane fade" id="pills-conta" role="tabpanel" aria-labelledby="pills-conta-tab">
                    <div class="row my-3 justify-content-center">
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Dados da conta bancária para recebimento</h3>
                                </div>
                                <div class="card-body pad table-responsive">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                        data-target="#atualizarAfiliado"><i
                                                        class="nav-icon fas fa-money-bill-wave"></i> Conta bancária
                                                </button>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="container mt-5">
                                    </div>
        
                                    <div class="mt-3">
                                        Saques Para contas Bradesco São Gratuitos.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="tab-pane fade" id="pills-ps-afiliado" role="tabpanel" aria-labelledby="pills-ps-afiliado-tab">
                    <div class="row my-3 justify-content-center">
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Produtos e Serviços para Afiliados</h3>
                                </div>
                                <div class="card-body pad table-responsive">
                                    <div class="container mt-5">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nome</th>
                                                        <th>Tipo</th>
                                                        <th>Comissão</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($affiliate->id))
                                                        @forelse ($psAfiliados as $psAfiliado)
                                                            <tr>
                                                                <td>{{$psAfiliado->name}}</td>
                                                                <td>{{$psAfiliado->reference_type == 'product' ? 'Produto' : 'Serviço'}}</td>
                                                                <td>{{$psAfiliado->price_type == 'percentage' ? '%' : 'R$'}} {{number_format($psAfiliado->price, 2 , ',', '.')}}</td>
                                                                <td>
                                                                    <div class="btn-group" role="group" aria-label="">
                                                                        @php
                                                                            $affiliateps = collect($psAfiliado->affiliatePs)->filter(function($query) {
                                                                                return auth()->guard('web')->user()->id == $query->user->user_id;
                                                                            })->first();
                                                                        @endphp
                                                                        @if (isset($affiliateps))
                                                                            <a href="{{$affiliateps->url}}" class="btn btn-success btn-sm btn-copiar-link" data-url="{{route('perfil.salvarLinkAfiliado')}}" data-reference_id="{{$psAfiliado->reference_id}}" data-affiliate_id="{{$affiliate->id}}" data-affiliate_item={{$psAfiliado->id}}><i class="fas fa-at"></i> Copiar Link</a>
                                                                        @else
                                                                            <a href="#" class="btn btn-info btn-sm btn-gerar-link" data-url="{{route('perfil.salvarLinkAfiliado')}}" data-reference_id="{{$psAfiliado->reference_id}}" data-affiliate_id="{{$affiliate->id}}" data-affiliate_item={{$psAfiliado->id}}><i class="fas fa-at"></i> Gerar Link</a>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                        @endforelse
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                {{-- <div class="tab-pane fade @isset($_GET['resumo_pedidos_aff']) show active @endif" id="pills-ps-afiliado-pedido" role="tabpanel" aria-labelledby="pills-ps-afiliado-pedido-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Número do Pedido</th>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Valor da Comissão</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($psAfiliadosPedidos as $order_aff)
                                    <tr>
                                        <td>{{$order_aff->order_number}}</td>
                                        <td>{{$order_aff->created_at}}</td>
                                    @if ($order_aff->type_reference == 'product')
                                            <td>{{$order_aff->orderP->user_name}}</td>
                                            <td>{{$order_aff->product->nome}}</td>
                                            <td>{{$order_aff->qty}}</td>
                                    @else
                                            <td>{{$order_aff->orderS->user_name}}</td>
                                            <td>{{$order_aff->service->service_title}}</td>
                                            <td>Q{{$order_aff->qty}}</td>
                                    @endif
                                        <td>R$ {{number_format($order_aff->value, 2 , ',', '.')}}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{$psAfiliadosPedidos->links()}}
                </div> --}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="alterarDados">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{asset('perfilSave')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Editar Dados</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="name">Nome Completo</label>
                                <input type="text" name="name" class="form-control" value="{{auth()->user()->name}}" placeholder="Nome do Usuário">
                            </div>
                            <div class="form-group col-12">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" value="{{auth()->user()->email}}" placeholder="Email da Conta" disabled>
                            </div>
                            <div class="form-group col-12">
                                <label for="cnpj_cpf">CNPJ/CPF</label>
                                <input type="text" name="cnpj_cpf" class="form-control" value="{{auth()->user()->cnpj_cpf}}" placeholder="CNPJ ou CPF">
                            </div>
                            <div class="form-group col-12">
                                <label for="birth_date">Data de Nascimento</label>
                                <input type="text" name="birth_date" class="form-control" value="{{date("d/m/Y", strtotime(str_replace('-','/', auth()->user()->birth_date)))}}" placeholder="Data de Nascimento">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade show" id="alterarSenha">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{asset('senhaSave')}}" action="#" method="post">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Alterar senha</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="current_password">Senha Antiga</label>
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Senha Antiga">
                                @error('current_password')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label for="password">Nova Senha</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nova Senha">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-12">
                                <label for="password_confirmation">Confirma Nova Senha</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar Nova Senha">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="enderecos">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{asset('enderecoSave')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Endereço <div class="spinner-border d-none loadCep" role="status"><span class="sr-only">Loading...</span></div></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-5 col-md-4">
                                <label for="post_code">CEP:</label>
                                <input type="text" class="form-control @error('post_code') is-invalid @enderror" name="post_code" placeholder="00000-000">

                                @error('post_code')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-7 col-md-8">
                                <label for="address">Endereço:</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Endereço/Rua/Avenida" >

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-3">
                                <label for="number">Nª:</label>
                                <input type="text" class="form-control @error('number') is-invalid @enderror" name="number" placeholder="0000">

                                @error('number')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-9">
                                <label for="complement">Complemento:</label>
                                <input type="text" class="form-control" name="complement" placeholder="Complemento">
                            </div>

                            <div class="form-group col-12">
                                <label for="address2">Bairro:</label>
                                <input type="text" class="form-control @error('address2') is-invalid @enderror" name="address2" placeholder="Bairro">

                                @error('address2')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="state">Estado</label>
                                <select name="state" class="form-control select2 state @error('state') is-invalid @enderror"">
                                    <option value="">::Selecione uma Opção::</option>
                                </select>

                                @error('state')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="city">Cidade</label>
                                <select name="city" class="form-control select2 city @error('city') is-invalid @enderror">
                                    <option value="">::Selecione uma Opção::</option>
                                </select>

                                @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12">
                                <label for="phone1">Telefone:</label>
                                <input type="text" class="form-control" name="phone1" placeholder="Telefone">
                            </div>
                            <div class="form-group col-12">
                                <label for="phone2">Celular:</label>
                                <input type="text" class="form-control @error('phone2') is-invalid @enderror" name="phone2" placeholder="Celular">

                                @error('phone2')
                                    <span class="invalid-feedback" role="alert">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="atualizarAfiliado">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="cadastroContaBancaria">
                    @csrf
                    <input type="hidden" name="id" value="{{$affiliate->id ?? ''}}">
                    <input type="hidden" name="user_id" value="{{auth()->user()->id ?? null}}">
                    <div class="modal-header">
                        <h4 class="modal-title">Dados da conta bancária para recebimento
                            <div class="spinner-border d-none loadCep" role="status"><span
                                    class="sr-only">Loading...</span></div>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="bank_code_id">Banco</label>
                                <select id="bank_code_id" name="bank_code" class="form-control bank select2">
                                    {!! collect(bancos())->map(function ($bancoNome, $bancoCode){ return "<option ".(($affiliate->bank??0) == $bancoCode ?'selected':'')." value='{$bancoCode}'>{$bancoCode} - {$bancoNome}</option>";})->join(' ') !!}
                                </select>
                            </div>
                            <div class="form-group col-8">
                                <label for="store_name">Agência</label>
                                <input name="agencia" class="form-control" value="{{$affiliate->branch_number??''}}">
                            </div>
    
                            <div class="form-group col-4">
                                <label for="agencia_dv_id">{{--Agência DV--}}Dígito Verificador</label>
                                <input id="agencia_dv_id" name="agencia_dv" class="form-control" value="{{$affiliate->branch_check_digit??''}}">
                            </div>
    
                            <div class="form-group col-8">
                                <label for="conta_id">Conta</label>
                                <input id="conta_id" name="conta" class="form-control" value="{{$affiliate->account_number??''}}">
                            </div>
                            <div class="form-group col-4">
                                <label for="conta_dv_id">{{--Conta DV--}}Dígito Verificador</label>
                                <input id="conta_dv_id" name="conta_dv" class="form-control" value="{{$affiliate->account_check_digit??''}}">
                            </div>
    
                            <div class="form-group col-12">
                                <label for="store_name">Tipo da conta</label>
                                <select name="type" class="form-control">
                                    <option
                                        {{($affiliate->type??'') == 'checking' && ($affiliate->type??'') == 'individual'?'selected':''}} value="conta_corrente">
                                        Conta corrente
                                    </option>
                                    <option
                                        {{($affiliate->type??'') == 'savings' && ($affiliate->type??'') == 'individual'?'selected':''}} value="conta_poupanca">
                                        Conta poupança
                                    </option>
                                    <option
                                        {{($affiliate->type??'') == 'checking' && ($affiliate->type??'') == 'company'?'selected':''}} value="conta_corrente_conjunta">
                                        Conta corrente conjunta
                                    </option>
                                    <option
                                        {{($affiliate->type??'') == 'savings' && ($affiliate->type??'') == 'company'?'selected':''}} value="conta_poupanca_conjunta">
                                        Conta poupanca conjunta
                                    </option>
                                </select>
                            </div>
    
                            <div class="form-group col-12">
                                <label for="document_number_id">CPF/CNPJ atrelado à conta</label>
                                <input id="document_number_id" name="document_number" class="form-control"
                                        value="{{$affiliate->holder_document??''}}">
                            </div>
    
                            <div class="form-group col-12">
                                <label for="legal_name_id">Nome/Razão social do dono da conta</label>
                                <input id="legal_name_id" name="legal_name" class="form-control max-caracteres" data-max_caracteres="28" value="{{$affiliate->holder_name??''}}">
                            </div>
    
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                                class="fas fa-times"></i> Fechar
                        </button>
                        <button type="button" class="btn btn-success btn-salvar"
                                data-refresh="S"
                                data-save_target="#cadastroContaBancaria"
                                data-save_route="{{route('perfil.salvarAfiliado')}}"
                        ><i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            if($('#alterarSenha').find('.is-invalid').length > 0) $('#alterarSenha').modal('show');
            if($('#enderecos').find('.is-invalid').length > 0) $('#enderecos').modal('show');

            $(document).on('click', '[data-toggle="pill"]', function(){
                $('[data-toggle="pill"]').removeClass('active');
            });

            if("{{session()->has('success')}}"){
                Toast.fire({
                    icon: 'success',
                    title: "{{session()->get('success')}}"
                });
            }
        });
    </script>
@endsection
