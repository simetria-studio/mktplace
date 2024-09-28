<?php

namespace App\Http\Controllers\Seller;

use App\Models\OwnTransport;
use App\Models\Bairro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OwnTransportController extends Controller
{
    public function ownTransport()
    {
        $transportes = OwnTransport::where('seller_id', auth()->guard('seller')->user()->id)->get();
        return view('seller.fretes.ownTransport', get_defined_vars());
    }

    public function novoTransporte(Request $request)
    {
        $transporte['seller_id'] = auth()->guard('seller')->user()->id;
        $transporte['estado'] = $request->estado;
        $transporte['cidade'] = isset($request->em_todas_cidades) ? 'todas as cidades' : $request->cidade;
        $transporte['bairro'] = isset($request->em_todas_cidades) ? 'todos os bairros' : (isset($request->toda_cidade) ? 'todos os bairros' : $request->bairro);
        $transporte['toda_cidade'] = isset($request->em_todas_cidades) ? 1 : (isset($request->toda_cidade) ? 1 : 0);
        $transporte['em_todas_cidades'] = isset($request->em_todas_cidades) ? 1 : 0;
        $transporte['valor_entrega'] = str_replace(['.',','], ['','.'], $request->valor_frete);
        $transporte['tempo_entrega'] = $request->tempo_entrega;
        $transporte['tempo'] = $request->tempo;
        $transporte['semana'] = $request->semana ?? null;
        $transporte['descricao'] = $request->descricao ?? null;
        $transporte['frete_gratis'] = isset($request->frete_gratis) ? 1 : 0;
        $transporte['valor_minimo'] = isset($request->frete_gratis) ? $request->valor_minimo : 0;

        $dados_retorno = '';
        if(isset($request->bairro)){
            foreach($request->bairro as $bairro){
                if($bairro){
                    $transporte['bairro'] = $bairro;
                    $transporte_proprio = OwnTransport::create($transporte);
    
                    $dados_retorno .= '<tr class="tr-id-'.$transporte_proprio->id.'">
                        <td>'.$transporte_proprio->id.'</td>
                        <td>'.$transporte_proprio->estado.'</td>
                        <td>'.$transporte_proprio->cidade.'</td>
                        <td>'.$transporte_proprio->bairro.'</td>
                        <td>R$ '.$transporte_proprio->valor_entrega.'</td>
                        <td>'.$transporte_proprio->tempo_entrega.' '.($transporte_proprio->tempo == 'H' ? 'Horas' : 'Dias').'</td>
                        <td>
                            <div class="btn-group" role="group" aria-label="">
                                <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarTransporte" data-dados=\''.json_encode($transporte_proprio).'\'><i class="fas fa-edit"></i> Alterar</a>
                                <a href="#" class="btn btn-danger btn-xs btn-excluir-categoria" data-toggle="modal" data-target="#excluirTransporte" data-dados=\''.json_encode($transporte_proprio).'\'><i class="fas fa-trash"></i> Apagar</a>
                            </div>
                        </td>
                    </tr>';
                }
            }
        }else{
            $transportes = OwnTransport::create($transporte);

            $dados_retorno .= '<tr class="tr-id-'.$transportes->id.'">
                    <td>'.$transportes->id.'</td>
                    <td>'.$transportes->estado.'</td>
                    <td>'.$transportes->cidade.'</td>
                    <td>'.$transportes->bairro.'</td>
                    <td>R$ '.$transportes->valor_entrega.'</td>
                    <td>'.$transportes->tempo_entrega.' '.($transportes->tempo == 'H' ? 'Horas' : 'Dias').'</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="">
                            <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarTransporte" data-dados=\''.json_encode($transportes).'\'><i class="fas fa-edit"></i> Alterar</a>
                            <a href="#" class="btn btn-danger btn-xs btn-excluir-categoria" data-toggle="modal" data-target="#excluirTransporte" data-dados=\''.json_encode($transportes).'\'><i class="fas fa-trash"></i> Apagar</a>
                        </div>
                    </td>
                </tr>';
        }

        return response()->json([
            'table' => $dados_retorno
        ]);
    }

    public function editarTransporte(Request $request)
    {
        $transporte['seller_id'] = auth()->guard('seller')->user()->id;
        $transporte['estado'] = $request->edit_estado;
        $transporte['cidade'] = isset($request->em_todas_cidades) ? 'todas as cidades' : $request->edit_cidade;
        $transporte['bairro'] = isset($request->em_todas_cidades) ? 'todos os bairros' : (isset($request->toda_cidade) ? 'todos os bairros' : $request->edit_bairro);
        $transporte['toda_cidade'] = isset($request->em_todas_cidades) ? 1 : (isset($request->toda_cidade) ? 1 : 0);
        $transporte['em_todas_cidades'] = isset($request->em_todas_cidades) ? 1 : 0;
        $transporte['valor_entrega'] = str_replace(['.',','], ['','.'], $request->valor_frete);
        $transporte['tempo_entrega'] = $request->tempo_entrega;
        $transporte['tempo'] = $request->tempo;
        $transporte['semana'] = $request->semana ?? null;
        $transporte['descricao'] = $request->descricao ?? null;
        $transporte['frete_gratis'] = isset($request->frete_gratis) ? 1 : 0;
        $transporte['valor_minimo'] = isset($request->frete_gratis) ? $request->valor_minimo : 0;

        $transporte = OwnTransport::where('id', $request->id)->update($transporte);
        $transporte = OwnTransport::where('id', $request->id)->first();

        return response()->json([
            'tb_id' => $transporte->id,
            'tb_up' => '
                <td>'.$transporte->id.'</td>
                <td>'.$transporte->estado.'</td>
                <td>'.$transporte->cidade.'</td>
                <td>'.$transporte->bairro.'</td>
                <td>R$ '.$transporte->valor_entrega.'</td>
                <td>'.$transporte->tempo_entrega.' '.($transporte->tempo == 'H' ? 'Horas' : 'Dias').'</td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarTransporte" data-dados=\''.json_encode($transporte).'\'><i class="fas fa-edit"></i> Alterar</a>
                        <a href="#" class="btn btn-danger btn-xs btn-excluir-categoria" data-toggle="modal" data-target="#excluirTransporte" data-dados=\''.json_encode($transporte).'\'><i class="fas fa-trash"></i> Apagar</a>
                    </div>
                </td>'
        ]);
    }

    public function excluirTransporte(Request $request)
    {
        $transporte = OwnTransport::find($request->id);
        $transporte->delete();

        return response()->json([
            'tb_trash' => $request->id
        ]);
    }

    public function bairrosStore(Request $request)
    {
        $verif = Bairro::where('localidade_estado_id', $request->estado_id)->where('localidade_municipio_id', $request->cidade_id)->where('titulo', $request->bairro_name)->get();

        if($verif->count() > 0) return response()->json(['msg_alert' => 'Bairro ('.$request->bairro.') ja existente', 'icon_alert' => 'warning'], 412);

        $bairro['localidade_estado_id'] = $request->estado_id;
        $bairro['localidade_municipio_id'] = $request->cidade_id;
        $bairro['titulo'] = $request->bairro_name;

        $bairro = Bairro::insert($bairro);

        return response()->json([]);
    }

    public function bairrosEdit(Request $request)
    {
        // return response()->json(isset($request->toda_cidade), 412);
        $verif = Bairro::where('localidade_estado_id', $request->estado_id)->where('localidade_municipio_id', $request->cidade_id)->where('titulo', $request->bairro_name)->get();

        if($verif->count() > 0) return response()->json(['msg_alert' => 'Bairro ('.$request->bairro.') ja existente', 'icon_alert' => 'warning'], 412);

        $bairro['localidade_estado_id'] = $request->edit_estado_id;
        $bairro['localidade_municipio_id'] = $request->edit_cidade_id;
        $bairro['titulo'] = $request->edit_bairro_name;

        $bairro = Bairro::where('id', $request->id)->update($bairro);
        $bairro = Bairro::where('id', $request->id)->first();

        return response()->json([
            'tb_id' => $bairro->id,
            'tb_up' => '
                <td>'.$bairro->id.'</td>
                <td>'.$bairro->estado->titulo.'</td>
                <td>'.$bairro->cidade->titulo.'</td>
                <td>'.$bairro->titulo.'</td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarBairro" data-dados=\''.json_encode($bairro).'\'><i class="fas fa-edit"></i> Alterar</a>
                        <a href="#" class="btn btn-danger btn-xs btn-excluir-categoria" data-toggle="modal" data-target="#excluirBairro" data-dados=\''.json_encode($bairro).'\'><i class="fas fa-trash"></i> Apagar</a>
                    </div>
                </td>'
        ]);
    }

    public function bairrosDestroy(Request $request)
    {
        $transporte = Bairro::find($request->id);
        $transporte->delete();

        return response()->json([
            'tb_trash' => $request->id
        ]);
    }
}
