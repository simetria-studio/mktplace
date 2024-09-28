@extends('layouts.site')

@section('container')
    <div class="container border-top">
        <form action="{{ route('seller.forgot.password') }}" method="post">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12 mt-3 text-center">
                    <p>Esqueceu sua senha? Sem problemas. Basta nos informar seu endereço de e-mail e nós enviaremos um link de redefinição de senha que permitirá que você escolha uma nova.</p>
                </div>
                <div class="col-12 col-md-4">
                    <div class="row mb-5 mt-2">
                        @if (session('status'))
                            <div class="col-12 mb-3">
                                <div class="py-2 text-center rounded alert-success">{{ session('status') }}</div>
                            </div>
                        @endif
                        @error('email')
                            <div class="col-12 mb-3">
                                <div class="py-2 text-center rounded alert-danger">{{ $message }}</div>
                            </div>
                        @enderror

                        <div class="form-group col-12">
                            <input type="email" name="email" class="form-control" placeholder="Email de Login">
                        </div>

                        <div class="form-group col-12 text-center">
                            <button type="submit" class="btn btn-c-primary btn-block">Enviar link para redefinição de senha</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection