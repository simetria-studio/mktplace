<?php

namespace App\Payments\MetodosPagamento;

use JetBrains\PhpStorm\ArrayShape;

class Boleto extends MetodoPagamento
{

    public function __construct(public string $boleto_instructions = '')
    {
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape(['payment_method' => "string", 'boleto_instructions' => "string"])]
    public function toArray(): array
    {
        return [
            'payment_method' => 'boleto',
            'boleto_instructions' => $this->boleto_instructions
        ];
    }
}
