@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pedidos em Andamento</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Pedidos</li>
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
                            <h3 class="card-title">Pedidos</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="container my-2">
                                <div class="row">
                                    <form action="" method="GET">
                                        <div class="form-row">
                                            <div class="form-group col-12 col-sm-3">
                                                <label for="">Período Inicial</label>
                                                <input type="text" name="start" class="form-control form-control-sm date-mask-single" value="{{isset($request->start) ? $request->start : date('d/m/Y', strtotime(date('Y').'-'.date('m').'-01'))}}">
                                            </div>
                                            <div class="form-group col-12 col-sm-3">
                                                <label for="">Período Final</label>
                                                <input type="text" name="end" class="form-control form-control-sm date-mask-single" value="{{isset($request->end) ? $request->end : date('d/m/Y')}}">
                                            </div>
                                            <div class="form-group col-12 col-sm-3">
                                                <label for="">Status</label>
                                                <select class="selectpicker" name="status[]" multiple title="Selecione um ou +">
                                                    <option value="0">Aguardando Pagamento</option>
                                                    <option value="1">Em Andamento</option>
                                                    <option value="3">Cancelado</option>
                                                    <option value="11">Cancelado pelo Cliente</option>
                                                    <option value="2">Finalizado</option>
                                                    <option value="10">Solicitação de Cancelamento</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <button type="submit" class="btn btn-primary">Filtrar</button>
                                            <a href="{{asset('admin/comercial/pedidos-servicos')}}" class="btn btn-secondary ml-2">Limpar</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="container mt-4">
                                <table class="table table-hover" id="table_pedido">
                                    <thead>
                                        <tr>
                                            <th>Número do Pedido</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                            <th>Status do Pedido</th>
                                            <th>Nome do Vendedor</th>
                                            <th>Nome do Comprador</th>
                                            <th>Valor do Serviço</th>
                                            <th>Metodo de Pagamento</th>
                                            <th>Nota Fiscal</th>
                                            <th>Status de Visualização</th>
                                            <th>Impressão</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($orders as $order)
                                            <tr class="tr-order-id-{{$order->order_number}}">
                                                <td>{{$order->order_number}}</td>
                                                <td>{{date('d/m/Y', strtotime(str_replace('-','/', $order->created_at)))}}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="">
                                                        <a href="{{route('ver_pedido.servico', $order->order_number)}}" class="btn btn-primary btn-sm">Visualizar Pedido</a>
                                                        <button type="button" class="btn btn-info btn-sm alterarStatusOrder" data-url="{{route('admin.atualizar_status_order_service')}}" data-order_number="{{$order->order_number}}">Alterar Status</button>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($order->pay == 0)
                                                        <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #fdc300;">Aguardando Pagamento</button>
                                                    @elseif ($order->pay == 1)
                                                        <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #58bc9a;">Em Andamento</button>
                                                    @elseif ($order->pay == 2)
                                                        <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #c6d300; height: 52px;">Finalizado</button>
                                                    @elseif ($order->pay == 3)
                                                        <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #db5812; height: 52px;">Cancelado</button>
                                                    @elseif($order->pay == 10)
                                                        <div class="btn-group" role="group" aria-label="">
                                                            <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #db5812; height: 52px;">Solicitação de Cancelamento</button>
                                                            <button type="button" class="btn btn-sm btn-info btn-verifica-cancelamento" data-toggle="modal" data-target="#verificarSolicitacaoCancelamento" data-order_number="{{$order->order_number}}" data-url="{{route('admin.verificarSolicitacaoCancelamentoServico')}}">Verificar</button>
                                                        </div>
                                                    @elseif($order->pay == 11)
                                                        <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #db5812;">Cancelado Pelo Cliente</button>
                                                    @endif
                                                </td>
                                                <td>{{$order->seller->name}}</td>
                                                <td>{{$order->user_name}}</td>
                                                <td>R$ {{number_format($order->service_value, 2, ',', '.')}}</td>
                                                <td>{{$order->payment_method}}</td>
                                                <td>
                                                    @if ($order->path_fiscal)
                                                        <div class="btn-group">
                                                            <a href="{{$order->url_fiscal}}" class="btn btn-info btn-sm" target="_blank">Anexo</a>
                                                            <button type="button" class="btn btn-danger btn-remove-file" data-id="{{$order->id}}"><i class="fas fa-times"></i></button>
                                                        </div>
                                                    @else
                                                        <button type="button" class="btn btn-info btn-sm btn-anexar">Anexar</button>
                                                        <input type="file" class="d-none path-anexo" data-id={{$order->id}}>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($order->status_v == 0)
                                                        @php
                                                            $since_start = getTimeDiff(date('Y-m-d H:i:s'), $order->created_at);
                                                            $minutes = $since_start->days * 24 * 60;
                                                            $minutes += $since_start->h * 3600;
                                                            $minutes += $since_start->i * 60;
                                                            $minutes += $since_start->s;
                                                        @endphp
                                                        {{$since_start->days}} dia(s) <br> horas: {{$since_start->h}}:{{$since_start->i}}
                                                        <br>
                                                        <button type="button" class="btn btn-info btn-sm notificarVendedor" data-url="{{route('admin.notificarVendedor.pedido-servico')}}" data-order_number="{{$order->order_number}}">Notificar</button>
                                                    @else
                                                        Visualizado
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('impressaoPedidoServico', $order->order_number)}}" class="btn btn-primary btn-sm" target="_blank">Imprimir Pedido</a>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="row table-options mt-3"><div class="col-md-6"></div></div>
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
                                <div class="_reason"></div>
                            </div>
                        </div>

                        <div class="row d-none boleto">
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

