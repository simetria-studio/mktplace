@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configuração de SEO nas Páginas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">SEO</li>
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
                            <h3 class="card-title">Configurar SEO nas Páginas</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
                            <form id="seoConfig" method="post">
                                <input type="hidden" name="id">
                                <div class="row mb-3 justify-content-center">
                                    <div class="col-12 col-sm-4">
                                        <select name="page" class="form-control select-page selectpicker" data-size="6" data-actions-box="true" data-live-search="true" title="Selecione uma Página">
                                            <option value="rsa-home">Página Principal (Home)</option>
                                            <option value="rsa-products">Todos os Produtos</option>
                                            <option value="rsa-rural_tourism">Turismo Rural</option>
                                            <option value="rsa-search">Página de Busca</option>
                                            <option value="rsa-contactus">Página de Contato</option>
                                            <option value="rsa-blog">Página do Blog</option>
                                            <option value="rsa-faq">Página Faq</option>
                                            <option value="rsa-indexnew">Página Novidades</option>
                                            <option value="rsa-specialselection">Página Seleção Especial</option>
                                            <option value="rsa-produtoresLocais">Página Produtos Locais</option>
                                            <option value="rsa-loginComprador">Login - Comprador</option>
                                            <option value="rsa-loginVendedor">Login - Vendedor</option>
                                            <option value="rsa-registroComprador">Registro - Comprador</option>
                                            <option value="rsa-registroVendedor">Registro - Vendedor</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row justify-content-center">
                                    <div class="form-group col-12 col-sm-4">
                                        <label for="">Título da Página</label>
                                        <input type="text" name="title" class="form-control">
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-12 col-sm-4">
                                        <label for="">Link Permanente</label>
                                        <input type="text" name="link" class="form-control">
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="">Palavras-Chave</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control keywords-adds" placeholder="Inserir termos separados por ';'">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success btn-add-keywords">Adicionar</button>
                                            </div>
                                        </div>
                                        <div class="form-group mt-2">
                                            <div class="card collapsed-card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Lista de palavras chave</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                                title="Collapse">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="overflow-auto" style="max-height: 280px;">
                                                        <table class="table table-sm table-striped keywords_adds"></table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="">Descrição do Site</label>
                                        <textarea name="description" class="form-control max-caracteres" data-max_caracteres="160"></textarea>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-12 col-sm-6">
                                        <label for="banner_path">Imagem</label>
                                        <input type="file" name="banner_path" class="form-control">
                                        <div class="banner_path my-2"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 text-right">
                                        <button type="button" class="btn btn-success btn-save-seo d-none"><i class="fas fa-save"></i> Gravar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection