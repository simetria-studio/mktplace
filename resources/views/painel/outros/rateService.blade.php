@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Serviços Avaliados</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Serviços Avaliados</li>
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
                            <h3 class="card-title">Serviços Avaliados</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
                            <div class="container mt-2 table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Imagem</th>
                                            <th>Usuário</th>
                                            <th>Nome do Serviço</th>
                                            <th>Estrelas</th>
                                            <th>Comentário</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($star_services as $star_service)
                                            <tr>
                                                <td><img width="60px" class="img-fluid rounded" src="{{($star_service->product->images[0] ?? null) ? $star_service->product->images[0]->caminho : asset('site/imgs/logo.png')}}" alt=""></td>
                                                <td>{{$star_service->user->name}}</td>
                                                <td>{{$star_service->service->service_title}}</td>
                                                <td>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $star_service->star)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="fas fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </td>
                                                <td>
                                                    <textarea class="form-control">{{$star_service->comment}}</textarea>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="">
                                                        <a href="#" class="btn btn-primary btn-send-admin-star" data-info="1" data-id="{{$star_service->id}}" data-route="{{route('admin.rateService.send')}}">Aprovar</a>

                                                        <a href="#" class="btn btn-danger btn-send-admin-star" data-info="2" data-id="{{$star_service->id}}" data-route="{{route('admin.rateService.send')}}">Excluir</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
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