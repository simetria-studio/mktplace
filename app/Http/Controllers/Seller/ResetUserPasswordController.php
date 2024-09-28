<?php

namespace App\Http\Controllers\Seller;

use App\Models\Seller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;

class ResetUserPasswordController extends Controller
{
    public function forgotPassword()
    {
        return view('sellerAuth.forgot-password');
    }

    public function forgotPasswordSend(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $Seller = Seller::where ('email', $request->email)->first();
        if (!$Seller) return redirect()->back()->withErrors(['email' => 'Email não existe em nossa base de dados!']);

        $token = Str::random(60); //change 60 to any length you want
        PasswordReset::insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $url = route('seller.reset.password',$token).'?email='.$request->email;

        Notification::send(Seller::where('email', $request->email)->first(), new ResetPasswordNotification($url));
        
        return redirect()->back()->with('status','Link com redefinição de senha enviado para o email!');
    }

    public function resetPassword($token, Request $request)
    {
        return view('sellerAuth.reset-password', get_defined_vars());
    }

    public function resetPasswordSend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = PasswordReset::where(['email' => $request->email, 'token' => $request->token])->first();

        if(!$updatePassword){
            return back()->withInput()->with('error', 'Token invalido!');
        }

        $seller = Seller::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        PasswordReset::where(['email'=> $request->email])->delete();

        return redirect('/vendedor/login')->with('status', 'Sua senha foi redefinida!');
    }
}
