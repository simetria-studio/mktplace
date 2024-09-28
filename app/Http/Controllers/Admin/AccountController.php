<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Store;
use App\Models\Seller;
use Illuminate\Http\Request;

use App\Models\CustomerAddress;
use App\Models\SellerAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Intervention\Image\ImageManagerStatic as Image;

class AccountController extends Controller
{
    /** ############## CONTAS DE USUARIOS ################### */
    public function novoCliente(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $account = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cnpj_cpf' => $request->cnpj_cpf,
            'password' => Hash::make($request->password),
            'permission' => 0,
        ]);

        return response()->json([
            'table' => '<tr class="tr-id-'.$account->id.'">
                <td>'.$account->id.'</td>
                <td>'.$account->name.'</td>
                <td>'.$account->cnpj_cpf.'</td>
                <td>'.$account->email.'</td>
                <td><a href="'.url('admin/cliente/enderecos-cliente', $account->id).'" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Visualizar</a></td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#atualizarSenha" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Trocar Senha</a>
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarCliente" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Editar Cliente</a>
                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirConta" data-dados=\''.json_encode($account).'\'><i class="fas fa-trash"></i> Apagar Conta</a>
                    </div>
                </td>
            </tr>'
        ]);
    }

    public function atualizarCliente(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'unique:users,email,'.$request->id,
        ]);

        $accounts['name'] = $request->name;
        $accounts['email'] = $request->email;
        $accounts['cnpj_cpf'] = $request->cnpj_cpf;

        User::where('id', $request->id)->update($accounts);
        $account = User::where('id', $request->id)->first();

        return response()->json([
            'tb_id' => $account->id,
            'tb_up' => '
                <td>'.$account->id.'</td>
                <td>'.$account->name.'</td>
                <td>'.$account->cnpj_cpf.'</td>
                <td>'.$account->email.'</td>
                <td><a href="'.url('admin/cliente/enderecos-cliente', $account->id).'" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Visualizar</a></td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#atualizarSenha" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Trocar Senha</a>
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarCliente" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Editar Cliente</a>
                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirConta" data-dados=\''.json_encode($account).'\'><i class="fas fa-trash"></i> Apagar Conta</a>
                    </div>
                </td>'
        ]);
    }

    public function excluirCliente(Request $request)
    {
        User::where('id', $request->id)->delete();
        CustomerAddress::where('user_id', $request->id)->delete();

        return response()->json([
            'tb_trash' => $request->id
        ]);
    }

    public function atualizarSenhaCliente(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        User::where('id',$request->id)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => 'Senha Atualizada com Sucesso!!']);
    }

    /** ############## ENDEREÇOS DE USUARIOS ################### */
    public function novoEnderecoCliente(Request $request)
    {
        $request->validate([
            'post_code'       => 'required',
            'address'   => 'required',
            'number'    => 'required',
            'address2'  => 'required',
            'state'     => 'required',
            'city'      => 'required',
            'phone2'    => 'required',
        ]);

        $addresses['user_id']       = $request->user_id;
        $addresses['post_code']     = $request->post_code;
        $addresses['address']       = $request->address;
        $addresses['number']        = $request->number;
        $addresses['complement']    = $request->complement;
        $addresses['address2']      = $request->address2;
        $addresses['state']         = $request->state;
        $addresses['city']          = $request->city;
        $addresses['phone1']        = $request->phone1;
        $addresses['phone2']        = $request->phone2;

        if(CustomerAddress::where('user_id', $request->user_id)->get()->count() == 4) return response()->json(['msg_alert' => 'Maximo 4 endereço nesse usuario!', 'icon_alert' => 'warning'], 412);

        $address = CustomerAddress::create($addresses);

        return response()->json([
            'table' => '<tr class="tr-id-'.$address->id.'">
                <td>'.$address->id.'</td>
                <td>'.$address->post_code.'</td>
                <td>'.$address->address.' - '.$address->number.'</td>
                <td>'.$address->complement.'</td>
                <td>'.$address->address2.'</td>
                <td>'.$address->city.'</td>
                <td>'.$address->state.'</td>
                <td>'.$address->phone1.' // '.$address->phone2.'</td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarEndereco" data-dados=\''.json_encode($address).'\'><i class="fas fa-edit"></i> Editar</a>
                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirEndereco" data-dados=\''.json_encode($address).'\'><i class="fas fa-trash"></i> Apagar</a>
                    </div>
                </td>
            </tr>'
        ]);
    }

    public function atualizarEnderecoCliente(Request $request)
    {
        $request->validate([
            'post_code' => 'required',
            'address'   => 'required',
            'number'    => 'required',
            'address2'  => 'required',
            'state'     => 'required',
            'city'      => 'required',
            'phone2'    => 'required',
        ]);

        $addresses['post_code']     = $request->post_code;
        $addresses['address']       = $request->address;
        $addresses['number']        = $request->number;
        $addresses['complement']    = $request->complement;
        $addresses['address2']      = $request->address2;
        $addresses['state']         = $request->state;
        $addresses['city']          = $request->city;
        $addresses['phone1']        = $request->phone1;
        $addresses['phone2']        = $request->phone2;

        CustomerAddress::where('id', $request->id)->update($addresses);
        $address = CustomerAddress::where('id', $request->id)->first();

        return response()->json([
            'tb_id' => $address->id,
            'tb_up' => '
                <td>'.$address->id.'</td>
                <td>'.$address->post_code.'</td>
                <td>'.$address->address.' - '.$address->number.'</td>
                <td>'.$address->complement.'</td>
                <td>'.$address->address2.'</td>
                <td>'.$address->city.'</td>
                <td>'.$address->state.'</td>
                <td>'.$address->phone1.' // '.$address->phone2.'</td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarEndereco" data-dados=\''.json_encode($address).'\'><i class="fas fa-edit"></i> Editar</a>
                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirEndereco" data-dados=\''.json_encode($address).'\'><i class="fas fa-trash"></i> Apagar</a>
                    </div>
                </td>'
        ]);
    }

    public function excluirEnderecoCliente(Request $request)
    {
        CustomerAddress::where('id', $request->id)->delete();

        return response()->json([
            'tb_trash' => $request->id
        ]);
    }

    /** ############## CONTAS DE VENDEDORES ################### */
    public function novoVendedor(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:sellers',
            'cnpj_cpf' => 'required|string',
            'phone' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $account = Seller::create([
            'name' => $request->name,
            'email' => $request->email,
            'cnpj_cpf' => $request->cnpj_cpf,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'permission' => 0,
            'status' => 1,
        ]);

        return response()->json([
            'table' => '<tr class="tr-id-'.$account->id.'">
                <td>'.$account->id.'</td>
                <td>'.$account->name.'</td>
                <td>'.$account->cnpj_cpf.'</td>
                <td>'.$account->email.'</td>
                <td><button type="button" class="btn btn-sm '.($account->status == 0 ? 'btn-danger' : 'btn-success').' btn-update-status-vendedor" data-id="'.$account->id.'" data-status="'.$account->status.'">'.($account->status == 0 ? 'Inativo' : 'Ativo').'</button></td>
                <td><a href="'.url('admin/cliente/enderecos-vendedor', $account->id).'" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Visualizar</a></td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#atualizarSenhaVendedor" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Trocar Senha</a>
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarVendedor" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Editar Cliente</a>
                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirVendedor" data-dados=\''.json_encode($account).'\'><i class="fas fa-trash"></i> Apagar Conta</a>
                    </div>
                </td>
            </tr>'
        ]);
    }

    public function vendedorResponsavel(Request $request)
    {
        Seller::find($request->id)->update(['responsavel_id' => $request->responsavel_id]);

        return response()->json('okay', 200);
    }

    public function atualizarVendedor(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'unique:sellers,email,'.$request->id,
        ]);

        $accounts['name'] = $request->name;
        $accounts['email'] = $request->email;
        $accounts['cnpj_cpf'] = $request->cnpj_cpf;
        $accounts['phone'] = $request->phone;

        Seller::where('id', $request->id)->update($accounts);
        $account = Seller::where('id', $request->id)->first();

        return response()->json([
            'tb_id' => $account->id,
            'tb_up' => '
                <td>'.$account->id.'</td>
                <td>'.$account->name.'</td>
                <td>'.$account->cnpj_cpf.'</td>
                <td>'.$account->email.'</td>
                <td><button type="button" class="btn btn-sm '.($account->status == 0 ? 'btn-danger' : 'btn-success').' btn-update-status-vendedor" data-id="'.$account->id.'" data-status="'.$account->status.'">'.($account->status == 0 ? 'Inativo' : 'Ativo').'</button></td>
                <td><a href="'.url('admin/cliente/enderecos-vendedor', $account->id).'" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Dados</a></td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#atualizarSenhaVendedor" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Trocar Senha</a>
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarVendedor" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Editar Cliente</a>
                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirVendedor" data-dados=\''.json_encode($account).'\'><i class="fas fa-trash"></i> Apagar Conta</a>
                    </div>
                </td>'
        ]);
    }

    public function excluirVendedor(Request $request)
    {
        Seller::where('id', $request->id)->delete();
        SellerAddress::where('user_id', $request->id)->delete();

        return response()->json([
            'tb_trash' => $request->id
        ]);
    }

    public function atualizarSenhaVendedor(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        Seller::where('id',$request->id)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => 'Senha Atualizada com Sucesso!!']);
    }

    public function updateStatusVendedor(Request $request)
    {
        Seller::find($request->id)->update(['status' => $request->status]);
    }

    /** ############## LOJA DO VENDEDORES ################### */
    public function atualizarSEOVendedor(Request $request)
    {
        $seller = Store::find($request->id);
        $seller_seo['title'] = isset($request->title) ? $request->title : $seller->store_name;
        $seller_seo['keywords'] = $request->keywords;
        $seller_seo['description'] = $request->description;
        if(isset($request->banner_path_two)){
            $originalPathSeo = storage_path('app/public/img_page_store/');
            if (!file_exists($originalPathSeo)) {
                mkdir($originalPathSeo, 0777, true);
            }

            $page_img = Image::make($request->banner_path_two);
            $page_img_name = Str::random().'.'.$request->banner_path_two->extension();
            $page_img->save($originalPathSeo.$page_img_name);

            $seller_seo['banner_path_two'] = 'img_page_store/'.$page_img_name;
        }

        Store::find($request->id)->update($seller_seo);
        $seller = Store::find($request->id);

        if(empty($request->link)){
            $seller_seo['link'] = 'loja-vendedor/'.$seller->store_slug;
        }else{
            $seller_seo['link'] = $request->link;
        }

        Store::find($request->id)->update($seller_seo);

        return response()->json('');
    }

    public function atualizarIMGSVendedor(Request $request)
    {
        $seller = Store::find($request->store_id);

        $originalPath = storage_path('app/public/salemans'.$seller->seller_id.'/logo_banner/');
        if (!file_exists($originalPath)) {
            mkdir($originalPath, 0777, true);
        }

        if(isset($request->logo_path)){
            Storage::delete('public/'.$seller->logo_path);
            // $width_max = 260;
            // $height_max = 260;

            // list($width_orig, $height_orig) = getimagesize($request->logo_path);
            // $ratio_orig = $width_orig/$height_orig;
            // if ($width_max/$height_max > $ratio_orig) {
            //     $width_max = $height_max*$ratio_orig; //----
            // } else {
            //     $height_max = $width_max/$ratio_orig; //----
            // }
            // $logo_path = Image::make($request->logo_path)->resize($width_max, $height_max);
            $logo_path = Image::make($request->logo_path);
            $img_name = Str::random().'.'.$request->logo_path->extension();
            $logo_path->save($originalPath.$img_name);

            Store::find($request->store_id)->update(['logo_path' => 'salemans'.$request->seller_id.'/logo_banner/'.$img_name]);
        }
        if(isset($request->banner_path)){
            Storage::delete('public/'.$seller->banner_path);
            // $width_max = 580;
            // $height_max = 260;

            // list($width_orig, $height_orig) = getimagesize($request->banner_path);
            // $ratio_orig = $width_orig/$height_orig;
            // if ($width_max/$height_max > $ratio_orig) {
            //     $width_max = $height_max*$ratio_orig; //----
            // } else {
            //     $height_max = $width_max/$ratio_orig; //----
            // }
            // $banner_path = Image::make($request->banner_path)->resize($width_max, $height_max);
            $banner_path = Image::make($request->banner_path);
            $img_name = Str::random().'.'.$request->banner_path->extension();
            $banner_path->save($originalPath.$img_name);

            Store::find($request->store_id)->update(['banner_path' => 'salemans'.$request->seller_id.'/logo_banner/'.$img_name]);
        }

        return response()->json('');
    }
}
