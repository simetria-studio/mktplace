<tr>
    <td>
        <div class="pt-2">{{$servico->id}}</div>
    </td>
    <td>
        <div class="pt-2">{{$servico->service_title}}</div>
    </td>
    @if (auth()->guard('admin')->check())
        <td>
            <div class="pt-2">{!! $servico->seller->store->store_name ?? '<span class="text-warning">Loja não criada</span>' !!}</div>
        </td>
    @endif
    <td>
        <div class="d-flex">
            @php
                $function_slug = explode('/',\Request::path());
                $function_slug = $function_slug[count($function_slug)-1];
            @endphp
            <a class="btn btn-default mr-1 @if($servico->ativo == 'N') d-none @endif @if($function_slug == 'analise') d-none @endif" href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'editar')}}?id={{$servico->id}}">Editar</a>
            <a class="btn btn-default mr-1 @if($function_slug !== 'analise') d-none @endif" href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'analisar')}}?id={{$servico->id}}">{{auth()->guard('seller')->check() ? 'Editar' : 'Analisar'}}</a>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuAcoes" data-toggle="dropdown" aria-expanded="false">Ver mais ações</button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuAcoes">
                    <a href="{{route('service', $servico->service_slug)}}" target="_blank" class="dropdown-item">Ver Serviço</a>
                    <a href="{{route('reservaManual')}}?search_value={{$servico->service_title}}&per_page=20" target="_blank" class="dropdown-item">Reserva Manual</a>
                    <button class="dropdown-item btn-clonar-ps @if($servico->ativo == 'N') d-none @endif" data-id="{{$servico->id}}" data-href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'clonar')}}"> Clonar</button>
                    <button type="button" data-id="{{$servico->id}}" data-ativo="{{$servico->ativo == 'S' ? 'N' : 'S'}}" data-href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'ativar-desativar')}}" class="dropdown-item btn-desativar-ps">{{$servico->ativo == 'S' ? 'Desativar' : 'Ativar'}}</button>
                    <button data-href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'servico', 'apagar')}}?id={{$servico->id}}" class="dropdown-item apagar-service">Apagar</button>
                </div>
            </div>
        </div>
    </td>
</tr>