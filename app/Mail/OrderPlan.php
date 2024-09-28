<?php

namespace App\Mail;

use App\Models\SignedPlan;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlan extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order_number;
    public $send_email;
    public $order_type;
    public function __construct($order_number,$send_email,$order_type)
    {
        $this->order_number = $order_number;
        $this->send_email = $send_email;
        $this->order_type = $order_type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($order_type == "compra")
        {
            $subject = "Compra Efetuada";
        }
        else{
            $subject = "Pagamento Aprovado";
        }

        switch($this->send_email){
            case 'comprador':
                if($order_type == "compra"){
                    $msg1 = 'Obrigada por comprar na Biguaçu. Sua assinatura está sendo processado e nós te avisaremos quando o pagamento for aprovado.';
                }
                else{
                    $msg1 = 'Obrigada por comprar na Raesay. Seu pagamento foi aprovado na assinatura '. $order_number . '.';
                }
            break;
            case 'vendedor':
                if($order_type == "compra"){
                    $msg1 = 'Obrigada por comprar na Biguaçu. Sua assinatura está sendo processado e nós te avisaremos quando o pagamento for aprovado.';
                }
                else{
                    $msg1 = 'Obrigada por comprar na Raesay. Seu pagamento foi aprovado na assinatura '. $order_number . '.';
                }
            break;
            case 'biguacu':
                if($order_type == "compra")
                {
                    $msg1 = 'Obrigada por comprar na Biguaçu. Sua assinatura está sendo processado e nós te avisaremos quando o pagamento for aprovado.';
                }
                else{
                    $msg1 = 'Obrigada por comprar na Raesay. Seu pagamento foi aprovado na assinatura '. $order_number . '.';
                }
            break;
        }

        $orders = SignedPlan::where('id', $this->order_number)->with(['seller.store', 'produto', 'user'])->first();

        return $this->markdown('emails.orderPlan')->with([
            'order_number' => $this->order_number,
            'orders' => $orders,
            'msg1' => $msg1
        ])->subject($subject)->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
