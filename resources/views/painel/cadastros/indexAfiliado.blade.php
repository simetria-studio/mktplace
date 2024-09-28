@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Comissões Afiliados</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Comissões Afiliados</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Produtos / Serviços Afiliados</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
                            <div class="container">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#novaComissao"><i class="fas fa-plus"></i> Nova Comissão</button>
                                    </div>
                                </div>

                                <form action="" method="get">
                                    <div class="row mt-2">
                                        <div class="col-12"><h4>Filtros para comissões</h4></div>
                                        <div class="col-12 col-md-5 form-group @if(auth('seller')->check()) d-none @endif">
                                            <div class="input-group">
                                                <select name="order" class="form-control" @if(auth('seller')->check()) disabled @endif>
                                                    <option value="">.: Selecione uma Ordenação :.</option>
                                                    <option value="ASC" @isset($_GET['order']) @if($_GET['order'] == 'ASC') selected @endif @endisset>Ordem Crescente</option>
                                                    <option value="DESC" @isset($_GET['order']) @if($_GET['order'] == 'DESC') selected @endif @endisset>Ordem Descrecente</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-5 form-group">
                                            <div class="input-group">
                                                <select name="type" class="form-control" @if(auth('seller')->check()) disabled @endif>
                                                    <option value="">.: Selecione um Tipo :.</option>
                                                    <option value="product" @isset($_GET['type']) @if($_GET['type'] == 'product') selected @endif @endisset>Produto</option>
                                                    <option value="service" @isset($_GET['type']) @if($_GET['type'] == 'service') selected @endif @endisset>Serviço</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-2 form-group">
                                            <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-12 col-md-4 form-group">
                                            <label for="">Comissões por Página</label>
                                            <select name="per_page_p" class="form-control">
                                                <option value="20" @isset($_GET['per_page_p']) @if($_GET['per_page_p'] == '20') selected @endif @endisset>20 por Página</option>
                                                <option value="30" @isset($_GET['per_page_p']) @if($_GET['per_page_p'] == '30') selected @endif @endisset>30 por Página</option>
                                                <option value="50" @isset($_GET['per_page_p']) @if($_GET['per_page_p'] == '50') selected @endif @endisset>50 por Página</option>
                                                <option value="100" @isset($_GET['per_page_p']) @if($_GET['per_page_p'] == '100') selected @endif @endisset>100 por Página</option>
                                                <option value="500" @isset($_GET['per_page_p']) @if($_GET['per_page_p'] == '500') selected @endif @endisset>500 por Página</option>
                                                <option value="1000" @isset($_GET['per_page_p']) @if($_GET['per_page_p'] == '1000') selected @endif @endisset>1000 por Página</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="container mt-2 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nº</th>
                                            <th>Nome</th>
                                            <th>Tipo</th>
                                            <th>Comissão</th>
                                            {{-- <th>Slug</th> --}}
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($items as $item)
                                            <tr class="tr-id-{{$item->id}}">
                                                <td>{{$item->id}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->reference_type == 'product' ? 'Produto' : 'Serviço'}}</td>
                                                <td>{{$item->price_type == 'percentage' ? '%' : 'R$'}} {{number_format($item->price,2,',','.')}}</td>
                                                {{-- <td>{{$category->slug}}</td> --}}
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="">
                                                        <a href="#" class="btn btn-info btn-xs btn-editar" data-toggle="modal" data-target="#editarComissao" data-dados="{{json_encode($item)}}"><i class="fas fa-edit"></i> Alterar</a>

                                                        <a href="#" class="btn btn-danger btn-xs btn-editar" data-toggle="modal" data-target="#excluirComissao" data-dados="{{json_encode($item)}}"><i class="fas fa-trash"></i> Apagar</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <th colspan="6">{{$items->count()}} Comissões</th>
                                    </tfoot>
                                </table>
                                <div class="col-12 mt-2">
                                    {{$items->appends(['order' => $_GET['order'] ?? null, 'search' => $_GET['search'] ?? null, 'per_page_p' => $_GET['per_page_p'] ?? ''])->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="novaComissao" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="#" method="post" id="postNovaComissao">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Nova Comissão</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <input type="radio" class="click-frete custom-radio r-type" name="reference_type" value="product">
                                <label for="">Produto</label>
                                <input type="radio" class="click-frete custom-radio r-type ml-3" name="reference_type" value="service">
                                <label for="">Serviço</label>
                            </div>
                            <div class="form-group col-12 d-none div-produto">
                                <label for="produto_id">Produto</label>
                                <select name="produto_id[]" class="form-control selectpicker select-produto" data-header="Selecione os Produtos" data-size="5" data-actions-box="true" data-live-search="true" title="Escolha os Produtos" multiple>
                                    @foreach ($produtos as $produto)
                                        <option value="{{$produto->id}}">{{$produto->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 d-none div-servico">
                                <label for="servico_id">Serviço</label>
                                <select name="servico_id[]" class="form-control selectpicker select-servico" data-header="Selecione os Serviços" data-size="5" data-actions-box="true" data-live-search="true" title="Escolha os Serviços" multiple>
                                    @foreach ($servicos as $servico)
                                        <option value="{{$servico->id}}">{{$servico->service_title}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12">
                                <label for="">Preço</label><br>
                                <input type="radio" class="click-frete custom-radio" name="price_type" value="percentage">
                                <label for="">Porcentagem</label>
                                <input type="radio" class="click-frete custom-radio ml-3" name="price_type" value="money">
                                <label for="">Valor Real</label>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="price">Valor</label>
                                <input type="text" name="price" class="form-control real" placeholder="Informe de acordo com a opção selecionada acima">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-refresh="S" data-save_target="#postNovaComissao" data-save_route="{{route('novaComissao')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarComissao" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postEditarComissao">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="reference_id">
                    <input type="hidden" name="reference_type">
                    <div class="modal-header">
                        <h4 class="modal-title">Atualizar Comissão</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="">Preço</label><br>
                                <input type="radio" class="click-frete custom-radio price-0" name="price_type" value="percentage">
                                <label for="">Porcentagem</label>
                                <input type="radio" class="click-frete custom-radio ml-3 price-1" name="price_type" value="money">
                                <label for="">Valor Real</label>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="price">Valor</label>
                                <input type="text" name="price" class="form-control real">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar" data-update_table="S" data-save_target="#postEditarComissao" data-save_route="{{route('atualizarComissao')}}"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="excluirComissao">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="postExcluirComissao">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Excluir Comissão</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>Tem certeza que gostaria de apagar essa Comissão?</h5>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-danger btn-salvar" data-refresh="S" data-trash="S" data-save_target="#postExcluirComissao" data-save_route="{{route('excluirComissao')}}"><i class="fas fa-trash"></i> Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.r-type', function(){
            var valor = $(this).val();

            if(valor == 'product')
            {
                $(".div-produto").removeClass('d-none');
                $(".div-servico").addClass('d-none');
                $('.select-produto').selectpicker('deselectAll');
            }
            else if(valor == 'service')
            {
                $(".div-servico").removeClass('d-none');
                $(".div-produto").addClass('d-none');
                $('.select-servico').selectpicker('deselectAll');
            }
        });

        $("#novaComissao").on('hidden.bs.modal', function(){
            $('#postNovaComissao').trigger("reset");
            $(".div-servico, .div-produto").addClass('d-none');
        });
    </script>
@endsection