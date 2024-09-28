<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderNotifySellerShipping extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orderNotifySellerShipping')->with([
            'msg' => '
                <p><h3><b>Agora que você já enviou o pedido, não esqueça de acompanhar o transporte, se esse for o seu caso</b></h3></p>
                <p><h3><b>Quando o pedido for entregue ou o serviço finalizado, avance no painel como pedido FINALIZADO, dessa forma o consumidor receberá um link para avaliar seu produto.</b></h3></p>
                <div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('seller.dashboard').'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Painel do Vendedor</a></div>
                <p><h3><b>Parabéns pela venda.</b></h3></p>
            ',
        ])->subject('Notificação de envio de produto')->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
