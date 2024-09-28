@extends('layouts.painelSman')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Melhor Envio</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/vendedor')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Fretes</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @if (session()->get('success'))
                    <div class="col-12 text-center bg-success rounded py-3 my-2">
                        {{session()->get('success')}}
                    </div>
                @endif
                @if (session()->get('error'))
                    <div class="col-12 text-center bg-danger rounded py-3 my-2">
                        {{session()->get('error')}}
                    </div>
                @endif
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Pedidos Melhor Envio</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
                            <div class="container-fluid">
                                <div class="row mb-4">
                                    @if (!isset($apiME))
                                        <div class="col-12 col-md-3 text-center mb-2">
                                            <p><b>Você já possui conta no Melhor Envio?<br>Se não, clique em:</b></p>
                                            <a target="_blank" class="btn btn-success btn-sm" href="https://melhorenvio.com.br/cadastre-se"><i class="fas fa-cogs"></i> Criar Conta</a>
                                        </div>
                                    @endif
                                    <div class="col-12 col-md-3 text-center mb-2">
                                        <p><b>Caso você já tenha uma conta no Melhor Envio,<br>configure aqui:</b></p>
                                        <a target="_blank" class="btn btn-success btn-sm" href="{{$url_melhor_envio}}oauth/authorize?client_id={{ENV('CLIENT_ID')}}&redirect_uri={{asset('callback/melhor-envio')}}&response_type=code&scope=cart-read cart-write companies-read companies-write coupons-read coupons-write notifications-read orders-read products-read products-write purchases-read shipping-calculate shipping-cancel shipping-checkout shipping-companies shipping-generate shipping-preview shipping-print shipping-share shipping-tracking ecommerce-shipping transactions-read users-read users-write"><i class="fas fa-cogs"></i> Configurar Melhor Envio</a>
                                        {{-- <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#configurarME"><i class="fas fa-cogs"></i> Configurar Melhor Envio</button> --}}
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <p><b>OBS: A Biguaçu oferece a opção de frete para todo o Brasil, através do Melhor Envio. Com essa parceria os valores costumam ser menores do que a cotação direta com as empresas de logística, proporcionando uma ótima opção para o seu cliente!</b></p>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <div class="row mb-2">
                                    @if ($saldo)
                                        <div class="col-12 my-2"><b>Saldo: </b>R$ {{number_format($saldo->balance, 2, ',', '.')}}</div>
                                    @endif
                                </div>

                                <div class="container">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Numero do Pedido</th>
                                                    <th>Preço</th>
                                                    <th>Codigo</th>
                                                    <th>Pacotes</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($orderME as $order_ME)
                                                    <tr>
                                                        <td>{{$order_ME->order_number}}</td>
                                                        <td>R$ {{number_format($order_ME->price, 2, ',', '.')}}</td>
                                                        <td>{{$order_ME->code}}</td>
                                                        <td>
                                                            {{-- Altura: <b>{{$order_ME->height}} cm</b><br>
                                                            Largura: <b>{{$order_ME->width}} cm</b><br>
                                                            Comprimento: <b>{{$order_ME->length}} cm</b><br>
                                                            Peso: <b>{{$order_ME->weight}} kg</b><br> --}}
                                                            <div class="btn-group-vertical" role="group" aria-label="Button group with nested dropdown">
                                                                @if (isset($order_ME->package))
                                                                    @php
                                                                        $count_order_me = 0;
                                                                    @endphp
                                                                    @foreach ($order_ME->package as $package)
                                                                        @php
                                                                            $count_order_me++;
                                                                        @endphp
                                                                        <div class="btn-group" role="group">
                                                                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{$count_order_me}}ª Pacote</button>
                                                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                                                <a class="dropdown-item">Altura: <b>{{$package['dimensions']['height']}} cm</b></a>
                                                                                <a class="dropdown-item">Largura: <b>{{$package['dimensions']['width']}} cm</b></a>
                                                                                <a class="dropdown-item">Comprimento: <b>{{$package['dimensions']['length']}} cm</b></a>
                                                                                <a class="dropdown-item">Peso: <b>{{$package['weight']}} kg</b></a>
                                                                            </div>
                                                                        </div>  
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                @if ($order_ME->order_id)
                                                                    @if($order_ME->code)
                                                                        <a href="{{route('seller.melhor_envio.etiqueta.imp', $order_ME->id)}}" target="_blank" class="btn btn-primary">Imprimir Etiqueta</a>
                                                                    @else
                                                                        <a href="{{route('seller.melhor_envio.etiqueta', $order_ME->id)}}" target="_blank" class="btn btn-primary">Gerar Etiqueta</a>
                                                                    @endif
                                                                @else
                                                                    <button type="button" class="btn btn-success btn-ME-purchase" data-id="{{$order_ME->id}}" data-toggle="modal" data-target="#MEPurchase">Comprar</button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="configurarME">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('seller.melhor_envio')}}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$apiME->id ?? null}}">
                    <div class="modal-header">
                        <h4 class="modal-title">Configuração do Mercado Envio <div class="spinner-border d-none loadCep" role="status"><span class="sr-only">Loading...</span></div></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="status">Integração Ativa?</label>
                                <input type="checkbox" name="status" value="on" @isset($apiME->status){{$apiME->status == 'true' ? 'checked' : ''}}@endisset @empty($apiME->status) checked @endempty>
                            </div>
                            <div class="form-group col-12">
                                <label for="app_name">Nome da Aplicação</label>
                                <input type="text" name="app_name" class="form-control" placeholder="Nome da Aplicação" value="{{ $apiME->app_name ?? ''}}">
                            </div>
                            <div class="form-group col-12">
                                <label for="token">Token da Aplicação</label>
                                {{-- <input name="token" type="text" class="form-control" placeholder="Token da Aplicação" value="{{$apiME->token ?? 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjM5ZWExMDc5MGYzNDE0NDg2YjdlZWJkMDBmYjNmZTQ5Yjg3ZWE3M2E1YWU5NmYzN2M0MTkwYzM0Mzc4MzAxNmViMDkwMjBlZTU4ZGI0MDVjIn0.eyJhdWQiOiI5NTYiLCJqdGkiOiIzOWVhMTA3OTBmMzQxNDQ4NmI3ZWViZDAwZmIzZmU0OWI4N2VhNzNhNWFlOTZmMzdjNDE5MGMzNDM3ODMwMTZlYjA5MDIwZWU1OGRiNDA1YyIsImlhdCI6MTYzMjc4NDU3OSwibmJmIjoxNjMyNzg0NTc5LCJleHAiOjE2NjQzMjA1NzksInN1YiI6Ijg2NDEzZTE2LTM0ODctNDgxZC1iNDYzLWM2Y2U1ODQxZjM4ZiIsInNjb3BlcyI6WyJjYXJ0LXJlYWQiLCJjYXJ0LXdyaXRlIiwiY29tcGFuaWVzLXJlYWQiLCJjb21wYW5pZXMtd3JpdGUiLCJjb3Vwb25zLXJlYWQiLCJjb3Vwb25zLXdyaXRlIiwibm90aWZpY2F0aW9ucy1yZWFkIiwib3JkZXJzLXJlYWQiLCJwcm9kdWN0cy1yZWFkIiwicHJvZHVjdHMtZGVzdHJveSIsInByb2R1Y3RzLXdyaXRlIiwicHVyY2hhc2VzLXJlYWQiLCJzaGlwcGluZy1jYWxjdWxhdGUiLCJzaGlwcGluZy1jYW5jZWwiLCJzaGlwcGluZy1jaGVja291dCIsInNoaXBwaW5nLWNvbXBhbmllcyIsInNoaXBwaW5nLWdlbmVyYXRlIiwic2hpcHBpbmctcHJldmlldyIsInNoaXBwaW5nLXByaW50Iiwic2hpcHBpbmctc2hhcmUiLCJzaGlwcGluZy10cmFja2luZyIsImVjb21tZXJjZS1zaGlwcGluZyIsInRyYW5zYWN0aW9ucy1yZWFkIiwidXNlcnMtcmVhZCIsInVzZXJzLXdyaXRlIiwid2ViaG9va3MtcmVhZCIsIndlYmhvb2tzLXdyaXRlIl19.L1gzNiy8BwjUTdzcdoW59ZsMnK1JLM8wYo4ooJYdSGdEhcr-aSumzgqliEpJP5nfi3eJF29S34Cq-vLOV2h4VKYqOkS4xhVLRMiAsxHRO2j9h6vjJkzjuCoROf3mf-hurifhreodIIWJihI3f20TBPqFIEGp_svQtOX4hL-Ox-8BxyWW-MvWU8_Qtj23Qm61Le_qF_iNJCcZZ1HnVZV6eXJzis06b-WACYEAdmPjPgztH9E1wo_NHu3LCvdE8b4MklLCCvr2IGaHmSOrlw8wdp4XH7UkMJuhvkzRNIjmcKGFRRXBDyGkV-UXcHBYrN3kLIUJi5izzW2DwiwSSa0vUBVxaMGvEpFPCXvt3wvQ0l0HvO1cNuJqKpNMBcHGIEnsteBRFYEhc0eMy_CdoZEyCxA9XASQNAep1bjbeDdPETonB9KwDsxFA1xXOLsUF_fJElZAk8moS-1lrbYlpxwLnd-0hUjcTQrD384iIq_WZgG55iH2kYj1D8w8e-iUvc4B2VOo_7YjP0ChjWDVCyFmfgw5cj8azrh32439s1d2UixxsIgWI-VmNhhvbCNgtzYpv1vi3Sks970JefCgbC1YtB6jNT72YsY0fgQCHHA-JdsJXMKEqRMEJX4q-fF6BR33oMEo6iV7snoASigV8DmUAZyBNf6MZ5xszcHuc_ahdAY'}}"> --}}
                                <input name="token" type="text" class="form-control" placeholder="Token da Aplicação" value="{{$apiME->token ?? ''}}">
                            </div>
                            <div class="form-group col-12">
                                <label for="cnpj_cpf">CNPJ/CPF</label>
                                <input name="cnpj_cpf" type="text" class="form-control" value="{{$apiME->document ?? ''}}" placeholder="Numero do Documento">
                            </div>
                            <div class="form-group col-12">
                                <label for="state_register">Inscrição Estadual</label>
                                <input name="state_register" type="text" class="form-control" value="{{$apiME->state_register ?? ''}}" placeholder="Inscrição Estadual">
                            </div>
                            <div class="form-group col-12">
                                <label for="email">Email para Contato</label>
                                <input name="email" type="text" class="form-control" value="{{$apiME->email ?? ''}}" placeholder="Email para Contato">
                            </div>

                            <div class="form-group col-5 col-md-4">
                                <label for="post_code">CEP</label>
                                <input type="text" name="post_code" class="form-control" value="{{$apiME->zip_code ?? ''}}" placeholder="00000-000">
                            </div>
                            <div class="form-group col-7 col-md-8">
                                <label for="address">Endereço</label>
                                <input type="text" name="address" class="form-control" value="{{$apiME->address ?? ''}}" @isset($apiME->address) readonly @endisset placeholder="Endereço/Rua/Avenida">
                            </div>
                            <div class="form-group col-3">
                                <label for="number">Nº</label>
                                <input type="text" name="number" value="{{$apiME->number ?? ''}}" class="form-control" placeholder="0000">
                            </div>
                            <div class="form-group col-9">
                                <label for="complement">Complemento</label>
                                <input type="text" name="complement" value="{{$apiME->complement ?? ''}}" class="form-control" placeholder="Complemento">
                            </div>
                            <div class="form-group col-12">
                                <label for="address2">Bairro</label>
                                <input type="text" name="address2" value="{{$apiME->address2 ?? ''}}" @isset($apiME->address2) readonly @endisset class="form-control" placeholder="Bairro">
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="state">Estado</label>
                                <select name="state" class="form-control select2 state" @isset($apiME->country_id) readonly @endisset>
                                    <option value="">::Selecione uma Opção::</option>
                                    @isset ($apiME->country_id)
                                        <option value="{{$apiME->country_id}}" selected>{{$apiME->country_id}}</option>
                                    @endisset
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="city">Cidade</label>
                                <select name="city" class="form-control select2 city" @isset($apiME->city) readonly @endisset>
                                    <option value="">::Selecione uma Opção::</option>
                                    @isset ($apiME->city)
                                        <option value="{{$apiME->city}}" selected>{{$apiME->city}}</option>
                                    @endisset
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="phone">Telefone/Celular</label>
                                <input type="text" name="phone" value="{{$apiME->phone ?? ''}}" class="form-control" placeholder="(00) 0000-0000">
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

    <div class="modal fade" id="MEPurchase">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('seller.melhor_envio.purchase')}}" method="post">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Comprar Frete<br> <span class="_name_service"></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="nfe_key">Chave da Nota Fiscal (Opcional)</label>
                                <input type="text" name="nfe_key" class="form-control">
                            </div>
                            <div class="form-group col-12 agency">
                                <label for="agency_id">Agencia</label>
                                <select name="agency_id" class="form-control" id="agency"></select>
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
@endsection

@section('script')
    <script>
        // $(function(){
        //     if($('[name="post_code"]').val().length > 0){
        //         $('[name="post_code"]').trigger('keyup');
        //     }
        // });
    </script>
@endsection
