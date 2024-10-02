<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $url_asaas       = '';
    public $access_token    = '';
    public $wallet_id       = '';

    public function __construct()
    {
        // if (url('') == 'https://feitoporbiguacu.com') {
        //     // $this->url_asaas = 'https://api.asaas.com/v3';
        //     // $this->access_token = '';
        //     // $this->wallet_id = 'e043a22b-1ceb-42c5-8e8a-9489ea68b9c9';
        // } else {
            $this->url_asaas = 'https://sandbox.asaas.com/api/v3';
            $this->access_token = env('ASAAS_ACCESS');
            $this->wallet_id = env('ASAAS_WALLET_ID');
        // }
    }

    public function dataConfig(Request $request, $data_type)
    {
        $data_type = \Str::camel($data_type);

        return $this->{'data' . ucfirst($data_type)}($request);
    }
}
