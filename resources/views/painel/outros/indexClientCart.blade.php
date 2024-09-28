@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Carrinhos de Clientes</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Carrinho de Cliente</li>
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
                            <h3 class="card-title">Carrinhos de Clientes</h3>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="row">
                                @foreach ($carts as $user_id => $cart)
                                    <div class="col-12">
                                        <div class="card card-primary card-outline">
                                            {{-- Header do Card --}}
                                            <div class="card-header">
                                                <h3 class="card-title"><b>Cliente: </b>{{\App\Models\User::find($user_id)->name ?? 'Carrinho sem Cliente'}} / <b>Email: </b>{{\App\Models\User::find($user_id)->email ?? ''}} / <a class="btn btn-sm btn-danger" href="{{route('admin.client.cart.remove', $user_id)}}">Limpar Carrinho</a></h3>
                                            </div>
                                            {{-- Corpo do Card --}}
                                            <div class="card-body pad">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Valor</th>
                                                            <th>Qtd.</th>
                                                            <th>Adicionado em</th>
                                                            <th>Atualizado em</th>
                                                            <th>Ação</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cart as $item)
                                                            <tr>
                                                                <td>{{$item->name}}</td>
                                                                <td>R$ {{number_format($item->price, 2, ',', '.')}}</td>
                                                                <td>{{$item->quantity}}</td>
                                                                <td>{{date('d/m/Y H:i:s', strtotime($item->created_at))}}</td>
                                                                <td>{{date('d/m/Y H:i:s', strtotime($item->updated_at))}}</td>
                                                                <td><a class="btn btn-sm btn-danger" href="{{route('admin.client.cart.removeitem', $item->id)}}">Remover Item</a></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            
        });
    </script>
@endsection