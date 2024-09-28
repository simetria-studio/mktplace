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
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Número do Pedido:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{$order->order_number}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Data do Pedido:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{date('d/m/Y', strtotime($order->created_at))}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Método de Pagamento:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{!! __($order->payment_method) !!}</div>
                                    {{-- <div class="col-6 pt-3 px-2 border-bottom"><b>Transportadoras:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">{{$order->shippingCustomer->transport}}</div> --}}
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Valor dos Produtos:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">R$ {{number_format($order->product_value, 2, ',', '.')}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Valor do Frete:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">R$ {{number_format($order->cost_freight, 2, ',', '.')}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Desconto:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">R$ {{number_format($order->discount, 2, ',', '.')}}</div>
                                    <div class="col-6 pt-3 px-2 border-bottom"><b>Valor Total:</b></div>
                                    <div class="col-6 pt-3 px-2 text-right border-bottom">R$ {{number_format($order->total_value-$order->discount, 2, ',', '.')}}</div>
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
                                    <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->orderParent[0]->shippingCustomer->phone1}} / {{$order->orderParent[0]->shippingCustomer->phone2}}</div>

                                    @if ($order->orderParent[0]->shippingCustomer->transport == 'Retirada em local selecionado')
                                        <div class="col-12 pt-3 px-2"><h6>Local para retirada</h6></div>
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>Endereço:</b></div>
                                        <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->orderParent[0]->shippingCustomer->general_data['frete']['localidade']['address']}}, Nº {{$order->orderParent[0]->shippingCustomer->general_data['frete']['localidade']['number']}}</div>
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>Bairro:</b></div>
                                        <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->orderParent[0]->shippingCustomer->general_data['frete']['localidade']['district']}}</div>
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>UF/Cidade:</b></div>
                                        <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->orderParent[0]->shippingCustomer->general_data['frete']['localidade']['city']}}/{{$order->orderParent[0]->shippingCustomer->general_data['frete']['localidade']['state']}}</div>
                                    @else
                                        <div class="col-12 pt-3 px-2"><h6>Endereço de Entrega</h6></div>
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>Endereço:</b></div>
                                        <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->orderParent[0]->shippingCustomer->address}}, Nº {{$order->orderParent[0]->shippingCustomer->number}} - {{$order->orderParent[0]->shippingCustomer->complement}}</div>
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>Bairro:</b></div>
                                        <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->orderParent[0]->shippingCustomer->address2}}</div>
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>UF/Cidade:</b></div>
                                        <div class="col-7 pt-3 px-2 text-right border-bottom">{{$order->orderParent[0]->shippingCustomer->city}}/{{$order->orderParent[0]->shippingCustomer->state}}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="card ">
                            {{-- Header do Card --}}
                            <div class="card-header">
                                <h5>Informações dos Vendedores</h5>
                            </div>
                            {{-- Corpo do Card --}}
                            <div class="card-body pad">
                                @foreach ($order->orderParent as $sub_order)
                                    <div class="row mt-2 border rounded ">
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>Nome:</b></div>
                                        <div
                                            class="col-7 pt-3 px-2 text-right border-bottom">{{$sub_order->seller->name}}</div>
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>Email:</b></div>
                                        <div
                                            class="col-7 pt-3 px-2 text-right border-bottom">{{$sub_order->seller->email}}</div>
                                        <div class="col-5 pt-3 px-2 border-bottom"><b>Loja:</b></div>
                                        <div
                                            class="col-7 pt-3 px-2 text-right border-bottom">{{$sub_order->seller->store->store_name ?? 'Loja sem Nome'}}</div>
                                    </div>
                                @endforeach
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
                            <div class="col-6"><h5>Produtos Comprados</h5></div>
                            <div class="col-6 text-right"><a class="btn btn-primary btn-sm" href="{{route('perfil')}}"><i class="fas fa-arrow-left"></i> Voltar</a></div>
                        </div>
                    </div>
                    {{-- Corpo do Card --}}
                    <div class="card-body">
                        @foreach ($order->orderParent as $sub_order)
                            <div class="container border rounded mb-3" style="position: relative;">
                                <div class="info-storename" style="position: absolute; top: -12px; background-color: #fff; width: auto; margin: 0 22px;">Loja - <b>{{$sub_order->seller->store->store_name ?? $sub_order->seller->name}}</b>: <span class="info-popover" data-container="body" data-toggle="popover" data-placement="top" data-content="Sub. Pedido - @if($sub_order->path_fiscal) <a href='{{$sub_order->url_fiscal}}'' target='_blank'>{{$sub_order->order_number}}</a> @else {{$sub_order->order_number}} @endif"><i class="fas fa-info info-popover-svg"></i></span></div>
                                @foreach ($sub_order->orderProducts as $orderProducts)
                                    <div class="row">
                                        <div class="col-12 pt-3 px-2">
                                            {{$orderProducts->product_name}} 
                                            @if($orderProducts->attributes)
                                                @foreach ($orderProducts->attributes as $attribute)
                                                    <br>
                                                    {{App\Models\Attribute::find($attribute['parent_id'])->name}}:{{$attribute['name']}} /
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="col-12 px-2 text-right border-bottom">{{$orderProducts->quantity}} x
                                            R$ {{number_format($orderProducts->product_sales_unit, 2, ',', '.')}}</div>
                                    </div>
                                @endforeach
                                <div class="row">
                                    <div class="col-12 pt-3 px-2">
                                        @php
                                            $general_data = $sub_order->shippingCustomer->general_data ?? null;
                                            if($general_data){
                                                if(!is_array($general_data)) $general_data = json_decode($general_data, true);
                                            }
                                        @endphp
                                        Frete - {{isset($general_data['company']['name']) ? $general_data['company']['name'].' /' : ''}} {{$sub_order->shippingCustomer->transport}} {{isset($general_data['custom_delivery_time']) ? $general_data['custom_delivery_time'].' Dias' : ''}}
                                        <br>
                                        @isset ($general_data['descricao'])
                                            {{$general_data['descricao'] ?? ''}}
                                        @endisset
                                        {{-- {{\Log::info($sub_order->shippingCustomer->general_data)}} --}}
                                    </div>
                                    <div class="col-12 px-2 text-right border-bottom">
                                        R$ {{number_format($sub_order->shippingCustomer->price, 2, ',', '.')}}
                                    </div>
                                </div>
                                @if ($order->discount > 0)
                                    <div class="row">
                                        <div class="col-12 pt-3 px-2">
                                            Desconto
                                        </div>
                                        <div class="col-12 px-2 text-right border-bottom">
                                            R$ {{number_format($order->discount, 2, ',', '.')}}
                                        </div>
                                    </div>
                                @endif
                                @if ($sub_order->shippingCustomer->tracking_id)
                                    <div class="row">
                                        @if (!empty(\App\Models\OrderME::where('order_number', $sub_order->order_number)->first()->code))
                                            <div class="col-12 pt-3 pb-2 px-2">
                                                Código de Rastreio - <a href="{{route('rastreio', $sub_order->shippingCustomer->tracking_id)}}" class="btn btn-primary btn-rastreio-melhor-envio">{{$sub_order->shippingCustomer->tracking_id}}</a>
                                            </div>
                                        @else
                                            <div class="col-12 pt-3 pb-2 px-2">
                                                Código de Rastreio - {!! verificaUrlTrackind($sub_order->shippingCustomer->tracking_id) !!}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if ($sub_order->pay < 3)
                                    <div class="row">
                                        <div class="col-12 pt-3 px-2">
                                            @if ($sub_order->pay == 2)
                                                @if (date('Y-m-d', strtotime('-7 Days')) <= date('Y-m-d', strtotime($order->updated_at)))
                                                    <button class="btn btn-danger btn-sm btn-input-pedido" data-toggle="modal" data-target="#solicitarCancelamentoPedido" data-order_number="{{$sub_order->order_number}}"><i class="fas fa-times"></i> Cancelar Pedido</button>
                                                @endif
                                            @else
                                                <button class="btn btn-danger btn-sm btn-input-pedido" data-toggle="modal" data-target="#solicitarCancelamentoPedido" data-order_number="{{$sub_order->order_number}}"><i class="fas fa-times"></i> Cancelar Pedido</button>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-12 pt-3 pb-2 px-2">
                                        Status do pedido - 
                                        @if ($sub_order->pay == 0)
                                            Aguardando Pagamento
                                        @elseif($sub_order->pay == 1)
                                            Em Andamento/Pagamento Aprovado
                                            @if (isset($sub_order->shippingCustomer->tracking_id))
                                                <button type="button" class="btn btn-success btn-sm finalizarOrder" data-order_number="{{$sub_order->order_number}}">Finalizar Pedido</button>
                                            @endif
                                        @elseif($sub_order->pay == 2)
                                            Finalizado
                                            @if (date('Y-m-d', strtotime('-7 Days')) <= date('Y-m-d', strtotime($sub_order->updated_at)))
                                                (Você possui 7 dias para fazer o cancelamento do pedido)
                                            @endif
                                        @elseif($sub_order->pay == 3)
                                            Cancelado
                                        @elseif($sub_order->pay == 10)
                                            Solicitação de Cancelamento
                                        @elseif($sub_order->pay == 11)
                                            Cancelado Pelo Cliente
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="row">
                            <div class="col-6 pt-3 px-2"><b>Total:</b></div>
                            <div class="col-6 pt-3 px-2 text-right">
                                R$ {{number_format($order->total_value-$order->discount, 2, ',', '.')}}</div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    {{-- Header do Card --}}
                    <div class="card-header">
                        <h5>Dados de pagamento</h5>
                        <span class="badge badge-success position-absolute asaas-orderid" style="right: 15px;top: 15px;">{{$order->payment_id}}</span>
                    </div>
                    {{-- Corpo do Card --}}
                    <div class="card-body">

                        <b class="float-left mr-2">Status: </b>
                        @switch ($pedido_asaas->status ?? null)
                            @case('PENDING')
                                Pendente
                                @break
                            @case('CANCELED')
                                Cancelado
                                @break
                            @default
                                {!! $pedido_asaas->status ?? 'sem status' !!}
                        @endswitch
                        <br>
                        <b class="float-left mr-2">Metodo de Pagamento: </b>
                        {!! __(($pedido_asaas->billingType ?? '')) !!}
                        <br>
                        @if (($pedido_asaas->billingType ?? '') == 'PIX')
                            <div class="qrcode">
                                <button class="btn btn-primary my-2" onclick="copyQrCode(this)">Copiar código pix</button>
                                <button class="btn btn-primary my-2" onclick="verQrCode(this)">Ver código  pix</button>
                                <br>
                                <div class="digitos-codigo-pix"></div>
                                <br>
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::generate($pedido_asaas->charges[0]->last_transaction->qr_code) !!}</div>
                                <br>
                                <script>
                                    function copyQrCode(e) {
                                        /* Copy the text inside the text field */
                                        navigator.clipboard.writeText("{{$pedido_asaas->charges[0]->last_transaction->qr_code}}");

                                        $(e).removeClass('btn-primary').addClass('btn-success').text('Código pix copiado');
                                        setTimeout(() => {
                                            $(e).removeClass('btn-success').addClass('btn-primary').text('Copiar código pix');
                                        }, 2400);
                                    }
                                    function verQrCode(e) {
                                        /* Copy the text inside the text field */
                                        $(e).addClass('d-none');
                                        $('.digitos-codigo-pix').css({
                                            'border': '1px solid #000',
                                            'border-radius': '.25rem',
                                            'padding': '2px 4px'
                                        }).text("{{$pedido_asaas->charges[0]->last_transaction->qr_code}}");
                                    }
                                </script>
                        @elseif (($pedido_asaas->billingType ?? '') == 'BOLETO')
                            <b class="float-left mr-2">Pagar Até: </b>
                            {{date('d/m/Y', strtotime($pedido_asaas->dueDate))}}
                            <br>
                            @if ($pedido_asaas->status)
                                <a target='__blank' href='{{$pedido_asaas->bankSlipUrl}}' class='btn btn-success font-italic font-weight-bold link-success btn-boleto-trigger'>Clique aqui para pagar o boleto</a>
                                <br>
                            @endif
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
                <form action="{{route('perfil.solicitaCancelamentoPedido')}}" method="post">
                    <input type="hidden" name="order_number">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="title">Selecione o motivo:</label>
                                <select name="title" class="form-control">
                                    <option
                                        value="Devolução">
                                        Devolução
                                    </option>
                                    <option
                                        value="Troca">
                                        Troca
                                    </option>
                                    <option
                                        value="Outro Motivo">
                                        Outro Motivo
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <label for="">Descreva o motivo da solicitação:</label>
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

    <div class="modal fade" id="rastreioMelhorEnvio">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Rastrear Produto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    ...
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                </div>
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