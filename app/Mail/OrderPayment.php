<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPayment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order;
    public $reason;
    public $send_email;
    public function __construct($order, $reason, $send_email)
    {
        $this->order = $order;
        $this->reason = $reason;
        $this->send_email = $send_email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $assunto = 'Compra Aprovada';
        switch($this->send_email){
            case 'comprador':
                if($this->reason == 'canceled'){
                    $msg1 = '
                        <p style="text-align: center;"><h3><b>Ixiii, seu pagamento não foi aprovado.</b></h3></p>
                        <p style="text-align: center;"><h3><b>Mas não tem problema, você pode definir outro método de pagamento em nosso site ou reemitir o boleto se for essa sua opção. Acesse a página do seu pedido abaixo:</b></h3></p>
                        <div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('perfil').'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Ir para meus pedidos</a></div>
                        <p style="text-align: center;"><h3><b>Queremos resolver essa situação o mais rapido possível, clique abaixo e fale com um de nós:</b></h3></p>
                        <div style="margin:20px 0;text-align: center;">
                            <a target="_blank" href="https://api.whatsapp.com/send?phone=5547996003481"style="text-decoration: none;padding: 8px 16px;font-weight: 700;background-color: #c6d30b;border-radius: 1rem;color: #000; margin-right: 10px">Whatssap 1</a>
                            <a target="_blank" href="https://api.whatsapp.com/send?phone=5547988476422"style="text-decoration: none;padding: 8px 16px;font-weight: 700;background-color: #c6d30b;border-radius: 1rem;color: #000; margin-right: 10px">Whatssap 2</a>
                        </div>
                    ';
                    $assunto = 'Compra Não Aprovada';
                }else{
                    $msg1 = '
                        <p><h3><b>Uhulll</b></h3></p>
                        <p><h3><b>Você sabia que com essa compra você está apoiando um produtor ou um pequeno empresário?</b></h3></p>
                        <p><h3><b>Seu pagamento foi aprovado e seu pedido já está liberado em nosso site.</b></h3></p>
                    ';
                }
            break;
            case 'vendedor':
                if($this->reason == 'canceled'){
                    $msg1 = '
                        <p style="text-align: center;"><h3><b>Ixiii, o Pedido não foi aprovado pelo meio de pagamento.</b></h3></p>
                        <p style="text-align: center;"><h3><b>Mas fica tranquilo(a) que nós da equipe Biguaçu entraremos em contato para resolver esse problema, se após a nova tentativa o pedido for aprovado, nós avisaremos aqui.</b></h3></p>
                    ';
                    $assunto = 'Compra Não Aprovada';
                }else{
                    $msg1 = '
                        <p><h3><b>UHULL pedido aprovado e dindim no bolso.</b></h3></p>
                        <p><h3><b> - Não esqueça de uma etapa muito importante de VISUALIZAR O PEDIDO no painel, assim ficaremos tranquilos que você está ciente desse pedido.</b></h3></p>
                        <p><h3><b>Clique aqui para visualizar os detalhes no seu painel.</b></h3></p>
                        <div style="margin:20px 0;text-align: center;"><a target="_blank" href="'.route('seller.pedidos').'"style="text-decoration: none;padding: 12px 20px;font-weight: 700;background-color: #da5913;border-radius: 2rem;font-size: 1.3rem;color: #000;">Ir para meus pedidos</a></div>
                    ';
                }
            break;
            case 'biguacu':
                $msg1 = 'Compra não aprovado para o cliente '.$this->order->user_name.' do Pedido - '.$this->order->order_number;
                $assunto = 'Compra Não Aprovada';
            break;
        }

        return $this->markdown('emails.orderPayments')->with([
            'orders' => $this->order,
            'msg1' => $msg1,
            'send_mail' => $this->send_email,
        ])->subject($assunto)->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
