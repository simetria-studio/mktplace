@extends('layouts.mail')

@section('body')
    <p><h3><b>Olá, tudo bem com você?</b></h3></p>

    <p>Agredecemos o seu interesse em receber as nossas novidades e promoções.</p>

    <p>Para cancelar sua inscrição, clique no botão abaixo.</p>
    @component('mail::button', ['url' => $url])
    Cancelar Inscrição
    @endcomponent

    <p>
        Obrigado por usar nosso aplicativo,<br>
        {{ config('app.name') }}
    </p>
@endsection