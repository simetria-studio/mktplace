<tr>
    <td>{{$account->id}}</td>
    <td>
        {{$account->name}}
        <br>
        Loja: <a target="_blank" href="{{isset($account->store->store_slug) ? route('seller.store',[$account->store->store_slug]) : '#'}}">{{$account->store->store_name ?? 'sem nome'}}</a>
    </td>
    <td>{{$account->cnpj_cpf}}</td>
    <td>{{$account->email}}</td>
    <td>
        <select class="form-control form-control-sm selectResponsavel" data-id="{{$account->id}}" data-url="{{route('admin.atualizar_responsavel_vendedor')}}">
            <option value="">Sem Responsavel</option>
            {!!App\Models\Admin::get()->map(function($query) use($account){
                return '<option value="'.$query->id.'" '.($account->responsavel_id == $query->id ? 'selected' : '').'>'.$query->name.'</option>';
            })->join('')!!}
        </select>
    </td>
    <td>
        <select class="form-control form-control-sm selectStatus" data-id="{{$account->id}}" data-url="{{route('admin.updateStatusVendedor')}}">
            <option value="2" {{$account->status == 2 ? 'selected' : ''}}>Em Andamento</option>
            <option value="1" {{$account->status == 1 ? 'selected' : ''}}>Ativo</option>
            <option value="0" {{$account->status == 0 ? 'selected' : ''}}>Inativo</option>
        </select>
        {{-- <button type="button" class="btn btn-sm {{$account->status == 0 ? 'btn-danger' : 'btn-success'}} btn-update-status-vendedor" data-id="{{$account->id}}" data-status="{{$account->status}}">{{$account->status == 0 ? 'Inativo' : 'Ativo'}}</button> --}}
    </td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-expanded="false">Ações</button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="{{route('admin-logar', ['id' => $account->id, 'user' => 'seller'])}}"><i class="fas fa-eye"></i> Acessar Conta</a>
                <a class="dropdown-item" href="{{url('admin/cliente/loja-vendedor', $account->id)}}"><i class="fas fa-eye"></i>Ver Dados da Loja</a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#atualizarSenhaVendedor" data-dados="{{json_encode($account)}}"><i class="fas fa-edit"></i> Trocar Senha</a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editarVendedor" data-dados="{{json_encode($account)}}"><i class="fas fa-edit"></i> Editar Cliente</a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#excluirVendedor" data-dados="{{json_encode($account)}}"><i class="fas fa-trash"></i> Apagar Conta</a>
            </div>
        </div>
    </td>
</tr>