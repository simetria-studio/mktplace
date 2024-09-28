@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Produtos Avaliados</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Produtos Avaliados</li>
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
                            <h3 class="card-title">Produtos Avaliados</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
                            <div>
                                <div>Filtro de Status</div>
                                <a href="{{ route('admin.rateProduct.apro', 'nao-aprovado') }}" class="btn btn-sm {{ $status_code == '1' ? 'btn-outline-primary' : 'btn-primary' }}">Não Aprovados</a>
                                <a href="{{ route('admin.rateProduct.apro', 'aprovado') }}" class="btn btn-sm {{ $status_code == '0' ? 'btn-outline-primary' : 'btn-primary' }}">Aprovados</a>
                            </div>
                            <div class="mt-2 table-responsive">
                                <table class="table table-hover table-ajax" data-url="{{route('allTables').'?status_code='.$status_code}}" data-table="rate_product" data-columns="{{json_encode($form_contacts_table)}}">
                                    <thead>
                                        <tr>
                                            <th>Imagem</th>
                                            <th>Usuário</th>
                                            <th>Nome do Produto</th>
                                            <th>Estrelas</th>
                                            <th>Comentário</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @forelse ($star_products as $star_product)
                                            <tr>
                                                <td><img width="60px" class="img-fluid rounded" src="{{($star_product->product->images->sortBy('position')->first() ?? null) ? $star_product->product->images->sortBy('position')->first()->caminho : asset('site/imgs/icone-logo.png')}}" alt=""></td>
                                                <td>{{$star_product->user->name}}</td>
                                                <td>{{$star_product->product->nome}}</td>
                                                <td>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $star_product->star)
                                                            <i class="fas fa-star text-warning"></i>
                                                        @else
                                                            <i class="fas fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </td>
                                                <td>
                                                    <textarea class="form-control">{{$star_product->comment}}</textarea>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="">
                                                        <a href="#" class="btn btn-primary btn-send-admin-star" data-info="1" data-id="{{$star_product->id}}" data-route="{{route('admin.rateProduct.send')}}">Aprovar</a>

                                                        <a href="#" class="btn btn-danger btn-send-admin-star" data-info="2" data-id="{{$star_product->id}}" data-route="{{route('admin.rateProduct.send')}}">Excluir</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse --}}
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