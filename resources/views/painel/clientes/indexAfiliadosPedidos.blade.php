@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Resumo dos Pedidos Afiliados</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Pedidos Afiliados</li>
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
                            <h3 class="card-title">Resumo dos Pedidos de Afiliados</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
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
                                                <td><a href="{{route('ver_pedido', $order_aff->order_number)}}" target="_blank">{{$order_aff->order_number}}</a></td>
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
        
                            <div class="container mt-2">{{$psAfiliadosPedidos->links()}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection