<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Nº</th>
                <th>Titulo</th>
                <th>Endereço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($localidades as $localidade)
                <tr>
                    <td>{{$localidade->id}}</td>
                    <td>{{$localidade->title}}</td>
                    <td>{{ $localidade->address }}, Nº {{ $localidade->number }}, {{ $localidade->district }} - {{ $localidade->city }}/{{ $localidade->state }} - {{ $localidade->zip_code }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="">
                            <a href="#" class="btn btn-info btn-xs charge-modal" data-url="{{ route('admin.localidadeRetirada', 'modal-localidade-retirada') }}" data-tag_event="#modalLocalidadeRetirada" data-info="{{collect(['edit_id' => $localidade->id])->toJson()}}"><i class="fas fa-edit"></i> Alterar</a>
    
                            <a href="#" class="btn btn-danger btn-xs btn-delete" data-url="{{ route('admin.localidadeRetirada', 'delete-localidade-retirada') }}" data-id="{{$localidade->id}}" data-table_html="#tableLocalidadesRetirada"><i class="fas fa-trash"></i> Apagar</a>
                        </div>
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
        <tfoot>
            <th colspan="6">{{$localidades->count()}} Localidades</th>
        </tfoot>
    </table>
</div>

<div class="mt-2">
    {{$localidades->links()}}
</div>