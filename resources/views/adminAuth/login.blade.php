<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Acesso Painel Administrativo</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <link rel="stylesheet" href="{{asset('plugin/bootstrap-4.6.0/css/bootstrap.min.css')}}">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="{{asset('plugin/icheck-bootstrap/icheck-bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('plugin/fontawesome-free/css/all.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('plugin/AdminLTE/css/adminlte.min.css')}}">
    </head>
    <body class="hold-transition login-page">
        <div class="login-box">
            <!-- /.login-logo -->
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <a href="{{route('home')}}" class="h1"><img class="img-fluid" src="{{asset('site/imgs/logo.png')}}" alt=""></a>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">Faça login para iniciar sua sessão</p>
                    @if (session()->get('error'))
                        <div class="mb-3 py-2 text-center rounded alert-danger">{{session()->get('error')}}</div>
                    @endif

                    <form action="{{route('admin.login')}}" method="post">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Senha">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" name="remember" id="remember">
                                    <label for="remember">
                                        Lembrar
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.login-box -->

        <!-- jQuery -->
        <script src="{{asset('plugin/jquery-3.6.0.min.js')}}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{asset('plugin/bootstrap-4.6.0/js/bootstrap.bundle.min.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{asset('plugin/AdminLTE/js/adminlte.min.js')}}"></script>
    </body>
</html>