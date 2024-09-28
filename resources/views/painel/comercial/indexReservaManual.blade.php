@extends(auth()->guard('seller')->check() ? 'layouts.painelSman' : 'layouts.painelAdm')

@section('container')
    <section class="content">
        <div class="container-fluid">
            <div class="row tab-content" id="pills-tabContent">
                <div class="col-12 mt-3">
                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Reservas Manuais</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive" >
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#reservarServico">
                                            <i class="fas fa-plus"></i> Nova Reserva </button>
                                    </div>
                                </div>

                                <form action="" method="get">
                                    <div class="row mt-2">
                                        <div class="col-12 col-md-5 form-group">
                                            <label for="">Nome do Serviço</label>
                                            <input type="text" class="form-control form-control-sm" name="search_value" placeholder="Pesquisar por..." @isset($_GET['search_value']) value="{{$_GET['search_value']}}" @endisset>
                                        </div>

                                        <div class="col-12 col-md-2 d-flex form-group">
                                            <div class="mt-auto">
                                                <button type="submit" class="btn btn-sm btn-primary btn-block">Buscar</button>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4 form-group">
                                            <label for="">Reservas por Página</label>
                                            <select name="per_page" class="form-control form-control-sm">
                                                <option value="20" @isset($_GET['per_page']) @if($_GET['per_page'] == '20') selected @endif @endisset>20 por Página</option>
                                                <option value="30" @isset($_GET['per_page']) @if($_GET['per_page'] == '30') selected @endif @endisset>30 por Página</option>
                                                <option value="50" @isset($_GET['per_page']) @if($_GET['per_page'] == '50') selected @endif @endisset>50 por Página</option>
                                                <option value="100" @isset($_GET['per_page']) @if($_GET['per_page'] == '100') selected @endif @endisset>100 por Página</option>
                                                <option value="500" @isset($_GET['per_page']) @if($_GET['per_page'] == '500') selected @endif @endisset>500 por Página</option>
                                                <option value="1000" @isset($_GET['per_page']) @if($_GET['per_page'] == '1000') selected @endif @endisset>1000 por Página</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="container mt-2 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome Serviço</th>
                                            <th>Data - Horario</th>
                                            <th>Status do Dia</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @each('tables.trs.trsReservaManual', $reservas, 'reserva')
                                    </tbody>
                                </table>

                                <div class="col-12 mt-2">
                                    {{$reservas->appends(['search_value' => $_GET['search_value'] ?? null, 'per_page' => $_GET['per_page'] ?? ''])->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="reservarServico" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                        <h4 class="modal-title">Reservar Data</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('reservaManual', 'post-reserva')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="">Selecione o Serviço</label>
                            <select name="service_id" class="form-control form-control-sm select-service-reservation selectpicker" data-live-search="true">
                                <option value="">Selecione um Serviço</option>
                                @foreach ($services as $service)
                                    <option value="{{$service->id}}">{{$service->service_title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="data-modal-body"></div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fas fa-times"></i> Fechar
                    </button>
                    <button type="button" class="btn btn-success btn-save" data-refresh="true" data-target="#reservarServico">
                        <i class="fas fa-save"></i> Reservar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section(auth()->guard('seller')->check() ? 'script' : 'scripts')
    <script>
        $(document).ready(function(){
            $(document).on('click', '.span-calendar', function(){$(this).parent().find('.ui-datepicker-trigger').trigger('click');});

            $(document).on('change', '.day_inactive', function(){
                if($(this).prop('checked')){
                    $('.reserva-hora').addClass('d-none');
                }else{
                    if($('select.hours').find('option').length > 0){
                        $('.reserva-hora').removeClass('d-none');
                    }
                }
            });

            var count_calendar = 0;
            $(document).on('click', '.btn-add-date-reservation', function(){
                var json_service = JSON.parse($('.json_service').val());
                count_calendar++;
                $('.div-calendar-reservation').append(`
                    <div class="col-12 data-card-reserva mb-1">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" name="reservation[${count_calendar}][service_id]" value="${json_service.id}">
                                <input type="hidden" name="reservation[${count_calendar}][seller_id]" value="${json_service.seller_id}">
                                <input type="hidden" name="reservation[${count_calendar}][service_name]" value="${json_service.service_title}">
                                <input type="hidden" name="reservation[${count_calendar}][day_inactive]" value="${$('.day_inactive').prop('checked')}">
                                <input type="hidden" name="reservation[${count_calendar}][calendar_ini]" value="${$('.calendar_ini').val()}">
                                <input type="hidden" name="reservation[${count_calendar}][calendar_fim]" value="${$('.calendar_fim').val() ? $('.calendar_fim').val() : ''}">
                                <input type="hidden" name="reservation[${count_calendar}][hours]" value="${$('select.hours').val()}">
                                <div class="">${json_service.service_title} (${$('.calendar_ini').val()} - ${$('.calendar_fim').val() ? $('.calendar_fim').val() : ''} / ${$('select.hours').val()})</div>
                                <div><button type="button" class="btn btn-sm btn-danger btn-remover-reserva">Remover Reserva</button></div>
                            </div>
                        </div>
                    </div>
                `);
            });
            $(document).on('click', '.btn-remover-reserva', function(){
                $(this).closest('.data-card-reserva').remove();
            });

            $('[name="per_page"]').on('change', function(){
                $(this).closest('form').submit();
            });

            $(document).on('click', '[data-target="#reservarServico"]', function(){
                $("#reservarServico").find('.select-service-reservation').selectpicker('val', '');
                $('.data-modal-body').empty();
            });

            $(document).on('change', 'select.select-service-reservation', function(){
                $('.data-modal-body').empty();
                $.ajax({
                    url: `{{route('reservaManual', 'servico-calendar')}}`,
                    type: 'POST',
                    data: {service_id: $(this).val()},
                    success: (data)=>{
                        $('#reservarServico').modal('show');
                        $('#reservarServico').find('.data-modal-body').html(data);
                        $('.selectpicker').selectpicker();
                        calendar_start_ini();
                        calendar_start_fim();
                    }
                });
            });
        });

        function semanaNumber(semana){
            var semana_number = [];
            $.each(semana, (key_s, value_s)=>{
                switch(key_s){
                    case 'domingo':
                        semana_number.push(0);
                        break;
                    case 'segunda':
                        semana_number.push(1);
                        break;
                    case 'terca':
                        semana_number.push(2);
                        break;
                    case 'quarta':
                        semana_number.push(3);
                        break;
                    case 'quinta':
                        semana_number.push(4);
                        break;
                    case 'sexta':
                        semana_number.push(5);
                        break;
                    case 'sabado':
                        semana_number.push(6);
                        break;
                }
            });

            return semana_number;
        }

        function calendar_repetir(calendar){
            var date_inicio = new Date(calendar.data_inicial.replace('-','/'));

            switch(calendar.select_control){
                case 'semana':
                    if(calendar.select_termino == "data_fim"){
                        var date_fim = new Date(calendar.data_fim.replace('-','/'));
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var semana = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(semana == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            if(6 == date_atualizada.getDay()) semana++;
                            if(semana == calendar.number_select) semana = 0;
                        }
                    }else if(calendar.select_termino == "ocorrencia"){
                        var date_inicial_ocorrencia = new Date(calendar.data_inicial.replace('-','/'));
                        var dias = (calendar.ocorrencia*7);
                        date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias)
                        var diff = Math.abs(date_inicial_ocorrencia.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var semana = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(semana == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            if(6 == date_atualizada.getDay()) semana++;
                            if(semana == calendar.number_select) semana = 0;
                        }
                    }else if(calendar.select_termino == "nunca"){
                        var date_fim = new Date(`${(new Date().getFullYear()+5)}/12/31`);
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var semana = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(semana == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            if(6 == date_atualizada.getDay()) semana++;
                            if(semana == calendar.number_select) semana = 0;
                        }
                    }
                    break;
                case 'mes':
                    if(calendar.select_termino == "data_fim"){
                        var date_fim = new Date(calendar.data_fim.replace('-','/'));
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var mes = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(mes == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            mes++;
                            if(mes == calendar.number_select) mes = 0;
                        }
                    }else if(calendar.select_termino == "ocorrencia"){
                        var date_inicial_ocorrencia = new Date(calendar.data_inicial.replace('-','/'));
                        var dias = (calendar.ocorrencia*7);
                        date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias)
                        var diff = Math.abs(date_inicial_ocorrencia.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var mes = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(mes == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            mes++;
                            if(mes == calendar.number_select) mes = 0;
                        }
                    }else if(calendar.select_termino == "nunca"){
                        var date_fim = new Date(`${(new Date().getFullYear()+5)}/12/31`);
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var mes = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(mes == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            mes++;
                            if(mes == calendar.number_select) mes = 0;
                        }
                    }
                    break;
                case 'ano':
                    if(calendar.select_termino == "data_fim"){
                        var date_fim = new Date(calendar.data_fim.replace('-','/'));
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var anual = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(anual == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            anual++;
                            if(anual == calendar.number_select) anual = 0;
                        }
                    }else if(calendar.select_termino == "ocorrencia"){
                        var date_inicial_ocorrencia = new Date(calendar.data_inicial.replace('-','/'));
                        var dias = (calendar.ocorrencia*7);
                        date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias)
                        var diff = Math.abs(date_inicial_ocorrencia.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var anual = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(anual == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            anual++;
                            if(anual == calendar.number_select) anual = 0;
                        }
                    }else if(calendar.select_termino == "nunca"){
                        var date_fim = new Date(`${(new Date().getFullYear()+5)}/12/31`);
                        var diff = Math.abs(date_fim.getTime() - date_inicio.getTime()); // Subtrai uma data pela outra
                        const days = Math.ceil(diff / (1000 * 60 * 60 * 24)); // Divide o total pelo total de milisegundos correspondentes a 1 dia. (1000 milisegundos = 1 segundo).

                        var data = [];
                        var anual = 0;
                        for(var i = 0; days>=i; i++){
                            var date_atualizada = new Date(calendar.data_inicial.replace('-','/'));
                            date_atualizada.setDate(date_atualizada.getDate()+i);
                            if(anual == 0){
                                data.push(`${date_atualizada.getFullYear()}/${date_atualizada.getMonth()+1}/${date_atualizada.getDate()}`);
                            }
                            anual++;
                            if(anual == calendar.number_select) anual = 0;
                        }
                    }
                    break;
            }

            return data;
        }

        function arrayChunk(array, perChunk){
            return array.reduce((resultArray, item, index) => { 
                const chunkIndex = Math.floor(index/perChunk)

                if(!resultArray[chunkIndex]) {
                    resultArray[chunkIndex] = [] // start a new chunk
                }

                resultArray[chunkIndex].push(item)

                return resultArray
            }, []);
        }

        function filterServiceReservationDate(date){
            date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,'0')+'-'+date.getDate().toString().padStart(2,'0');
            var service_reservation = JSON.parse($('.service_reservation').val());
            service = service_reservation.filter(function(service) {
                if(service.date_reservation_fim){
                    if(date >= service.date_reservation_ini){
                        if(date <= service.date_reservation_fim){
                            return service;
                        }
                    }
                }else if(!service.hour_reservation){
                    if(service.date_reservation_ini == date) return service;
                }
            });

            if($('.vaga_controller_date').val() == 1){
                if(service.length >= $('.vagas_controller').val()){
                return true;
            }
            }
            return false;
        }

        function filterServiceReservationHour(date, hour){
            date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,'0')+'-'+date.getDate().toString().padStart(2,'0');
            var service_reservation = JSON.parse($('.service_reservation').val());

            var count_horario = 0;
            var horarios = hour.map(function(value){
                return value.join(' - ');
            });
            // console.log(horarios);
            var service = service_reservation.map(function(service) {
                if(service.date_reservation_fim){
                    if(date >= service.date_reservation_ini){
                        if(date <= service.date_reservation_fim){
                            for(var i=0;horarios.length>i;i++){
                                if(horarios[i] == service.hour_reservation){
                                    count_horario++;
                                }
                            }
                        }
                    }
                }else if(service.date_reservation_ini == date){
                    for(var i=0;horarios.length>i;i++){
                        if(horarios[i] == service.hour_reservation){
                            count_horario++;
                        }
                    }
                }
            });

            if($('.vaga_controller_date').val() == 1){
                if(count_horario >= (horarios.length * $('.vagas_controller').val())){
                    return true;
                }
            }
            return false;
        }

        function filterServiceReservationHourSelected(date, hour){
            var hour_old_new = hour;
            date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,'0')+'-'+date.getDate().toString().padStart(2,'0');
            var service_reservation = JSON.parse($('.service_reservation').val());

            var count_horario = 0;
            var service_hours = [];
            service_reservation.filter(function(service) {
                if(service.date_reservation_fim){
                    if(date >= service.date_reservation_ini){
                        if(date <= service.date_reservation_fim){
                            return true;
                        }
                    }
                }else if(service.date_reservation_ini == date){
                    return true;
                }
            }).map(function(service){
                service_hours[service.hour_reservation] = service_hours[service.hour_reservation] ? service_hours[service.hour_reservation]+1 : 1;
            });

            for(var i=0;hour.length>i;i++){
                if(service_hours[hour[i]]){
                    if(service_hours[hour[i]] >= $('.vagas_controller').val()){
                        hour_old_new.splice(i,1);
                    }
                }
                // console.log(service_hours[hour[i]]);
            }
            return hour_old_new;
        }

        function calendar_start_ini(){
            let semana_dia = {
                0: 'domingo',
                1: 'segunda',
                2: 'terca',
                3: 'quarta',
                4: 'quinta',
                5: 'sexta',
                6: 'sabado',
            };

            $('.date-calendar').datepicker({
                showOn: 'button',
                buttonImage: "/site/imgs/calendar.png",
                buttonImageOnly: true,
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
                dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                changeMonth: true,
                changeYear: true,
                beforeShow: function(input, inst){
                    // console.log(($(input).parent().offset().top - $(window).scrollTop()));
                    // console.log(inst.dpDiv);
                    setTimeout(()=>{
                        inst.dpDiv.css({ 
                            'top': ($(input).parent().offset().top - $(window).scrollTop())+'px'
                        });
                    }, 200);
                },
                beforeShowDay: function(date){
                    var calendar = $(this).data('calendar');
                    if(filterServiceReservationDate(date)) return [false, ''];
                    for(var i=0; calendar.length>i; i++){ // lendo todos os dados do calendario registrado
                        var date_calendar = new Date(calendar[i].data_inicial); // setamos a dat inicial
                        if(new Date() > date_calendar) date_calendar = new Date(); // serve para bloquear as datas anteriores
                        date_calendar.setDate(date_calendar.getDate()+(calendar[i].antecedencia)); // adicionamos antecendencia caso para bloquear as datas
                        if(date >= date_calendar){ // entra na função para liberar as datas caso
                            if(calendar[i].select_termino == "data_fim" && date <= new Date(calendar[i].data_fim) ){
                                if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                if(calendar[i].semana){
                                    var semana_number = semanaNumber(calendar[i].semana);
                                    for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                        if(date.getDay() == semana_number[semana_i]) {
                                            return [true, ''];
                                        }
                                    }
                                }else{
                                    return [true, ''];
                                }
                            }else if(calendar[i].select_termino == "ocorrencia"){
                                var date_inicial_ocorrencia = new Date(calendar[i].data_inicial.replace('-','/'));
                                var dias = (calendar[i].ocorrencia*7)-(date_inicial_ocorrencia.getDay()+1);
                                date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias);
                                if(date <= date_inicial_ocorrencia){
                                    if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                    if(calendar[i].semana){
                                        var semana_number = semanaNumber(calendar[i].semana);
                                        for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                            if(date.getDay() == semana_number[semana_i]) {
                                                return [true, ''];
                                            }
                                        }
                                    }else{
                                        return [true, ''];
                                    }
                                }
                            }else if(calendar[i].select_termino == "nunca"){
                                if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]] || [];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2);
                                        if(filterServiceReservationHour(date, semana_horario)) return [false, ''];
                                    }
                                    var semana_number = semanaNumber(calendar[i].semana);
                                    for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                        if(date.getDay() == semana_number[semana_i]) {
                                            return [true, ''];
                                        }
                                    }
                                }else{
                                    return [true, ''];
                                }
                            }
                        }
                    }
                    return [false, ''];
                },
                onSelect: function(date){
                    $(this).parent().find('span').html(date);
                    var calendar = $(this).data('calendar');
                    var date = date.split('/');
                    date = `${date[2]}/${date[1]}/${date[0]}`;
                    date = new Date(date);
                    for(var i=0; calendar.length>i; i++){ // lendo todos os dados do calendario registrado
                        var date_calendar = new Date(calendar[i].data_inicial.replace('-','/')); // setamos a dat inicial
                        if(date >= date_calendar){ // entra na função para liberar as datas caso
                            if(calendar[i].select_termino == "data_fim" && date <= new Date(calendar[i].data_fim.replace('-','/')) ){
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2).map(function(value){
                                            return `${value[0]} - ${value[1]}`;
                                        });
                                        semana_horario = filterServiceReservationHourSelected(date,semana_horario);

                                        if(!$('.day_inactive').prop('checked')){
                                            $('select.hours').empty();
                                            $('select.hours').append('<option value="" >Selecione um horario</option>'+semana_horario.map(function(value){
                                                return `<option value="${value}">${value}</option>`;
                                            }).join('')).selectpicker('refresh');
                                            $('.reserva-hora').removeClass('d-none');
                                        }
                                    }else{
                                        $('.reserva-hora').addClass('d-none');
                                    }
                                }
                            }else if(calendar[i].select_termino == "ocorrencia"){
                                var date_inicial_ocorrencia = new Date(calendar[i].data_inicial.replace('-','/'));
                                var dias = (calendar[i].ocorrencia*7)-(date_inicial_ocorrencia.getDay()+1);
                                date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias)
                                    if(date <= date_inicial_ocorrencia){
                                        if(calendar[i].semana){
                                        var semana_select = calendar[i].semana[semana_dia[date.getDay()]];
                                        if(semana_select['horario']){
                                            var semana_horario = arrayChunk(semana_select['horario'],2).map(function(value){
                                                return `${value[0]} - ${value[1]}`;
                                            });
                                            semana_horario = filterServiceReservationHourSelected(date,semana_horario);

                                            if(!$('.day_inactive').prop('checked')){
                                                $('select.hours').empty();
                                                $('select.hours').append('<option value="" >Selecione um horario</option>'+semana_horario.map(function(value){
                                                    return `<option value="${value}">${value}</option>`;
                                                }).join('')).selectpicker('refresh');
                                                $('.reserva-hora').removeClass('d-none');
                                            }
                                        }else{
                                            $('.reserva-hora').addClass('d-none');
                                        }
                                    }
                                }
                            }else if(calendar[i].select_termino == "nunca"){
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2).map(function(value){
                                            return `${value[0]} - ${value[1]}`;
                                        });
                                        semana_horario = filterServiceReservationHourSelected(date,semana_horario);

                                        if(!$('.day_inactive').prop('checked')){
                                            $('select.hours').empty();
                                            $('select.hours').append('<option value="" >Selecione um horario</option>'+semana_horario.map(function(value){
                                                return `<option value="${value}">${value}</option>`;
                                            }).join('')).selectpicker('refresh');
                                            $('.reserva-hora').removeClass('d-none');
                                        }
                                    }else{
                                        $('.reserva-hora').addClass('d-none');
                                    }
                                }
                            }
                        }
                    }
                    $('.date-calendar-verif').prop('disabled', false).val('');
                    select_date_fim = false;
                }
            });
        }

        function calendar_start_fim(){
            let semana_dia = {
                0: 'domingo',
                1: 'segunda',
                2: 'terca',
                3: 'quarta',
                4: 'quinta',
                5: 'sexta',
                6: 'sabado',
            };
            var select_date_fim = false;
            $('.date-calendar-verif').datepicker({
                showOn: 'button',
                buttonImage: "/site/imgs/calendar.png",
                buttonImageOnly: true,
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
                dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                changeMonth: true,
                changeYear: true,
                beforeShow: function(input, inst){
                    select_date_fim = false;
                    setTimeout(()=>{
                        inst.dpDiv.css({ 
                            'top': ($(input).parent().offset().top - $(window).scrollTop())+'px'
                        });
                    }, 200);
                },
                onChangeMonthYear: function(){
                    select_date_fim = false;
                },
                beforeShowDay: function(date){
                    var calendar = $(this).data('calendar');
                    var date_select = $(this).parent().parent().find('.date-calendar').val().split('/');
                    date_select = new Date(`${date_select[2]}/${date_select[1]}/${date_select[0]}`);
                    if(select_date_fim) return [false, ''];
                    if(date >= date_select){
                        if(filterServiceReservationDate(date)) {
                            select_date_fim = true;
                            return [false, ''];
                        }
                        for(var i=0; calendar.length>i; i++){ // lendo todos os dados do calendario registrado
                            if(calendar[i].select_termino == "data_fim" && date <= new Date(calendar[i].data_fim) ){
                                if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)){
                                    select_date_fim = true;
                                    return [false, ''];
                                }
                                if(calendar[i].semana){
                                    var semana_number = semanaNumber(calendar[i].semana);
                                    for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                        if(date.getDay() == semana_number[semana_i]) {
                                            return [true, ''];
                                        }
                                    }
                                }else{
                                    return [true, ''];
                                }
                            }else if(calendar[i].select_termino == "ocorrencia"){
                                var date_inicial_ocorrencia = new Date(calendar[i].data_inicial.replace('-','/'));
                                var dias = (calendar[i].ocorrencia*7)-(date_inicial_ocorrencia.getDay()+1);
                                date_inicial_ocorrencia.setDate(date_inicial_ocorrencia.getDate()+dias);
                                if(date <= date_inicial_ocorrencia){
                                    if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                    if(calendar[i].semana){
                                        var semana_number = semanaNumber(calendar[i].semana);
                                        for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                            if(date.getDay() == semana_number[semana_i]) {
                                                return [true, ''];
                                            }
                                        }
                                    }else{
                                        return [true, ''];
                                    }
                                }
                            }else if(calendar[i].select_termino == "nunca"){
                                if(!calendar_repetir(calendar[i]).includes(`${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()}`)) return [false, ''];
                                if(calendar[i].semana){
                                    var semana_select = calendar[i].semana[semana_dia[date.getDay()]] || [];
                                    if(semana_select['horario']){
                                        var semana_horario = arrayChunk(semana_select['horario'],2);
                                        if(filterServiceReservationHour(date, semana_horario)){
                                            select_date_fim = true;
                                            return [false, ''];
                                        }
                                    }
                                    var semana_number = semanaNumber(calendar[i].semana);
                                    for(var semana_i=0; semana_number.length>=semana_i; semana_i++) {
                                        if(date.getDay() == semana_number[semana_i]) {
                                            return [true, ''];
                                        }
                                    }
                                }else{
                                    return [true, ''];
                                }
                            }
                        }
                    }else{
                        return [false, ''];
                    }
                    select_date_fim = true;
                    return [false, ''];
                },
                onSelect: function(date){
                    $(this).parent().find('span').html(date);
                }
            });
        }
    </script>
@endsection