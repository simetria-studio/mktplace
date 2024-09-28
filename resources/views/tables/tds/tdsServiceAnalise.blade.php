@php
    /** @var \App\Dtos\Produto $produto */
@endphp

<td>{{$service->id}}</td>
<td>{{$service->service_title}}</td>
@if (auth()->guard('admin')->check())
    <td>{{$service->seller->id}} - {{$service->seller->nome}}</td>
@endif
<td>
    @if (auth()->guard('admin')->check())
        <button class="btn btn-default btn-edit-service btn-analise-product"
                data-target="#novoServico"
                data-toggle="modal" data-service_id="{{$service->id}}" data-href="{{route((auth('seller')->check() ?'seller.vendedor' : 'admin').'.service.show', $service->id)}}">
            Análisar
        </button>
    @else
        <button class="btn btn-default btn-edit-service"
                data-target="#novoServico"
                data-toggle="modal" data-service_id="{{$service->id}}" data-href="{{route((auth('seller')->check() ?'seller.vendedor' : 'admin').'.service.show', $service->id)}}">
            Editar
        </button>
    @endif

    <button data-href="{{route((auth('seller')->check() ?'seller.vendedor' : 'admin').'.service.destroy', $service->id)}}" class="btn btn-default apagar-service">Apagar</button>

    <button type="button" data-id="{{$service->id}}" data-ativo="{{$service->ativo == 'S' ? 'N' : 'S'}}" data-href="{{route((auth('seller')->check() ?'seller.vendedor' : 'admin').'.servicos.ativo')}}" class="btn btn-default btn-desativar-ps">{{$service->ativo == 'S' ? 'Desativar' : 'Ativar'}}</button>

    @if ($service->service_slug)
        <a href="{{route('service', $service->service_slug)}}" target="_blank" class="btn btn-default">Ver Serviço</a>
    @endif
</td>
