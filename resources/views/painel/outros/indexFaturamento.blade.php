@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Faturamento</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Faturamento</li>
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
                            <h3 class="card-title">Faturamento</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="row justify-content-end">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <table class="w-100">
                                        <tr>
                                            <th>Período</th>
                                            <td>
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <input type="text" class="form-control form-control-sm periodo_f_ini date-mask-single" value="{{date('d/m/Y', strtotime(date('Y').'-'.date('m').'-01'))}}">
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <input type="text" class="form-control form-control-sm periodo_f_fim date-mask-single" value="{{date('d/m/Y')}}">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12 col-sm-4">
                                    <table class="w-100">
                                        <tr>
                                            <th class="text-center">Totais</th>
                                            <td><input type="text" class="form-control total-v" readonly></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12 col-sm-4">
                                    <div class="card">
                                        <div class="card-body" style="max-height: 360px; overflow:auto;">
                                            <div class="row list-vendedores mt-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <table class="w-100">
                                    <tr>
                                        <th>Vendedor: </th>
                                        <td><input type="text" class="form-control name-vendedor" readonly></td>
                                        <th>CPF/CNPJ: </th>
                                        <td><input type="text" class="form-control cpf_cnpj-vendedor" readonly></td>
                                    </tr>
                                    <tr>
                                        <th>Total: </th>
                                        <td><input type="text" class="form-control total-vendedor" readonly></td>
                                    </tr>
                                    <tr>
                                        <th>Total Líquido: </th>
                                        <td><input type="text" class="form-control total-liquido-vendedor" readonly></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        let valor_total_vendedor_p;
        let valor_liquido_vendedor_p;
        let valor_liquido_vendedor_s;
        let sellers;

        $(document).ready(function(){
            getFaturamento();
            $(document).on('change', '.periodo_f_ini, .periodo_f_fim', function(){
                $('.name-vendedor, .cpf_cnpj-vendedor, .total-vendedor, .total-liquido-vendedor').val('');
                getFaturamento();
            });

            $(document).on('click', '.btn-vendedor', function(){
                $('.name-vendedor').val(sellers[$(this).data('id')].store.store_name);
                $('.cpf_cnpj-vendedor').val(sellers[$(this).data('id')].cnpj_cpf);
                $('.total-vendedor').val(((valor_total_vendedor_p[$(this).data('id')] ? valor_total_vendedor_p[$(this).data('id')] : 0) + (valor_liquido_vendedor_s[$(this).data('id')] ? valor_liquido_vendedor_s[$(this).data('id')] : 0)).toFixed(2).toString().replace('.',','));
                $('.total-liquido-vendedor').val(((valor_liquido_vendedor_p[$(this).data('id')] ? valor_liquido_vendedor_p[$(this).data('id')] : 0) + (valor_liquido_vendedor_s[$(this).data('id')] ? valor_liquido_vendedor_s[$(this).data('id')] : 0)).toFixed(2).toString().replace('.',','));
            });
        });

        function getFaturamento(){
            $.ajax({
                url: `{{route('admin.getFaturamento')}}`,
                type: 'POST',
                data: {date_ini: $('.periodo_f_ini').val(), date_fim: $('.periodo_f_fim').val()},
                success: (data) => {
                    // console.log(data);
                    valor_total_vendedor_p = data.valor_total_vendedor_p;
                    valor_liquido_vendedor_p = data.valor_liquido_vendedor_p;
                    valor_liquido_vendedor_s = data.valor_liquido_vendedor_s;
                    sellers = data.sellers;

                    $('.total-v').val(`R$ ${(data.total_v).toFixed(2).toString().replace('.',',')}`);

                    $('.list-vendedores').empty();
                    for(var seller in sellers){
                        $('.list-vendedores').append(`
                            <div class="col-12 px-1 py-2"><button type="button" class="btn btn-sm btn-vendedor" data-id="${seller}">${sellers[seller].store.store_name} >> ${((valor_total_vendedor_p[seller] ? valor_total_vendedor_p[seller] : 0)+(valor_liquido_vendedor_s[seller] ? valor_liquido_vendedor_s[seller] : 0)).toFixed(2).toString().replace('.',',')}</button></div>
                        `);
                    }
                }
            });
        }
    </script>
@endsection