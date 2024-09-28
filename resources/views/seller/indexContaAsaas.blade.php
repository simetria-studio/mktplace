@extends('layouts.painelSman')

@section('container')
    <div class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Conta do Asaas</h1>
                    @if($recipient?->id??false)
                        <span class="badge badge-success">{{$recipient?->id??'(sem id)'}}</span>
                    @endif
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/vendedor')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Dados da conta</li>
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
                            <h3 class="card-title">Dados da conta bancária para recebimento</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="container-fluid">
                                <div class="row mb-3">
                                    <div class="col-12 mb-4">
                                        Utilizamos o Metodo de pagamento <strong>Asaas</strong> para realizar os pagamentos de suas vendas. Para isso, é necessário que você tenha uma conta no Asaas para receber os valores de suas vendas. Caso você ainda não tenha uma conta no Asaas, <a class="link" target="_blank" href="https://www.asaas.com/r/076ddab9-80c8-4003-8349-83039681eb87">clique aqui</a>.
                                    </div>
                                    <div class="col-12 mb-2">
                                        <button type="button" class="btn btn-sm btn-success btn-update-wallet-id">Atualizar</button> <strong>Wallet ID:</strong> {{ auth('seller')->user()->wallet_id ?? '' }}
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-success btn-update-chave-api">Atualizar</button> <strong>Chave da API:</strong> {{ $apiAsaas->token ?? '' }}
                                    </div>
                                </div>

                                @if (($apiAsaas->token ?? null))
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <div class="px-4 py-3 bg-success text-center border rounded w-25">
                                                <h3>R$ {{ number_format($balance->balance, 2, '.', ',') }}</h3>
                                                <span>disponivel</span>
                                            </div>
                                        </div>

                                        <div class="col-12"><h4>Extrato</h4></div>

                                        <div class="col-12">
                                            <table class="table table-striped table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Transação ID</th>
                                                        <th>Status</th>
                                                        <th>Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($splits->data as $split)
                                                        <tr>
                                                            <td>{{ $split->id }}</td>
                                                            <td>
                                                                @switch($split->status)
                                                                    @case('RECEIVED')
                                                                        RECEBIMENTO
                                                                        @break
                                                                    @case('PENDING')
                                                                        PENDENTE
                                                                        @break
                                                                    @case('CANCELLED')
                                                                        CANCELADO
                                                                        @break
                                                                    @case('AWAITING_CREDIT')
                                                                        AGUARDANDO CRÉDITO
                                                                        @break
                                                                    @default
                                                                        Não Encontrado - {{ $split->type }}
                                                                @endswitch
                                                            </td>
                                                            <td>R$ {{ number_format($split->totalValue, 2, '.', ',') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            // Atualuzação do Wallet ID
            $(document).on('click', '.btn-update-wallet-id', function(){
                Swal.fire({
                    title: 'Atualizar Wallet ID',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não'
                }).then((result) => {
                    if(result.isConfirmed){
                        $.ajax({
                            url: '{{route("seller.contaRecebimento", "update-wallet-id")}}',
                            type: 'POST',
                            data: {
                                _token: '{{csrf_token()}}',
                                wallet_id: result.value
                            },
                            success: function(data){
                                if(data.status == 'success'){
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sucesso',
                                        text: 'Wallet ID atualizado com sucesso'
                                    });
                                    location.reload();
                                }else{
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro',
                                        text: 'Erro ao atualizar Wallet ID'
                                    });
                                }
                            }
                        });
                    }
                });
            });

            // Atualização da Chave da API
            $(document).on('click', '.btn-update-chave-api', function(){
                Swal.fire({
                    title: 'Atualizar Chave da API',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não'
                }).then((result) => {
                    if(result.isConfirmed){
                        $.ajax({
                            url: '{{route("seller.contaRecebimento", "update-chave-api")}}',
                            type: 'POST',
                            data: {
                                _token: '{{csrf_token()}}',
                                chave_api: result.value
                            },
                            success: function(data){
                                if(data.status == 'success'){
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sucesso',
                                        text: 'Chave da API atualizada com sucesso'
                                    });
                                    location.reload();
                                }else{
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro',
                                        text: 'Erro ao atualizar Chave da API'
                                    });
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection