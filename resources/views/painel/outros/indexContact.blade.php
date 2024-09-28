@extends('layouts.painelAdm')

@section('container')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Contatos</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{asset('/admin')}}">Dashboard</a></li>
                        <li class="breadcrumb-item">Contatos</li>
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
                            <h3 class="card-title">Contatos Feito Pelo Site</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Corpo do Card --}}
                        <div class="card-body pad table-responsive">
                            <div class="container-fluid mt-2 table-responsive">
                                <table class="table table-hover table-ajax" data-url="{{route('allTables')}}" data-table="form_contact" data-columns="{{json_encode($form_contacts_table)}}">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Telefone/Celular</th>
                                            <th>Assunto</th>
                                            <th>Mensagem</th>
                                            <th>Status</th>
                                            <th>Reponsável</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @forelse ($contacts as $contact)
                                            <tr class="tr-id-{{$contact->id}}">
                                                <td>{{$contact->name}}</td>
                                                <td>{{$contact->email}}</td>
                                                <td>{{$contact->phone}}</td>
                                                <td>{{$contact->assunto}}</td>
                                                <td>
                                                    @if ($contact->status == 0)
                                                        <button type="button" class="btn btn-sm btn-block btn-status-contact" style="background-color: #fdc300;">Em Andamento</button>
                                                    @elseif ($contact->status == 1)
                                                        <button type="button" class="btn btn-sm btn-block btn-status-contact" style="background-color: #58bc9a;">Já Resolvido</button>
                                                    @endif
                                                </td>
                                                <td>{{$contact->mensagem}}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="">
                                                        <button type="button" class="btn btn-info btn-sm alterarStatusContact" data-url="{{route('admin.atualizar_status_contact')}}" data-id="{{$contact->id}}">Alterar Status</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse --}}
                                    </tbody>
                                </table>
                            </div>

                            {{-- <div>{{$contacts->links()}}</div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection