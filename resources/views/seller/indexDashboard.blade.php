@extends('layouts.painelSman')

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
                            <a href="{{route('seller.pedidos')}}" target="_blank" class="btn btn-primary btn-block">Ver Pedidos de Produtos</a>
                        </div>
                        <div class="col-12 col-sm-3 my-1">
                            <a href="{{route('seller.pedidos.servicos')}}" target="_blank" class="btn btn-custom-1 btn-block">Ver Pedidos de Serviços</a>
                        </div>
                        <div class="col-12 col-sm-3 my-1">
                            <a href="{{route('seller.produto', 'aprovados')}}" target="_blank" class="btn btn-primary btn-block">Ver Produtos</a>
                        </div>
                        <div class="col-12 col-sm-3 my-1">
                            <a href="{{route('seller.servico')}}" target="_blank" class="btn btn-custom-1 btn-block">Ver Serviços</a>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-2 mt-4">
                    <div class="row">
                        <div class="col-3 d-none d-md-flex align-items-center">
                            <div><img src="{{asset('site/imgs/logo.png')}}" alt="" width="160px"></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card card-outline-bottom-custom-1">
                                <div class="card-body">
                                    <div class="mb-1 text-center"><b>Suporte</b></div>
                                    <div>
                                        Caro(a) Vendedor(a), caso tenha alguma dúvida ou problema, basta nos chamar no WhatsApp ao lado. Responderemos o mais breve possível.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div>
                                {{-- <a href="https://api.whatsapp.com/send?phone=5547996003481" target="_blank" style="color: #000;"><h4>(47) 99600-3481 - Deise</h4></a>
                                <a href="https://api.whatsapp.com/send?phone=5547988476422" target="_blank" style="color: #000;"><h4>(47) 98847-6422 - Marcos</h4></a> --}}
                            </div>
                        </div>
                        <div class="col-12 d-md-none">
                            <div class="text-center text-md-left"><img src="{{asset('site/imgs/logo.png')}}" alt="" width="160px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
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