@section('scripts')
    <script>
        $(document).ready(function(){
            $(document).on('click', '.btn-anexar', function(){$(this).parent().find('.path-anexo').trigger('click');});
            $(document).on('change', '.path-anexo', function(){
                var td = $(this).parent();
                $(this).parent().find('.btn-anexar').html(`<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>`);
                var form_data = new FormData();
                form_data.append('order_id', $(this).data('id'));
                form_data.append('path_fiscal', $(this).prop('files')[0]);
                $.ajax({
                    url: `{{route('admin.orderServiceAnexarFiscal')}}`,
                    type: 'POST',
                    data: form_data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data)=>{
                        td.html(`
                            <div class="btn-group">
                                <a href="${data}" class="btn btn-info btn-sm" target="_blank">Anexo</a>
                                <button type="button" class="btn btn-danger btn-remove-file" data-id="${td.find('.path-anexo').data('id')}"><i class="fas fa-times"></i></button>
                            </div>
                        `);
                    }
                });
            });
            $(document).on('click', '.btn-remove-file', function(){
                var div = $(this).parent();
                $(this).html(`<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>`);
                $.ajax({
                    url: `{{route('admin.orderServiceDesanexarFiscal')}}`,
                    type: 'POST',
                    data: {order_id: $(this).data('id')},
                    success: (data)=>{
                        div.parent().html(`
                            <button type="button" class="btn btn-info btn-sm btn-anexar">Anexar</button>
                            <input type="file" class="d-none path-anexo" data-id=${div.find('.btn-remove-file').data('id')}>
                        `);
                    }
                });
            });
            $(".btn-status-pay").matchDimensions("height");
            $(".btn-status-pay").matchDimensions("width");
        });

        /* Ajusta os botões ao interagir com a tabela */
        $(document).on("click", ".page-item", function(){
            $(".btn-status-pay").matchDimensions("height");
            $(".btn-status-pay").matchDimensions("width");
        });
        $(document).on("click", "#table_assinatura th", function(){
            $(".btn-status-pay").matchDimensions("height");
            $(".btn-status-pay").matchDimensions("width");
        });
        $(document).on("change", "#table_assinatura_length select", function(){
            $(".btn-status-pay").matchDimensions("height");
            $(".btn-status-pay").matchDimensions("width");
        });
        $(document).on("keyup", "input[type=search]", function(){
            $(".btn-status-pay").matchDimensions("height");
            $(".btn-status-pay").matchDimensions("width");
        });
    </script>
@endsection