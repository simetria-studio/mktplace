@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dados do Pedido <b>#{{$order->order_number}}</b></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{asset('/admin/comercial/pedidos-servicos')}}">Pedidos</a></li>
                        <li class="breadcrumb-item">Dados do Pedido</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                {{-- dados do Pedido --}}
                <div class="col-12 col-md-6">
                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Informações do Pedido</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="row">
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Número do Pedido:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{$order->order_number}} <a href="{{route('impressaoPedidoServico', $order->order_number)}}" class="btn btn-info btn-sm ml-2" target="_blank">Imprimir</a></div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Método de Pagamento:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">{{$order->payment_method}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Valor do Serviço:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">R$ {{number_format($order->service_value, 2, ',', '.')}}</div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Status do Pedido:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom tr-order-id-{{$order->order_number}}">
                                    @if ($order->pay == 0)
                                        <button type="button" class="btn btn-sm btn-status-pay" style="background-color: #fdc300;">Aguardando Pagamento</button>
                                    @elseif ($order->pay == 1)
                                        <button type="button" class="btn btn-sm btn-status-pay" style="background-color: #58bc9a;">Em Andamento</button>
                                    @elseif ($order->pay == 2)
                                        <button type="button" class="btn btn-sm btn-status-pay" style="background-color: #c6d300; height: 52px;">Finalizado</button>
                                    @elseif ($order->pay == 3)
                                        <button type="button" class="btn btn-sm btn-status-pay" style="background-color: #db5812; height: 52px;">Cancelado</button>
                                    @elseif($order->pay == 10)
                                        <button type="button" class="btn btn-sm btn-status-pay" style="background-color: #db5812;">Solicitação de Cancelamento</button>
                                        <button type="button" class="btn btn-sm btn-info btn-verifica-cancelamento" data-toggle="modal" data-target="#verificarSolicitacaoCancelamento" data-order_number="{{$order->order_number}}" data-url="{{route('admin.verificarSolicitacaoCancelamentoServico')}}">Verificar</button>
                                    @elseif($order->pay == 11)
                                        <button type="button" class="btn btn-sm btn-status-pay" style="background-color: #db5812;">Cancelado Pelo Cliente</button>
                                    @endif
                                </div>
                                <div class="col-6 pt-3 px-2 border-bottom"><b>Alterar Status do Pedido:</b></div>
                                <div class="col-6 pt-3 px-2 text-right border-bottom">
                                    <button type="button" class="btn btn-info btn-sm alterarStatusOrder" data-url="{{route('admin.atualizar_status_order_service')}}" data-order_number="{{$order->order_number}}">Alterar Status</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Informações do Comprador</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="row">
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Nome:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->user_name}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>CPF:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->user_cnpj_cpf}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Email:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->user_email}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Telefones:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->user->adresses->last()->phone1}} / {{$order->user->adresses->last()->phone2}}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Informações do Vendedor</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="row">
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Nome:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->seller->name}}</div>
                                <div class="col-5 pt-3 px-2 border-bottom"><b>Email:</b></div>
                                <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->seller->email}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    @if ($order->note)
                        <div class="card card-primary card-outline">
                            {{-- Header do Card --}}
                            <div class="card-header">
                                <h3 class="card-title">Cliente Deixou uma Observação no Pedido</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            {{-- Corpo do Card --}}
                            <div class="card-body pad">
                                {{$order->note}}
                            </div>
                        </div>
                    @endif

                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Serviços Adquiridos</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="row">
                                <div class="col-12 pt-3 px-2">
                                    <b>
                                        {{$order->serviceReservation->service_name}} - #{{$order->serviceReservation->service_id}} - 
                                        @php
                                            $count = 0;
                                            $count_total = count($order->serviceReservation->attributes['selected_attribute'] ?? []);
                                        @endphp
                                        @isset($order->serviceReservation->attributes['selected_attribute'])
                                            @foreach ($order->serviceReservation->attributes['selected_attribute'] as $attribute)
                                                @php
                                                    $count++;
                                                @endphp
                                                {{App\Models\Attribute::find($attribute['parent_id'])->name}}:{{$attribute['name']}} 
                                                @if ($count != $count_total)
                                                    -
                                                @endif
                                            @endforeach
                                        @endisset
                                    </b>
                                </div>
                                <div class="col-12 pt-3 px-2">
                                    Data da Reserva {{date('d/m/Y', strtotime($order->serviceReservation->date_reservation_ini))}} {{$order->serviceReservation->date_reservation_fim ? date('d/m/Y', strtotime($order->serviceReservation->date_reservation_fim)) : ''}} {{$order->serviceReservation->hour_reservation ? $order->serviceReservation->hour_reservation : ''}}
                                </div>
                                <div class="col-12 px-2 text-right border-bottom">{{$order->serviceReservation->service_quantity}} x R$ {{number_format($order->serviceReservation->service_price, 2, ',', '.')}}</div>
                            </div>
                            <div class="row">
                                <div class="col-6 pt-5 px-2"><b>Total:</b></div>
                                <div class="col-6 pt-5 px-2 text-right">R$ {{number_format(($order->service_value), 2, ',', '.')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="verificarSolicitacaoCancelamento">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Solicitação de Cancelamento do Pedido</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.confirmarSolicitacaoCancelamentoServico')}}" method="post">
                    <input type="hidden" name="order_number">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 form-group">
                                <label for="">Motivo:</label>
                                <div class="_title"></div>
                            </div>
                            <div class="col-12 form-group">
                                <label for="">Descrição:</label>
                                <div class="_reason"></div>
                            </div>
                        </div>

                        <div class="row d-none">
                            <div class="form-group col-12">
                                <label for="bank_code_id">Banco</label>
                                <div class="_bank_code_id"></div>
                            </div>
                            <div class="form-group col-12">
                                <label for="agencia">Agência</label>
                                <div><span class="_agencia"></span> - <span class="_agencia_dv_id"></span></div>
                            </div>

                            <div class="form-group col-12">
                                <label for="conta_id">Conta</label>
                                <div><span class="_conta_id"></span> - <span class="_conta_dv_id"></span></div>
                            </div>

                            <div class="form-group col-12">
                                <label for="type">Tipo da conta</label>
                                <div class="_type"></div>
                            </div>

                            <div class="form-group col-12">
                                <label for="document_number_id">CPF/CNPJ atrelado à conta</label>
                                <div class="_document_number_id"></div>
                            </div>

                            <div class="form-group col-12">
                                <label for="legal_name_id">Nome/Razão social do dono da conta</label>
                                <div class="_legal_name_id"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-primary btn-confirma-cancelamento"><i class="fas fa-check"></i> Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection