<?php

namespace App\Console\Commands;

use App\Models\ApiIntegration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class melhorenvio extends Command
{
    public $url_me = 'https://melhorenvio.com.br/';
    public $client_id_me = '15355';
    public $client_secret_me = '07sTyqoGUcjWAwCnh6UIeZUadqftDTgNP7YB7Gk5';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:melhorenvio {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Roda Funções referente ao Melhor Envio';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = $this->option('service') ?? '';

        switch($service){
            case 'updatetoken':
                $this->info("[".date('Y-m-d H:i:s')."] Inicio do processo de atualização!");

                $url_me = $this->url_me;
                $data = [
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->client_id_me,
                    'client_secret' => $this->client_secret_me,
                    'refresh_token' => '',
                ];

                ApiIntegration::where('api_name', 'Melhor-Envio-Token')->where('expires_in', '<=', date('Y-m-d', strtotime('+2Days')))->each(function($query) use($data, $url_me){
                    $data['refresh_token'] = $query->refresh_token;

                    $oauth = Http::withHeaders([
                        'Accept' => 'application/json',
                        'User-Agent' => 'Biguaçu (ellernetpar@gmail.com.br)'
                    ])->asForm()->post($url_me.'oauth/token',$data)->object();

                    if(!($oauth->error ?? null)){
                        $query->update([
                            'token'         => 'Bearer '.$oauth->access_token,
                            'refresh_token' => $oauth->refresh_token,
                            'expires_in'    => date('Y-m-d H:i:s', strtotime('+ 2592000 Seconds')),
                        ]);
                    }
                });

                $this->info("[".date('Y-m-d H:i:s')."] Fim do precesso de atualização!");
                break;
            default:
                $this->info("[".date('Y-m-d H:i:s')."] Precisa escolher um serviço para rodar o processo");
                break;
        }
    }
}
