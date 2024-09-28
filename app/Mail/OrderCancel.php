<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancel extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order;
    public $reason;
    public function __construct($order, $reason)
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orderCancel')->with([
            'orders' => $this->order,
            'msg' => 'Cliente '.$this->order->user_name.' Solicitou o cancelamento do pedido '.$this->order->order_number.' Verificar!',
            'reason' => $this->reason
        ])->subject('Pedido '.$this->order->order_number.'/Solicitação de Cancelamento');
    }
}
