<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Seller;
use App\Models\Produto;
use App\Models\Category;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class Sitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:simplexml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Faz um mapa dos link que o site possui';

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
        $sitemap = SitemapGenerator::create('https://feitoporbiguacu.com/')->getSitemap();
        foreach (Produto::whereHas('images')->where('status', 1)->get() as $produto){
            $sitemap = $sitemap->add(Url::create('https://feitoporbiguacu.com/produto/'.$produto->slug)
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.8));
        }
        foreach (Category::all() as $category){
            $sitemap = $sitemap->add(Url::create('https://feitoporbiguacu.com/categoria/'.$category->slug)
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.8));
        }
        foreach (Seller::where('wallet_id', '!=', null)->whereHas('store')->get() as $seller){
            $sitemap = $sitemap->add(Url::create('https://feitoporbiguacu.com/loja-vendedor/'.$seller->store->store_slug)
            ->setLastModificationDate(Carbon::yesterday())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.8));
        }
        $sitemap = $sitemap->writeToFile('public/sitemap.xml');
        return true;
    }
}
