<?php

namespace App\Payments\MetodosPagamento;

use Carbon\Carbon;
use JetBrains\PhpStorm\ArrayShape;

class Pix extends MetodoPagamento
{
    public function __construct(public Carbon $pix_expiration_date)
    {
    }

    /**
     * @inheritDoc
     */
    #[ArrayShape(['payment_method' => "string", 'pix_expiration_date' => "string"])]
    public function toArray(): array
    {
        return [
            'payment_method' => 'pix',
            'pix_expiration_date' => $this->pix_expiration_date->format('Y-m-d')
        ];
    }
}
