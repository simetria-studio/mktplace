<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderServiceFinish extends Mailable
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
        return $this->markdown('emails.orderServiceFinish')->with([
            'orders' => $this->order,
            'msg' => '
                <p style="text-align: center;"><h3><b>Que legal!! Soubemos que você já está com seus produtos em mãos.</b></h3></p>
                <p style="text-align: center;"><h3><b>A biguaçu trabalha com pequenos empresários e produtores rurais, por isso, quando você compra na biguaçu você está diretamente apoiando diversas famílias que obtem seu lucro e sustento atráves da venda de seus produtos.</b></h3></p>
                <p style="text-align: center;"><h3><b>Você pode ajudar ainda mais ao avaliar o seu pedido em nosso site, assim mais pessoas conhecerão os produtos e po-derão ajudar mais produtores/pequenos empresários.</b></h3></p>
                <br><br>
                <div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('perfil.rateProduct').'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Fazer Avaliação</a></div>
                <br><br>
                <p style="text-align: center;"><h3><b>Precisa falar conosco?</b></h3></p>
                <div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('contactus').'"style="text-decoration: none;padding: 8px 16px;font-weight: 700;background-color: #da5913;border-radius: 1rem;color: #000;">Fale Conosco</a></div>
                <p style="text-align: center;"><h3><b>Uhulll</b></h3></p>
                <p style="text-align: center;"><h3><b>Estamos dispostos a te ajudar!</b></h3></p>
            '
        ])->subject('Finalizado/Compra Recebida')->from('naoresponder@biguacu.com.br', 'Marketplace biguaçu');
    }
}
