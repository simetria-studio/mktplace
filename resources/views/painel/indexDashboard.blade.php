@extends('layouts.painelAdm')

@section('container')
    {{-- <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div> --}}

    <div class="content pt-2">
        <div class="container-fluid">
            <div class="row my-2">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="card card-outline-bottom-primary">
                                <div class="card-body">
                                    <table class="w-100">
                                        <tr>
                                            <th>Vendas</th>
                                            <td class="text-right" id="info_vendas">R$ 000,00</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="card card-outline-bottom-custom-1">
                                <div class="card-body">
                                    <table class="w-100">
                                        <tr>
                                            <th>Produtos/Serviços</th>
                                            <td class="text-right" id="qty_produtos_servicos">000</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="card card-outline-bottom-custom-2">
                                <div class="card-body">
                                    <table class="w-100">
                                        <tr>
                                            <th>Acessos</th>
                                            <td class="text-right" id="qty_acessos">000000</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="card dashboard-periodo card-outline-bottom-custom-3">
                                <div class="card-body">
                                    <table class="w-100">
                                        <tr>
                                            <th>Periodo</th>
                                            <td>
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <input type="text" class="form-control form-control-sm periodo_ini date-mask-single" value="{{date('d/m/Y', strtotime(date('Y').'-'.date('m').'-01'))}}">
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <input type="text" class="form-control form-control-sm periodo_fim date-mask-single" value="{{date('d/m/Y')}}">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <table class="table table-dash">
                                <thead>
                                    <tr>
                                        <th>Pedido</th>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody id="table_pedidos">
                                    @for ($i = 0; $i < 12; $i++)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4>Vendas Realizadas</h4>
                                    <div id="vendas_realizadas"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4>Acessos aos Produtos/Serviços</h4>
                                    <div id="acessos_produtos_servicos"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <div class="row">
                        <div class="col-12 col-sm-3 my-1">
                            <a href="{{route('pedidos')}}" target="_blank" class="btn btn-primary btn-block">Ver Pedidos de Produtos</a>
                        </div>
                        <div class="col-12 col-sm-3 my-1">
                            <a href="{{route('pedidos.servicos')}}" target="_blank" class="btn btn-custom-1 btn-block">Ver Pedidos de Serviços</a>
                        </div>
                        <div class="col-12 col-sm-3 my-1">
                            <a href="{{route('produto', 'aprovados')}}" target="_blank" class="btn btn-primary btn-block">Ver Produtos</a>
                        </div>
                        <div class="col-12 col-sm-3 my-1">
                            <a href="{{route('servico')}}" target="_blank" class="btn btn-custom-1 btn-block">Ver Serviços</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <div class="row justify-content-center">
                        <div class="col-12 col-sm-4">
                            <div class="card">
                                <div class="card-body text-center"><h3 class="mb-0">CRESCIMENTO</h3></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2 crescimento">
                    <div class="row mb-1">
                        <div class="col">
                            <div class="card card-outline-bottom-custom-1">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Total de Vendedores</b></p>
                                    <span class="crescimento" data-target="vendedor">000</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-primary">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Produtos Ativos</b></p>
                                    <span data-target="produto_ativo">000</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-2">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Serviços Ativos</b></p>
                                    <span data-target="servico_ativo">000</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-3">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Novos Produtos(mês)</b></p>
                                    <span data-target="novos_produtos">000</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-4">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Total de Clientes</b></p>
                                    <span data-target="total_clientes">000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col">
                            <div class="card card-outline-bottom-custom-1">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Novos Vendedores (mês)</b></p>
                                    <span data-target="novos_vendedores">000</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-primary">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Produtos Pendentes</b></p>
                                    <span data-target="produtos_pendente">000</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-2">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Serviços Pendentes</b></p>
                                    <span data-target="servicos_pendentes">000</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-3">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Novos Serviços(mês)</b></p>
                                    <span data-target="novos_servicos">000</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-4">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>Novos Clientes(mês)</b></p>
                                    <span data-target="total_clientes">000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <div class="row justify-content-center">
                        <div class="col-12 col-sm-4">
                            <div class="card">
                                <div class="card-body text-center"><h3 class="mb-0">RANKING</h3></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2 ranking">
                    <div class="row">
                        <div class="col">
                            <div class="card card-outline-bottom-custom-1">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>TOP 5 Produtos</b></p>
                                    <div data-target="top_produtos">000</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-primary">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>TOP 5 Serviços</b></p>
                                    <div data-target="top_servicos">000</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-2">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>TOP 5 Vendedores P</b></p>
                                    <div data-target="top_vendedores_p">000</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-3">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>TOP 5 Vendedores S</b></p>
                                    <div data-target="top_vendedores_s">000</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-outline-bottom-custom-4">
                                <div class="card-body text-center">
                                    <p class="mb-0"><b>TOP 5 Acessos</b></p>
                                    <div data-target="top_acessos">000</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="card card-outline-bottom-custom-1">
                                <div class="card-body">
                                    <p class="mb-0 text-center"><b>FALE CONOSCO</b></p>
                                    <table class="w-100">
                                        @foreach ($fale_conosco as $item)
                                            <tr>
                                                <td class="py-1 px-2">{{date('d/m/Y', strtotime($item->created_at))}}</td>
                                                <td class="py-1 px-2">{{$item->name}}</td>
                                                <td class="py-1 px-2">{{$item->assunto}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                    <div class="row justify-content-center">
                                        <div class="col-12 col-sm-6">
                                            <a href="{{route('admin.contatcs')}}" target="_blank" class="btn btn-custom-1 btn-block">Ver Contatos</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="card card-outline-bottom-custom-2">
                                <div class="card-body">
                                    <p class="mb-0 text-center"><b>AVALIAÇÕES</b></p>
                                    @if ($star_product->count() > 0)
                                        <table class="w-100">
                                            @foreach ($star_product as $key => $item)
                                                <tr>
                                                    <td class="py-1 px-2" title="{{$item->user->name}}">CLIENTE {{explode(' ', $item->user->name)[0]}}</td>
                                                    <td class="py-1 px-2 text-truncate" title="{{$item->product->nome}}">PRODUTO {{$item->product->nome}}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 col-sm-6">
                                                <a href="{{route('admin.rateProduct.apro')}}" target="_blank" class="btn btn-custom-2 btn-block">Ver Avaliações Produtos</a>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($star_service->count() > 0)
                                        <table class="w-100">
                                            @foreach ($star_service as $key => $item)
                                                <tr>
                                                    <td class="py-1 px-2" title="{{$item->user->name}}">CLIENTE {{explode(' ', $item->user->name)[0]}}</td>
                                                    <td class="py-1 px-2 text-truncate" title="{{$item->service->nome}}">PRODUTO {{$item->service->nome}}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-sm-6">
                                                <a href="{{route('admin.rateService.apro')}}" target="_blank" class="btn btn-custom-2 btn-block">Ver Avaliações Serviços</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            getInfoDash();
            let nome_mes = {
                '01': 'Jan',
                '02': 'Fev',
                '03': 'Mar',
                '04': 'Abr',
                '05': 'Mai',
                '06': 'Jun',
                '07': 'Jul',
                '08': 'Ago',
                '09': 'Set',
                '10': 'Out',
                '11': 'Nov',
                '12': 'Dez',
            };

            let md_chart = [];
            let data_chart_1 = [];
            let data_chart_2 = [];
            var date_ini = $('.periodo_ini').val() || null;
            date_ini = date_ini ? date_ini.split('/') : null;
            var date_fim = $('.periodo_fim').val() || null;
            date_fim = date_fim ? date_fim.split('/') : null;
            if(date_ini && date_fim){
                var date1 = new Date(`${date_ini[2]}/${date_ini[1]}/${date_ini[0]}`);
                var date2 = new Date(`${date_fim[2]}/${date_fim[1]}/${date_fim[0]}`);
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

                for(var i=1;i<=(diffDays+1);i++){
                    data_chart_1.push(0);
                    data_chart_2.push(0);
                    // data_chart_1.push((Math.floor(Math.random() * (200)) + 1));
                    // data_chart_2.push((Math.floor(Math.random() * (200)) + 1));
                    md_chart.push(nome_mes[date_ini[1]]+' '+i);
                }
            }

            vendas_realizadas = new ApexCharts(document.querySelector("#vendas_realizadas"), {
                chart: {
                    type: 'line'
                },
                colors: ['#59981A'],
                stroke: {
                    curve: 'smooth',
                },
                series: [
                    {
                        data: data_chart_1,
                        name: 'Vendas Realizadas'
                    }
                ],
                xaxis: {
                    categories: md_chart,
                }
            });
            vendas_realizadas.render();
            acessos_produtos_servicos = new ApexCharts(document.querySelector("#acessos_produtos_servicos"), {
                chart: {
                    type: 'line'
                },
                colors: ['#FF8300'],
                stroke: {
                    curve: 'smooth',
                },
                series: [
                    {
                        data: data_chart_2,
                        name: 'Produtos/Serviços'
                    }
                ],
                xaxis: {
                    categories: md_chart,
                }
            });
            acessos_produtos_servicos.render();
        });
    </script>
@endsection