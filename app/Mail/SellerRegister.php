<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerRegister extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $vendedor;
    public function __construct($vendedor)
    {
        $this->vendedor = $vendedor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.sellerRegister')->with(['vendedor' => $this->vendedor])->subject('Novo Vendedor Registrado')->from('naoresponder@biguacu.com.br', 'Marketplace Biguaçu');
    }
}
