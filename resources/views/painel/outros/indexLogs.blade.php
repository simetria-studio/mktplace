@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Logs do Sistema Geral</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Logs do Sistema Geral</li>
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
                            <h3 class="card-title">Logs do Sistema Geral</h3>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="row">
                                <div class="col-6">
                                    <select id="select_log_name" class="form-control">
                                        @foreach ($logs as $log)
                                            <option value="{{$log[0]}}">{{$log[1]}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn btn-primary" id="charge_log">Carregar Log</button>
                                </div>
                            </div>

                            <div class="card mt-2" style="background-color: #000;height: 600px;overflow: auto;width: 100%;">
                                <div class="card-body" style="color: #fff;width: 100%;white-space: break-spaces;" id="log_selected"></div>
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
        $(document).ready(function(){
            $(document).on('click', '#charge_log', function(){
                var select_log_name = $('#select_log_name').val();

                $('#log_selected').html('<h2>CARREGANDO LOG...</h2>');

                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {select_log_name},
                    success: (data) => {
                        $('#log_selected').text(data.data_response);
                    }
                })
            });
        });
    </script>
@endsection