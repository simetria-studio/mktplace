@extends('layouts.site')

@section('container')
    <div class="container border-top">
        <form action="{{ route('seller.reset.password') }}" method="post">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12 col-md-4">
                    <div class="row my-5">
                        <div class="col-12 mb-5 text-center">
                            <h2>Redefinição Senha</h2>
                        </div>

                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="form-group col-12">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email do Usuário" value="{{old('email', $request->email)}}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <span class="text-muted">A senha deve conter no mínimo 8 caracteres</span>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Senha de Usuário">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar Senha">
                        </div>

                        <div class="form-group col-12 text-center">
                            <button type="submit" class="btn btn-c-primary btn-block">Resetar Senha</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection