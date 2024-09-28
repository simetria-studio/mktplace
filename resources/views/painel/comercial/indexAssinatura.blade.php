@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Assinaturas em Andamento</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Assinaturas</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Assinaturas</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
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
                                                    <option value="1">Ativa</option>
                                                    <option value="2">Cancelada</option>
                                                    <option value="5">Cancelada pelo Cliente</option>
                                                    <option value="3">Finalizada</option>
                                                    <option value="4">Solicitação de Cancelamento</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <button type="submit" class="btn btn-primary">Filtrar</button>
                                            <a href="{{asset('admin/comercial/assinaturas')}}" class="btn btn-secondary ml-2">Limpar</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="container mt-4">
                                <table class="table table-hover" id="table_assinatura">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Assinatura</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                            <th>Status</th>
                                            <th>Nome do Vendedor</th>
                                            <th>Nome do Comprador</th>
                                            <th>Valor Total</th>
                                            <th>Intervalo</th>
                                            <th>Data Expiração</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($assinaturas as $assinatura)
                                            <tr class="tr-plan-id-{{$assinatura->id}}">
                                                <td>{{$assinatura->id}}</td>
                                                <td>{{$assinatura->plan_title}}</td>
                                                <td>{{date('d/m/Y', strtotime($assinatura->created_at))}}</td>
                                                <td>
                                                    <a href="{{route('assinaturaDetalhe', $assinatura->id)}}" class="btn {{$assinatura->status == 1 ? 'btn-primary' : 'btn-dark'}} btn-sm">Visualizar Assinatura</a>
                                                </td>
                                                <td>
                                                    @switch($assinatura->status)
                                                        @case('1')
                                                            <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #58bc9a;min-height: 50px;">Ativa</button>
                                                            @break
                                                        @case('2')
                                                            <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #db5812;min-height: 50px;">Cancelada</button>
                                                            @break
                                                        @case('3')
                                                            <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #c6d300;min-height: 50px;">Finalizada</button>
                                                            @break
                                                        @case('4')
                                                            <button type="button" class="btn btn-sm btn-block btn-status-pay btn-verifica-cancelamento" data-toggle="modal" data-target="#verificarSolicitacaoCancelamento" data-order_number="{{$assinatura->id}}" data-url="{{route('admin.verificarSolicitacaoCancelamentoPlan')}}" style="background-color: #db5812;min-height: 50px;">Solicitação de Cancelamento</button>
                                                            @break
                                                        @case('5')
                                                            <button type="button" class="btn btn-sm btn-block btn-status-pay" style="background-color: #db5812;min-height: 50px;">Cancelado pelo Cliente</button>
                                                            @break
                                                        @default
                                                    @endswitch
                                                </td>
                                                <td>{{$assinatura->seller->store->store_name}}</td>
                                                <td>{{$assinatura->user->name}}</td>
                                                <td>{{number_format( ($assinatura->plan_value + $assinatura->shipping['price']) , 2, ',', '.')}}</td>
                                                <td>{!!planCobranca($assinatura->select_interval)!!}</td>
                                                <td>{{date('d/m/Y', strtotime($assinatura->finish))}}</td>
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
                <form action="{{route('admin.confirmarSolicitacaoCancelamentoPlan')}}" method="post">
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