<td>{{$reserva->service_name}}</td>
<td>
    {{date('d/m/Y', strtotime($reserva->date_reservation_ini))}} {{$reserva->date_reservation_fim ? ' - '.date('d/m/Y', strtotime($reserva->date_reservation_fim)) : ''}} {{$reserva->hour_reservation ? ' - '.$reserva->hour_reservation : ''}}
</td>
<td>
    @switch($reserva->status)
        @case(0)
            <button type="button" class="btn btn-sm btn-primary">Ativo</button>
            @break
        @case(1)
            <button type="button" class="btn btn-sm btn-danger">Inativo</button>
            @break
    @endswitch
</td>
<td>
    <button data-url="{{route((auth('seller')->check() ?'seller.' : '').'reservaManual', 'apagar-reserva')}}" data-id="{{$reserva->id}}" class="btn btn-sm btn-danger btn-destroy">Apagar</button>
</td>