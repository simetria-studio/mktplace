@extends('layouts.painelSman')

@section('container')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dados da Loja</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{asset('/vendedor')}}">Dashboard</a></li>
                    <li class="breadcrumb-item">Dados do Loja</li>
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
                        <h3 class="card-title">Dados da Loja</h3>
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
                                <div class="col-12 col-md-6">
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#atualizarLoja"><i class="fas fa-store"></i> Atualizar Loja</button>
                                </div>
                            </div>
                        </div>

                        <div class="container mt-5">
                            <div class="row">
                                <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                    <strong>Nome da Loja:</strong> {{$seller->store->store_name ?? ''}}
                                </div>
                                <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                    <strong>Link da Loja:</strong> <a target="_blank" href="{{isset($seller->store->store_slug) ? route('seller.store',[$seller->store->store_slug]) : '#'}}">{{isset($seller->store->store_slug) ? asset('loja-vendedor/'.$seller->store->store_slug) : 'Sem Link'}}</a>
                                </div>

                                <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                    <strong>Endereço:</strong> {{$seller->store->address ?? ''}}, Nº {{$seller->store->number ?? ''}} - {{$seller->store->address2 ?? ''}}
                                </div>
                                <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                    <strong>Estado/Cidade:</strong> {{$seller->store->state ?? ''}} / {{$seller->store->city ?? ''}} - {{$seller->store->post_code ?? ''}}
                                </div>
                                <div class="col-12 col-sm-6 py-3 px-3 border-bottom">
                                    <strong>Complemento:</strong> {{$seller->store->complement ?? ''}}
                                </div>
                                <div class="col-6 col-sm-3 py-3 px-3 border-bottom">
                                    <strong>Telefone:</strong> {{$seller->store->phone1 ?? ''}}
                                </div>
                                <div class="col-6 col-sm-3 py-3 px-3 border-bottom">
                                    <strong>Celular:</strong> {{$seller->store->phone2 ?? ''}}
                                </div>
                                @if ($seller->store->id ?? null)
                                    <div class="col-12 col-sm-6 py-3 px-3">
                                        <div>
                                            <strong>Logotipo: (260x260)</strong>
                                            <button type="button" class="btn btn-sm btn-success btn-path-update">{{isset($seller->store->logo_path) ? 'Atualizar' : 'Inserir'}} logotipo</button>
                                            <input type="file" name="logo_path" class="d-none file-change" data-url="{{route('seller.atualizarLojaLogoBanner')}}" data-wh="[130,130]" data-id="{{$seller->store->id ?? null}}">
                                        </div>
                                        <br>
                                        <div class="image">
                                            @isset($seller->store->logo_path)
                                                <img src="{{($seller->store->logo_path ?? null) ? asset('storage/'.$seller->store->logo_path) : asset('site/imgs/logo.png')}}" alt="LOGO" width="130" height="130" class="rounded">
                                            @endisset
                                        </div>
                                    </div>
                                @endif
                                @if ($seller->store->id ?? null)
                                    <div class="col-12 col-sm-6 py-3 px-3">
                                        <div>
                                            <strong>Banner: (760x260)</strong>
                                            <button type="button" class="btn btn-sm btn-success btn-path-update">{{isset($seller->store->banner_path) ? 'Atualizar' : 'Inserir'}} banner</button>
                                            <input type="file" name="banner_path" class="d-none file-change" data-url="{{route('seller.atualizarLojaLogoBanner')}}" data-wh="[370,130]" data-id="{{$seller->store->id ?? null}}">
                                        </div>
                                        <br>
                                        <div class="image">
                                            @isset($seller->store->banner_path)
                                                <img src="{{($seller->store->logo_path ?? null) ? asset('storage/'.$seller->store->banner_path) : asset('site/imgs/banner-vendedor-faltante.png')}}" alt="LOGO" width="370" height="130" class="rounded">
                                            @endisset
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="atualizarLoja">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="post" id="postAtualizarLoja">
                @csrf
                <input type="hidden" name="id" value="{{$seller->store->id ?? null}}">
                <div class="modal-header">
                    <h4 class="modal-title">Dados da Loja <div class="spinner-border d-none loadCep" role="status"><span class="sr-only">Loading...</span></div></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="store_name">Nome da Loja</label>
                            <input type="text" name="store_name" class="form-control" placeholder="Nome da Loja" value="{{$seller->store->store_name ?? ''}}">
                        </div>
                        {{-- Mapa --}}
                        <div class="form-group col-12" id="mapa" style="min-height: 300px;"></div>
                        <div class="form-group address-maps-search"></div>
                        <div class="form-group col-12">
                            <label for="">Pesquisar Endereço no Mapa</label>
                            <div class="input-group">
                                <input type="text" class="form-control address-maps" placeholder="av. tal, 111, bairro piau - curitiba/pr">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary btn-address-maps">Pesquisar</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-5 col-md-4">
                            <label for="post_code">CEP</label>
                            <input type="text" name="post_code" class="form-control" placeholder="00000-000" value="{{$seller->store->post_code ?? ''}}">
                        </div>
                        <div class="form-group col-7 col-md-8">
                            <label for="address">Endereço</label>
                            <input type="text" name="address" class="form-control" placeholder="Endereço/Rua/Avenida" value="{{$seller->store->address ?? ''}}">
                        </div>
                        <div class="form-group col-3">
                            <label for="number">Nº</label>
                            <input type="text" name="number" class="form-control" placeholder="0000" value="{{$seller->store->number ?? ''}}">
                        </div>
                        <div class="form-group col-9">
                            <label for="complement">Complemento</label>
                            <input type="text" name="complement" class="form-control" placeholder="Complemento" value="{{$seller->store->complement ?? ''}}">
                        </div>
                        <div class="form-group col-12">
                            <label for="address2">Bairro</label>
                            <input type="text" name="address2" class="form-control" placeholder="Bairro" value="{{$seller->store->address2 ?? ''}}">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="state">Estado</label>
                            <select name="state" class="form-control select2 state">
                                <option value="">::Selecione uma Opção::</option>
                                @isset ($seller->store->state)
                                    <option value="{{$seller->store->state}}" selected>{{$seller->store->state}}</option>
                                @endisset
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="city">Cidade</label>
                            <select name="city" class="form-control select2 city">
                                <option value="">::Selecione uma Opção::</option>
                                @isset ($seller->store->city)
                                    <option value="{{$seller->store->city}}" selected>{{$seller->store->city}}</option>
                                @endisset
                            </select>
                        </div>
                        <input type="hidden" name="latitude" value="{{$seller->store->lat ?? ''}}">
                        <input type="hidden" name="longitude" value="{{$seller->store->lng ?? ''}}">
                        <div class="form-group col-12 col-md-6">
                            <label for="phone1">Telefone</label>
                            <input type="text" name="phone1" class="form-control" placeholder="(00) 0000-0000" value="{{$seller->store->phone1 ?? ''}}">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="phone2">Celular</label>
                            <input type="text" name="phone2" class="form-control" placeholder="(00) 00000-0000" value="{{$seller->store->phone2 ?? ''}}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                    <button type="button" class="btn btn-success btn-salvar" data-refresh="S" data-save_target="#postAtualizarLoja" data-save_route="{{route('seller.atualizarLoja')}}"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $(document).on('click', '.btn-path-update',function(){$(this).parent().find('input.file-change').trigger('click');});
            $(document).on('change', '.file-change', function(){
                $(this).parent().parent().find('.image').empty();

                var wh = [100,100];
                if($(this).data('wh')) wh = $(this).data('wh');

                var preview = $(this).parent().parent().find('.image');
                var files   = $(this).prop('files');

                function readAndPreview(file) {
                    // Make sure `file.name` matches our extensions criteria
                    if ( /\.(jpe?g|png|gif)$/i.test(file.name) ) {
                        var reader = new FileReader();

                        reader.addEventListener("load", function () {
                        var image = new Image();
                        image.classList = 'rounded';
                        image.width = parseFloat(wh[0]);
                        image.height = parseFloat(wh[1]);
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

                var formData = new FormData();
                formData.append($(this).attr('name'), $(this).prop('files')[0]);
                formData.append('store_id', $(this).data('id'));
                $.ajax({
                    url: $(this).data('url'),
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        // console.log(data);
                    }
                });
            });
        });
    </script>
    <script>
        let lat_ini = "{{$seller->store->lat ?? '-25.4321587'}}";
        let lng_ini = "{{$seller->store->lng ?? '-49.2796458'}}";
        function inicializar() {
            map = new google.maps.Map(document.getElementById("mapa"), {
                zoom: 8,
                center: { lat: parseFloat(lat_ini), lng: parseFloat(lng_ini)},
            });
            geocoder = new google.maps.Geocoder();

            marker = new google.maps.Marker({
                map,
            });
            map.addListener("click", (e) => {
                geocode({ location: e.latLng });
            });
            clear();
        }

        function clear() {
            marker.setMap(null);
        }

        function geocode(request) {
            clear();
            geocoder
                .geocode(request)
                .then((result) => {
                    const { results } = result;

                    map.setCenter(results[0].geometry.location);
                    marker.setPosition(results[0].geometry.location);
                    marker.setMap(map);
                    // console.log(results[0]);
                    var geometry = JSON.parse(JSON.stringify(results[0].geometry.location));

                    $('.address-maps-search').html('Endereço Encontrado: '+results[0].formatted_address);
                    $('[name="latitude"]').val(geometry.lat);
                    $('[name="longitude"]').val(geometry.lng);
                    return results;
                })
                .catch((e) => {
                    alert("O geocódigo não foi bem-sucedido pelo seguinte motivo: " + e);
                });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=inicializar&v=weekly" async defer></script>
    <script>
        $(document).ready(function(){
            geocode({location:{lat: parseFloat(lat_ini), lng: parseFloat(lng_ini)}});
            $('.btn-address-maps').on('click', function(e){
                // if(e.keyCode == 13){
                    if($('.address-maps').val() !== ''){
                        geocode({address: $('.address-maps').val()});
                    }
                    // geocode({location:{lat: -25.423749, lng: -49.197049}});
                // }
            });
        });
    </script>
@endsection