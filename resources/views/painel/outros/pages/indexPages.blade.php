@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Páginas adicionais</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Páginas</li>
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
                            <h3 class="card-title">Páginas</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <a href="{{route('admin.page.new')}}" class="btn btn-primary">Criar Nova Página</a>
                            <div class="container mt-2 table-responsive">
                                <table class="table table-hover" id="table_pedido">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Link</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($page_views as $page_view)
                                            <tr>
                                                <td>{{$page_view->title}}</td>
                                                <td><a href="{{asset($page_view->link)}}" target="_blank">{{asset($page_view->link)}}</a></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{route('admin.page.edit', $page_view->id)}}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Editar</a>
                                                        <button type="button" class="btn btn-sm btn-danger btn-destroy" data-id="{{$page_view->id}}" data-url="{{route('admin.page.destroy')}}"><i class="fas fa-trash"></i> Excluir</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection