@php
    $rand = rand(1,999);
    $variationRandId = "variation{$rand}";
    $inputName = "variations[{$rand}]";
    /** @var \App\Models\VariationsProduto $variationUpdateModel */
    $tr_proprio = $data['transporte_proprio'] ?? ($variationUpdateModel->produto->perecivel ?? 'false');
    $stock_controller = $data['stock_controller'] ?? ($variationUpdateModel->produto->stock_controller ?? 'false');
    $attrs_id = $data['attrs_id'] ?? [];
    if(isset($variationUpdateModel->produto->attrAttrs)){
        foreach($variationUpdateModel->produto->attrAttrs as $attr_temp){
            $attrs_id[] = $attr_temp->attribute_id;
        }
    }
    $check_discount = $data['check_discount'] ?? false;
@endphp

<div class="card col-12 col-sm-3 mx-2" style="width: 18rem;" id="{{$variationRandId}}" data-varranid="{{$rand}}">
    @if (isset($variationUpdateModel))
        <input type="hidden" value="{{$variationUpdateModel->id}}" name="{{$inputName}}[variation_id]" id="id_{{$variationRandId}}variationid">
    @endif
    <div id="itensVariacoes{{$rand}}">
        <div class="card-body">
            <div class="card-div-var">
                @foreach(\App\Models\Attribute::with('variations')->whereIn('id', $attrs_id)->get(['id', 'name']) as $atributo)
                    <div class="row mb-2 select-variation-{{$atributo->id}}">
                        <div class="col-12">
                            <label style="font-size: .8rem;" for="id_{{$variationRandId}}attributos{{$atributo->id}}">{{$atributo->name}}:</label>
                        </div>
                        <div class="col-5 pr-0">
                            <select class="form-control form-control-sm attr-variation-{{$atributo->id}}" id="id_{{$variationRandId}}attributos{{$atributo->id}}" data-attr_id="{{$atributo->id}}" name="{{$inputName}}[attributos][]">
                                <option value="0-{{$atributo->id}}">Qualquer um</option>
                                @foreach($atributo->variations as $variation)
                                    @php
                                        $selected = '';
                                        if(isset($variationUpdateModel))
                                            $selected = ($variationUpdateModel?->variations->groupBy('attribute_pai_id')->map(function ($query){return ['total' => $query->count(), 'attr_id' => $query[0]->attribute_id];})->where('attr_id', $variation->id)->where('total', '1')[$atributo->id]['attr_id'] ?? 0)?'selected':'';
                                    @endphp
                                    <option {{$selected}} value="{{$variation->id}}-{{$atributo->id}}">{{$variation->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-7 pl-1 d-flex align-items-end">
                            <div class="pr-1"><b>OU</b></div>
                            <div>
                                <button class="btn btn-sm btn-outline-success btn-new-attribute-value" style="font-size: .7rem;" type="button">CRIAR VARIAÇÃO</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div id="itensVariacoesCollapse{{$rand}}">
                <div class="row mb-1">
                    <div class="col-6 pr-0 pt-1 border rounded text-bold" style="background-color: #F2F2F2;">Preço</div>
                    <div class="col-6 pl-0"><input type="number" step="0.01" name="{{$inputName}}[preco]" value="{{$variationUpdateModel->preco??''}}" class="form-control form-control-sm" placeholder="R$"></div>
                </div>
                <div class="row mb-1 stock-attr @if($stock_controller == 'false') d-none @endif">
                    <div class="col-6 pr-0 pt-1 border rounded text-bold" style="background-color: #F2F2F2;">Estoque</div>
                    <div class="col-6 pl-0"><input type="number" name="{{$inputName}}[stock]" value="{{$variationUpdateModel->stock??''}}" class="form-control form-control-sm" placeholder="un"></div>
                </div>
                <div class="row mb-1 perecivel-attr @if($tr_proprio == 'true' || $tr_proprio == '1') d-none @endif">
                    <div class="col-6 pr-0 pt-1 border rounded text-bold" style="background-color: #F2F2F2;">Peso</div>
                    <div class="col-6 pl-0"><input type="number" name="{{$inputName}}[peso]" value="{{$variationUpdateModel->peso??''}}" class="form-control form-control-sm" placeholder="Kg"></div>
                </div>
                <div class="row mb-1 perecivel-attr @if($tr_proprio == 'true' || $tr_proprio == '1') d-none @endif">
                    <div class="col-6 pr-0 pt-1 border rounded text-bold" style="background-color: #F2F2F2;">Altura</div>
                    <div class="col-6 pl-0"><input type="number" name="{{$inputName}}[dimensoes_A]" value="{{$variationUpdateModel->dimensoes_A??''}}" class="form-control form-control-sm" placeholder="cm"></div>
                </div>
                <div class="row mb-1 perecivel-attr @if($tr_proprio == 'true' || $tr_proprio == '1') d-none @endif">
                    <div class="col-6 pr-0 pt-1 border rounded text-bold" style="background-color: #F2F2F2;">Largura</div>
                    <div class="col-6 pl-0"><input type="number" name="{{$inputName}}[dimensoes_C]" value="{{$variationUpdateModel->dimensoes_C??''}}" class="form-control form-control-sm" placeholder="cm"></div>
                </div>
                <div class="row mb-1 perecivel-attr @if($tr_proprio == 'true' || $tr_proprio == '1') d-none @endif">
                    <div class="col-6 pr-0 pt-1 border rounded text-bold" style="background-color: #F2F2F2;">Comprimento</div>
                    <div class="col-6 pl-0"><input type="number" name="{{$inputName}}[dimensoes_L]" value="{{$variationUpdateModel->dimensoes_L??''}}" class="form-control form-control-sm" placeholder="cm"></div>
                </div>
                <div class="form-group my-2">
                    <div class="div-row-desconto-var row justify-content-center">
                        @isset($variationUpdateModel->progressiveDiscount)
                            @foreach ($variationUpdateModel->progressiveDiscount as $key => $discount)
                                <div class="col-12 discount-var-{{$key}}">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <input type="hidden" name="{{$inputName}}[discount][{{$key}}][id]" value="{{$discount->id}}">
                                            <div class="row justify-content-center" style="font-size: .8rem;">
                                                <div class="col-12 d-flex justify-content-center">
                                                    <label for="">Acima de</label>
                                                    <input type="number" class="form-control form-control-sm mx-2" name="{{$inputName}}[discount][{{$key}}][discount_quantity]" value="{{$discount->discount_quantity}}" style="font-size: .8rem; width: 20%; height: 23px;">
                                                    <label for=""> unidades </label>
                                                </div>
                                                <div class="d-flex justify-content-center col-12">
                                                    <label for=""> o preço é </label>
                                                    <input type="text" class="form-control form-control-sm mx-2 real" name="{{$inputName}}[discount][{{$key}}][discount_value]" value="{{$discount->discount_value}}" placeholder="R$" style="font-size: .8rem; width: 30%; height: 23px;">
                                                </div>
    
                                                <div class="col-12 text-center mt-1">
                                                    <button type="button" class="btn btn-sm btn-primary btn-remove-desconto-card" style="font-size: .8rem; padding: 0 8px;"><i class="fas fa-times"></i> remover</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endisset
                    </div>
                </div>
                <div class="d-flex mt-2">
                    <button type="button" class="btn btn-sm btn-success btn-add-desconto-card-variacao mr-1" data-rand="{{$inputName}}"><i class="fas fa-percentage"></i> Adicionar Desconto</button>
                    <button type="button" onclick="removerVariacao{{$variationRandId}}()" class="btn btn-sm btn-danger ml-1">Remover</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function removerVariacao{{$variationRandId}}() {
            $('#{{$variationRandId}}').remove();
        }

        function atualizaValores{{$variationRandId}}() {
            console.log('atualizaValores{{$variationRandId}}');
            $("#variacaoTextoExplicativo{{$variationRandId}}").html(
                @foreach(\App\Models\Attribute::with('variations')->whereHas('variations')->get(['id', 'name']) as $atributo)
                `{{$atributo->name}}: ${$("#id_{{$variationRandId}}attributos{{$atributo->id}} option:selected").text()}<br>` +
                @endforeach
                `<br>o valor será: <b>${$('input[name="{{$inputName}}[preco]"]').val()}</b>`
            );
        }

        if (typeof $ === 'function') {
            atualizaValores{{$variationRandId}}();

            $('#itensVariacoes{{$rand}} input, #itensVariacoes{{$rand}} select').on('change', atualizaValores{{$variationRandId}});
            $('#itensVariacoes{{$rand}} input, #itensVariacoes{{$rand}} select').on('click', atualizaValores{{$variationRandId}});
            $('#itensVariacoes{{$rand}} input, #itensVariacoes{{$rand}} select').on('keyup', atualizaValores{{$variationRandId}});
        }
    </script>
</div>
