<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipping extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order;
    public $tracking_id;
    public $send_type;
    public function __construct($order,$tracking_id, $send_type)
    {
        $this->order = $order;
        $this->tracking_id = $tracking_id;
        $this->send_type = $send_type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch($this->send_type){
            case 'melhor_envio':
                $btn = route('perfil.pedido', $this->order->orderParentInverse->order_number);
                $btn = '<div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('perfil.pedido', $this->order->orderParentInverse->order_number).'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Rastreamento</a></div>';
            break;
            case 'envio_proprio':
                $btn = route('perfil.pedido', $this->order->orderParentInverse->order_number);
                $btn = '<div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('perfil.pedido', $this->order->orderParentInverse->order_number).'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Rastreamento</a></div>';
            break;
            case 'envio_url':
                $return = '';
                if(!empty($this->tracking_id)){
                    $tracking = explode(';', $this->tracking_id);

                    if(empty(str_replace('link=', '', $tracking[1]))){
                        $return = str_replace('code=', '', $tracking[0]);
                    }else{
                        $return = '<a target="_blank" href="'.str_replace('link=', '', $tracking[1]).'" class="btn btn-sm btn-primary" style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">'.str_replace('code=', '', $tracking[0]).'</a>';
                    }
                }
                $btn = $tracking ?? $this->tracking_id;
                // $btn = '<div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.$this->tracking_id.'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Rastreamento</a></div>';
                $btn = '<div style="margin:20px 0;text-align: center;">'.$return.'</div>';
            break;
        }

        return $this->markdown('emails.orderShipping')->with([
            'orders' => $this->order,
            'msg1' => '
                <p><h3><b>Temos uma ótima notícia!! O vendedor já está com os itens separando pronto para fazer o envio, logo você estará com seu pedido</b></h3></p>
                <p><h3><b>No botão abaixo você poderá conferir o rastreamento do seu pedido, e assim acompanhar as etapas até ele chegar aí.</b></h3></p>
                '.$btn.'
                <p><h3><b>Qualquer dúvida, problema ou sugestão, não hesite em nos chamar.</b></h3></p>
                <p><h3><b>Estamos dispostos a te ajudar!</b></h3></p>
                <div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('contactus').'"style="text-decoration: none;padding: 8px 16px;font-weight: 700;background-color: #da5913;border-radius: 1rem;color: #000;">Fale Conosco</a></div>
            '
        ])->subject('Aviso de Envio');
    }
}
