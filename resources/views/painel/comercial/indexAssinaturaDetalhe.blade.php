@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dados da Assinatura <b>#{{$assinatura->id}}</b></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{asset('/admin/comercial/assinaturas')}}">Assinaturas</a></li>
                        <li class="breadcrumb-item">Dados da Assinatura</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-6">
                    {{-- Info Assinatura --}}
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informações da Assinatura</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pad">
                            <div class="row">
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Número da Assinatura:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{$assinatura->id}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Intervalo de Pagamento:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{!!planCobranca($assinatura->select_interval)!!}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Duração da Assinatura:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{$assinatura->duration_plan}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Data da Assinatura:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{date('d/m/Y', strtotime($assinatura->created_at))}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Data da Expiração:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{date('d/m/Y', strtotime($assinatura->finish))}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Plano de Entrega:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom" style="text-transform: capitalize;">{{$assinatura->select_entrega}}</div>

                                <div class="col-6 pt-3 px-2 border-bottom"><b>Status da Assinatura:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">
                                    @switch($assinatura->status)
                                        @case('1')
                                            Ativa
                                            @break
                                        @case('2')
                                            Cancelada
                                            @break
                                        @case('3')
                                            Finalizada
                                            @break
                                        @case('4')
                                            Solicitação de Cancelamento
                                            @break
                                        @case('5')
                                            Cancelada pelo Cliente
                                            @break
                                        @default
                                    @endswitch
                                </div>

                                <div class="col-6 pt-3 px-2 border-bottom"><b>Valor do Plano:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">R$ {{number_format($assinatura->plan_value, 2, ',', '.')}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Valor da Entrega:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">
                                    R$ {{number_format($assinatura->shipping['price'], 2, ',', '.')}}
                                </div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Valor Total:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">
                                    R$ {{number_format( ($assinatura->plan_value + $assinatura->shipping['price']) , 2, ',', '.')}}</div>
                            </div>
                        </div>
                    </div>
                    {{-- Info Comprador --}}
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informações do Comprador</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pad">
                            <div class="row">
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Nome:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$assinatura->user->name}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>CPF:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$assinatura->user->cnpj_cpf}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Email:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$assinatura->user->email}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Telefone:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$assinatura->shipping['phone1']}} {{$assinatura->shipping['phone2']}}</div>
                            </div>
                        </div>
                    </div>
                    {{-- Info Vendedor --}}
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informações do Vendedor</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pad">
                            <div class="row mt-2 border rounded ">
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Nome:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$assinatura->seller->name}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Email:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$assinatura->seller->email}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Loja:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$assinatura->seller->store->store_name ?? 'Loja sem Nome'}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Contato:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$assinatura->seller->store->phone1 ?? $assinatura->seller->store->phone2}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    {{-- Infos Entrega --}}
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Informações da Entrega</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pad">
                            <div class="row">
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Endereço da Entrega:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{$assinatura->shipping['address']}}, {{$assinatura->shipping['number']}} {{$assinatura->shipping['complement']}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Bairro:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{$assinatura->shipping['address2']}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>UF/Cidade:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{$assinatura->shipping['state']}}/{{$assinatura->shipping['city']}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Método da Entrega:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{$assinatura->shipping['transport']}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Valor da Entrega:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">R$ {{number_format($assinatura->shipping['price'], 2, ',', '.')}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Produto Adquirido:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom"><a href="{{route('product', $assinatura->product['slug'])}}" title="{{$assinatura->product['title'] ?? $assinatura->product['nome']}}" target="_blank">{{$assinatura->product['nome']}}</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection