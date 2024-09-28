<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.registerNewsletter')->with([
            'url'   => route('cancelNewsletter', 't='.base64_encode($this->data)),
        ])->subject('Confirmação de Inscrição em nossa Newsletter')->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
