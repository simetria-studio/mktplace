<input type="hidden" class="vaga_controller_date" value="{{$service->vaga_controller}}">
<input type="hidden" class="service_reservation" value="{{$service->serviceReservation}}">
<input type="hidden" class="vagas_controller" value="{{$service->vaga}}">
<input type="hidden" class="json_service" value="{{$service->toJson()}}">

<div class="card mb-2">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="form-check">
                    <input type="checkbox" id="day_inactive" class="form-check-input day_inactive">
                    <label for="day_inactive">Inativar o dia</label>
                </div>
            </div>

            <div class="col-12 col-sm-9">
                <div class="row calendar-custom">
                    <div class="col-12 col-sm-5 d-flex align-items-end @if($service->hospedagem_controller == 1) border-right border-dark @endif">
                        <input type="text" class="form-control date-calendar d-none calendar_ini" data-calendar="{{collect($service->calendars)->toJson()}}">
                        <span class="mx-2 span-calendar">__/__/____</span>
                    </div>
                    @if ($service->hospedagem_controller == 1)
                        <div class="col-12 col-sm-5 d-flex align-items-end border-left border-dark">
                            <input type="text" class="form-control date-calendar-verif d-none calendar_fim" disabled data-calendar="{{collect($service->calendars)->toJson()}}">
                            <span class="mx-2 span-calendar">__/__/____</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-12 col-sm-6 mt-2 reserva-hora d-none">
                <label for="">Reservar Hora</label>
                <select class="form-control form-control-sm selectpicker hours"></select>
            </div>
            <div class="col-12 col-sm-6 mt-2 d-flex">
                <div class="mt-auto"><button type="button" class="btn btn-sm btn-primary btn-add-date-reservation">Adicionar Reserva</button></div>
            </div>
        </div>
    </div>
</div>

<div class="row div-calendar-reservation"></div>