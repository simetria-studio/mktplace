<?php

namespace App\Payments\MetodosPagamento;

use JetBrains\PhpStorm\ArrayShape;

class CartaoDeCredito extends MetodoPagamento
{
    public function __construct(
        public ?string $card_hash = null,
        public ?string $card_id = null,
        public ?string $card_holder_name = null,
        public ?string $card_expiration_date = null,
        public ?string $card_number = null,
        public ?string $card_cvv = null,
        public ?string $payment_method = null)
    {
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape(['card_holder_name' => "null|string", 'card_expiration_date' => "null|string", 'card_number' => "null|string", 'card_cvv' => "null|string", 'payment_method' => "null|string"])]
    public function toArray(): array
    {
        // todo implementar lógica para quando receber cardh_hash e ou card_id
        // por enquanto deixar passando todos os dados do cartão de crédito
        return [
            'card_holder_name' => $this->card_holder_name,
            'card_expiration_date' => $this->card_expiration_date,
            'card_number' => $this->card_number,
            'card_cvv' => $this->card_cvv,
            'payment_method' => $this->payment_method,
        ];
    }
}
