<?php

namespace App\Mail;

use App\Models\SignedPlan;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlanCancel extends Mailable
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
        $orders = SignedPlan::where('id', $this->order_number)->with(['seller.store', 'produto', 'user'])->first();

        switch($this->send_email){
            case 'comprador':
                $msg1 = '<p style="text-align: center;"><h3><b>Ixiii, seu pagamento não foi aprovado.</b></h3></p>
                <p style="text-align: center;"><h3><b>Mas não tem problema, você pode definir outro método de pagamento em nosso site ou reemitir o boleto se for essa sua opção. Acesse a página do sua assinatura abaixo:</b></h3></p>
                <div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('perfil').'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Ir para minhas assinaturas</a></div>
                <p style="text-align: center;"><h3><b>Queremos resolver essa situação o mais rapido possível, clique abaixo e fale com um de nós:</b></h3></p>
                <div style="margin:20px 0;text-align: center;">
                    <a target="_blank" href="https://api.whatsapp.com/send?phone=5547996003481"style="text-decoration: none;padding: 8px 16px;font-weight: 700;background-color: #c6d30b;border-radius: 1rem;color: #000; margin-right: 10px">Whatssap 1</a>
                    <a target="_blank" href="https://api.whatsapp.com/send?phone=5547988476422"style="text-decoration: none;padding: 8px 16px;font-weight: 700;background-color: #c6d30b;border-radius: 1rem;color: #000; margin-right: 10px">Whatssap 2</a>
                </div>';
            break;
            case 'vendedor':
                $msg1 = '<p style="text-align: center;"><h3><b>Ixiii, a Assinatura não foi aprovado pelo meio de pagamento.</b></h3></p>
                <p style="text-align: center;"><h3><b>Mas fica tranquilo(a) que nós da equipe Biguaçu entraremos em contato para resolver esse problema, se após a nova tentativa da assinatura for aprovada, nós avisaremos aqui.</b></h3></p>';
            break;
            case 'biguacu':
                $msg1 = 'Compra não aprovado para o cliente '.$orders->user->name.' da Assinatura - '.$order_number.'.';
            break;
        }

        return $this->markdown('emails.orderPlan')->with([
            'order_number' => $this->order_number,
            'orders' => $orders,
            'msg1' => $msg1
        ])->subject($subject)->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
