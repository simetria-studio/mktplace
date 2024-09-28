<?php

namespace App\Console\Commands;

use App\Models\SignedPlan;
use Illuminate\Console\Command;

class SignedPlanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signedplan:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualizando Assinaturas';

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
        $signed_plans = SignedPlan::where('status', 1)->where('finish', '<', date('Y-m-d', strtotime('+2 Days')))->get()->each(function ($query) {
            $pagarmeTransaction = \Http::withHeaders(get_header_conf_pm())->delete(url_pagarme('subscriptions', '/'.$query->pagarme_id), ['cancel_pending_invoices' => true])->object();
            \Log::info(collect($pagarmeTransaction)->toArray());
            $query->update(['status' => 3]);
        });
        return true;
    }
}
