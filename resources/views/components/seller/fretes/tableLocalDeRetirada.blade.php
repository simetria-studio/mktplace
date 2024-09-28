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
            @forelse ($localretiradas as $localretirada)
                <tr>
                    <td>{{$localretirada->id}}</td>
                    <td>{{$localretirada->localidade->title ?? ''}}</td>
                    <td>{{ $localretirada->localidade->address ?? '' }}, Nº {{ $localretirada->localidade->number ?? '' }}, {{ $localretirada->localidade->district ?? '' }} - {{ $localretirada->localidade->city ?? '' }}/{{ $localretirada->localidade->state ?? '' }} - {{ $localretirada->localidade->zip_code ?? '' }}</td>
                    <td>
                        <div class="btn-group" role="group" aria-label="">
                            <a href="#" class="btn btn-info btn-xs charge-modal" data-url="{{ route('seller.localDeRetirada', 'modal-local-de-retirada') }}" data-tag_event="#modalLocalDeRetirada" data-info="{{collect(['edit_id' => $localretirada->id])->toJson()}}"><i class="fas fa-edit"></i> Alterar</a>
    
                            <a href="#" class="btn btn-danger btn-xs btn-delete" data-url="{{ route('seller.localDeRetirada', 'delete-local-de-retirada') }}" data-id="{{$localretirada->id}}" data-table_html="#tableLocalDeRetirada"><i class="fas fa-trash"></i> Apagar</a>
                        </div>
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
        <tfoot>
            <th colspan="6">{{$localretiradas->count()}} Localidades</th>
        </tfoot>
    </table>
</div>

<div class="mt-2">
    {{$localretiradas->links()}}
</div>