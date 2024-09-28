<?php

namespace App\Mail;

use App\Models\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Orders extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order_number;
    public $send_email;
    public function __construct($order_number,$send_email)
    {
        $this->order_number = $order_number;
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
                $msg1 = 'Obrigada por comprar na Biguaçu. Seu pedido está sendo processado e nós te avisaremos quando o pagamento for aprovado.';
            break;
            case 'vendedor':
                $msg1 = 'Obrigada por comprar na Biguaçu. Seu pedido está sendo processado e nós te avisaremos quando o pagamento for aprovado.';
            break;
            case 'biguacu':
                $msg1 = 'Obrigada por comprar na Biguaçu. Seu pedido está sendo processado e nós te avisaremos quando o pagamento for aprovado.';
            break;
        }

        $orders = Order::where('order_number', $this->order_number)->with(['seller', 'orderProducts', 'shippingCustomer'])->first();

        return $this->markdown('emails.orders')->with([
            'order_number' => $this->order_number,
            'orders' => $orders,
            'msg1' => $msg1
        ])->subject('Compra Efetuada')->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
