<?php

namespace App\Http\Controllers\Seller;

use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use App\Mail\SellerRegister;

use App\Models\CustomerAddress;
use App\Models\SellerAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthSellerController extends Controller
{
    public function indexLogin()
    {
        return view('sellerAuth.login');
    }

    public function login(Request $request)
    {
        $remember = $request->remember ? true : false;
        $authValid = Auth::guard('seller')->validate(['email' => $request->email, 'password' => $request->password]);

        if($authValid){
            if (Auth::guard('seller')->attempt(['email' => $request->email, 'password' => $request->password], $remember)) {

                if(Auth::guard('seller')->validate(['email' => $request->email, 'password' => $request->password, 'status' => 0])) return redirect()->back()->with('error', 'Seu cadastro ainda está em analise, aguarde!');

                return redirect()->route('seller.dashboard');
            }
        }else{
            return redirect()->back()->with('error', 'Email ou senha invalidos');
        }
    }

    public function indexRegister()
    {
        return view('sellerAuth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:sellers',
            'cnpj_cpf' => 'required|string|cpf_cnpj',
            'phone' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ]);

        $user_create['name'] = $request->name;
        $user_create['document']            = str_replace(['-', '.', '/'], '', $request->cnpj_cpf);
        $user_create['document_type']       = strlen($user_create['document']) > 11 ? 'CNPJ' : 'CPF';
        if($request->birth_date ?? null)    $user_create['birth_date']  = $request->birth_date;
        if($request->phone ?? null)         $user_create['phones']      = $request->phone;
        if($request->inscricao_estadual ?? null)    $user_create['inscricao_estadual']  = $request->inscricao_estadual;

        $user_create['email'] = $request->email;
        $user_create['password'] = Hash::make($request->password);

        // Temporario
        $user_create['status'] = 1;

        $seller = Seller::create($user_create);


        Mail::to('contato@raeasy.com')->send(new SellerRegister($seller));

        return redirect()->route('seller.login')->with('success', 'Uhulll <br> Que ótimo ter você por aqui! <br> Logo um de nós entrará em contato para auxiliar na sua loja, aguarde.');
    }

    public function logout()
    {
        auth()->guard('seller')->logout();
        return redirect()->route('seller.login');
    }

    /** ############## PERFIL ################### */
    public function nomePerfil(Request $request)
    {
        $perfilName['name'] = $request->name;

        Seller::where('id', auth()->guard('seller')->user()->id)->update($perfilName);

        return response()->json('Sucesso!');
    }

    public function emailPerfil(Request $request)
    {
        $request->validate([
            'email' => 'unique:sellers,email,'.auth()->guard('seller')->user()->id,
        ]);

        $perfilEmail['email'] = $request->email;

        Seller::where('id', auth()->guard('seller')->user()->id)->update($perfilEmail);

        return response()->json('Sucesso!');
    }

    public function cnpjCpfPerfil(Request $request)
    {
        $perfilCnpjCpf['document']            = str_replace(['-', '.', '/'], '', $request->cnpj_cpf);
        $perfilCnpjCpf['document_type']       = strlen($perfilCnpjCpf['document']) > 11 ? 'CNPJ' : 'CPF';

        Seller::where('id', auth()->guard('seller')->user()->id)->update($perfilCnpjCpf);

        return response()->json('Sucesso!');
    }

    public function phonePerfil(Request $request)
    {
        $perfilPhone['phone'] = $request->phone;

        Seller::where('id', auth()->guard('seller')->user()->id)->update($perfilPhone);

        return response()->json('Sucesso!');
    }

    public function senhaPerfil(Request $request)
    {
        if(Hash::check($request->current_password, auth()->guard('seller')->user()->password)){
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            Seller::where('id',auth()->guard('seller')->user()->id)->update([
                'password' => Hash::make($request['password']),
            ]);

            return response()->json(['success' => 'Senha Atualizada com Sucesso!!']);
        }else{
            return response()->json(['errors' => ['current_password' => ['Senha Atual invalida!']]], 422);
        }
    }

    /** ############## ENDEREÇOS DE VENDEDORES ################### */
    public function novoEndereco(Request $request)
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

        $addresses['user_id']       = auth()->guard('seller')->user()->id;
        $addresses['post_code']     = $request->post_code;
        $addresses['address']       = $request->address;
        $addresses['number']        = $request->number;
        $addresses['complement']    = $request->complement;
        $addresses['address2']      = $request->address2;
        $addresses['state']         = $request->state;
        $addresses['city']          = $request->city;
        $addresses['phone1']        = $request->phone1;
        $addresses['phone2']        = $request->phone2;

        if(SellerAddress::where('user_id', auth()->guard('seller')->user()->id)->get()->count() == 4) return response()->json(['msg_alert' => 'Maximo 4 endereço!', 'icon_alert' => 'warning'], 412);

        $address = SellerAddress::create($addresses);

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

    public function atualizarEndereco(Request $request)
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

        SellerAddress::where('id', $request->id)->update($addresses);
        $address = SellerAddress::where('id', $request->id)->first();

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

    public function excluirEndereco(Request $request)
    {
        SellerAddress::where('id', $request->id)->delete();

        return response()->json([
            'tb_trash' => $request->id
        ]);
    }
}
