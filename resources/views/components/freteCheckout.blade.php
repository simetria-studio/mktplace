<div class="div-fretes">
    @if ($fretes['transporte_proprio'][0] ?? null)
        <div class="col-frete">
            <div class="d-flex justify-content-between">
                <div class="col-3">
                    <div class="custom-checkbox">
                        <input type="checkbox" class="check-fretes" data-seller_id="{{ $seller_id }}" data-transporte='{!! json_encode($fretes['transporte_proprio'][0]) !!}'' data-tipo="TransportePropio" id="checkTransportePropio">
                    </div>
                    <label for="checkTransportePropio">Transporte próprio</label>
                </div>
                <div class="col-3">
                    Entrega até {{ $fretes['transporte_proprio'][0]['tempo_entrega'] }} 
                    @switch($fretes['transporte_proprio'][0]['tempo'])
                        @case('D')
                            dias utéis
                            @break
                        @case('H')
                            horas
                            @break
                        @default
                            
                    @endswitch
                </div>
                <div class="col-3 price">R$ {{ number_format($fretes['transporte_proprio'][0]['valor_entrega'], 2, ',', '.') }}</div>
            </div>
            <div style="margin-left: 14px;">{{ $fretes['transporte_proprio'][0]['descricao'] }}</div>
        </div>
    @endif

    <div style="border: 1px solid #b1b1b1e0;"></div>

    <div style="margin-left: 27px;margin-top: 12px;">Melhor envio</div>
    @foreach (($fretes['transportadoras'] ?? []) as $item)
        @if ((!($item->error ?? null) && ($item->id ?? null)))
            <div class="col-frete-2">
                <div class="d-flex justify-content-between">
                    <div class="col-3">
                        <div class="custom-checkbox">
                            <input type="checkbox" class="check-fretes" data-seller_id="{{ $seller_id }}" data-transporte='{!! json_encode($item) !!}'' data-tipo="Transportadora" id="checkTransporte{{ $item->id }}">
                        </div>
                        <label for="checkTransporte{{ $item->id }}">{{ $item->name }}</label>
                    </div>
                    <div class="col-3">
                        Entrega até {{ $item->custom_delivery_time }} dias utéis
                    </div>
                    <div class="col-3 price">R$ {{ number_format($item->custom_price, 2, ',', '.') }}</div>
                </div>
            </div>
        @endif
    @endforeach

    <div style="border: 1px solid #b1b1b1e0;"></div>

    <div style="margin-left: 27px;margin-top: 12px;">Lugares para retirada</div>
    @foreach (($fretes['locais_retirada'] ?? []) as $item)
        <div class="col-frete-2">
            <div class="d-flex justify-content-between">
                <div class="col-3">
                    <div class="custom-checkbox">
                        <input type="checkbox" class="check-fretes" data-seller_id="{{ $seller_id }}" data-transporte='{!! json_encode($item) !!}'' data-tipo="LocaisDeRetirada" id="checkLocalRetirada{{ $item['id'] }}">
                    </div>
                    <label for="checkLocalRetirada{{ $item['id'] }}">{{ $item['localidade']['title'] }}</label>
                </div>
                <div class="col-6">
                    {{ $item['localidade']['address'] }}, {{ $item['localidade']['number'] }} - {{ $item['localidade']['district'] }} - {{ $item['localidade']['city'] }}/{{ $item['localidade']['state'] }}
                </div>
                <div class="col-3 price"></div>
            </div>
        </div>
        <div style="margin-left: 27px;">{{ $item['description'] }}</div>
    @endforeach
</div>