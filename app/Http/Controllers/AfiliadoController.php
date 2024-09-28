<?php

namespace App\Http\Controllers;

use App\Models\AffiliateInfo;
use App\Models\AffiliateItem;
use App\Models\Produto;
use App\Models\Service;
use Illuminate\Http\Request;

class AfiliadoController extends Controller
{
    public function index()
    {
        $per_page_p = $_GET['per_page_p'] ?? 20;
        $items = AffiliateItem::paginate($perPage = $per_page_p, $columns = ['*'], $pageName = 'comissoes');
        $produtos = Produto::where('nome', '!=', '')->with(['affiliatedProduct', 'planPurchases'])->orderBy('nome', 'ASC')->get()->filter(function ($query) {
            $valid = true;
            if(!empty($query->affiliatedProduct)) $valid = false;
            if($query->planPurchases->count() > 0) $valid = false;

            return $valid;
        });
        $servicos = Service::with(['affiliatedService'])->orderBy('service_title', 'ASC')->get()->filter(function ($query) {
            return (empty($query->affiliatedService));
        });

        if(isset($_GET['order']) && $_GET['order'] != "")
        {
            $items = AffiliateItem::orderBy('name', $_GET['order'])->paginate($perPage = $per_page_p, $columns = ['*'], $pageName = 'comissoes');
        }
        if(isset($_GET['type']) && $_GET['type'] != "")
        {
            $items = AffiliateItem::where('reference_type', $_GET['type'])->paginate($perPage = $per_page_p, $columns = ['*'], $pageName = 'comissoes');
        }

        return view('painel.cadastros.indexAfiliado', get_defined_vars());
    }

    public function novaComissao(Request $request)
    {
        $rules = [
            'reference_type' => 'required',
            'price_type'     => 'required',
            'price'          => 'required',
        ];

        $customMessages = [
            'reference_type.required' => 'O Tipo de Comissão é obrigatório!',
            'price_type.required'     => 'O Tipo de Preço é obrigatório!',
            'price.required'          => 'O Campo Preço é obrigatório!',
        ];

        if($request->reference_type == 'product')
        {
            if(empty($request->produto_id)){
                $rules += ['produto_id[]' => 'required'];
                $customMessages += ['produto_id[].required' => 'Selecione ao menos 1 (um) produto.'];
            }
            else{
                $items = $request->produto_id;
            }
        }
        else if($request->reference_type == 'service')
        {
            if(empty($request->servico_id)){
                $rules += ['servico_id[]' => 'required'];
                $customMessages += ['servico_id[].required' => 'Selecione ao menos 1 (um) serviço.'];
            }
            else{
                $items = $request->servico_id;
            }
        }

        $this->validate($request, $rules, $customMessages);

        if(isset($items))
        {
            foreach($items as $item)
            {
                if($request->reference_type == 'product')
                {
                    $produto    = Produto::find($item);
                    $nome_item  = $produto->nome;
                }
                else{
                    $servico    = Service::find($item);
                    $nome_item  = $servico->service_title;
                }

                $valor_item = str_replace(['.',','],[',','.'], $request->price);

                $affiliateItem = [
                    'reference_id'   => $item,
                    'name'           => $nome_item,
                    'reference_type' => $request->reference_type,
                    'price_type'     => $request->price_type,
                    'price'          => $valor_item,
                    'status'         => 1
                ];

                AffiliateItem::create($affiliateItem);
            }
        }

        return response()->json(['']);
    }

    public function atualizarComissao(Request $request)
    {
        $rules = [
            'price_type'     => 'required',
            'price'          => 'required',
        ];

        $customMessages = [
            'price_type.required'     => 'O Tipo de Preço é obrigatório!',
            'price.required'          => 'O Campo Preço é obrigatório!',
        ];

        $this->validate($request, $rules, $customMessages);

        if($request->reference_type == 'product')
        {
            $produto    = Produto::find($request->reference_id);
            $nome_item  = $produto->nome;
        }
        else{
            $servico    = Service::find($request->reference_id);
            $nome_item  = $servico->service_title;
        }

        $valor_item = str_replace(['.',','],[',','.'], $request->price);

        $affiliateItem = [
            'price_type'     => $request->price_type,
            'price'          => $valor_item,
            'status'         => 1
        ];

        AffiliateItem::where('id', $request->id)->update($affiliateItem);

        return response()->json(['']);
    }

    public function excluirComissao(Request $request)
    {
        AffiliateItem::where('id', $request->id)->delete();

        return redirect()->back()->with('success', 'Comissão excluída com sucesso!');
    }
}