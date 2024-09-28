@extends('layouts.site')

@section('container')
    <div class="container my-5">
        <div class="row">
            {{-- dados do Pedido --}}
            <div class="col-12 col-md-6">
                <div class="row">
                    <div class="col-12 mb-2">
                        <div class="card">
                            {{-- Header do Card --}}
                            <div class="card-header">
                                <h5>Informações do Pedido</h5>
                            </div>
                            {{-- Corpo do Card --}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Numero do Pedido:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{$order->order_number}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Data do Pedido:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{date('d/m/Y', strtotime($order->created_at))}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Metodo de Pagamento:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{!! __($order->payment_method) !!}</div>
                                    {{-- <div class="col-6 pt-3 px-2 border-bottom"><b>Transportadoras:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{$order->shippingCustomer->transport}}</div> --}}
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Valor do Serviço:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">
                                        R$ {{number_format($order->service_value, 2, ',', '.')}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 my-2">
                        <div class="card">
                            {{-- Header do Card --}}
                            <div class="card-header">
                                <h5>Informações do Comprador</h5>
                            </div>
                            {{-- Corpo do Card --}}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5 pt-3 px-2 border-bottom"><b>Nome:</b></div>
                                    <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->user_name}}</div>
                                    <div class="col-5 pt-3 px-2 border-bottom"><b>CPF:</b></div>
                                    <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->user_cnpj_cpf}}</div>
                                    <div class="col-5 pt-3 px-2 border-bottom"><b>Email:</b></div>
                                    <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->user_email}}</div>
                                    <div class="col-5 pt-3 px-2 border-bottom"><b>Telefones:</b></div>
                                    <div class="col-7 pt-3 px-2 text-right border-bottom">
                                        {{$order->user->adresses->last()->phone2}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="card ">
                            {{-- Header do Card --}}
                            <div class="card-header">
                                <h5>Informações do Vendedor</h5>
                            </div>
                            {{-- Corpo do Card --}}
                            <div class="card-body pad">
                                <div class="row mt-2 border rounded ">
                                    <div class="col-5 pt-3 px-2 border-bottom"><b>Nome:</b></div>
                                    <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->seller->name}}</div>
                                    <div class="col-5 pt-3 px-2 border-bottom"><b>Email:</b></div>
                                    <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->seller->email}}</div>
                                    <div class="col-5 pt-3 px-2 border-bottom"><b>Loja:</b></div>
                                    <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->seller->store->store_name ?? 'Loja sem Nome'}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card ">
                    {{-- Header do Card --}}
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6"><h5>Serviços Adquiridos</h5></div>
                            <div class="col-6 text-right"><a class="btn btn-primary btn-sm" href="{{route('perfil')}}"><i class="fas fa-arrow-left"></i> Voltar</a></div>
                        </div>
                    </div>
                    {{-- Corpo do Card --}}
                    <div class="card-body">
                        <div class="container border rounded mb-3" style="position: relative;">
                            <div class="info-storename" style="position: absolute; top: -12px; background-color: #fff; width: auto; margin: 0 22px;">Loja - <b>{{$order->seller->store->store_name ?? $order->seller->name}}</b></div>
                            <div class="row">
                                <div class="col-12 pt-3 px-2">
                                    {{$order->serviceReservation->service_name}} 
                                    @if($order->serviceReservation->attributes['selected_attribute'])
                                        @foreach ($order->serviceReservation->attributes['selected_attribute'] as $attribute)
                                            <br>
                                            {{App\Models\Attribute::find($attribute['parent_id'])->name}}:{{$attribute['name']}} /
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-12 pt-3 px-2">
                                    Data da Reserva {{date('d/m/Y', strtotime($order->serviceReservation->date_reservation_ini))}} - {{$order->serviceReservation->date_reservation_fim ? date('d/m/Y', strtotime($order->serviceReservation->date_reservation_fim)) : ''}} | {{$order->serviceReservation->hour_reservation ? $order->serviceReservation->hour_reservation : ''}}
                                </div>
                                <div class="col-12 px-2 text-right border-bottom">{{$order->serviceReservation->service_quantity}} x R$ {{number_format($order->serviceReservation->service_price*($order->serviceReservation->attributes['diaria']), 2, ',', '.')}}</div>
                            </div>
                            @if ($order->pay < 3)
                                <div class="row">
                                    <div class="col-12 pt-3 px-2">
                                        @if ($order->pay == 2)
                                            @if (date('Y-m-d', strtotime('-7 Days')) <= date('Y-m-d', strtotime($order->updated_at)))
                                                <button class="btn btn-danger btn-sm btn-input-pedido" data-toggle="modal" data-target="#solicitarCancelamentoPedido" data-order_number="{{$order->order_number}}"><i class="fas fa-times"></i> Cancelar Pedido</button>
                                            @endif
                                        @else
                                            <button class="btn btn-danger btn-sm btn-input-pedido" data-toggle="modal" data-target="#solicitarCancelamentoPedido" data-order_number="{{$order->order_number}}"><i class="fas fa-times"></i> Cancelar Pedido</button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-12 pt-3 pb-2 px-2">
                                    Status do pedido - 
                                    @if ($order->pay == 0)
                                        Aguardando Pagamento
                                    @elseif($order->pay == 1)
                                        Em Andamento/Pagamento Aprovado
                                        <button type="button" class="btn btn-success btn-sm finalizarOrder" data-order_number="{{$order->order_number}}">Finalizar Pedido</button>
                                    @elseif($order->pay == 2)
                                        Finalizado
                                        @if (date('Y-m-d', strtotime('-7 Days')) <= date('Y-m-d', strtotime($order->updated_at)))
                                            (Você possui 7 dias para fazer o cancelamento do pedido)
                                        @endif
                                    @elseif($order->pay == 3)
                                        Cancelado
                                    @elseif($order->pay == 10)
                                        Solicitação de Cancelamento
                                    @elseif($order->pay == 11)
                                        Cancelado Pelo Cliente
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 pt-3 px-2"><b>Total:</b></div>
                            <div class="col-6 pt-3 px-2 text-right">
                                R$ {{number_format($order->total_value, 2, ',', '.')}}</div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    {{-- Header do Card --}}
                    <div class="card-header">
                        <h5>Dados de pagamento</h5>
                        <span class="badge badge-success position-absolute pagarme-orderid" style="right: 15px;top: 15px;">{{$order->payment_id}}</span>
                    </div>
                    {{-- Corpo do Card --}}
                    <div class="card-body">

                        @if ($pagarme_v == '2.0')
                            <b class="float-left mr-2">{!! \Illuminate\Support\Str::ucfirst((__("pagarme.status"))) !!}: </b>
                            {!! __($pedido_pagarme->status) !!}
                            <br>
                            <b class="float-left mr-2">{!! \Illuminate\Support\Str::ucfirst((__("pagarme.payment_method"))) !!}: </b>
                            {!! __($pedido_pagarme->charges[0]->payment_method) !!}
                            <br>
                            @if ($pedido_pagarme->charges[0]->payment_method == 'pix')
                                <div class="qrcode">
                                    <button class="btn btn-success my-2" onclick="copyQrCode()">copiar código pix</button>
                                    <br>
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::generate($pedido_pagarme->charges[0]->last_transaction->qr_code) !!}</div>
                                    <br>
                                    <script>
                                        function copyQrCode() {
                                            /* Copy the text inside the text field */
                                            navigator.clipboard.writeText("{{$pedido_pagarme->charges[0]->last_transaction->qr_code}}");
                                        }
                                    </script>
                            @elseif ($pedido_pagarme->charges[0]->payment_method == 'boleto')
                                {{date('d/m/Y', strtotime($pedido_pagarme->charges[0]->last_transaction->due_at))}}
                                <br>
                                <a target='__blank' href='{{$pedido_pagarme->charges[0]->last_transaction->url}}' class='btn btn-success font-italic font-weight-bold link-success btn-boleto-trigger'>Clique aqui para pagar o boleto</a>
                                <br>
                            @endif

                            {{-- @php
                                $valor = collect($pedido_pagarme->items)->map(function ($item){
                                    $unitPrice = print_valor_vindo_pagar_me($item->amount);
                                    $total = print_valor_vindo_pagar_me($item->amount * $item->quantity);
                                    return "<br><b>{$item->quantity}</b> de <b>{$item->description}</b> por <b>{$unitPrice}</b> Total: +<span class='font-weight-bold text-success'>{$total}</span>";
                                })->join('');
                            @endphp
                            <b class="float-left mr-2">{!! \Illuminate\Support\Str::ucfirst((__("pagarme.items"))) !!}: </b>
                            {!! __($valor) !!} --}}
                        @elseif($pagarme_v == '1.0')
                            @foreach(collect(get_pagarme()->transactions()->get(['id'=>$order->payment_id]))->only(['payment_method', 'status', 'boleto_url', 'boleto_expiration_date', 'boleto_barcode', 'items', 'pix_qr_code']) as $campo => $valor)
                                @if($valor != null && $valor != '')
                                    @php
                                        if(!is_array($valor)){
                                            [$campos] = explode('T', $valor);
                                            if(count($explodeCampos = explode('-', $campos)) === 3) {
                                                    [$ano, $mes, $dia] = $explodeCampos;
                                                    if(checkdate($mes, $dia, $ano)){
                                                        $valor = (new \Carbon\Carbon(
                                                            $valor
                                                        ))->format('d/m/Y - H:i:s');
                                                    }
                                            }
                                            if($campo === 'boleto_url'){
                                                $valor = "<a target='__blank' href='$valor' class='btn btn-success font-italic font-weight-bold link-success btn-boleto-trigger'>Clique aqui para pagar o boleto</a>";
                                            }
                                        }else{
                                            $valor = collect($valor)->map(function ($item){
                                                $unitPrice = print_valor_vindo_pagar_me($item->unit_price);
                                                $total = print_valor_vindo_pagar_me($item->unit_price * $item->quantity);
                                                return "<br><b>{$item->quantity}</b> de <b>{$item->title}</b> por <b>{$unitPrice}</b> Total: +<span class='font-weight-bold text-success'>{$total}</span>";
                                            })->join('');
                                        }
                                    @endphp
                                    <b class="float-left mr-2">
                                        {!! \Illuminate\Support\Str::ucfirst((__("pagarme.$campo"))) !!}: </b>
                                    {!! __($valor) !!}
                                    <br>
                                    @if($campo == 'pix_qr_code')
                                        <div class="qrcode">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::generate($valor) !!}</div>
                                        <br>
                                        <button class="btn btn-success" onclick="copyQrCode()">copiar codigo pix</button>
                                        <script>
                                            function copyQrCode() {
                                                /* Copy the text inside the text field */
                                                navigator.clipboard.writeText("{{$valor}}");
                                            }
                                        </script>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="solicitarCancelamentoPedido">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Solicitar o Cancelamento do Pedido</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('perfil.solicitaCancelamentoPedidoServico')}}" method="post">
                    <input type="hidden" name="order_number">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 form-group">
                                <label for="">Informe o motivo:</label>
                                <textarea name="reason" class="form-control"></textarea>
                            </div>
                            @if ($order->payment_method == 'boleto')
                                <div class="form-group col-12">
                                    <label for="bank_code_id">Banco</label>
                                    <select id="bank_code_id" name="bank_code_id" class="form-control select2">
                                        {!! collect(bancos())->map(function ($bancoNome, $bancoCode){ return "<option value='{$bancoCode}'>{$bancoCode} - {$bancoNome}</option>";})->join(' ') !!}
                                    </select>
                                </div>
                                <div class="form-group col-8">
                                    <label for="agencia">Agência</label>
                                    <input name="agencia" class="form-control">
                                </div>

                                <div class="form-group col-4">
                                    <label for="agencia_dv_id">{{--Agência DV--}}Digito Verificador</label>
                                    <input id="agencia_dv_id" name="agencia_dv_id" class="form-control">
                                </div>

                                <div class="form-group col-8">
                                    <label for="conta_id">Conta</label>
                                    <input id="conta_id" name="conta_id" class="form-control">
                                </div>
                                <div class="form-group col-4">
                                    <label for="conta_dv_id">{{--Conta DV--}}Digito Verificador</label>
                                    <input id="conta_dv_id" name="conta_dv_id" class="form-control">
                                </div>

                                <div class="form-group col-12">
                                    <label for="store_name">Tipo da conta</label>
                                    <select name="type" class="form-control">
                                        <option
                                            value="conta_corrente">
                                            Conta corrente
                                        </option>
                                        <option
                                            value="conta_poupanca">
                                            Conta poupanca
                                        </option>
                                        <option
                                            value="conta_corrente_conjunta">
                                            Conta corrente conjunta
                                        </option>
                                        <option
                                            value="conta_poupanca_conjunta">
                                            Conta poupanca conjunta
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label for="document_number_id">CPF/CNPJ atrelado à conta</label>
                                    <input id="document_number_id" name="document_number_id" class="form-control"
                                        >
                                </div>

                                <div class="form-group col-12">
                                    <label for="legal_name_id">Nome/Razão social do dono da conta</label>
                                    <input id="legal_name_id" name="legal_name_id" class="form-control max-caracteres" data-max_caracteres="30">
                                </div>
                            @endif
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
            // Condifgurações
            var Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 4000
            });

            if("{{session()->has('success')}}"){
                if($('.btn-boleto-trigger').length>0){
                    window.open($('.btn-boleto-trigger').attr('href'), '_blank');
                }
                Toast.fire({
                    icon: 'success',
                    title: "{{session()->get('success')}}"
                });
            }
        });
    </script>
@endsection