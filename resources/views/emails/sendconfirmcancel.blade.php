@extends('layouts.mail')

@section('body')
    <p><h3><b>Olá, tudo bem com você?</b></h3></p>

    <p><h4>Estamos entrando em contato para lhe atualizar sobre a solicitação de cancelamento do {{$orders}}.</h4></p>
    <p><h3><b>{!!$msg1!!}</b></h3></p>

@endsection