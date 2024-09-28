@extends('layouts.painelSman')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Locais de Retirada</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/vendedor')}}">Dashboard</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Localidades</h3>
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
                                        <button type="button" class="btn btn-success btn-sm charge-modal" data-url="{{ route('seller.localDeRetirada', 'modal-local-de-retirada') }}" data-tag_event="#modalLocalDeRetirada"><i class="fas fa-plus"></i> Nova Localidade</button>
                                    </div>
                                </div>
                            </div>

                            <div class="container mt-2" id="tableLocalDeRetirada">
                                @include('components.seller.fretes.tableLocalDeRetirada', get_defined_vars())
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $(document).on('change', '.all_products', function(){
                if($(this).is(':checked')){
                    $('.produtos').addClass('d-none');
                }else{
                    $('.produtos').removeClass('d-none');
                }
            });

            $(document).on('change', 'select[name="localidade_id"]', function(){
                let dados = $(this).find(':selected').data('dados');

                if(dados == undefined){
                    $('.outros-dados').addClass('d-none');
                    return;
                }
                let html = `
                    <div class="row">
                        <div class="col-12">
                            <label for="address">Endereço</label>
                            <p>${dados.address}, Nº ${dados.number}, ${dados.district} - ${dados.city}/${dados.state} - ${dados.zip_code}</p>
                        </div>
                        <div class="col-12">
                            <label for="address">Observações</label>
                            <p>${dados.description || ''}</p>
                        </div>
                    </div>
                `;
                $('.outros-dados').html(html);
                $('.outros-dados').removeClass('d-none');
            });
        });
    </script>
@endsection