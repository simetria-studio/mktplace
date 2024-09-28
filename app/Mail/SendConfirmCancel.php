<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendConfirmCancel extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order;
    public $send_email;
    public function __construct($order,$send_email)
    {
        $this->order = $order;
        $this->send_email = $send_email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch($this->send_email){
            case 'comprador':
                $msg1 = 'Solicitação de cancelamento confirmada.';
            break;
            case 'vendedor':
                $msg1 = 'Solicitação de cancelamento confirmada.';
            break;
            case 'biguacu':
                $msg1 = 'Solicitação de cancelamento confirmada.';
            break;
        }

        return $this->markdown('emails.sendconfirmcancel')->with([
            'orders' => $this->order,
            'msg1' => $msg1
        ])->subject('Cancelamento Confirmado')->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
