@php
    $semana_array = [
        ['Domingo', 'domingo', 'dom', 'D'],
        ['Segunda', 'segunda', 'seg', 'S'],
        ['Terça', 'terca', 'ter', 'T'],
        ['Quarta', 'quarta', 'qua', 'Q'],
        ['Quinta', 'quinta', 'qui', 'Q'],
        ['Sexta', 'sexta', 'sex', 'S'],
        ['Sábado', 'sabado', 'sab', 'S'],
    ];
@endphp

<div class="card col-12 col-md-{{$data['z']}} mx-2 date-card-custom" id="data-card-rand-{{$data['x']}}">
    @if (isset($data['data_x']))
        <input type="hidden" name="{{$data['y']}}[{{$data['x']}}][id]" value="{{$data['data_x']['id']}}">
    @endif
    <div class="card-body">
        <div class="form-group">
            <label>Data de Início</label>
            <input type="text" class="form-control form-control-sm date-mask-custom text-center"  @isset($data['data_x'])value="{{date('d/m/Y', strtotime($data['data_x']['data_inicial']))}}"@endisset name="{{$data['y']}}[{{$data['x']}}][data_inicial]">
        </div>
        <div class="form-group">
            <label>Selecionar com Antecedência</label>
            <div class="input-group input-group-sm">
                <input type="text" class="form-control text-center" @isset($data['data_x'])value="{{$data['data_x']['antecedencia']}}"@endisset name="{{$data['y']}}[{{$data['x']}}][antecedencia]" value="0">
                <div class="input-group-append">
                    <span class="input-group-text">Dias</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Repetir a cada</label>
            <div class="input-group">
                <input type="text" class="form-control form-control-sm w-25 text-center" @isset($data['data_x'])value="{{$data['data_x']['number_select']}}"@endisset name="{{$data['y']}}[{{$data['x']}}][number_select]" value="1">
                <select class="form-control form-control-sm w-75" name="{{$data['y']}}[{{$data['x']}}][select_control]">
                    <option value="semana" @isset($data['data_x'])@if($data['data_x']['select_control'] == 'semana')selected @endif @endisset>Semana</option>
                    <option value="mes" @isset($data['data_x'])@if($data['data_x']['select_control'] == 'mes')selected @endif @endisset>Mês</option>
                    <option value="ano" @isset($data['data_x'])@if($data['data_x']['select_control'] == 'ano')selected @endif @endisset>Ano</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Repetir</label>
            <div class="d-flex justify-content-between">
                @foreach ($semana_array as $sem)
                    <div>
                        <label for="semana_{{$sem[2]}}-{{$data['x']}}" class="check-custom @isset($data['data_x']['semana'][$sem[1]][$sem[2]])active @endisset">{{$sem[3]}}</label>
                        <input id="semana_{{$sem[2]}}-{{$data['x']}}" type="checkbox" class="d-none check-semana" @isset($data['data_x']['semana'][$sem[1]][$sem[2]])checked @endisset data-card="{{$sem[2]}}" name="{{$data['y']}}[{{$data['x']}}][semana][{{$sem[1]}}][{{$sem[2]}}]" value="{{$sem[2]}}">    
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label>Horário da Semana</label>
            <div class="accordion" id="accordionSemana">
                @foreach ($semana_array as $sem)
                    <div class="card mb-1 @if(!isset($data['data_x']['semana'][$sem[1]][$sem[2]]))d-none @endif  card-semana-{{$sem[2]}}">
                        <div class="card-header p-1" id="div-semana-{{$sem[2]}}-{{$data['x']}}">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#div-semana-{{$sem[2]}}" aria-expanded="true" aria-controls="div-semana-{{$sem[2]}}">
                                    <i class="fas fa-arrow-down"></i> Editar Horários de {{$sem[0]}}
                                </button>
                            </h2>
                        </div>

                        <div id="div-semana-{{$sem[2]}}" class="collapse" aria-labelledby="div-semana-{{$sem[2]}}-{{$data['x']}}" data-parent="#accordionSemana">
                            <div class="card-body">
                                <div class="row input-time">
                                    @isset($data['data_x']['semana'][$sem[1]]['horario'])
                                        @foreach (array_chunk($data['data_x']['semana'][$sem[1]]['horario'], 2) as $item)
                                            <div class="form-group col-12 d-flex">
                                                <button type="button" class="close btn-remove-time">x</button>
                                                <input type="time" class="form-control form-control-sm mr-1" value="{{$item[0] ?? ''}}" name="{{$data['y']}}[{{$data['x']}}][semana][{{$sem[1]}}][horario][]">
                                                <input type="time" class="form-control form-control-sm ml-1" value="{{$item[1] ?? ''}}" name="{{$data['y']}}[{{$data['x']}}][semana][{{$sem[1]}}][horario][]">
                                            </div>
                                        @endforeach
                                    @endisset
                                    <div class="form-group col-12 d-flex">
                                        <button type="button" class="close btn-remove-time">x</button>
                                        <input type="time" class="form-control form-control-sm mr-1" name="{{$data['y']}}[{{$data['x']}}][semana][{{$sem[1]}}][horario][]">
                                        <input type="time" class="form-control form-control-sm ml-1" name="{{$data['y']}}[{{$data['x']}}][semana][{{$sem[1]}}][horario][]">
                                    </div>
                                    <div class="form-group col-12 d-flex">
                                        <button type="button" class="btn btn-sm btn-primary btn-qty-add-time" data-semana="{{$sem[1]}}" data-x="{{$data['x']}}" data-y="{{$data['y']}}"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label>termina em</label>
            <select class="form-control form-control-sm select_termino" name="{{$data['y']}}[{{$data['x']}}][select_termino]">
                <option value="nunca" @isset($data['data_x'])@if($data['data_x']['select_termino'] == 'nunca')selected @endif @endisset>Nunca</option>
                <option value="data_fim" @isset($data['data_x'])@if($data['data_x']['select_termino'] == 'data_fim')selected @endif @endisset>EM</option>
                <option value="ocorrencia" @isset($data['data_x'])@if($data['data_x']['select_termino'] == 'ocorrencia')selected @endif @endisset>Após</option>
            </select>
        </div>
        <div class="form-group date-fim @if(!isset($data['data_x']))d-none @endif @isset($data['data_x'])@if($data['data_x']['select_termino'] !== 'data_fim')d-none @endif @endisset">
            <label>Data do Fim</label>
            <input type="text" class="form-control form-control-sm date-mask-custom text-center" @isset($data['data_x'])value="{{date('d/m/Y', strtotime(str_replace('-','/',$data['data_x']['data_fim'])))}}"@endisset name="{{$data['y']}}[{{$data['x']}}][data_fim]">
        </div>
        <div class="form-group date-ocorrencia @if(!isset($data['data_x']))d-none @endif @isset($data['data_x'])@if($data['data_x']['select_termino'] !== 'ocorrencia')d-none @endif @endisset">
            <label>Ocorrência</label>
            <input type="text" class="form-control form-control-sm" @isset($data['data_x'])value="{{$data['data_x']['ocorrencia']}}"@endisset name="{{$data['y']}}[{{$data['x']}}][ocorrencia]">
        </div>

        <div class="mt-2 text-center"><a href="#" class="btn btn-sm btn-danger btn-remove-data-card" data-id="{{$data['x']}}">remover</a></div>
    </div>
</div>