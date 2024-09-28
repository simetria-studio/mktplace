@extends(auth()->guard('seller')->check() ? 'layouts.painelSman' : 'layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cupons</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Cupons</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row tab-content" id="pills-tabContent">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Cupons Cadastrados</h3>
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
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#novoCupom"
                                            {{$vendedores->isNotEmpty()?: 'disabled' }}>
                                            <i class="fas fa-plus"></i> Novo Cupom
                                        </button>
                                    </div>
                                </div>

                                <form action="" method="get">
                                    <div class="row mt-2">
                                        <div class="col-12"><h4>Filtros para Cupons</h4></div>
                                        <div class="col-12 col-md-4 form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Código do Cupom</span>
                                                </div>
                                                <input type="text" class="form-control search-filter" name="search_c" placeholder="Codigo" @isset($_GET['search_c']) value="{{$_GET['search_c']}}" @endisset>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Status</span>
                                                </div>
                                                <select name="status_c" class="form-control">
                                                    <option value="todos" @isset($_GET['status_c']) @if($_GET['status_c'] == 'todos') selected @endif @endisset>Todos</option>
                                                    <option value="ativo" @isset($_GET['status_c']) @if($_GET['status_c'] == 'ativo') selected @endif @endisset>Ativo</option>
                                                    <option value="inativo" @isset($_GET['status_c']) @if($_GET['status_c'] == 'inativo') selected @endif @endisset>Inativo</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-2 form-group">
                                            <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-12 col-md-4 form-group">
                                            <label for="">Cupons por Página</label>
                                            <select name="per_page_c" class="form-control">
                                                <option value="20" @isset($_GET['per_page_c']) @if($_GET['per_page_c'] == '20') selected @endif @endisset>20 por Página</option>
                                                <option value="30" @isset($_GET['per_page_c']) @if($_GET['per_page_c'] == '30') selected @endif @endisset>30 por Página</option>
                                                <option value="50" @isset($_GET['per_page_c']) @if($_GET['per_page_c'] == '50') selected @endif @endisset>50 por Página</option>
                                                <option value="100" @isset($_GET['per_page_c']) @if($_GET['per_page_c'] == '100') selected @endif @endisset>100 por Página</option>
                                                <option value="500" @isset($_GET['per_page_c']) @if($_GET['per_page_c'] == '500') selected @endif @endisset>500 por Página</option>
                                                <option value="1000" @isset($_GET['per_page_c']) @if($_GET['per_page_c'] == '1000') selected @endif @endisset>1000 por Página</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="mt-2">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>Código</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($coupons as $coupon)
                                            <tr>
                                                <td>#{{$coupon->id}}</td>
                                                <td>{{$coupon->code_coupon}}</td>
                                                <td><button type="button" class="btn btn-sm {{$coupon->status == 1 ? 'btn-success' : 'btn-danger'}} @if(auth()->guard('seller')->check()) @if($coupon->fee == 'seller') btn-desativar-ps @endif @endif @if(auth()->guard('admin')->check()) btn-desativar-ps @endif" data-ativo="{{$coupon->status == 1 ? 'N' : 'S'}}" data-id="{{$coupon->id}}" data-href="{{route((auth('seller')->check() ? 'seller' : 'admin').'.coupon.ativo')}}">{{$coupon->status == 1 ? 'Ativo' : 'Inativo'}}</button></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-danger btn-destroy" data-url="{{route((auth('seller')->check() ? 'seller' : 'admin').'.coupon.destroy')}}" data-id="{{$coupon->id}}">Excluir</button>
                                                        <button type="button" class="btn btn-sm btn-info btn-edit-coupon" data-href="{{route((auth('seller')->check() ? 'seller' : 'admin').'.coupon.show', $coupon->id)}}" data-toggle="modal" data-target="#novoCupom">Editar</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="col-12 mt-2">
                                    {{$coupons->appends(['status_c' => $_GET['status_c'] ?? null, 'search_c' => $_GET['search_c'] ?? null, 'per_page_c' => $_GET['per_page_c'] ?? ''])->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="novoCupom" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="#" method="post" id="postNovoCupom">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Novo Cupom</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-4">
                                <label for="code_coupon">Código do Cupom</label>
                                <input type="text" class="form-control text-uppercase" name="code_coupon" placeholder="Código do Cupom">
                            </div>
                            <div class="form-group col-12 col-sm-4">
                                <label for="">Cupom válido para</label>
                                <select name="coupon_valid" class="form-control">
                                    <option value="product_discount">Desconto no Produto</option>
                                    <option value="delivery_free">Frete Grátis</option>
                                    <option value="delivery_discount">Desconto no Frete</option>
                                </select>
                            </div>
                            @if(!auth('seller')->check())
                                <div class="form-group col-12 col-sm-9">
                                    <label for="name">Vendedor</label>
                                    <select name="seller_id[]" multiple class="form-control selectpicker seller_id" data-header="Selecione os Vendedores" data-size="4" data-live-search="true" title="Escolha os Vendedores" data-selected-text-format="count" data-actions-box="true" required>
                                        {!! $vendedores->map(function ($seller){
                                            return "<option value=$seller->id>".($seller->store->store_name ?? $seller->name)."</option>";
                                        })->join("\n") !!}
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <div class="row seller_products"></div>
                                </div>
                            @endif
                            @if(auth('seller')->check())
                                <input type="hidden" name="seller_id[]" class="seller_id" value="{{auth('seller')->user()->id}}">
                                <div class="form-group col-12 d-flex align-items-end">
                                    <input type="checkbox" style="width: 20px;" name="check_loja[{{auth('seller')->user()->id}}]" class="form-control mr-2 check-loja" value="true" checked>
                                    <div class="d-flex flex-column">
                                        <label class="m-0" style="line-height: 1;">Válido para toda loja</label>
                                        <span style="font-size: .7rem;">Desmarcar caso o cupom seja válido para alguns produtos.</span>
                                    </div>
                                </div>

                                <div class="form-group col-12 col-sm-6 check_loja d-none">
                                    <label for="name">Produtos</label>
                                    <select name="product_id[{{{auth()->guard('seller')->user()->id}}}][]" multiple class="form-control selectpicker" data-header="Selecione os Produtos" data-size="4" data-live-search="true" title="Escolha os Produtos" data-selected-text-format="count" data-actions-box="true" required>
                                        {!! $products->map(function ($product){
                                            return "<option value=$product->id>".($product->nome)."</option>";
                                        })->join("\n") !!}
                                    </select>
                                </div>

                                <div class="form-group col-12 col-sm-6 check_loja d-none">
                                    <label for="name">Serviços</label>
                                    <select name="service_id[{{{auth()->guard('seller')->user()->id}}}][]" multiple class="form-control selectpicker" data-header="Selecione os Servicos" data-size="4" data-live-search="true" title="Escolha os Serviços" data-selected-text-format="count" data-actions-box="true" required>
                                        {!! $services->map(function ($service){
                                            return "<option value=$service->id>".($service->service_name)."</option>";
                                        })->join("\n") !!}
                                    </select>
                                </div>
                            @endif
                            <div class="form-group col-12 discount_coupon">
                                <div class="row justify-content-center">
                                    <div class="form-group col-12 col-sm-4">
                                        <label for="">Selecione o tipo de desconto</label>
                                        <select name="discount_config" class="form-control">
                                            <option value="porcentage">Porcentagem</option>
                                            <option value="money">Valor Real</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-12 col-sm-4">
                                        <label for="">Valor de desconto do Cupom</label>
                                        <input type="text" class="form-control real" name="value_discount">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-12 col-sm-6">
                                <label for="">Valor mínimo para Aplicar o cupom</label>
                                <input type="text" class="form-control real" name="value_min">
                                <span>Deixar vazio caso não tenha valor mínimo para o cupom</span>
                            </div>
                            <div class="form-group col-12 col-sm-6">
                                <label for="">Valor máximo para Aplicar o cupom</label>
                                <input type="text" class="form-control real" name="value_max">
                                <span>Deixar vazio caso não tenha valor máximo para o cupom</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                                class="fas fa-times"></i> Fechar
                        </button>
                        <button type="button" class="btn btn-success btn-salvar" data-refresh="S"
                                data-save_target="#postNovoCupom"
                                data-save_route="{{route((auth('seller')->check() ?'seller' : 'admin').'.coupon.store')}}">
                            <i
                                class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section((auth()->guard('seller')->check() ? 'script' : 'scripts'))
    <script>
        $(document).ready(function(){
            let ajaxBuscandoServicos;

            $('[name="per_page_p"]').on('change', function(){
                $(this).closest('form').submit();
            });
            $(document).on('change', '.check-loja', function(){
                if($(this).prop('checked')){
                    $(this).closest(`{{auth()->guard('seller')->check() ? 'form' : '.card'}}`).find('.check_loja').addClass('d-none');
                }else{
                    $(this).closest(`{{auth()->guard('seller')->check() ? 'form' : '.card'}}`).find('.check_loja').removeClass('d-none');
                }
            });
            $('[name="coupon_valid"]').on('change', function(){
                if($(this).val() == 'delivery_free'){
                    $(this).closest('form').find('.discount_coupon').addClass('d-none');
                }else{
                    $(this).closest('form').find('.discount_coupon').removeClass('d-none');
                }
            });

            $(document).on('change', 'select.seller_id', function(){
                var sellers_id = $(this).val();
                $('.seller_products').find(`.seller-row`).each(function(){
                    if(!sellers_id.includes($(this).data('seller_id').toString())){
                        $(this).remove();
                    }
                });

                $.ajax({
                    url: `{{route('allTables')}}?table=getProductsSellers&sellers_id=${sellers_id}`,
                    type: 'GET',
                    success: (data)=>{
                        // console.log(data);
                        for(var i=0; i<data.length; i++){
                            if(data[i].seller_id){
                                data
                                if($('.seller_products').find(`.seller-row-id-${data[i].seller_id}`).length == 0){
                                    $('.seller_products').append(`
                                        <div class="col-12 seller-row seller-row-id-${data[i].seller_id}" data-seller_id="${data[i].seller_id}">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3>Vendedor: ${data[i].seller.store.store_name}</h3>
                                                    <div class="form-group d-flex align-items-end">
                                                        <input type="checkbox" style="width: 20px;" name="check_loja[${data[i].seller_id}]" class="form-control mr-2 check-loja" value="true" checked>
                                                        <div class="d-flex flex-column">
                                                            <label class="m-0" style="line-height: 1;">Valido para toda loja</label>
                                                            <span style="font-size: .7rem;">Desmarcar caso o cupom seja válido para alguns produtos.</span>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-12 col-sm-6 check_loja d-none">
                                                            <label for="name">Produtos</label>
                                                            <select name="product_id[${data[i].seller_id}][]" multiple class="form-control selectpicker" data-header="Selecione os Produtos" data-size="4" data-live-search="true" title="Escolha os Produtos" data-selected-text-format="count" data-actions-box="true" required>
                                                                ${data[i].products.map(function(query){
                                                                    return `<option value=${query.id}>${query.nome}</option>`;
                                                                }).join('')}
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-12 col-sm-6 check_loja d-none">
                                                            <label for="name">Serviços</label>
                                                            <select name="service_id[${data[i].seller_id}][]" multiple class="form-control selectpicker" data-header="Selecione os Serviços" data-size="4" data-live-search="true" title="Escolha os Serviços" data-selected-text-format="count" data-actions-box="true" required>
                                                                ${data[i].services.map(function(query){
                                                                    return `<option value=${query.id}>${query.service_title}</option>`;
                                                                }).join('')}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                }
                            }
                        }
                        $('.selectpicker').selectpicker();
                    }
                });
            });

            $('#novoCupom').on('hidden.bs.modal', function(){
                $(this).find('[name="code_coupon"]').attr('readonly', false);
                $(this).find('input[type="text"]').val('');
                $(this).find('input[type="number"]').val('');
                $(this).find('.selectpicker').val('').trigger('change');
                $(this).find('.seller_products').empty();
                $('[data-save_target="#postNovoCupom"]').attr('data-save_route', "{{route((auth('seller')->check() ?'seller' : 'admin').'.coupon.store')}}");
                $('[data-save_target="#postNovoCupom"]').prop('disabled', false);
                $(this).find('[name="check_loja"]').prop('checked', true).trigger('change');
            });
            $('.btn-edit-coupon').on('click', function(){
                $('#novoCupom').find('.modal-title').html('Carregando informações do serviço...');

                $('[data-save_target="#postNovoCupom"]').attr('data-save_route', "{{route((auth('seller')->check() ?'seller' : 'admin').'.coupon.edit')}}");
                console.log($('[data-save_target="#postNovoCupom"]'));
                $(`#imagensJaCadastradas`).html('carregando imagens...');
                $('[data-save_target="#postNovoCupom"]').prop('disabled', true);
                $('#novoCupom').find('[name="code_coupon"]').attr('readonly', true);

                if (ajaxBuscandoServicos) ajaxBuscandoServicos.abort();
                ajaxBuscandoServicos = $.ajax({
                    url: $(this).data('href'),
                    type: 'GET',
                    success: editandoServico
                });
            });

            // $(document).on('click', '.btn-remove-data-card', function(e) {
            //     e.preventDefault();
            //     $('#data-card-rand-'+$(this).data('id')).remove();
            // });

            $(document).on('click', '.btn-add-campo-desconto', function(){
                var qty_desconto = $('.div-row-desconto').find('.row').length;
                for(var i=0;i<=qty_desconto;i++){
                    if($('.div-row-desconto').find('.discount-'+i).length == 0){
                        $('.div-row-desconto').append(`
                        <div class="col-12 discount-${i}">
                            <div class="row form-inline mb-3">
                                <label class="ml-2" for="">Acima de</label>
                                <input type="number" class="form-control mx-2" name="discount[${i}][discount_quantity]" style="width:15%;">
                                <label for=""> unidades o preço é </label>
                                <input type="text" class="form-control form-control-md real mx-2" name="discount[${i}][discount_value]">
                                <div class="col-2"> <button type="button" class="btn btn-block btn-sm btn-danger btn-remove-campo-desconto"><i class="fas fa-times"></i> remover</button></div>
                                </div>
                            </div>
                        `);
                    }
                }
                $('.real').mask('000.000,00', {reverse: true});
            });
        });

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

        function editandoServico(data){
            // console.log(data);
            $('#novoCupom').find('.modal-title').html(`Editando Cupom #${data.id}`);
            $.each(data, (key, value) => {
                $('[name="'+key+'"]').val(value);
            });

            if(`{{auth()->guard('seller')->check()}}`){
                for(var i=0; i<data.sellers.length; i++){
                    var seller = data.sellers[i];
                    $('[name="check_loja['+seller.seller_id+']"]').prop('checked',(seller.check_loja == 'true' ? true : false)).trigger('change');

                    $('select[name="product_id['+seller.seller_id+'][]"]').val(seller.product_id).selectpicker('refresh');
                    $('select[name="service_id['+seller.seller_id+'][]"]').val(seller.service_id).selectpicker('refresh');
                }
            }else{
                var sellers_ids = [];
                for(var i=0; i<data.sellers.length; i++){
                    sellers_ids.push(data.sellers[i].seller_id);
                }
                $('select.seller_id').val(sellers_ids).selectpicker('refresh').trigger('change');
                
                setTimeout(() => {
                    for(var i=0; i<data.sellers.length; i++){
                        var seller = data.sellers[i];
                        $('[name="check_loja['+seller.seller_id+']"]').prop('checked',(seller.check_loja == 'true' ? true : false)).trigger('change');
                        $('select[name="product_id['+seller.seller_id+'][]"]').val(seller.product_id).selectpicker('refresh');
                        $('select[name="service_id['+seller.seller_id+'][]"]').val(seller.service_id).selectpicker('refresh');
                    }
                }, 1000);
            }

            $('[name="coupon_valid"]').trigger('change');
            $('[data-save_target="#postNovoCupom"]').prop('disabled', false);
        }
    </script>
@endsection