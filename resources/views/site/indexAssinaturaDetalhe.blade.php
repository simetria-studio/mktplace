@extends('layouts.site')

@section('container')
    <div class="container my-5">
        <div class="row">
            <div class="col-6">
                <h3>Assinatura {{$assinatura->plan_title}}</h3>
            </div>
            <div class="col-6" style="text-align: end;">
                <a class="btn btn-primary btn-sm" href="{{route('perfil.assinatura')}}"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>

        <div class="row my-5">
            <div class="col-12 col-md-6">
                <div class="row">
                    {{-- Infos Assinatura --}}
                    <div class="col-12 mb-2">
                        <div class="card">
                            <div class="card-header">
                                <h5>Informações da Assinatura</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Número da Assinatura:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{$assinatura->id}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Intervalo de Pagamento:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{!!planCobranca($assinatura->select_interval)!!}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Duração da Assinatura:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{$assinatura->duration_plan}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Data da Expiração:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{date('d-m-Y', strtotime($assinatura->finish))}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Plano de Entrega:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom" style="text-transform: capitalize;">{{$assinatura->select_entrega}}</div>

                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Status da Assinatura:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">
                                        @switch($assinatura->status)
                                            @case('1')
                                                Ativa
                                                @if (date('Y-m-d', strtotime($assinatura->created_at)) == date('Y-m-d'))
                                                    <button class="btn btn-danger btn-sm btn-cancela-assinatura ml-3" data-order_number="{{$assinatura->id}}"><i class="fas fa-times"></i> Cancelar</button>
                                                @else
                                                    <button class="btn btn-danger btn-sm btn-input-pedido ml-3" data-toggle="modal" data-target="#solicitarCancelamentoAssinatura" data-order_number="{{$assinatura->id}}"><i class="fas fa-times"></i> Cancelar</button>
                                                @endif
                                                @break
                                            @case('2')
                                                Cancelada
                                                @break
                                            @case('3')
                                                Finalizada
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
                    </div>

                    {{-- Infos Vendedor --}}
                    <div class="col-12 mt-2">
                        <div class="card ">
                            <div class="card-header">
                                <h5>Informações do Vendedor</h5>
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
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="row">
                    {{-- Infos Entrega --}}
                    <div class="col-12 mb-2">
                        <div class="card ">
                            <div class="card-header">
                                <h5>Informações da Entrega</h5>
                            </div>
                            <div class="card-body">
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
        </div>
    </div>

    <div class="modal fade" id="solicitarCancelamentoAssinatura">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Solicitar o Cancelamento da Assinatura</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('perfil.solicitaCancelamentoAssinatura')}}" method="post">
                    <input type="hidden" name="order_number">
                    <div class="modal-body">
                        <div class="row">
                            {{-- <div class="form-group col-12">
                                <label for="title">Selecione o motivo:</label>
                                <select name="title" class="form-control">
                                    <option value="Cancelamento">Cancelamento</option>
                                    <option value="Outro Motivo">Outro Motivo</option>
                                </select>
                            </div> --}}
                            <div class="col-12 form-group">
                                <label for="">Descreva o motivo da solicitação:</label>
                                <textarea name="reason" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-primary btn-cancelar-pedido"><i class="fas fa-check"></i> Solicitar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            $('.btn-cancela-assinatura').on('click', function(){
                Swal.fire({
                    icon: 'warning',
                    title: 'Cancelar Assinatura?',
                    showCancelButton: true,
                    confirmButtonText: 'SIM',
                    cancelButtonText: 'NÃO',
                }).then((result) => {
                    if(result.isConfirmed){
                        Swal.fire({
                            title: 'Cancelando, aguarde...',
                            allowOutsideClick: false,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        $.ajax({
                            url: `{{route('perfil.solicitaCancelamentoAssinatura')}}`,
                            type: 'POST',
                            data: {signedplan_id: $(this).data('order_number')},
                            success: (data) => {
                                Swal.fire({
                                    icon: 'sucess',
                                    title: 'Cancelamento efutado com successo!'
                                }).then((result) => {
                                    window.location.reload();
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection