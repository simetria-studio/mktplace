<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <link rel="Stylesheet" href="{{asset('plugin/bootstrap-4.6.1/css/bootstrap.min.css')}}" type="text/css">
    @isset($order->shippingCustomer)<title>Biguaçu - Impressão do Pedido {{$order_number}}</title>@endisset
    @isset($order->serviceReservation)<title>Biguaçu - Impressão do Serviço {{$order_number}}</title>@endisset

    <style>
        @page {
            size: A4;
            /* margin: 11mm 17mm 17mm 17mm; */
            /* margin: 8mm 17mm 8mm 17mm; */
            margin: 0 0 0 0;
        }

        .content-block {
            font-size: 13px;
            padding: 0;
        }

        .content-block .header {
            /* border-bottom: 2px solid black; */
            transform: translateZ(0);
            perspective: 1000;
            width: 100%;
        }
        .content-block .footer {
            /* border-top: 2px solid #F27415; */
            transform: translateZ(0);
            perspective: 1000;
            width: 100%;
        }

        @media print {
            div.header {
                position: fixed;
                top: 0;
            }
            div.footer {
                position: fixed;
                bottom: 0;
            }

            .header, .header-space{
                height: 20px;
            }

            .footer, .footer-space {
                height: 100px;
            }

            .dados {
                /* padding-top: 120px !important; */
            }

            /*.content-block, .dados {
                page-break-inside: avoid;
            }*/

            html{
                margin: 0px;
            }

            body {
                width: 210mm;
                height: 297mm;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container my-1 content-block">
        <div class="header"></div>

        <table width="100%">
            <thead>
                <tr><td><div class="header-space">&nbsp;</div></td></tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="dados p-5">
                            <div class="row">
                                <div class="col-4 pr-5">
                                    <div class="text-center mb-5"><img class="img-fluid" style="width: 75%" src="{{asset('storage/'.$order->seller->store->logo_path)}}" alt="LOGO"></div>
                                </div>
                                <div class="col-8 pt-3">
                                    <p style="font-size: 1.2rem;">{{$order->seller->store->store_name}}</p>
                                    <p style="font-size: 1.2rem;">{{$order->seller->store->address}}, {{$order->seller->store->number}} - {{$order->seller->store->address2}}</p>
                                    <p style="font-size: 1.2rem;">{{$order->seller->store->city}} - {{$order->seller->store->state}} - {{$order->seller->store->post_code}}</p>
                                    <p style="font-size: 1.2rem;">{{$order->seller->store->phone1}} @isset($order->seller->store->phone2) - {{$order->seller->store->phone2}}@endisset</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 px-2">
                                    <p class="mb-0" style="font-size: 1.2rem;"><b>Nome:</b> {{$order->user_name}}</p>
                                    <p class="mb-0" style="font-size: 1.2rem;"><b>E-mail:</b> {{$order->user_email}}</p>
                                    @isset($order->customerAddress)
                                        <p class="mb-0" style="font-size: 1.2rem;"><b>Telefone:</b> {{$order->customerAddress->phone1}} @isset($order->customerAddress->phone2) - {{$order->customerAddress->phone2}}@endisset</p>
                                    @endisset
                                    @isset($order->serviceReservation->customerAddress)
                                        <p class="mb-0" style="font-size: 1.2rem;"><b>Telefone:</b> {{$order->serviceReservation->customerAddress->phone1}} @isset($order->serviceReservation->customerAddress->phone2) - {{$order->serviceReservation->customerAddress->phone2}}@endisset</p>
                                    @endisset
                                    <p class="mb-0" style="font-size: 1.2rem;"><b>CPF/CNPJ:</b> {{$order->user->cnpj_cpf}}</p>
                                </div>

                                <div class="col-6 px-2">
                                    <p class="mb-0 text-right" style="font-size: 1.2rem;"><b>Pedido:</b> {{$order_number}}</p>
                                    <p class="mb-0 text-right" style="font-size: 1.2rem;"><b>Data:</b> 
                                        @isset($order->shippingCustomer) {{date('d/m/Y', strtotime($order->shippingCustomer->created_at))}} @endisset
                                        @isset($order->serviceReservation) {{date('d/m/Y', strtotime($order->serviceReservation->created_at))}} @endisset
                                    </p>
                                    
                                    <p class="mb-0 text-right" style="font-size: 1.2rem;"><b>Forma de Pagamento:</b> 
                                        @switch($order->payment_method)
                                            @case('boleto')
                                                Boleto
                                                @break
                                            @case('pix')
                                                PIX
                                                @break
                                            @case('credit_card')
                                                Cartão de Crédito
                                                @break
                                            @default
                                                
                                        @endswitch
                                    </p>
                                </div>
                            </div>

                            @isset($order->shippingCustomer)
                                <div class="row mt-3">
                                    <div class="col-12 px-2">
                                        <p class="mb-0" style="font-size: 1.2rem;"><b>DADOS DA ENTREGA</b></p>
                                        <p class="mb-0" style="font-size: 1.2rem;">{{$order->shippingCustomer->address}}, {{$order->shippingCustomer->number}} - {{$order->shippingCustomer->address2}}</p>
                                        <p class="mb-0" style="font-size: 1.2rem;">{{$order->shippingCustomer->city}} - {{$order->shippingCustomer->state}} - {{$order->shippingCustomer->zip_code}}</p>
                                        @isset($order->note)<p class="mb-0" style="font-size: 1.2rem;"><b>OBS:</b> {{$order->note}}</p>@endisset
                                    </div>
                                </div>
                            @endisset

                            @isset($order->serviceReservation)
                                <div class="row mt-3">
                                    <div class="col-12 px-2">
                                        <p class="mb-0" style="font-size: 1.2rem;"><b>DADOS DA RESERVA</b></p>
                                        <p class="mb-0" style="font-size: 1.2rem;">{{date('d/m/Y', strtotime($order->serviceReservation->date_reservation_ini))}} {{$order->serviceReservation->date_reservation_fim ? date('d/m/Y', strtotime($order->serviceReservation->date_reservation_fim)) : ''}} {{$order->serviceReservation->hour_reservation ? $order->serviceReservation->hour_reservation : ''}}</p>
                                        @isset($order->note)<p class="mb-0" style="font-size: 1.2rem;"><b>OBS:</b> {{$order->note}}</p>@endisset
                                    </div>
                                </div>
                            @endisset

                            <div class="row mt-5">
                                <div class="col-12 px-2">
                                    <div class="mb-1 text-center"><h2 style="font-size: 2rem;letter-spacing: .4rem;">RESUMO DE COMPRA</h2></div>
                                    <div class="border border-dark mb-5" style="position: relative; left: 8%;width: 85%;"></div>

                                    <div>
                                        <table class="w-100">
                                            <thead>
                                                <tr>
                                                    <th class="bg-light px-2 py-3" style="font-size: 18px">#</th>
                                                    <th class="bg-light px-2 py-3" style="font-size: 18px">Produto</th>
                                                    <th class="bg-light px-2 py-3" style="font-size: 18px">Qtde</th>
                                                    <th class="bg-light px-2 py-3 text-right" style="font-size: 18px">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @isset($order->orderProducts)
                                                    @foreach ($order->orderProducts as $orderProduct)
                                                        <tr>
                                                            <td class="px-2 py-3">{{$orderProduct->sequence}}</td>
                                                            <td class="px-2 py-3">
                                                                <p class="mb-0"><b>{{$orderProduct->product_name}}</b></p>
                                                                <p>Peso {{$orderProduct->product_weight}}kg - Altura {{$orderProduct->product_height}}cm - Largura {{$orderProduct->product_width}}cm - Comprimento {{$orderProduct->product_length}}cm</p>
                                                                @php
                                                                    $count = 0;
                                                                    $count_total = count($orderProduct->attributes ?? []);
                                                                @endphp
                                                                @isset($orderProduct->attributes)
                                                                    @foreach ($orderProduct->attributes as $attribute)
                                                                        @php
                                                                            $count++;
                                                                        @endphp
                                                                        {{App\Models\Attribute::find($attribute['parent_id'])->name}}:{{$attribute['name']}} 
                                                                        @if ($count != $count_total)
                                                                            -
                                                                        @endif
                                                                    @endforeach
                                                                @endisset
                                                            </td>
                                                            <td class="px-2 py-3">{{$orderProduct->quantity}}</td>
                                                            <td class="px-2 py-3 text-right">R$ {{number_format($orderProduct->product_price,2,',','.')}}</td>
                                                        </tr>
                                                    @endforeach
                                                @endisset

                                                @isset($order->serviceReservation)
                                                    <tr>
                                                        <td class="px-2 py-3">1</td>
                                                        <td class="px-2 py-3">
                                                            <p class="mb-0"><b>{{$order->serviceReservation->service_name}} - #{{$order->serviceReservation->service_id}}</b></p>
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
                                                        </td>
                                                        <td class="px-2 py-3">{{$order->serviceReservation->service_quantity}}</td>
                                                        <td class="px-2 py-3 text-right">R$ {{number_format($order->serviceReservation->service_price,2,',','.')}}</td>
                                                    </tr>
                                                @endisset

                                                <tr>
                                                    <th class="border-top border-bottom border-dark px-2 py-1" style="font-size: 14px" colspan="3">TOTAL PRODUTOS</th>
                                                    <th class="border-top border-bottom border-dark px-2 py-1 text-right" style="font-size: 14px">R$ 
                                                        @isset($order->shippingCustomer) {{number_format($order->product_value,2,',','.')}} @endisset
                                                        @isset($order->serviceReservation) {{number_format($order->service_value,2,',','.')}} @endisset
                                                    </th>
                                                </tr>
                                                @isset($order->shippingCustomer)
                                                    <tr>
                                                        <th class="border-top border-bottom border-dark px-2 py-1" style="font-size: 14px" colspan="3">TOTAL FRETE</th>
                                                        <th class="border-top border-bottom border-dark px-2 py-1 text-right" style="font-size: 14px">R$ {{number_format($order->cost_freight,2,',','.')}}</th>
                                                    </tr>
                                                @endisset
                                                <tr>
                                                    <th class="border-top border-bottom border-dark px-2 py-3" style="font-size: 18px" colspan="3">TOTAL</th>
                                                    <th class="border-top border-bottom border-dark px-2 py-3 text-right" style="font-size: 18px">R$ 
                                                        @isset($order->shippingCustomer) {{number_format($order->total_value,2,',','.')}} @endisset
                                                        @isset($order->serviceReservation) {{number_format($order->service_value,2,',','.')}} @endisset
                                                    </th>
                                                </tr>
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr><td><div class="footer-space">&nbsp;</div></td></tr>
            </tfoot>
        </table>

        <div class="footer pt-3 pb-2 px-2" style="font-size: 14px;">
            <div class="row">
                <div class="col-12">
                    <div class="row justify-content-end px-5">
                        <div class="col-7 text-left border-top border-dark"><img class="img-fluid" style="width: 5%" src="{{asset('site/imgs/logo.png')}}" alt="LOGO">NASCEMOS SABENDO QUE PRA SEMEAR FUTURO, PLANTA-SE NEGÓCIO</div>
                        <div class="col-3 text-center border-top border-dark">comercial@raeasy.com</div>
                        <div class="col-2 text-right border-top border-dark">www.feitoporbiguacu.com</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>