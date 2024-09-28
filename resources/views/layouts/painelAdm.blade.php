<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Painel Administrativo</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <link rel="stylesheet" href="{{asset('plugin/bootstrap-4.6.1/css/bootstrap.min.css')}}">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="{{asset('plugin/icheck-bootstrap/icheck-bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('plugin/fontawesome-free/css/all.min.css')}}">
        <link rel="stylesheet" href="{{asset('plugin/apexcharts-bundle/dist/apexcharts.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="{{asset('plugin/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
        <!-- DateRangerPicker -->
        <link rel="stylesheet" href="{{asset('plugin/daterangepicker/daterangepicker.css')}}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{asset('plugin/select2/css/select2.min.css')}}">
        <link rel="stylesheet" href="{{asset('plugin/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
        {{-- Colopicker --}}
        <link rel="stylesheet" href="{{asset('plugin/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('plugin/AdminLTE/css/adminlte.min.css')}}">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="{{asset('plugin/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
        <!-- summernote -->
        <link rel="stylesheet" href="{{asset('plugin/summernote/summernote-bs4.min.css')}}">

        <link rel="stylesheet" href="{{asset('plugin/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
        <link rel="stylesheet" href="{{asset('plugin/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
        <link rel="stylesheet" href="{{asset('/plugin/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

        <link rel="stylesheet" href="{{asset('/plugin/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css')}}">

        {{-- Jquery-ui --}}
        <link rel="stylesheet" href="{{asset('plugin/jquery-ui-1.13.1/jquery-ui.min.css')}}">
        <link rel="stylesheet" href="{{asset('plugin/jquery-ui-1.13.1/jquery-ui.theme.min.css')}}">

        <link rel="stylesheet" href="{{asset('painel/style.min.css')}}">

        <style>
            select[readonly].select2-hidden-accessible + .select2-container {
                pointer-events: none;
                touch-action: none;
            }

            select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
                background: #eee;
                box-shadow: none;
            }

            select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow
            select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
                display: none;
            }
        </style>
    </head>
    <body class="hold-transition layout-top-nav layout-navbar-fixed">
        <div class="wrapper">
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
                <div class="container-fluid">
                    <a href="/" class="navbar-brand">
                        <img src="{{asset('site/imgs/logo.png')}}" alt="Logo do Vendedor" height="35px" style="opacity: .8">
                        {{-- <span class="brand-text font-weight-light">Despachante JPG</span> --}}
                    </a>

                    <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-bars text-light" style="font-size: 24px"></i>
                    </button>

                    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                        <!-- Left navbar links -->
                        <ul class="navbar-nav">
                            <li class="nav-item @if(\Request::is('admin')) active-custom-menu @endif">
                                <a href="{{asset('admin')}}" class="nav-link">Painel</a>
                            </li>
                            <li class="nav-item dropdown dropdown-hover @if(\Request::is('admin/comercial/*')) active-custom-menu @endif">
                                <a id="adminPedidosDropDown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Pedidos</a>
                                <ul aria-labelledby="adminPedidosDropDown" style="top: 80%;" class="dropdown-menu border-0 shadow">
                                    <li>
                                        <a href="{{asset('admin/comercial/pedidos')}}" class="dropdown-item">Produtos</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/comercial/pedidos-servicos')}}" class="dropdown-item">Serviços</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/comercial/assinaturas')}}" class="dropdown-item">Assinaturas</a>
                                    </li>
                                    <li>
                                        <a href="{{route('reservaManual')}}" class="dropdown-item">Reserva Manual</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown dropdown-hover @if(\Request::is('admin/cadastro/*')) active-custom-menu @endif">
                                <a id="adminCadastrosDropDown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Cadastros</a>
                                <ul aria-labelledby="adminCadastrosDropDown" style="top: 80%;" class="dropdown-menu border-0 shadow">
                                    <li>
                                        <a href="{{asset('admin/cadastro/categoria_menu/produtos')}}" class="dropdown-item">Categorias-Produtos</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/cadastro/categoria_menu/servicos')}}" class="dropdown-item">Categorias-Serviços</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/cadastro/comissao-afiliados')}}" class="dropdown-item">Comissões Afiliados</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/cadastro/atributos')}}" class="dropdown-item">Atributos</a>
                                    </li>
                                    <li>
                                        <a href="{{route('produto', 'aprovados')}}" class="dropdown-item">Produtos</a>
                                    </li>
                                    <li>
                                        <a href="{{route('servico', 'aprovados')}}" class="dropdown-item">Serviços</a>
                                    </li>
                                    <li>
                                        <a href="{{route('admin.coupon.index')}}" class="dropdown-item">Cupons</a>
                                    </li>
                                    <li>
                                        <a href="{{route('admin.localidadeRetirada', 'localidades-de-retirada')}}" class="dropdown-item">Localidades</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/cadastro/bairros')}}" class="dropdown-item">Novo Bairro</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown dropdown-hover @if(\Request::is('admin/cliente/*')) active-custom-menu @endif">
                                <a id="adminClientesDropDown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Clientes</a>
                                <ul aria-labelledby="adminClientesDropDown" style="top: 80%;" class="dropdown-menu border-0 shadow">
                                    <li>
                                        <a href="{{asset('admin/cliente/clientes')}}" class="dropdown-item">Clientes</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/cliente/vendedores')}}" class="dropdown-item">Vendedores</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/cliente/afiliados')}}" class="dropdown-item">Afiliados</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown dropdown-hover @if(\Request::is('admin/outros/*')) active-custom-menu @endif">
                                <a id="adminOutrosDropDown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Outros</a>
                                <ul aria-labelledby="adminOutrosDropDown" style="top: 80%;" class="dropdown-menu border-0 shadow">
                                    <li>
                                        <a href="{{asset('admin/outros/aprovar-avaliacao-produto')}}" class="dropdown-item">Produtos Avaliados</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/aprovar-avaliacao-servico')}}" class="dropdown-item">Serviços Avaliados</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/contatos')}}" class="dropdown-item">Contatos</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/configurar-seo')}}" class="dropdown-item">Paginas SEO</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/banners')}}" class="dropdown-item">Banners</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/evento-home')}}" class="dropdown-item">Eventos Home</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/evento-home-rural')}}" class="dropdown-item">Eventos Home Rural</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/paginas')}}" class="dropdown-item">Páginas</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/faturamento')}}" class="dropdown-item">Faturamento</a>
                                    </li>
                                    <li>
                                        <a href="#parcelamentoRegras" data-toggle="modal" class="dropdown-item">Parcelamentos</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/newsletter')}}" class="dropdown-item">Inscrições Newsletter</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('admin/outros/clientes-esperando-aviso')}}" class="dropdown-item">Clientes Avise-Me</a>
                                    </li>
                                    <li>
                                        <a href="{{route('admin.client.cart')}}" class="dropdown-item">Carrinhos de Clientes</a>
                                    </li>
                                    <li>
                                        <a href="{{route('admin.logs')}}" class="dropdown-item">Logs do Sistema</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                            <li class="nav-item @if(\Request::is('admin/perfil')) active-custom-menu @endif">
                                <a href="{{asset('admin/perfil')}}" class="nav-link">Perfil</a>
                            </li>
                            <li class="nav-item @if(\Request::is('admin/contas')) active-custom-menu @endif">
                                <a href="{{asset('admin/contas')}}" class="nav-link">Contas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out-alt"></i> Sair</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Right navbar links -->
                    <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" href="#">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- /.navbar -->

            <div class="content-wrapper">
                @yield('container')
            </div>
        </div>

        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <div class="modal fade" id="parcelamentoRegras">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="#" method="post" id="postParcelamentoRegras">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title">Regras de Parcelamento</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-2">#</div>
                                <div class="col-4">Taxa %</div>
                                <div class="col-4" title="Mostrar Parcela depois que ultrappassar o valor">Valor</div>
                            </div>
                            @php
                                $parcelas_regras = getTabelaGeral('regra_parcelamento','parcelas')->array_text ?? [];
                            @endphp
                            @for ($i = 1; $i < 13; $i++)
                                <input type="hidden" class="form-control" name="parcela[{{$i}}][parcela]" value="{{$i}}">
                                <div class="row my-1">
                                    <div class="form-group col-2 text-center"><button type="button" class="btn btn-default btn-sm">{{$i}}</button></div>
                                    <div class="form-group col-4 text-center"><input type="text" class="form-control form-control-sm real" name="parcela[{{$i}}][porcentage]" value="{{$parcelas_regras[$i]['porcentage'] ?? null}}"></div>
                                    <div class="form-group col-4 text-center"><input type="text" class="form-control form-control-sm real" name="parcela[{{$i}}][valor]" value="{{$parcelas_regras[$i]['valor'] ?? null}}"></div>
                                </div>
                            @endfor
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                            <button type="button" class="btn btn-success btn-salvar" data-refresh="S" data-save_target="#postParcelamentoRegras" data-save_route="{{route('admin.parcelamentoRegras')}}"><i class="fas fa-save"></i> Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- jQuery -->
        <script src="{{asset('plugin/jquery-3.6.0.min.js')}}"></script>
        <!-- MaskJquery -->
        <script src="{{asset('plugin/mask.jquery.js')}}"></script>
        <!-- ValidaCnpjCpf -->
        <script src="{{asset('plugin/valida_cpf_cnpj.js')}}"></script>
        <!-- bootstrap-4.6.1 -->
        <script src="{{asset('plugin/bootstrap-4.6.1/js/bootstrap.bundle.min.js')}}"></script>
        <!-- Select2 -->
        <script src="{{asset('plugin/select2/js/select2.full.min.js')}}"></script>
        <!-- SweetAlert2 -->
        <script src="{{asset('plugin/sweetalert2/sweetalert2.min.js')}}"></script>
        <!-- Moment -->
        <script src="{{asset('plugin/moment/moment.min.js')}}"></script>
        <!-- Colorpicker -->
        <script src="{{asset('plugin/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
        <!-- DateRangerPicker -->
        <script src="{{asset('plugin/daterangepicker/daterangepicker.js')}}"></script>
        <!-- ChartJS -->
        <script src="{{asset('plugin/apexcharts-bundle/dist/apexcharts.min.js')}}"></script>
        {{-- <script src="{{asset('plugin/chart.js/Chart.min.js')}}"></script> --}}
        <script src="{{asset('plugin/summernote/summernote-bs4.min.js')}}"></script>
        <!-- overlayScrollbars -->
        <script src="{{asset('plugin/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{asset('plugin/AdminLTE/js/adminlte.min.js')}}"></script>

        <script src="{{asset('plugin/datatables/jquery.dataTables.min.js')}}"></script>
        <script src="//cdn.datatables.net/plug-ins/1.10.12/sorting/datetime-moment.js"></script>
        <script src="{{asset('plugin/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
        <script src="{{asset('plugin/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
        <script src="{{asset('plugin/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
        <script src="{{asset('plugin/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
        <script src="{{asset('plugin/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
        <script src="{{asset('plugin/jszip/jszip.min.js')}}"></script>
        <script src="{{asset('plugin/pdfmake/pdfmake.min.js')}}"></script>
        <script src="{{asset('plugin/pdfmake/vfs_fonts.js')}}"></script>
        <script src="{{asset('plugin/datatables-buttons/js/buttons.html5.min.js')}}"></script>
        <script src="{{asset('plugin/datatables-buttons/js/buttons.print.min.js')}}"></script>
        <script src="{{asset('plugin/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

        <script src="{{asset('plugin/bootstrap-select-1.13.14/dist/js/bootstrap-select.min.js')}}"></script>
        <script src="{{asset('plugin/bootstrap-select-1.13.14/dist/js/i18n/defaults-pt_BR.js')}}"></script>

        {{-- Jquert-ui --}}
        <script src="{{asset('plugin/jquery-ui-1.13.1/jquery-ui.min.js')}}"></script>
        {{-- Funções do painel --}}
        <script src="{{asset('painel/painel.funcoes.min.js')}}"></script>

        @yield('scripts')
    </body>
</html>
