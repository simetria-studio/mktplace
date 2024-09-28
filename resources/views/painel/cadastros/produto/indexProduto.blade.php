@extends(auth()->guard('seller')->check() ? 'layouts.painelSman' : 'layouts.painelAdm')

@section('container')
    <section class="content">
        <div class="container-fluid">
            <div class="row tab-content" id="pills-tabContent">
                <div class="col-12 mt-3">
                    {{-- @auth('seller')
                        @if(is_null(auth('seller')->user()->wallet_id) && empty(auth('seller')->user()->wallet_id))
                            @php $podeCriarProduto = false; @endphp
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Ooops!</h4>
                                <p>Você ainda não pode fazer isso =(</p>
                                <hr>
                                <p class="mb-0">
                                    Para cadastrar produtos, é necessário abrir uma conta para recebimento!
                                    <a href="{{route('seller.contaParaVenda')}}" class="alert-link">Clique aqui e crie
                                        agora</a>
                                    =D
                                </p>
                            </div>
                        @endif
                    @endauth --}}
                    @if($podeCriarProduto)
                        <div class="card card-primary card-outline">
                            {{-- Header do Card --}}
                            <div class="card-header">
                                <h3 class="card-title">Produtos</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                            title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            {{-- Corpo do Card --}}
                            <div class="card-body pad">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12 col-md-3 mb-2 mb-sm-0">
                                            <a href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'novo')}}" class="btn btn-success py-2 px-4" style="font-weight: bold;" {{$seller->isNotEmpty()?: 'disabled' }}>
                                                <i class="fas fa-plus"></i> Novo Produto
                                            </a>
                                            @if($seller->isEmpty())
                                                <br> Para adicionar um novo produto é preciso existir ao menos um
                                                vendedor
                                                cadastrado @endif
                                        </div>
                                        <div class="col-12 col-md-9 d-flex justify-content-sm-end">
                                            <ul class="nav mb-3" role="tablist">
                                                <li class="nav-item pl-0 pl-sm-1 col-12 col-sm-auto" role="presentation" style="cursor: pointer !important;">
                                                    <a class="nav-link pl-1 pl-sm-3 rounded @if($function_slug == 'aprovados') active-c @endif" href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'aprovados').$get_url_appends}}">Aprovados <span class="badge badge-light">{{$product_count['aprovados']}}</span></a>
                                                </li>
                                                <li class="nav-item pl-0 pl-sm-1 col-12 col-sm-auto" role="presentation" style="cursor: pointer !important;">
                                                    <a class="nav-link pl-1 pl-sm-3 rounded @if($function_slug == 'analise') active-c @endif" href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'analise').$get_url_appends}}">Em Análise <span class="badge badge-light">{{$product_count['analise']}}</span></a>
                                                </li>
                                                <li class="nav-item pl-0 pl-sm-1 col-12 col-sm-auto" role="presentation" style="cursor: pointer !important;">
                                                    <a class="nav-link pl-1 pl-sm-3 rounded @if($function_slug == 'rascunho') active-c @endif" href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'rascunho').$get_url_appends}}">Com itens Faltantes <span class="badge badge-light">{{$product_count['rascunho']}}</span></a>
                                                </li>
                                                <li class="nav-item pl-0 pl-sm-1 col-12 col-sm-auto" role="presentation" style="cursor: pointer !important;">
                                                    <a class="nav-link pl-1 pl-sm-3 rounded @if($function_slug == 'inativo') active-c @endif" href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', 'inativo').$get_url_appends}}">Inativos <span class="badge badge-light">{{$product_count['inativo']}}</span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <form action="" method="get">
                                        @if (($request->order_collumn ?? null))
                                            <input type="hidden" name="order_collumn" value="{{$request->order_collumn ?? ''}}">
                                        @endif
                                        @if (($request->order_by ?? null))
                                            <input type="hidden" name="order_by" value="{{$request->order_by ?? ''}}">
                                        @endif
                                        <div class="row my-2">
                                            @if (auth()->guard('admin')->check())
                                                <div class="col-12 col-md-4 form-group">
                                                    <label for="">Filtros para produtos</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control search-filter" name="product_name" placeholder="Digite o nome do produto" value="{{$request->product_name ?? ''}}">
                                                        <select name="type_search" class="form-control">
                                                            <option value="product" @if(($request->type_search ?? null) == 'product') selected @endif>Produto</option>
                                                            <option value="seller" @if(($request->type_search ?? null) == 'seller') selected @endif>Vendedor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-12 col-md-3 form-group">
                                                    <label for="">Filtros para produtos</label>
                                                    <input type="text" class="form-control form-control-sm search-filter" name="product_name" placeholder="Digite o nome do produto" value="{{$request->product_name ?? ''}}">
                                                </div>
                                            @endif

                                            <div class="col-12 col-md-2 form-group">
                                                <label for="">&nbsp;</label>
                                                <div class="d-flex">
                                                    <div style="width: 60%;" class="px-1"><button type="submit" class="btn btn-sm btn-primary btn-block">Buscar</button></div>
                                                    <div style="width: 30%" class="px-1"><a href="{{route((auth()->guard('admin')->check() ? '' : 'seller.').'produto', $function_slug)}}" class="btn btn-sm btn-default">Limpar</a></div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-3 form-group">
                                                <label for="">Produtos por Página</label>
                                                <select name="per_page" class="form-control form-control-sm">
                                                    <option value="20" @if(($request->per_page ?? null) == '20') selected @endif>20 por Página</option>
                                                    <option value="30" @if(($request->per_page ?? null) == '30') selected @endif>30 por Página</option>
                                                    <option value="50" @if(($request->per_page ?? null) == '50') selected @endif>50 por Página</option>
                                                    <option value="100" @if(($request->per_page ?? null) == '100') selected @endif>100 por Página</option>
                                                    <option value="500" @if(($request->per_page ?? null) == '500') selected @endif>500 por Página</option>
                                                    <option value="1000" @if(($request->per_page ?? null) == '1000') selected @endif>1000 por Página</option>
                                                </select>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive" style="min-height: 160px">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="th-ordena" style="cursor: pointer;" data-collumn="id">Nº <i class="fas"></i></th>
                                                    <th class="th-ordena" style="cursor: pointer;" data-collumn="name">Nome <i class="fas"></i></th>
                                                    @if (auth()->guard('admin')->check())
                                                        <th class="th-ordena" style="cursor: pointer;" data-collumn="store_name">Vendedor <i class="fas"></i></th>
                                                    @endif
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @each('components.tableProdutos', $produtos, 'produto')
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-2">
                                        {{$produtos->appends(['order_collumn' => ($request->order_collumn ?? null), 'order_by' => ($request->order_by ?? null), 'type_search' => ($request->type_search ?? null),'product_name' => ($request->product_name ?? null), 'per_page' => ($request->per_page ?? null)])->links()}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section(auth()->guard('seller')->check() ? 'script' : 'scripts')
    <script>
        $(document).ready(function(){
            var url = `{{\Request::url()}}`;
            var url_get = `{{$get_url_appends}}`;
            url = url.replaceAll('&amp;','&');
            url_get = url_get.replaceAll('&amp;','&').replace('?','');
            $(document).on('click', '.th-ordena', function(){
                url_get = url_get.split('&');
                // fas fa-arrow-up
                var order_by;
                $('.th-ordena').find('i').removeClass('fa-arrow-up fa-arrow-down');
                if(typeof $(this).attr('data-order_by') == 'undefined' ||  $(this).attr('data-order_by') == ''){
                    order_by = 'ASC';
                    $(this).attr('data-order_by', 'ASC');
                    $(this).find('i').addClass('fa-arrow-down');
                }else if($(this).attr('data-order_by') == 'ASC'){
                    order_by = 'DESC';
                    $(this).attr('data-order_by', 'DESC');
                    $(this).find('i').addClass('fa-arrow-up');
                }else if($(this).attr('data-order_by') == 'DESC'){
                    order_by = 'ASC';
                    $(this).attr('data-order_by', 'ASC');
                    $(this).find('i').addClass('fa-arrow-down');
                }

                var new_url_get = '?';
                var count_order = 0;
                for(i in url_get){
                    if(url_get[i].search('order_collumn') < 0 && url_get[i].search('order') < 0){
                        new_url_get += (count_order > 0 ? '&' : '')+url_get[i];
                        count_order++;
                    }
                }

                window.location.href = url+new_url_get+(count_order > 1 ? '&' : '')+'order_collumn='+$(this).data('collumn')+'&order_by='+order_by;
            });

            $('.th-ordena').each(function(){
                var order_collumn = `{{$request->order_collumn ?? null}}`;
                var order_by = `{{$request->order_by ?? null}}`;
                if($(this).attr('data-collumn') == order_collumn){
                    $(this).attr('data-order_by', order_by);
                    if(order_by == 'ASC'){
                        $(this).find('i').addClass('fa-arrow-down');
                    }else{
                        $(this).find('i').addClass('fa-arrow-up');
                    }
                }
            });
        });
    </script>
@endsection