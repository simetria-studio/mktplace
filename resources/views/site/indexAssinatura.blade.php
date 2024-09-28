@extends('layouts.site')

@section('container')
    <div class="container-fluid">
        <div class="container py-5">
            <div class="row">
                <div class="col-6">
                    <h3>MINHAS ASSINATURAS</h3>
                </div>
                <div class="col-6" style="text-align: end;">
                    <a class="btn btn-primary btn-sm" href="{{route('perfil')}}"><i class="fas fa-arrow-left"></i> Voltar</a>
                </div>
            </div>

            <div class="row my-5">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Plano</th>
                                <th>Valor Total</th>
                                <th>Intervalo</th>
                                <th>Data Expiração</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assinaturas as $assinatura)
                                <tr>
                                    <td>{{$assinatura->plan_title}}</td>
                                    <td>R$ {{number_format( ($assinatura->plan_value + $assinatura->shipping['price']) , 2, ',', '.')}}</td>
                                    <td>{!!planCobranca($assinatura->select_interval)!!}</td>
                                    <td>{{date('d-m-Y', strtotime($assinatura->finish))}}</td>
                                    <td>
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
                                            @default
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="">
                                            <a href="{{route('perfil.assinaturaDetalhe', $assinatura->id)}}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Vizualizar Assinatura</a>
                                            @if($assinatura->status == 1)
                                                @if (date('Y-m-d', strtotime($assinatura->created_at)) == date('Y-m-d'))
                                                    <button class="btn btn-danger btn-sm btn-cancela-assinatura ml-3" data-order_number="{{$assinatura->id}}"><i class="fas fa-times"></i> Cancelar Assinatura</button>
                                                @else
                                                    <button class="btn btn-danger btn-sm btn-input-pedido ml-3" data-toggle="modal" data-target="#solicitarCancelamentoAssinatura" data-order_number="{{$assinatura->id}}"><i class="fas fa-times"></i> Cancelar Assinatura</button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{$assinaturas->links()}}
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