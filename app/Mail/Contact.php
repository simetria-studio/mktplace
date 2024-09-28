<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
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
        return $this->markdown('emails.contact')->with([
            'name' => $this->data['name'],
            'phone' => $this->data['phone'],
            'email' => $this->data['email'],
            'assunto' => $this->data['assunto'],
            'msg' => $this->data['mensagem']
        ])->subject($this->data['assunto'])->from($this->data['email'], $this->data['name']);
    }
}
