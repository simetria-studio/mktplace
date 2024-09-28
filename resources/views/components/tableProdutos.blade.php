<tr>
    <td>
        <div class="pt-2">{{$produto->id}}</div>
    </td>
    <td>
        <div class="pt-2">{{$produto->nome}}</div>
    </td>
    @if (auth()->guard('admin')->check())
        <td>
            <div class="pt-2">{!! $produto->seller->store->store_name ?? '<span class="text-warning">Loja não criada</span>' !!}</div>
        </td>
    @endif
    <td>
        <div class="d-flex">
            @php
                $function_slug = explode('/',\Request::path());
                $function_slug = $function_slug[count($function_slug)-1];
            @endphp
            <a class="btn btn-default mr-1 @if($produto->ativo == 'N') d-none @endif @if($function_slug == 'analise') d-none @endif" href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'editar')}}?id={{$produto->id}}">Editar</a>
            <a class="btn btn-default mr-1 @if($function_slug !== 'analise') d-none @endif" href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'analisar')}}?id={{$produto->id}}">{{auth()->guard('seller')->check() ? 'Editar' : 'Analisar'}}</a>
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuAcoes" data-toggle="dropdown" aria-expanded="false">Ver mais ações</button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuAcoes">
                    {{-- <a class="dropdown-item" href="#">Action</a> --}}
                    <a href="{{route('product', $produto->slug)}}" target="_blank" class="dropdown-item">Ver Produto</a>
                    <button class="dropdown-item btn-add-stock @if(!$produto->stock_controller) d-none @endif" data-id="{{$produto->id}}" data-href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'add-stock')}}"> Ad. Estoque</button>
                    <button class="dropdown-item btn-clonar-ps @if($produto->ativo == 'N') d-none @endif" data-id="{{$produto->id}}" data-href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'clonar')}}"> Clonar</button>
                    <button type="button" data-id="{{$produto->id}}" data-ativo="{{$produto->ativo == 'S' ? 'N' : 'S'}}" data-href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'ativar-desativar')}}" class="dropdown-item btn-desativar-ps">{{$produto->ativo == 'S' ? 'Desativar' : 'Ativar'}}</button>
                    <button data-href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'apagar')}}?id={{$produto->id}}" class="dropdown-item apagar-produto">Apagar</button>
                </div>
            </div>
        </div>
    </td>
</tr>