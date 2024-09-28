@extends('layouts.site')

@section('seo')
    @php
        $seo = App\Models\SeoConfig::where('page', 'rsa-registroComprador')->first();
    @endphp
@endsection

@section('container')
    <div class="container border-top">
        <form action="{{ route('register') }}" method="post">
            @csrf
            <div class="row justify-content-center">
                <div class="col-12 col-md-4">
                    <div class="row my-2">
                        <div class="col-12 mb-2 text-center">
                            <h2>Registro do Comprador</h2>
                        </div>
                        <div class="form-group col-12">
                            <input type="text" name="name" value="{{old('name')}}" class="form-control @error('name') is-invalid @enderror" placeholder="Nome Completo">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <input type="email" name="email" value="{{old('email')}}" class="form-control @error('email') is-invalid @enderror" placeholder="Email de Usuário">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12">
                            <input type="text" name="cnpj_cpf" value="{{old('cnpj_cpf')}}" class="form-control @error('cnpj_cpf') is-invalid @enderror" placeholder="CNPJ/CPF">
                            @error('cnpj_cpf')
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

                        <div class="form-group col-12">
                            <input type="checkbox" class="@error('terms') is-invalid @enderror" name="terms" id="terms">
                            <label for="terms"><a href="{{route('privacypolicy')}}">Política de Privacidade</a> e <a href="{{route('termsofuse')}}">Termos de Uso</a></label>
                            @error('terms')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 text-center">
                            <button type="submit" class="btn btn-c-primary btn-block">REGISTRAR</button>
                        </div>

                        <div class="form-group col-12 text-center">
                            Já possui registro? <a class="link" href="{{route('login')}}">Clique aqui!</a>
                        </div>
                        <div class="form-group col-12 text-center">
                            É vendedor? <a class="link" href="{{route('seller.register')}}">Clique aqui!</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
{{-- <x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-jet-label for="name" value="{{ __('Name') }}" />
                <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-jet-label for="terms">
                        <div class="flex items-center">
                            <x-jet-checkbox name="terms" id="terms"/>

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-jet-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-jet-button class="ml-4">
                    {{ __('Register') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout> --}}
