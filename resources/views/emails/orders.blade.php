@extends('layouts.mail')

@section('body')
    <p><h3><b>Olá, tudo bem com você?</b></h3></p>
    <p><h3><b>{!!$msg1!!}</b></h3></p>
    <p><h3><b>Confira os dados do pedido abaixo:</b></h3></p>

    <p>
        <h3><b>Dados do comprador:</b></h3>
        <div>Nome Comprador: {{$orders->user_name}}</div>
        <div>Email Comprador: {{$orders->user_email}}</div>
        <div>Cnpj/Cpf Comprador: {{$orders->user_cnpj_cpf}}</div>
    </p>

    <p>
        <h3><b>Dados do Pedido:</b></h3>
        <div>Sub Total do Pedido: {{number_format($orders->product_value,2,',','.')}}</div>
        <div>Custo do Frete: {{number_format($orders->cost_freight,2,',','.')}}</div>
        <div>Valor Total do Pedido: {{number_format($orders->total_value,2,',','.')}}</div>
    </p>

    <p>
        <h3><b>Produtos comprados:</b></h3>

        @foreach ($orders->orderProducts as $orderProducts)
            <div>Codigo do Produto: {{$orderProducts->product_code}}</div>
            <div>Nome do Produto: {{$orderProducts->product_name}}</div>
            <div>Preço do Produto: {{number_format($orderProducts->product_price,2,',','.')}}</div>
            <div>Quantidade: {{$orderProducts->quantity}}</div>
            <div>Largura: {{$orderProducts->product_height}}</div>
            <div>Altura: {{$orderProducts->product_height}}</div>
            <div>Comprimento: {{$orderProducts->product_length}}</div>
            <div>Peso: {{$orderProducts->product_weight}}</div>

            @if($orderProducts->attributes)
                <h3><b>Atributos do Produto:</b></h3>

                @foreach ($orderProducts->attributes as $attributesValue)
                    <div>{{App\Models\Attribute::find($attributesValue['parent_id'])->name}}:{{$attributesValue['name']}}</div>
                @endforeach
            @endif
        @endforeach
    </p>

    @if ($orders->shippingCustomer->transport == 'Retirada na Loja')
        <p>
            <h3><b>Endereço da Loja:</b></h3>

            <div>CEP: {{$orders->seller->store->post_code}}</div>
            <div>Endereço: {{$orders->seller->store->address}} - Nª: {{$orders->seller->store->number}}</div>
            <div>Bairro: {{$orders->seller->store->address2}}</div>
            <div>Cidade: {{$orders->seller->store->city}} - Estado: {{$orders->seller->store->state}}</div>
            <div>Complemento: {{$orders->seller->store->complement}}</div>
            <div>Telefone 1: {{$orders->seller->store->phone1}} - Telefone 2: {{$orders->seller->store->phone2}}</div>
        </p>
    @endif

    <p>
        <h3><b>Endereço do Comprador:</b></h3>
        <div>CEP: {{$orders->shippingCustomer->zip_code}}</div>
        <div>Endereço: {{$orders->shippingCustomer->address}} - Nª: {{$orders->shippingCustomer->number}}</div>
        <div>Bairro: {{$orders->shippingCustomer->address2}}</div>
        <div>Cidade: {{$orders->shippingCustomer->city}} - Estado: {{$orders->shippingCustomer->state}}</div>
        <div>Complemento: {{$orders->shippingCustomer->complement}}</div>
        <div>Telefone 1: {{$orders->shippingCustomer->phone1}} - Telefone 2: {{$orders->shippingCustomer->phone2}}</div>
    </p>

    <p><h3><b>Obs:  Em nosso site você consegue acompanhar todas as informações referentes a sua compra! Acesse feitoporbiguacu.com/perfil</b></h3></p>
@endsection