<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FunctionRunGeneral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'general:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Função para coisas gerais';

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
        $imagens = \App\Models\ImagensService::each(function($query){
            $pasta = explode('/', $query->caminho);
            unset($pasta[0]);
            unset($pasta[1]);
            unset($pasta[2]);
            $pasta = str_replace('storage', 'public', implode('/', $pasta));
            $query->update(['pasta' => $pasta]);
        });
        return true;
    }
}
