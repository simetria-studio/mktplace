@extends('layouts.site')

@section('seo')
    @php
        $seo = App\Models\SeoConfig::where('page', 'rsa-loginComprador')->first();
    @endphp
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-12 col-md-4">
            <div class="pt-3 mb-2 d-flex header-site">
                <div class="col-6 px-1">
                    <a href="{{ route('login') }}" class="btn btn-p-success btn-block">Comprador</a>
                </div>
                <div class="col-6 px-1">
                    <a href="{{ route('seller.login') }}" class="btn btn-s-warning btn-block">Vendedor</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container border-top">
        <form action="{{ route('login') }}" method="post">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12 col-md-4">
                    <div class="row my-2">
                        <div class="col-12 mb-2 text-center">
                            <h2>Login do Comprador</h2>
                        </div>

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
                        @error('password')
                            <div class="col-12 mb-3">
                                <div class="py-2 text-center rounded alert-danger">{{ $message }}</div>
                            </div>
                        @enderror

                        <div class="form-group col-12">
                            <input type="email" name="email" class="form-control" placeholder="Email do usuário">
                        </div>
                        <div class="form-group col-12">
                            <input type="password" name="password" class="form-control" placeholder="Senha do usuário">
                            <span style="font-size: .8rem;color:  #797979;">Esqueceu a senha? <a class="link" href="{{route('password.request')}}">Clique aqui!</a></span>
                        </div>

                        <div class="form-group col-12 text-center">
                            <button type="submit" class="btn btn-c-primary btn-block">ENTRAR</button>
                        </div>

                        <div class="form-group col-12 text-center" style="font-size: .8rem;">
                            Não possui registro? <a class="link" href="{{route('register')}}">Clique aqui!</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
{{-- 
<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-jet-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-jet-button class="ml-4">
                    {{ __('Log in') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout> --}}
