@extends('layouts.site')

@section('container')
    <div class="container my-2 agradecimento">
        <div class="row">
            <div class="col-sm-10 col-md-10 bubble speech">
                <h2>OBRIGADA POR SUA COMPRA!</h2>
                <p>Seu pedido está sendo processado, e pode ficar tranquila(o) que no próximo passo, eu mando um e-mail...</p>
                <p>No botão "ver pedido", você pode conferir os dados e o status do seu pedido.</p>
                <p>Ahhh, e qualquer dúvida ou problema tem um time especializado no "fale conosco".</p>
                <p>Até logo <i class="fas fa-heart" style="color: #ff0000;"></i></p>
            </div>

            <div class="col-sm-3 col-md-4">
                {{-- <img src="{{ asset('site/imgs/avatar-mulher.png')}}" alt="Mascote Raesy"> --}}
            </div>
            <div class="col-sm-9 col-md-8">
                <div class="row">
                    <div class="my-2 col-12">
                        @switch($pedidoArray[0])
                            @case('P')
                                <a href="{{route('perfil.pedido', $pedidoArray[1])}}" class="btn btn-1">VER PEDIDO</a>
                                @break
                            @case('S')
                                <a href="{{route('perfil.servico.pedido', $pedidoArray[1])}}" class="btn btn-1">VER PEDIDO</a>
                                @break
                            @case('PN')
                                <a href="{{route('perfil.assinaturaDetalhe', $pedidoArray[1])}}" class="btn btn-1">VER PLANO</a>
                                @break
                            @default
                        @endswitch
                    </div>
                
                @if($order->payment_method == 'boleto')
                    <div class="my-2 col-12">
                        <a href="{{$pedido_asaas->bankSlipUrl}}" target="_blank" class="btn btn-2">IMPRIMIR BOLETO</a>
                    </div>
                @elseif($order->payment_method == 'pix')
                    <div class="my-2 col-12 qrcode">
                        <button class="btn btn-2 mb-3" onclick="copyQrCode()">COPIAR CHAVE PIX</button>
                        <br>
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::generate($pedido_pagarme->charges[0]->last_transaction->qr_code) !!}</div>
                        <br>
                        <script>
                            function copyQrCode() {
                                navigator.clipboard.writeText("{{$pedido_pagarme->charges[0]->last_transaction->qr_code}}");
                            }
                        </script>
                    </div>
                @endif
                </div>

                <div class="row mt-5 mx-3">
                    <div class="col-12 d-flex">
                        <p style="font-size: 1rem; font-weight: 800; margin-right: 10px;">Acompanhe as novidades da Vapu-vapu em nossas redes sociais</p>
                        {{-- <a class="btn-social" href="https://www.instagram.com/raeasy_/" target="_blank"><i class="fab fa-instagram-square" style="color: #59981A; font-size: 2rem; margin-right: 10px;"></i></a> --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="alert-success text-center py-3 rounded"><h2>Obrigado pela compra!</h2></div> --}}

    </div>
    <input type="hidden" class="itens" value="{{$dataItems->toJson()}}">
@endsection

@if ((session()->get('pedido-agradecimento') ?? '') !== $pedidoAnterior)
    @section('js')
        <script>
            gtag('event', 'purchase', {
                affiliation: 'Google Store',
                // coupon: 'SUMMER_FUN',
                currency: 'BRL',
                items: JSON.parse($('.itens').val()),
                transaction_id: `{{$pedidoArray[0]}}_{{$pedidoArray[1]}}`,
                shipping: parseFloat(`{{$frete}}`) || 0,
                value: parseFloat(`{{$total}}`) || 0,
                tax: 0
            });
        </script>
    @endsection
@endif