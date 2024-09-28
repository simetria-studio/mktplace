@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Banner</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Banners</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners da Home Page - Principal Slide (Largura=1600xAltura=350)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="home-page-principal">
                                <input type="hidden" name="wmax" value="1600">
                                <input type="hidden" name="hmax" value="350">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('home-page-principal') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][name]" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners da Home Page Mobile - Principal Slide (Largura=800xAltura=350)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="home-page-principal-mobile">
                                <input type="hidden" name="wmax" value="800">
                                <input type="hidden" name="hmax" value="350">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('home-page-principal-mobile') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][name]" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners da Home Page - Produto Local Slide (Largura=1200xAltura=480)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="home-page-produtor-local">
                                <input type="hidden" name="wmax" value="1200">
                                <input type="hidden" name="hmax" value="480">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('home-page-produtor-local') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}]['name']" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners do Produtor Local - Principal Slide (Largura=1600xAltura=350)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="produtor-local-principal">
                                <input type="hidden" name="wmax" value="1600">
                                <input type="hidden" name="hmax" value="350">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('produtor-local-principal') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][name]" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners do Pordutor Local - Produtor Local Não Encontrado Slide (Largura=1200xAltura=480)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="produtor-local-nao-encontrado">
                                <input type="hidden" name="wmax" value="1200">
                                <input type="hidden" name="hmax" value="480">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('produtor-local-nao-encontrado') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][name]" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners do Turismo Rural - Principal Slide (Largura=1600xAltura=350)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="turismo-principal">
                                <input type="hidden" name="wmax" value="1600">
                                <input type="hidden" name="hmax" value="350">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('turismo-principal') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][name]" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners do Turismo Rural Mobile - Principal Slide (Largura=800xAltura=350)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="turismo-principal-mobile">
                                <input type="hidden" name="wmax" value="800">
                                <input type="hidden" name="hmax" value="350">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('turismo-principal-mobile') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][name]" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners do Turismo Rural - Secundário Slide (Largura=1200xAltura=350)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="turismo-secundario">
                                <input type="hidden" name="wmax" value="1200">
                                <input type="hidden" name="hmax" value="350">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('turismo-secundario') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][name]" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-primary card-outline collapsed-card">
                        {{-- Header do Card --}}
                        <div class="card-header">
                            <h3 class="card-title">Banners do Turismo Rural - {{getTabelaGeral('titulos','titulo_turismo_rural_second')->valor??''}} Slide (Largura=600xAltura=600)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad">
                            <div class="card">
                                <div class="card-body">
                                    <form action="#" method="post">
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-sm-8 input-group">
                                                <input type="text" class="form-control" name="titulo_turismo_rural_second" value="{{getTabelaGeral('titulos','titulo_turismo_rural_second')->valor??''}}">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-success btn-save-title">Atualizar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <form action="#" method="post">
                                <input type="hidden" name="local" value="carousel_banner_turismo_rural_custom-second">
                                <input type="hidden" name="wmax" value="1200">
                                <input type="hidden" name="hmax" value="350">
                                <div class="row">
                                    <div class="col-6 col-md-3 imgs-count img-find-0">
                                        <button type="button" class="btn btn-success btn-add-c-image">+</button>
                                        <input type="file" class="d-none add-c-image" name="imagem[0][img]">
                                        <input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem[0][name]">
                                        <input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem[0][link]" placeholder="Link Direto">
                                        <div class="form-check link d-none">
                                            <input type="checkbox" class="form-check-input" name="imagem[0][new_tab]">
                                            <label for="">Abrir em Nova Aba?</label>
                                        </div>
                                        <div class="image my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach (banner_configs('carousel_banner_turismo_rural_custom-second') as $banner)
                                        <div class="col-6 col-md-3 imgs-count img-find-0">
                                            <div class="form-check">
                                                <input type="checkbox" id="destroy-image-{{$banner->id}}" class="form-check-input" name="imagem_delete[]" value={{$banner->id}}>
                                                <label for="destroy-image-{{$banner->id}}">Excluir Imagem?</label>
                                            </div>
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][name]" value="{{$banner->file_name}}">
                                            <input type="text" class="form-control form-control-sm mt-1" name="imagem_update[{{$banner->id}}][link]" value="{{$banner->link}}" placeholder="Link Direto">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="imagem_update[{{$banner->id}}][new_tab]" @if($banner->new_tab == 1) checked @endif>
                                                <label for="">Abrir em Nova Aba?</label>
                                            </div>
                                            <div class="image my-2">
                                                <img src="{{$banner->url_file}}" alt="{{$banner->file_name}}" title="{{$banner->file_name}}" class="rounded img-fluid img-bordered">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-12"><button type="button" class="btn btn-success btn-save-local">Atualizar</button></div>
                                </div>
                            </form>
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
            $(document).on('click', '.btn-save-local', function(){
                // Pegamos os dados do data
                let btn = $(this);
                btn.prop('disabled', true).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');

                $.ajax({
                    url: `{{route('admin.banner.store')}}`,
                    type: "POST",
                    data: new FormData(btn.closest('form')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        btn.prop('disabled', false).html('Atualizar');
                        window.location.reload();
                    },
                    error: (err) => {
                        // console.log(err);
                        btn.prop('disabled', false).html('Atualizar');

                        if(err.responseJSON.msg_alert){
                            Swal.fire({
                                icon: err.responseJSON.icon_alert,
                                text: err.responseJSON.msg_alert,
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.btn-add-c-image', function(e){
                e.preventDefault();
                $(this).parent().find('.add-c-image').trigger('click');
            });
            $(document).on('change', '.add-c-image', function(){
                $(this).removeClass('add-c-image');

                $(this).parent().find('.btn-add-c-image').removeClass('btn-success btn-add-c-image').addClass('btn-danger btn-remove-image').html('x');

                var count_i = 0;
                for(var i=0; ($(this).parent().parent().find('.imgs-count').length+1)>i; i++){
                    if(!$(this).parent().parent().find('.imgs-count').is('.img-find-'+i)){
                        count_i = i;
                    }
                }

                $(this).parent().parent().prepend(
                    '<div class="col-6 col-md-3 imgs-count img-find-'+count_i+'">'+
                        '<button type="button" class="btn btn-success btn-add-c-image">+</button>'+
                        '<input type="file" class="d-none add-c-image" name="imagem['+count_i+'][img]">'+
                        '<input type="text" class="form-control form-control-sm mt-1 file-title d-none" name="imagem['+count_i+'][name]">'+
                        '<input type="text" class="form-control form-control-sm mt-1 link d-none" name="imagem['+count_i+'][link]" placeholder="Link Direto">'+
                        '<div class="form-check link d-none">'+
                            '<input type="checkbox" class="form-check-input" name="imagem['+count_i+'][new_tab]">'+
                            '<label for="">Abrir em Nova Aba?</label>'+
                        '</div>'+
                        '<div class="image my-2"></div>'+
                    '</div>'
                );

                var form_img = $(this).parent();

                var preview = form_img.find('.image');
                var file_title = form_img.find('.file-title');
                var files   = $(this).prop('files');

                if(!$(this).parent().parent().is('.not-display-input')){
                    $(this).parent().find('.file-title').removeClass('file-title d-none');
                }
                $(this).parent().find('.link').removeClass('d-none');

                function readAndPreview(file) {
                    // Make sure `file.name` matches our extensions criteria
                    if ( /\.(jpe?g|png|gif)$/i.test(file.name) ) {
                        var reader = new FileReader();

                        file_title.val(file.name);

                        reader.addEventListener("load", function () {
                        var image = new Image();
                        image.classList = 'rounded img-fluid img-bordered';
                        // image.height = 180;
                        image.title = file.name;
                        image.src = this.result;
                        preview.append( image );
                        }, false);

                        reader.readAsDataURL(file);
                    }
                }

                if (files) {
                    [].forEach.call(files, readAndPreview);
                }
            });

            $(document).on('click', '.btn-save-title', function(){
                // Pegamos os dados do data
                let btn = $(this);
                btn.prop('disabled', true).html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
                var data = {
                    tabela: 'titulos',
                    coluna: 'titulo_turismo_rural_second',
                    valor: btn.closest('form').find('[name="titulo_turismo_rural_second"]').val(),
                };

                $.ajax({
                    url: `{{route('admin.tabelaGeral')}}`,
                    type: "POST",
                    data: data,
                    success: (data) => {
                        btn.prop('disabled', false).html('Atualizar');
                        Swal.fire({
                            icon: 'success',
                            text: 'Atualizado com successo!',
                        });
                    },
                    error: (err) => {
                        // console.log(err);
                        btn.prop('disabled', false).html('Atualizar');

                        if(err.responseJSON.msg_alert){
                            Swal.fire({
                                icon: err.responseJSON.icon_alert,
                                text: err.responseJSON.msg_alert,
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection