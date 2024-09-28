@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registro de Localidades de Retirada</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
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
                                        <button type="button" class="btn btn-success btn-sm charge-modal" data-url="{{ route('admin.localidadeRetirada', 'modal-localidade-retirada') }}" data-tag_event="#modalLocalidadeRetirada"><i class="fas fa-plus"></i> Nova Localidade</button>
                                    </div>
                                </div>
                            </div>

                            <div class="container mt-2" id="tableLocalidadesRetirada">
                                @include('components.admin.cadastros.tableLocalidadesRetirada', get_defined_vars())
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection