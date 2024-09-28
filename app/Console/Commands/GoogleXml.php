<?php

namespace App\Console\Commands;

use App\Models\Produto;
use App\Models\Service;
use Illuminate\Console\Command;

class GoogleXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:xmlgenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geração de xml do google';

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
        $item = file_get_contents('public/xmls/facebookxmlitem.txt');
        $xml = file_get_contents('public/xmls/facebookxml.txt');
        $itens = '';

        foreach (Produto::whereHas('images')->where('perecivel', 0)->where('status', 1)->get() as $produto){
            $item_edit = $item;
            $item_edit = str_replace('{{ID}}', 'P-'.$produto->id, $item_edit);
            $item_edit = str_replace('{{TITLE}}', str_replace(['&'], '', $produto->nome), $item_edit);
            $item_edit = str_replace('{{DESCRIPTION}}', strip_tags(str_replace(['&nbsp;'], '', $produto->descricao_curta)), $item_edit);
            $item_edit = str_replace('{{LINK}}', 'https://feitoporbiguacu.com/produto/'.$produto->slug, $item_edit);
            $item_edit = str_replace('{{IMAGE_LINK}}', $produto->images->sortBy('position')->first()->caminho, $item_edit);
            if (empty($product->stock_controller)){
                $item_edit = str_replace('{{AVAILABILITY}}', 'in stock', $item_edit);
            }else{
                $item_edit = str_replace('{{AVAILABILITY}}', ($produto->stock_controller == 'true' ? (!empty($produto->stock) ? ($produto->stock > 0 ? 'in stock' : 'out of stock') : 'available for order') : 'available for order'), $item_edit);
            }

            $item_edit = str_replace('{{PRICE}}', number_format($produto->preco, 2, '.', ''), $item_edit);
            // $item_edit = str_replace('{{GOOGLE_PRODUCT_CATEGORY}}', $produto->categories[0]->category->name, $item_edit);
            $item_edit = str_replace('{{GOOGLE_PRODUCT_CATEGORY}}', 'Alimentos, bebidas e tabaco', $item_edit);
            $itens .= $item_edit."\n";
        }
        foreach (Service::whereHas('images')->where('status', 1)->get() as $service){
            $item_edit = $item;
            $item_edit = str_replace('{{ID}}', 'S-'.$service->id, $item_edit);
            $item_edit = str_replace('{{TITLE}}', str_replace(['&'], '', $service->service_title), $item_edit);
            $item_edit = str_replace('{{DESCRIPTION}}', strip_tags(str_replace(['&nbsp;',], '', $service->short_description)), $item_edit);
            $item_edit = str_replace('{{LINK}}', 'https://feitoporbiguacu.com/servico/'.$service->service_slug, $item_edit);
            $item_edit = str_replace('{{IMAGE_LINK}}', $service->images[0]->caminho, $item_edit);
            if (empty($product->vaga_controller)){
                $item_edit = str_replace('{{AVAILABILITY}}', 'in stock', $item_edit);
            }else{
                $item_edit = str_replace('{{AVAILABILITY}}', ($service->vaga_controller == 'true' ? (!empty($service->vaga) ? ($service->vaga > 0 ? 'in stock' : 'out of stock') : 'available for order') : 'available for order'), $item_edit);
            }

            $item_edit = str_replace('{{PRICE}}', number_format($service->preco, 2, '.', ''), $item_edit);
            // $item_edit = str_replace('{{GOOGLE_PRODUCT_CATEGORY}}', $service->categories[0]->category->name, $item_edit);
            $item_edit = str_replace('{{GOOGLE_PRODUCT_CATEGORY}}', 'Alimentos, bebidas e tabaco', $item_edit);
            $itens .= $item_edit."\n";
        }

        $xml = str_replace('{{item}}',$itens, $xml);

        $file = fopen("public/google.xml", "w");
        fwrite($file, $xml);
        fclose($file);

        return true;
    }
}
