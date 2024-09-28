<div class="modal fade" id="modalLocalidadeRetirada">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postModalLocalidadeRetirada">
                @csrf
                @if (($request->edit_id ?? null))
                    <input type="hidden" name="id" value="{{$request->edit_id}}">
                @endif
                <div class="modal-header">
                    <h4 class="modal-title">Localidade de Retirada</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="title">Titulo</label>
                            <input type="text" name="title" class="form-control" placeholder="Titulo" value="{{$localidade->title ?? ''}}">
                        </div>

                        <div class="form-group col-12">
                            <label for="description">Descrição</label>
                            <textarea name="description" class="form-control">{{$localidade->description ?? ''}}</textarea>
                        </div>

                        <div class="form-group col-5 col-md-4">
                            <label for="zip_code">CEP</label>
                            <input type="text" name="zip_code" class="form-control post_code" placeholder="00000-000" value="{{$localidade->zip_code ?? ''}}">
                        </div>
                        <div class="form-group col-7 col-md-8">
                            <label for="address">Endereço</label>
                            <input type="text" name="address" class="form-control" placeholder="Endereço/Rua/Avenida" value="{{$localidade->address ?? ''}}">
                        </div>
                        <div class="form-group col-3">
                            <label for="number">Nº</label>
                            <input type="text" name="number" class="form-control" placeholder="0000" value="{{$localidade->number ?? ''}}">
                        </div>
                        <div class="form-group col-9">
                            <label for="district">Bairro</label>
                            <input type="text" name="district" class="form-control address2" placeholder="Bairro" value="{{$localidade->district ?? ''}}">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="state">Estado</label>
                            <select name="state" class="form-control select2 state">
                                <option value="">::Selecione uma Opção::</option>
                                @isset ($localidade->state)
                                    <option value="{{$localidade->state}}" selected>{{$localidade->state}}</option>
                                @endisset
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="city">Cidade</label>
                            <select name="city" class="form-control select2 city">
                                <option value="">::Selecione uma Opção::</option>
                                @isset ($localidade->city)
                                    <option value="{{$localidade->city}}" selected>{{$localidade->city}}</option>
                                @endisset
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar" data-table_html="#tableLocalidadesRetirada" data-save_target="#postModalLocalidadeRetirada" data-save_route="{{route('admin.localidadeRetirada', 'any-localidade-retirada')}}"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>