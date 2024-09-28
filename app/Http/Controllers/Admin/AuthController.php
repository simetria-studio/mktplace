<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function indexLogin()
    {
        return view('adminAuth.login');
    }

    public function login(Request $request)
    {
        $remember = $request->remember ? true : false;
        $authValid = Auth::guard('admin')->validate(['email' => $request->email, 'password' => $request->password]);

        if($authValid){
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $remember)) {

                return redirect()->route('dashboard');
            }
        }else{
            return redirect()->back()->with('error', 'Email ou senha invalidos');
        }
    }

    public function indexRegister()
    {
        return view('adminAuth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {

            return redirect()->route('dashboard');
        }
    }

    public function logout()
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    /** ############## PERFIL ################### */
    public function nomePerfil(Request $request)
    {
        $perfilName['name'] = $request->name;

        Admin::where('id', auth()->guard('admin')->user()->id)->update($perfilName);

        return response()->json('Sucesso!');
    }

    public function emailPerfil(Request $request)
    {
        $request->validate([
            'email' => 'unique:admins,email,'.auth()->guard('admin')->user()->id,
        ]);

        $perfilEmail['email'] = $request->email;

        Admin::where('id', auth()->guard('admin')->user()->id)->update($perfilEmail);

        return response()->json('Sucesso!');
    }

    public function senhaPerfil(Request $request)
    {
        if(Hash::check($request->current_password, auth()->guard('admin')->user()->password)){
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
            
            Admin::where('id',auth()->guard('admin')->user()->id)->update([
                'password' => Hash::make($request['password']),
            ]);

            return response()->json(['success' => 'Senha Atualizada com Sucesso!!']);
        }else{
            return response()->json(['errors' => ['current_password' => ['Senha Atual invalida!']]], 422);
        }
    }

    /** ############## CONTAS DO PAINEL ################### */
    public function novaConta(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $account = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'permission' => 10,
        ]);

        return response()->json([
            'table' => '<tr class="'.$account->id.'">
                <td>'.$account->id.'</td>
                <td>'.$account->name.'</td>
                <td>'.$account->email.'</td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#atualizarSenha" data-dados="\'.json_encode($account).\'"><i class="fas fa-edit"></i> Trocar Senha</a>
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarConta" data-dados="\'.json_encode($account).\'"><i class="fas fa-edit"></i> Editar Nome & Email</a>
                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirConta" data-dados="\'.json_encode($account).\'"><i class="fas fa-trash"></i> Apagar Conta</a>
                    </div>
                </td>
            </tr>'
        ]);
    }

    public function atualizarConta(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'unique:users,email,'.$request->id,
        ]);

        $accounts['name'] = $request->name;
        $accounts['email'] = $request->email;

        Admin::where('id', $request->id)->update($accounts);
        $account = Admin::where('id', $request->id)->first();

        return response()->json([
            'tb_id' => $account->id,
            'tb_up' => '
                <td>'.$account->id.'</td>
                <td>'.$account->name.'</td>
                <td>'.$account->email.'</td>
                <td>
                    <div class="btn-group" role="group" aria-label="">
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#atualizarSenha" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Trocar Senha</a>
                        <a href="#" class="btn btn-info btn-sm btn-editar" data-toggle="modal" data-target="#editarConta" data-dados=\''.json_encode($account).'\'><i class="fas fa-edit"></i> Editar Nome & Email</a>
                        <a href="#" class="btn btn-danger btn-sm btn-editar" data-toggle="modal" data-target="#excluirConta" data-dados=\''.json_encode($account).'\'><i class="fas fa-trash"></i> Apagar Conta</a>
                    </div>
                </td>'
        ]);
    }

    public function excluirConta(Request $request)
    {
        Admin::where('id', $request->id)->delete();

        return response()->json([
            'tb_trash' => $request->id
        ]);
    }

    public function atualizarSenha(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        Admin::where('id',$request->id)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => 'Senha Atualizada com Sucesso!!']);
    }
}
