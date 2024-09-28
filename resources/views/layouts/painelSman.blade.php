<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Painel Vendedor</title>

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
                        <img src="{{!empty(auth()->guard('seller')->user()->store->logo_path) ? asset('storage/'.auth()->guard('seller')->user()->store->logo_path) : asset('site/imgs/logo.png')}}" alt="Logo do Vendedor" height="35px" style="opacity: .8">
                        {{-- <span class="brand-text font-weight-light">Despachante JPG</span> --}}
                    </a>

                    <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-bars text-light" style="font-size: 24px"></i>
                    </button>

                    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                        <!-- Left navbar links -->
                        <ul class="navbar-nav">
                            <li class="nav-item @if(\Request::is('vendedor')) active-custom-menu @endif">
                                <a href="{{route('seller.dashboard')}}" class="nav-link">Painel</a>
                            </li>
                            <li class="nav-item dropdown dropdown-hover @if(\Request::is('vendedor/comercial/*')) active-custom-menu @endif">
                                <a id="sellerPedidosDropDown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Pedidos</a>
                                <ul aria-labelledby="sellerPedidosDropDown" style="top: 80%;" class="dropdown-menu border-0 shadow">
                                    <li>
                                        <a href="{{asset('vendedor/comercial/pedidos')}}" class="dropdown-item">Produtos</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('vendedor/comercial/pedidos-servicos')}}" class="dropdown-item">Serviços</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('vendedor/comercial/assinaturas')}}" class="dropdown-item">Assinaturas</a>
                                    </li>
                                    <li>
                                        <a href="{{route('seller.reservaManual')}}" class="dropdown-item">Reserva Manual</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown dropdown-hover @if(\Request::is('vendedor/cadastro/*')) active-custom-menu @endif">
                                <a id="sellerCadastrosDropDown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Cadastros</a>
                                <ul aria-labelledby="sellerCadastrosDropDown" style="top: 80%;" class="dropdown-menu border-0 shadow">
                                    <li>
                                        <a href="{{route('seller.produto', 'aprovados')}}" class="dropdown-item">Produtos</a>
                                    </li>
                                    <li>
                                        <a href="{{route('seller.servico', 'aprovados')}}" class="dropdown-item">Serviços</a>
                                    </li>
                                    <li>
                                        <a href="{{asset('vendedor/cadastro/atributos')}}" class="dropdown-item">Atributos</a>
                                    </li>
                                    <li>
                                        <a href="{{route('seller.coupon.index')}}" class="dropdown-item">Cupons</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown dropdown-hover @if(\Request::is('vendedor/entregas/*')) active-custom-menu @endif">
                                <a id="sellerFretesDropDown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Entregas</a>
                                <ul aria-labelledby="sellerFretesDropDown" style="top: 80%;" class="dropdown-menu border-0 shadow">
                                    <li>
                                        <a href="{{asset('vendedor/entregas/melhor_envio')}}" class="dropdown-item">Melhor Envio</a>
                                    </li>
                                    <li>
                                        <a href="{{route('seller.ownTransport')}}" class="dropdown-item">Trans. Próprio</a>
                                    </li>
                                    <li>
                                        <a href="{{route('seller.localDeRetirada', 'locais-de-retirada')}}" class="dropdown-item">Locais de Retirada</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item @if(\Request::is('vendedor/perfil')) active-custom-menu @endif">
                                <a href="{{route('seller.perfil')}}" class="nav-link">Perfil</a>
                            </li>
                            <li class="nav-item @if(\Request::is('vendedor/loja')) active-custom-menu @endif">
                                <a href="{{route('seller.loja')}}" class="nav-link">Loja</a>
                            </li>
                            <li class="nav-item @if(\Request::is('vendedor/conta-de-recebimento/asaas')) active-custom-menu @endif">
                                <a href="{{route('seller.contaRecebimento', 'asaas')}}" class="nav-link">Recebimento</a>
                            </li>
                        </ul>

                        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                            <li class="nav-item d-none d-sm-inline-block">
                                <a href="{{isset(auth()->guard('seller')->user()->store->store_slug) ? route('seller.store',[auth()->guard('seller')->user()->store->store_slug]) : '#'}}" target="_blank" class="nav-link btn-minha-loja">Ver Loja</a>
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

        <form id="logout-form" action="{{ route('seller.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

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

        @yield('script')
    </body>
</html>
