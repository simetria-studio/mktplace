<div class="modal fade" id="modalLocalDeRetirada">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postModalLocalDeRetirada">
                @csrf
                @if (($request->edit_id ?? null))
                    <input type="hidden" name="id" value="{{$request->edit_id}}">
                @endif
                <div class="modal-header">
                    <h4 class="modal-title">Local de Retirada</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="localidade_id">Locais Disponiveis</label>
                            <select name="localidade_id" class="form-control select2">
                                <option value="">::Selecione uma Opção::</option>
                                @foreach ($localidades as $localidade)
                                    <option @if(($localretirada->localidade->id ?? 'N') == $localidade->id) selected @endif data-dados='{{ $localidade->toJson() }}' value="{{ $localidade->id }}">{{$localidade->title}} -/- {{$localidade->city}}/{{$localidade->state}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-12 outros-dados">
                            @if ($localretirada->id ?? null)
                                <div class="row">
                                    <div class="col-12">
                                        <label for="address">Endereço</label>
                                        <p>{{$localretirada->localidade->address}}, Nº {{$localretirada->localidade->number}}, {{$localretirada->localidade->district}} - {{$localretirada->localidade->city}}/{{$localretirada->localidade->state}} - {{$localretirada->localidade->zip_code}}</p>
                                    </div>
                                    <div class="col-12">
                                        <label for="address">Observações</label>
                                        <p>{{$localretirada->localidade->description}}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-group col-12">
                            <label for="description">Descrição (data e hora entre outra informações)</label>
                            <textarea name="description" class="form-control">{!! $localretirada->description ?? '' !!}</textarea>
                        </div>

                        {{-- <div class="form-group col-12">
                            <div class="form-check">
                                <input type="checkbox" @if(($localretirada->all_products ?? null)) checked @endif name="all_products" id="all_products" class="form-check-input all_products" value="S">
                                <label for="all_products">Todos os produtos?</label>
                            </div>
                        </div> --}}

                        {{-- <div class="form-group col-12 produtos @if(($localretirada->all_products ?? null)) d-none @endif">
                            <label for="products_id">Produtos</label>
                            <select name="products_id[]" class="form-control select2" multiple>
                                <option value="">::Selecione uma Opção::</option>
                                @foreach ($produtos as $produto)
                                    <option @if(in_array($produto->id, ($localretirada->products_id ?? []))) selected @endif value="{{ $produto->id }}">{{$produto->nome}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar" data-table_html="#tableLocalDeRetirada" data-save_target="#postModalLocalDeRetirada" data-save_route="{{route('seller.localDeRetirada', 'any-local-de-retirada')}}"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>