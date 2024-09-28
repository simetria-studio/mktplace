@extends('layouts.mail')

@section('body')
    <p><h3><b>Olá, tudo bem com você?</b></h3></p>

    <p>Viemos informar que o produto que queria já está disponivel e voce pode acessar direatemnte nesse link.</p>

    @component('mail::button', ['url' => $url])
        Acessar Produto
    @endcomponent

    <p>
        Obrigado por usar nosso aplicativo,<br>
        {{ config('app.name') }}
    </p>
@endsection