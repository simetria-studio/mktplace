@extends(auth()->guard('seller')->check() ? 'layouts.painelSman' : 'layouts.painelAdm')

@section('container')
    @isset($request->id)
        <input type="hidden" id="product_id" value="{{$request->id}}">
    @endisset
    @isset($novo_product_id)
        <input type="hidden" id="product_id" value="{{$novo_product_id}}">
    @endisset
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mt-3">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">{{($request->id ?? null) ? 'Editar Produto '.$product->nome : 'Novo Produto'}}</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                        title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pad">
                            <div class="container-fluid">
                                <input type="hidden" class="pills-active" value="{{$request->step ?? 'inicio'}}">

                                <ul class="nav nav-pills mb-3 nav-form-produto-servico" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="pills-inicio-tab" data-toggle="pill" href="#pills-inicio" role="tab" aria-controls="pills-inicio" aria-selected="true">Início</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link @if($function_slug == 'novo') inactive @endif" id="pills-descricao-tab" data-toggle="pill" href="#pills-descricao" role="tab" aria-controls="pills-descricao" aria-selected="false">Descrição</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link @if($function_slug == 'novo') inactive @endif" id="pills-fotos-tab" data-toggle="pill" href="#pills-fotos" role="tab" aria-controls="pills-fotos" aria-selected="false">Fotos</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link @if($function_slug == 'novo') inactive @endif d-none" id="pills-variacoes-tab" data-toggle="pill" href="#pills-variacoes" role="tab" aria-controls="pills-variacoes" aria-selected="false">Variações</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link @if($function_slug == 'novo') inactive @endif d-none" id="pills-assinatura-tab" data-toggle="pill" href="#pills-assinatura" role="tab" aria-controls="pills-assinatura" aria-selected="false">Assinatura</a>
                                    </li>
                                    @if (auth()->guard('admin')->check())
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link @if($function_slug == 'novo') inactive @endif" id="pills-seo-tab" data-toggle="pill" href="#pills-seo" role="tab" aria-controls="pills-seo" aria-selected="false">SEO</a>
                                        </li>
                                    @endif
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-inicio" role="tabpanel" aria-labelledby="pills-inicio-tab">
                                        <form id="form-inicio">
                                            <input type="hidden" name="postType" value="postDataInicio">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="">Selecione o tipo de produto</label>
                                                    <div>
                                                        <div class="form-check-inline">
                                                            <input type="checkbox" id="check_produto_simples" name="produto_simples" value="true" class="form-check-input check-custom" @if((count($product->variations ?? []) == 0 && count($product->planPurchases ?? []) == 0)) checked @endif>
                                                            <label for="check_produto_simples" class="form-check-label btn-check-custom @if((count($product->variations ?? []) == 0 && count($product->planPurchases ?? []) == 0)) active @endif">Produto simples</label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <input type="checkbox" id="check_produto_variavel" name="produto_variavel" value="true" class="form-check-input check-custom" @if(count($product->variations ?? []) > 0) checked @endif>
                                                            <label for="check_produto_variavel" class="form-check-label btn-check-custom @if(count($product->variations ?? []) > 0) active @endif">Produto variável</label>
                                                        </div>
                                                        <div class="form-check-inline">
                                                            <input type="checkbox" id="check_plano_assinatura" name="plano_assinatura" value="true" class="form-check-input check-custom" @if(count($product->planPurchases ?? []) > 0) checked @endif>
                                                            <label for="check_plano_assinatura" class="form-check-label btn-check-custom @if(count($product->planPurchases ?? []) > 0) active @endif">Plano de assinatura</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(!auth('seller')->check())
                                                    <div class="form-group col-12 col-sm-8">
                                                        <label for="name">Vendedor</label>
                                                        <select name="seller_id" class="form-control selectpicker seller_id" data-header="Selecione o Vendedor" data-size="4" data-live-search="true" title="Escolha um Vendedor" required>
                                                            <option value="" disabled>(escolha um vendedor)</option>
                                                            {!! $seller->map(function (\App\Models\Seller $seller) use($product){
                                                                return "<option value='$seller->id' ".(($product->seller_id ?? 0) == $seller->id ? 'selected' : '').">".($seller->store->store_name ?? $seller->name)."</option>";
                                                            })->join("\n") !!}
                                                        </select>
                                                    </div>
                                                @endif
                                                @if(auth('seller')->check())
                                                    <input type="hidden" name="seller_id" class="seller_id" value="{{auth('seller')->user()->id}}">
                                                @endif

                                                <div class="form-group col-12 col-sm-8">
                                                    <label for="name">Nome do Produto</label>
                                                    <input id="name" type="text" name="name" value="{{$product->nome ?? ''}}" class="form-control" placeholder="Nome do produto">
                                                </div>
                                                @if (auth()->guard('seller')->check() == false)
                                                    <div class="form-group col-12 col-sm-8">
                                                        <label for="name">Slug</label>
                                                        <input type="text" name="slug" class="form-control" value="{{$product->slug ?? ''}}" placeholder="Slug do produto">
                                                    </div>
                                                @endif
                                                <div class="form-group col-12 col-sm-8">
                                                    <div class="check-select" data-title="Selecione as categorias" data-btn_save="salvar categorias">
                                                        @foreach ($categories as $category)
                                                            <div class="check-select-div-input col-12 col-sm-3" title="{{$category->name}}" data-toggle="tooltip">
                                                                <input type="checkbox" id="{{$category->slug}}-{{$category->id}}" name="categories[]" @if(count($product->categories ?? []) > 0) @if(in_array($category->id,$product->categories->map(function($query) {return $query->category_id;})->toArray())) checked @endif @endif value="{{$category->id}}">
                                                                <label class="text-truncate" for="{{$category->slug}}-{{$category->id}}">{{$category->name}}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-5">
                                                            <div class="row">
                                                                <div class="col-12"><label for="">&nbsp;</label></div>

                                                                <div class="form-group col-12">
                                                                    <div class="form-check">
                                                                        <input type="checkbox" name="stock_controller" id="check_stock_controller" class="form-check-input @if(($product->stock_controller ?? null)) event-time-click @endif" value="true">
                                                                        <label for="check_stock_controller" class="form-check-label text-bold">Controle de Estoque?</label>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <div class="form-check">
                                                                        <input type="checkbox" name="perecivel" id="check_perecivel" class="form-check-input @if(($product->perecivel ?? null)) event-time-click @endif" value="true">
                                                                        <label for="check_perecivel" class="form-check-label text-bold">Entrega Local</label>
                                                                        <div>
                                                                            <span style="font-size: .7rem;">
                                                                                O produto é entregue apenas localmente com entrega própria ou parceira.
                                                                                <br>
                                                                                (OBS.: Nesse caso não será mostrado as opções do Melhor Envio)
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <div class="form-check">
                                                                        <input type="checkbox" name="check_desconto" id="check_desconto" class="form-check-input @if(count($product->progressiveDiscount ?? []) > 0) event-time-click @endif" value="true">
                                                                        <label for="check_desconto" class="form-check-label text-bold">Desconto Progressivo</label>
                                                                        <div>
                                                                            <span style="font-size: .7rem;">
                                                                                Ex: Para compra a partir de 10 unidades o preço  unitário tem desconto
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group col-12 desconto-progressivo d-none">
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <h3 class="card-title">Desconto Progressivo</h3>
                                                                            <div class="card-tools">
                                                                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                                                        title="Collapse">
                                                                                    <i class="fas fa-minus"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="div-row-desconto row justify-content-center">
                                                                                @foreach (($product->progressiveDiscount ?? []) as $key => $discount)
                                                                                <div class="col-12 discount-${i}">
                                                                                    <div class="row mb-3" style="font-size: .8rem;">
                                                                                        <div class="col-9 d-flex justify-content-center">
                                                                                            <input type="hidden" name="discount[{{$key}}][id]" value="{{$discount->id}}">
                                                                                            <label class="ml-2 mt-1" for="">Acima de</label>
                                                                                            <input type="number" class="form-control form-control-sm mx-2" name="discount[{{$key}}][discount_quantity]" value="{{$discount->discount_quantity}}" style="width:15%; height: 23px; font-size: .8rem;">
                                                                                            <label class="mt-1" for=""> unidades </label>
                                                                                            <label class="mt-1 ml-2" for=""> o preço é </label>
                                                                                            <input type="text" class="form-control form-control-sm real mx-2" placeholder="R$" name="discount[{{$key}}][discount_value]" value="{{$discount->discount_value}}" style="width:20%; height: 23px; font-size: .8rem;">
                                                                                        </div>
                                                                                        <div class="col-3 text-center"><button type="button" class="btn btn-danger btn-remove-campo-desconto" style="padding: 2px 4px; font-size: .8rem;"><i class="fas fa-times"></i> remover</button></div>
                                                                                    </div>
                                                                                </div>
                                                                                @endforeach
                                                                            </div>

                                                                            <div class="mt-2"><button type="button" class="btn btn-primary btn-add-campo-desconto">Adicionar Campo</button></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-sm-3 preco-dimensao-geral">
                                                            <label for="">Preço e dimensão unitário</label>
                                                            <div class="row mb-1">
                                                                <div class="col-6 pr-0 pt-2 border rounded text-bold" style="background-color: #F2F2F2;">Preço</div>
                                                                <div class="col-6 pl-0"><input type="number" step="0.01" name="preco" value="{{$product->preco ?? ''}}" class="form-control" placeholder="R$"></div>
                                                            </div>
                                                            <div class="row mb-1 stock d-none">
                                                                <div class="col-6 pr-0 pt-2 border rounded text-bold" style="background-color: #F2F2F2;">Estoque</div>
                                                                <div class="col-6 pl-0"><input type="number" name="stock" value="{{$product->stock ?? ''}}" class="form-control" placeholder="un"></div>
                                                            </div>
                                                            <div class="row mb-1 perecivel">
                                                                <div class="col-6 pr-0 pt-2 border rounded text-bold" style="background-color: #F2F2F2;">Peso</div>
                                                                <div class="col-6 pl-0"><input type="number" name="weight" value="{{$product->weight ?? ''}}" class="form-control" placeholder="Kg"></div>
                                                            </div>
                                                            <div class="row mb-1 perecivel">
                                                                <div class="col-6 pr-0 pt-2 border rounded text-bold" style="background-color: #F2F2F2;">Altura</div>
                                                                <div class="col-6 pl-0"><input type="number" name="height" value="{{$product->height ?? ''}}" class="form-control" placeholder="cm"></div>
                                                            </div>
                                                            <div class="row mb-1 perecivel">
                                                                <div class="col-6 pr-0 pt-2 border rounded text-bold" style="background-color: #F2F2F2;">Largura</div>
                                                                <div class="col-6 pl-0"><input type="number" name="width" value="{{$product->width ?? ''}}" class="form-control" placeholder="cm"></div>
                                                            </div>
                                                            <div class="row mb-1 perecivel">
                                                                <div class="col-6 pr-0 pt-2 border rounded text-bold" style="background-color: #F2F2F2;">Comprimento</div>
                                                                <div class="col-6 pl-0"><input type="number" name="length" value="{{$product->length ?? ''}}" class="form-control" placeholder="cm"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if (($function_slug == 'analisar' || $function_slug == 'editar'))
                                                <div class="card mt-5">
                                                    <div class="card-header">Campos faltantes</div>
                                                    <div class="card-body">
                                                        @if (auth()->guard('admin')->check())
                                                            @foreach ($product->fatoresProduto as $item)
                                                                <div class="row">
                                                                    <div class="col-12 col-md-5 form-group">
                                                                        <select name="field_native[{{$item->id}}][field_name]" class="form-control" data-placeholder="Selecione um campo">
                                                                            <option value="">Selecione um campo</option>
                                                                            <option value="Nome do Produto" @if($item->field_name == 'Nome do Produto') selected @endif>Nome do Produto</option>
                                                                            <option value="Preço" @if($item->field_name == 'Preço') selected @endif>Preço</option>
                                                                            <option value="Peso" @if($item->field_name == 'Peso') selected @endif>Peso</option>
                                                                            <option value="Altura" @if($item->field_name == 'Altura') selected @endif>Altura</option>
                                                                            <option value="Largura" @if($item->field_name == 'Largura') selected @endif>Largura</option>
                                                                            <option value="Comprimento" @if($item->field_name == 'Comprimento') selected @endif>Comprimento</option>
                                                                            <option value="Categorias" @if($item->field_name == 'Categorias') selected @endif>Categorias</option>
                                                                            <option value="Descrição Curta" @if($item->field_name == 'Descrição Curta') selected @endif>Descrição Curta</option>
                                                                            <option value="Imagens" @if($item->field_name == 'Imagens') selected @endif>Imagens</option>
                                                                            <option value="Descrição Completa" @if($item->field_name == 'Descrição Completa') selected @endif>Descrição Completa</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-12 col-md-5 form-group">
                                                                        <input type="text" name="field_native[{{$item->id}}][field_value]" value="{{$item->field_value}}" class="form-control" placeholder="Descrição do problema">
                                                                    </div>
                                                                    <div class="col-12 col-md-2 form-group">
                                                                        <input type="checkbox" name="field_native[{{$item->id}}][field_status]" {{($item->status == 0 ? '' : 'checked')}} value="true"> <label>Resolvido?</label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @if ($product->fatoresProduto->where('field_name', 'textarea')->count() > 0)
                                                                <div class="mt-2">
                                                                    <textarea class="form-control" name="field_text_native[{{$product->fatoresProduto->where('field_name', 'textarea')->first()->id}}]" placeholder="Mais Informações (Opcional)">{{$product->fatoresProduto->where('field_name', 'textarea')->first()->field_value}}</textarea>
                                                                </div>
                                                            @endif
                                                        @else
                                                            @foreach ($product->fatoresProduto as $item)
                                                                <div class="row">
                                                                    <div class="col-12 col-md-5 form-group">
                                                                        Campo: {{$item->field_name}}
                                                                    </div>
                                                                    <div class="col-12 col-md-5 form-group">
                                                                        Informação: {{$item->field_value}}
                                                                    </div>
                                                                    <div class="col-12 col-md-2 form-group">
                                                                        <button type="button" class="btn btn-{{($item->status == '0' ? 'danger' : 'success')}}"><i class="fas fa-{{($item->status == '0' ? 'times' : 'check')}}"></i></button>
                                                                    </div>
                                                                </div>
                                                            @endforeach

                                                            @if ($product->fatoresProduto->where('field_name', 'textarea')->count() > 0)
                                                                <div class="mt-2">
                                                                    {{$product->fatoresProduto->where('field_name', 'textarea')->first()->field_value}}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="pills-descricao" role="tabpanel" aria-labelledby="pills-descricao-tab">
                                        <form id="form-descricao">
                                            <input type="hidden" name="postType" value="postDataDescricao">
                                            <div class="row">
                                                <div class="form-group col-12 col-sm-8">
                                                    <label for="name">Descrição curta <span class="count-max-length ml-2" style="font-size: .7rem;">(max. caracteres 255)</span></label>
                                                    <textarea name="descricao_curta" class="form-control short-textarea" data-max-caracteres="255">{{$product->descricao_curta ?? ''}}</textarea>
                                                </div>
    
                                                <div class="form-group col-12 col-sm-8">
                                                    <label for="name">Descrição completa</label>
                                                    <textarea name="descricao_completa" class="form-control textarea">{{$product->descricao_completa ?? ''}}</textarea>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="pills-fotos" role="tabpanel" aria-labelledby="pills-fotos-tab">
                                        <form id="form-fotos">
                                            <input type="hidden" name="postType" value="postDataFotos">
                                            <div class="row mb-2">
                                                <div class="col-12 col-sm-4 mb-3 m-sm-0">
                                                    <h4>Adicionar imagens</h4>
                                                    <span style="font-size: .7rem">OBS.: imagem quadrada (600x600)</span>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <button type="button" class="btn btn-block btn-success btn-upload-fotos py-2">Adicionar Foto</button>
                                                    <input type="file" multiple class="d-none upload-fotos">
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap div-image-registers">
                                                @each('painel.cadastros.produto.imagesProduto', collect($product->images ?? [])->sortBy('position'), 'image')
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="pills-variacoes" role="tabpanel" aria-labelledby="pills-variacoes-tab">
                                        <form id="form-variacoes">
                                            <input type="hidden" name="postType" value="postDataVariacoes">
                                            <div class="row">
                                                <div class="form-group col-8 col-sm-5">
                                                    <label for="">ATRIBUTOS <span style="font-size: .7rem;">(Ex: peso, tamanho, sabor, embalagem ou outro)</span></label>
                                                    <div class="check-select attribute-select" data-attrs_selected="{{collect($product->attrs ?? [])->map(function($query){return $query->attribute_id;})->toJson()}}" data-title="Selecione os atributos" data-btn_save="Fechar"></div>
                                                    {{-- <select class="form-control selectpicker select-attr" data-header="Selecione os Atributos" data-size="4" data-actions-box="true" data-live-search="true" title="Selecionar os Atributos" name="attrs[]" multiple></select> --}}
                                                </div>
                                                <div class="form-group col-4 col-sm-3 d-flex align-items-end">
                                                    <div class="pl-2 pr-3" style="font-size: 1.2rem;"><b>OU</b></div>
                                                    <div>
                                                        <button class="btn btn-outline-success btn-new-attribute" type="button">CRIAR ATRIBUTO</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-4 col-sm-3">
                                                    <b>APÓS SELECIONAR OS ATRIBUTOS, CLIQUE EM ADICIONAR VARIAÇÃO</b>
                                                </div>
                                                <div class="form-group col-8 col-sm-8">
                                                    <button type="button" class="btn btn-outline-info btn-plus-variation">ADICIONAR VARIAÇÃO</button>
                                                </div>
                                            </div>

                                            <div class="variacoes mt-2 row justify-content-center">
                                                @each('components.produtoVariacoes', ($product->variations ?? []), 'variationUpdateModel')
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="pills-assinatura" role="tabpanel" aria-labelledby="pills-assinatura-tab">
                                        <form id="form-assinatura">
                                            <input type="hidden" name="postType" value="postDataPlanos">
                                            <div class="row mb-2">
                                                <div class="col-12"><b>Crie planos para seu produto</b></div>
                                                <div class="col-12"><button type="button" class="btn btn-outline-success btn-add-campo-plano">Adicionar Campo</button></div>
                                            </div>

                                            <div class="div-row-planos row justify-content-center">
                                                @foreach (($product->planPurchases ?? []) as $key => $planPurchases)
                                                    <div class="col-12 col-sm-3 plan-{{$key}}">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row justify-content-center">
                                                                    <div class="form-group col-12">
                                                                        <label for="">Título do Plano</label>
                                                                        <input type="text" class="form-control form-control-sm" name="plan[{{$key}}][plan_title]" value="{{$planPurchases->plan_title ?? ''}}">
                                                                        <input type="hidden" class="form-control form-control-sm" name="plan[{{$key}}][id]" value="{{$planPurchases->id ?? ''}}">
                                                                    </div>
                                                                    <div class="col-12"><label for="">Realizar Cobrança a cada</label></div>
                                                                    <div class="form-group col-12">
                                                                        <select class="form-control form-control-sm" name="plan[{{$key}}][select_interval]">
                                                                            <option value="4-week" @if($planPurchases->select_interval == '4-week') selected @endif>4 Semanas</option>
                                                                            <option value="1-month" @if($planPurchases->select_interval == '1-month') selected @endif>1 Mês</option>
                                                                            <option value="3-month" @if($planPurchases->select_interval == '3-month') selected @endif>3 Meses</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group col-12">
                                                                        <label for="">Duração do Plano (Mês)</label>
                                                                        <input type="text" class="form-control form-control-sm" name="plan[{{$key}}][duration_plan]" value="{{$planPurchases->duration_plan ?? ''}}">
                                                                    </div>
                                                                    <div class="form-group col-12">
                                                                        <label for="">Valor da Assinatura</label>
                                                                        <input type="text" class="form-control form-control-sm real" name="plan[{{$key}}][plan_value]" value="{{$planPurchases->plan_value ?? ''}}">
                                                                    </div>
                                                                    <div class="form-group col-12">
                                                                        <label for="">Quantidade de Entregas</label>
                                                                        <select class="form-control form-control-sm" name="plan[{{$key}}][select_entrega]">
                                                                            <option value="semanal" @if($planPurchases->select_entrega == 'semanal') selected @endif>Semanal</option>
                                                                            <option value="quinzenal" @if($planPurchases->select_entrega == 'quinzenal') selected @endif>Quinzenal</option>
                                                                            <option value="mensal" @if($planPurchases->select_entrega == 'mensal') selected @endif>Mensal</option>
                                                                            <option value="trimestral" @if($planPurchases->select_entrega == 'trimestral') selected @endif>Trimestral</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group col-12 perecivel @if($product->perecivel) d-none @endif">
                                                                        <label for="">Peso KG</label>
                                                                        <input type="text" class="form-control form-control-sm" name="plan[{{$key}}][peso]" value="{{$planPurchases->peso}}">
                                                                    </div>
                                                                    <div class="form-group col-12 perecivel @if($product->perecivel) d-none @endif">
                                                                        <label for="">Altura CM</label>
                                                                        <input type="text" class="form-control form-control-sm cm" name="plan[{{$key}}][dimensoes_A]" value="{{$planPurchases->dimensoes_A}}">
                                                                    </div>
                                                                    <div class="form-group col-12 perecivel @if($product->perecivel) d-none @endif">
                                                                        <label for="">Comprimento CM</label>
                                                                        <input type="text" class="form-control form-control-sm cm" name="plan[{{$key}}][dimensoes_C]" value="{{$planPurchases->dimensoes_C}}">
                                                                    </div>
                                                                    <div class="form-group col-12 perecivel @if($product->perecivel) d-none @endif">
                                                                        <label for="">Largura CM</label>
                                                                        <input type="text" class="form-control form-control-sm cm" name="plan[{{$key}}][dimensoes_L]" value="{{$planPurchases->dimensoes_L}}">
                                                                    </div>
                                                                    <div class="form-group col-12 perecivel">
                                                                        <label for="">Descrição</label>
                                                                        <textarea class="form-control form-control-sm" name="plan[{{$key}}][descption_plan]">{{$planPurchases->descption_plan}}</textarea>
                                                                    </div>
                        
                                                                    <div class="col-6 mt-3">
                                                                        <button type="button" class="btn btn-block btn-sm btn-danger btn-remove-campo-plano"><i class="fas fa-times"></i> remover</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="pills-seo" role="tabpanel" aria-labelledby="pills-seo-tab">
                                        <form id="form-seo">
                                            <input type="hidden" name="postType" value="postDataSeo">
                                            <div class="row">
                                                <div class="form-group col-12 col-sm-8"><h2>Configuração de SEO da Página de Produtos</h2></div>
                                                <div class="form-group col-12 col-sm-8">
                                                    <label for="">Título da Página</label>
                                                    <input type="text" name="title" class="form-control" value="{{$product->title ?? ''}}">
                                                </div>
    
                                                <div class="form-group col-12 col-sm-8">
                                                    <label for="">Link Permanente</label>
                                                    <input type="text" name="link" class="form-control" value="{{$product->link ?? ''}}">
                                                </div>
    
                                                <div class="form-group col-12 col-sm-8">
                                                    <label for="">Palavras-Chave</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control keywords-adds" placeholder="Inserir termos separados por ';'">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-success btn-add-keywords">Adicionar</button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <div class="card collapsed-card">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Lista de palavras chave</h3>
                                                                <div class="card-tools">
                                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                                            title="Collapse">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <table class="table table-sm table-striped keywords_adds">
                                                                    @foreach (($product->keywords ?? []) as $item)
                                                                        <tr>
                                                                            <td class="border-right border-bottom" width="5%"><button type="button" class="btn py-0 btn-remove-keyword">x</button></td>
                                                                            <td class="border-bottom"><input type="hidden" name="keywords[]" value="{{$item}}">  <span class="ml-2">{{$item}}</span></td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group col-12 col-sm-8">
                                                    <label for="">Descrição do Site</label>
                                                    <textarea name="description" class="form-control max-caracteres" data-max_caracteres="160">{{strip_tags($product->description ?? '')}}</textarea>
                                                </div>

                                                <div class="form-group col-12 col-sm-8 mb-2">
                                                    <div class="custom-file">
                                                        <input name="banner_path" type="file" class="custom-file-input">
                                                        <label class="custom-file-label" for="banner_path">Imagem para Pré-visualização</label>
                                                    </div>
                                                    <div class="banner_path mt-2">
                                                        @isset($product->banner_path)
                                                            <img src="{{asset('storage/'.$product->banner_path)}}" width="280px" alt="">
                                                        @else
                                                            @isset($product->images)
                                                                <img src="{{$product->images->sortBy('position')->first()->caminho ?? ''}}" width="280px" alt="">
                                                            @endisset
                                                        @endisset
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="row mt-2 justify-content-center justify-content-sm-between">
                                    <div class="col-9 col-sm-3"><button type="button" class="btn btn-block btn-voltar d-none py-2">Voltar</button></div>
                                    @if ($function_slug == 'analisar')
                                        @if (auth()->guard('admin')->check())
                                            <div class="col--8 col-sm-4 text-center">
                                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#adicionarCampos">REJEITAR</button>
                                                <button type="button" class="btn btn-success btn-aprovar-analise">APROVAR</button>
                                            </div>
                                        @endif
                                    @endif
                                    <div class="col-9 col-sm-3"><button type="button" class="btn btn-block btn-info btn-continuar-save py-2">Salvar e Continuar</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="adicionarCampos">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">informe os campos faltantes</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="row">
                                <div class="col-12 col-md-6 form-group">
                                    <select name="fields[0][field_name]" class="form-control select-next" data-placeholder="Selecione um campo">
                                        <option value="">Selecione um campo</option>
                                        <option value="Nome do Produto">Nome do Produto</option>
                                        <option value="Preço">Preço</option>
                                        <option value="Peso">Peso</option>
                                        <option value="Altura">Altura</option>
                                        <option value="Largura">Largura</option>
                                        <option value="Comprimento">Comprimento</option>
                                        <option value="Categorias">Categorias</option>
                                        <option value="Descrição Curta">Descrição Curta</option>
                                        <option value="Imagens">Imagens</option>
                                        <option value="Descrição Completa">Descrição Completa</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6 form-group">
                                    <input type="text" name="fields[0][field_value]" class="form-control" placeholder="Descrição do problema">
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <textarea class="form-control" name="field_text" placeholder="Mais Informações (Opcional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <button type="button" class="btn btn-success btn-salvar-campos-rejeicao"><i class="fas fa-save"></i> Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section(auth()->guard('seller')->check() ? 'script' : 'scripts')
    <script>
        const get_full_url = `{{\Request::fullUrl()}}`;
        const url_geral_save = `{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', $function_slug)}}`;
        const texto_btn = `{{$function_slug !== 'novo' ? 'FINALIZAR ALTERAÇÕES' : 'FINALIZAR'}}`;
    </script>
    <script src="{{asset('painel/form.produtos.min.js')}}"></script>
@endsection