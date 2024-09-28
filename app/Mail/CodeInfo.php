<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CodeInfo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $method;
    public function __construct($user, $method)
    {
        $this->user = $user;
        $this->method = $method;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $assunto = '';
        $msg = '';
        $title_1 = '';
        $title_2 = '';
        switch($this->method){
            case 'env_code':
                $assunto = 'Codogo para excluir conta';
                $msg = 'Codigo: '.$this->user->code_delete;

                $title_1 = 'Caro usuario '.$this->user->name.', foi solicitado um codigo para exclusão total de sua conta';
                $title_2 = 'O codigo do mesmo segue abaixo.';
            break;
            case 'delete_account':
                $assunto = 'Conta apagada';
                $msg = 'Depois que sua conta foi apagada não tera mais como recuperalos, para poder utilizar nossos serviços novamente é preciso criar um nova conta.';

                $title_1 = 'Caro usuario '.$this->user->name.', Sua conta foi excluida junto com outros dados vinculados a ela!';
            break;
        }
        return $this->markdown('emails.code_info')->with([
            'title_1' => $title_1,
            'title_2' => $title_2,
            'msg' => $msg
        ])->subject($assunto)->from('naoresponder@biguacu.com.br', 'BIGUAÇU');
    }
}
