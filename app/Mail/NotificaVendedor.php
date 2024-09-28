<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificaVendedor extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order;
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notificaVendedor')->with([
            'orders' => $this->order,
            'msg' => '
                <p style="text-align: center;"><h3><b>Você ainda não VISUALIZOU O PEDIDO.</b></h3></p>
                <p style="text-align: center;"><h3><b>Além de ser importante para o nosso controle, evita atrasos ou quaisquer problemas.</b></h3></p>
                <div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('seller.pedidos').'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Ir para meus pedidos</a></div>
            ',
        ])->subject('PEDIDO MERECE ATENÇÃO')->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
