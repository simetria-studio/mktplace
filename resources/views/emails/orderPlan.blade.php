@extends('layouts.mail')

@section('body')
    <p><h3><b>Olá, tudo bem com você?</b></h3></p>
    <p><h3><b>{!!$msg1!!}</b></h3></p>
    <p><h3><b>Confira os dados do pedido abaixo:</b></h3></p>

    <p>
        <h3><b>Dados do comprador:</b></h3>
        <div>Nome Comprador: {{$orders->user->name}}</div>
        <div>Email Comprador: {{$orders->user->email}}</div>
        <div>Cnpj/Cpf Comprador: {{$orders->user->cnpj_cpf}}</div>
    </p>

    <p>
        <h3><b>Dados do Assinatura:</b></h3>
        <div>Sub Total do Assinatura: {{number_format($orders->plan_value, 2, ',', '.')}}</div>
        <div>Custo do Frete: {{number_format($orders->shipping['price'],2,',','.')}}</div>
        <div>Valor Total do Assinatura: {{number_format( ($orders->plan_value + $orders->shipping['price'] ),2,',','.')}}</div>
    </p>
    
    <p>
        <h3><b>Produtos comprados:</b></h3>
        <div>Nome do Produto: {{$orders->produto->nome}}</div>
        <div>Largura: {{$orders->produto->width}}</div>
        <div>Altura: {{$orders->produto->height}}</div>
        <div>Comprimento: {{$orders->produto->length}}</div>
        <div>Peso: {{$orders->produto->weight}}</div>
    </p>

    <p>
        <h3><b>Endereço do Comprador:</b></h3>
        <div>CEP: {{$orders->shipping['post_code']}}</div>
        <div>Endereço: {{$orders->shipping['address']}} - Nª: {{$orders->shipping['number']}}</div>
        <div>Bairro: {{$orders->shipping['address2']}}</div>
        <div>Cidade: {{$orders->shipping['city']}} - Estado: {{$orders->shipping['state']}}</div>
        <div>Complemento: {{$orders->shipping['complement']}}</div>
        <div>Telefone 1: {{$orders->shipping['phone1']}} - Telefone 2: {{$orders->shipping['phone2']}}</div>

        @php
            $generalData = json_decode($orders->shipping['general_data']);
        @endphp

        <div>Entrega por: {{isset($generalData->company->name) ? $generalData->company->name .'-' : $orders->shipping['transport'] }}  {{$generalData->name ?? ''}}</div>
        @isset($generalData->descricao)
            <div>{{$generalData->descricao}}</div>
        @endisset
        @isset($orders->observation)
            <div>Observações: {{$orders->observation}}</div>
        @endisset
    </p>

    <p><h3><b>Obs:  Em nosso site você consegue acompanhar todas as informações referentes a sua compra! Acesse feitoporbiguacu.com/perfil</b></h3></p>
@endsection