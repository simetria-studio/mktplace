@extends('layouts.mail')

@section('body')
    {!!$msg1!!}

    @if ($send_mail == 'comprador')
        <p><h3><b>Confira os dados do pedido abaixo:</b></h3></p>

        <p>
            <h3><b>Dados do comprador:</b></h3>
            <div>Nome Comprador: {{$orders->user_name}}</div>
            <div>Email Comprador: {{$orders->user_email}}</div>
            <div>Cnpj/Cpf Comprador: {{$orders->user_cnpj_cpf}}</div>
        </p>
    
        <p>
            <h3><b>Dados do Pedido:</b></h3>
            <div>Valor Total do Pedido: {{number_format($orders->service_value,2,',','.')}}</div>
        </p>
    
        <p>
            <h3><b>Produtos Adquiridos:</b></h3>
    
            <div>Codigo do Serviço: #{{$orders->serviceReservation->service->id}}</div>
            <div>Nome do Serviço: {{$orders->serviceReservation->service_name}}</div>
            <div>Preço do Serviço: {{number_format($orders->serviceReservation->service_price,2,',','.')}}</div>
            <div>Quantidade: {{$orders->serviceReservation->quantity}}</div>
    
            @if($orders->serviceReservation->attributes['selected_attribute'])
                <h3><b>Atributos do Serviço:</b></h3>
    
                @foreach (orders->serviceReservation->attributes['selected_attribute'] as $attributesValue)
                    <div>{{App\Models\Attribute::find($attributesValue['parent_id'])->name}}:{{$attributesValue['name']}}</div>
                @endforeach
            @endif
        </p>

        <p><h3><b>Obs:  Em nosso site você consegue acompanhar todas as informações referentes a sua compra! Acesse feitoporbiguacu.com/perfil</b></h3></p>
    @endif
@endsection