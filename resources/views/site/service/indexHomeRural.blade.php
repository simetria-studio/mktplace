@extends('layouts.site')

@section('category-top')
    @include('components.category', ['bg' => '#ca5812', 'tipo_home' => 'home-rural'])
@endsection
@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    <style>
        #custom-handle {
            width: 4em;
            height: 1.6em;
            top: 50%;
            margin-top: -.8em;
            text-align: center;
            line-height: 1.6em;
        }
    </style>

    {{-- Banner --}}
    @if (banner_configs('turismo-principal')->count() > 0)
        <div class="banner d-none d-md-block">
            <div id="carousel_banner" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    @for ($i = 0; $i < banner_configs('turismo-principal')->count(); $i++)
                        <li data-target="#carousel_banner" data-slide-to="{{$i}}" @if($i == 0)class="active"@endif></li>
                    @endfor
                </ol>
                <div class="carousel-inner">
                    @foreach (banner_configs('turismo-principal') as $key => $banner)
                        <div class="carousel-item @if($key == 0) active @endif">
                            <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                        </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#carousel_banner" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carousel_banner" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    @endif
    {{-- Banner Mobile --}}
    @if (banner_configs('turismo-principal-mobile')->count() > 0)
        <div class="banner d-md-none">
            <div id="carousel_banner_mobile" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    @for ($i = 0; $i < banner_configs('turismo-principal-mobile')->count(); $i++)
                        <li data-target="#carousel_banner_mobile" data-slide-to="{{$i}}" @if($i == 0)class="active"@endif></li>
                    @endfor
                </ol>
                <div class="carousel-inner">
                    @foreach (banner_configs('turismo-principal-mobile') as $key => $banner)
                        <div class="carousel-item @if($key == 0) active @endif">
                            <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                        </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#carousel_banner_mobile" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carousel_banner_mobile" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    @endif

    {{-- <div class="container my-5">
        <div class="row">
            <div class="form-group col-3 d-none d-sm-block">
                <img src="{{asset('site/imgs/mapa-icon.png')}}" width="60px" alt="">
                <b>Perto de você</b>
            </div>
            <div class="form-group col-12 col-sm-5 d-flex align-items-center">
                <input type="hidden" class="latitude">
                <input type="hidden" class="longitude">
                <input type="text" class="form-control address-maps" placeholder="Insira a Cidade ou CEP" style="border-radius: 2rem;">
                <div class="mr-2">
                    <button type="button" class="btn btn-address-maps"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="form-group col-12 col-sm-4 d-flex align-items-center">
                <div class="w-100">
                    <input type="hidden" id="formControlRange">
                    <div id="slider">
                        <div id="custom-handle" class="ui-slider-handle"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12 address-maps-search"></div>
            <div class="form-group col-12" id="mapa" style="min-height: 300px;"></div>
        </div>
    </div> --}}

    {{-- <div class="container my-5" id="servicos_proximo"></div> --}}

    <div class="container my-5">
        <div class="mb-2 d-flex">
            <h3 class="border-bottom border-dark mb-0 pb-2 d-none d-sm-block">SERVIÇOS PROXIMOS</h3>
            <h6 class="border-bottom border-dark mb-0 pb-2 d-sm-none">SERVIÇOS PROXIMOS</h6>
            <div class="d-flex align-items-center ml-2">
                <a href="{{route('servicosProximos')}}" class="text-dark">VER MAIS >>></a>
            </div>
        </div>
        <div class="row">{!! $services_html !!}</div>
    </div>

    <div class="container my-5">
        <div class="text-center"><h3>{{getTabelaGeral('titulos','titulo_turismo_rural_first')->valor??''}}</h3></div>
        <div id="carousel_banner_turismo_rural_custom-first1" class="carousel slide d-none d-sm-block" data-ride="carousel">
            <div class="carousel-inner">
                @foreach ($event_home_a->chunk(4) as $key => $banner)
                    <div class="carousel-item @if($key == 0) active @endif">
                        <div class="row">
                            @php
                                $count_bg = 0;
                            @endphp
                            @foreach ($banner as $item)
                                <div class="col-3">
                                    <div class="text-center" style="background-color: {{$bg_color_event_array[$count_bg]}};border-radius: 3rem;height: 100%;">
                                        <a href="{{$item->link}}" @if($item->new_tab == 1)target="_blank"@endif><img src="{{$item->url_file}}" style="border-radius: 3rem;" class="d-block w-100" alt="{{$item->file_name}}" title="{{$item->file_name}}"></a>
                                        <div class="text-center py-3 px-2">{!!$item->descricao_curta!!}</div>
                                    </div>
                                </div>
                                @php
                                    $count_bg++;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                @endforeach
                {{-- <div class="carousel-item">
                    <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                </div> --}}
            </div>
            {{-- <a class="carousel-control-prev" href="#carousel_banner_turismo_rural_custom-first1" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel_banner_turismo_rural_custom-first1" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                <span class="sr-only">Next</span>
            </a> --}}
        </div>
        <div id="carousel_banner_turismo_rural_custom-first2" class="carousel slide d-sm-none" data-ride="carousel">
            <div class="carousel-inner">
                @foreach ($event_home_a->chunk(2) as $key => $banner)
                    <div class="carousel-item @if($key == 0) active @endif">
                        <div class="row">
                            @php
                                $count_bg = 0;
                            @endphp
                            @foreach ($banner as $item)
                                <div class="col-6">
                                    <div class="text-center" style="background-color: {{$bg_color_event_array[$count_bg]}};border-radius: 3rem;height: 100%;">
                                        <a href="{{$item->link}}" @if($item->new_tab == 1)target="_blank"@endif><img src="{{$item->url_file}}" style="border-radius: 3rem;" class="d-block w-100" alt="{{$item->file_name}}" title="{{$item->file_name}}"></a>
                                        <div class="text-center py-3 px-2">{!!$item->descricao_curta!!}</div>
                                    </div>
                                </div>
                                @php
                                    $count_bg++;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                @endforeach
                {{-- <div class="carousel-item">
                    <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                </div> --}}
            </div>
            {{-- <a class="carousel-control-prev" href="#carousel_banner_turismo_rural_custom-first2" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel_banner_turismo_rural_custom-first2" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                <span class="sr-only">Next</span> --}}
            </a>
        </div>
    </div>
    <div class="container my-5">
        <div class="text-center"><h3>{{getTabelaGeral('titulos','titulo_turismo_rural_second')->valor??''}}</h3></div>
        <div id="carousel_banner_turismo_rural_custom-second1" class="carousel slide d-none d-sm-block" data-ride="carousel">
            <div class="carousel-inner">
                @foreach (banner_configs('carousel_banner_turismo_rural_custom-second')->chunk(2) as $key => $banner)
                    <div class="carousel-item @if($key == 0) active @endif">
                        <div class="row">
                            @foreach ($banner as $item)
                                <div class="col-6">
                                    <a href="{{$item->link}}" @if($item->new_tab == 1)target="_blank"@endif><img src="{{$item->url_file}}" class="d-block w-100" alt="{{$item->file_name}}" title="{{$item->file_name}}"></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                {{-- <div class="carousel-item">
                    <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                </div> --}}
            </div>
            <a class="carousel-control-prev" href="#carousel_banner_turismo_rural_custom-second1" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel_banner_turismo_rural_custom-second1" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <div id="carousel_banner_turismo_rural_custom-second2" class="carousel slide d-sm-none" data-ride="carousel">
            <div class="carousel-inner">
                @foreach (banner_configs('carousel_banner_turismo_rural_custom-second') as $key => $banner)
                    <div class="carousel-item @if($key == 0) active @endif">
                        <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                    </div>
                @endforeach
                {{-- <div class="carousel-item">
                    <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                </div> --}}
            </div>
            <a class="carousel-control-prev" href="#carousel_banner_turismo_rural_custom-second2" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel_banner_turismo_rural_custom-second2" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    <div class="container d-none d-sm-block my-5">
        <div id="carousel_banner_footer" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @for ($i = 0; $i < banner_configs('turismo-secundario')->count(); $i++)
                    <li data-target="#carousel_banner_footer" data-slide-to="{{$i}}" @if($i == 0)class="active"@endif></li>
                @endfor
            </ol>
            <div class="carousel-inner">
                @foreach (banner_configs('turismo-secundario') as $key => $banner)
                    <div class="carousel-item @if($key == 0) active @endif">
                        <a href="{{$banner->link}}" @if($banner->new_tab == 1)target="_blank"@endif><img src="{{$banner->url_file}}" class="d-block w-100" alt="{{$banner->file_name}}" title="{{$banner->file_name}}"></a>
                    </div>
                @endforeach
                {{-- <div class="carousel-item">
                    <img src="{{asset('site/imgs/banner-home-secundario.png')}}" class="d-block w-100" alt="...">
                </div> --}}
            </div>
            <a class="carousel-control-prev" href="#carousel_banner_footer" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/previous.png')}}" alt=""></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel_banner_footer" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"><img width="40%" src="{{asset('site/imgs/next.png')}}" alt=""></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
@endsection

@section('js')
    {{-- <script>
        let lat_ini = '-25.4321587';
        let lng_ini = '-49.2796458';
        let marker_array_add = [];
        let map;
        let marker;
        let geocoder;
        let infoWindow;

        function inicializar() {
            infoWindow = new google.maps.InfoWindow();
            geocoder = new google.maps.Geocoder();
            map = new google.maps.Map(document.getElementById("mapa"), {
                zoom: 12,
                center: { lat: parseFloat(lat_ini), lng: parseFloat(lng_ini)},
            });

            marker = new google.maps.Marker({
                map,
            });

            // marker.addListener("click", () => {
            //     infoWindow.close();
            //     infoWindow.setContent(marker.getTitle());
            //     infoWindow.open(marker.getMap(), marker);
            // });

            map.addListener("click", (e) => {
                geocode({ location: e.latLng });
            });
            clear();
        }

        function clear() {
            for(var marker_arr_c in marker_array_add){
                marker_array_add[marker_arr_c].setMap(null);
            }
            marker_array_add = [];
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
                    $.ajax({
                        url: '/getDistance-map',
                        type: 'POST',
                        data: {geometry, km_max: $('#formControlRange').val(), address_search: $('.address-maps').val()},
                        success: getSuccessFun
                    });

                    $('.address-maps-search').html('Endereço Encontrado: '+results[0].formatted_address);
                    $('.latitude').val(geometry.lat);
                    $('.longitude').val(geometry.lng);
                    return results;
                })
                .catch((e) => {
                    alert("O geocódigo não foi bem-sucedido pelo seguinte motivo: " + e);
                });
        }

        function getSuccessFun(data){
            // console.log(data);
            var services = data[0];
            var services_id = data[1];
            for(var latlng in services){
                var position = {
                    lat: parseFloat(services[latlng].lat),
                    lng: parseFloat(services[latlng].lng)
                };
                var marker_custom = new google.maps.Marker({
                    title: services[latlng].loja,
                    position: position,
                    // icon: 'https://feitoporbiguacu.com/site/imgs/download.png',
                    // draggable:true,
                    map: map,
                });

                // infowindow = new google.maps.InfoWindow();
                google.maps.event.addListener(marker_custom, 'click', (function(marker_custom, latlng) {
                    return function() {
                        infoWindow.setContent(`<a target="_blank" href="${latlng.slug}">${latlng.loja}</a>`);
                        infoWindow.open(map, marker_custom);
                    }
                }) (marker_custom, services[latlng]));
                marker_array_add.push(marker_custom);
            }

            $('#servicos_proximo').empty();
            $('#servicos_proximo').append(`
                <div class="mb-2 d-flex">
                    <h3 class="border-bottom border-dark mb-0 pb-2 d-none d-sm-block">SERVIÇOS PROXIMOS</h3>
                    <h6 class="border-bottom border-dark mb-0 pb-2 d-sm-none">SERVIÇOS PROXIMOS</h6>
                    <div class="d-flex align-items-center ml-2">
                        <a href="{{route('servicosProximos')}}" class="text-dark">VER MAIS >>></a>
                    </div>
                </div>
            `);

            $.ajax({
                url: '/singleService',
                type: 'POST',
                data: {services_id, service_gtag_type: ['Serviços Proximos', 'servico_proximo']},
                success: (data_s) => {
                    // console.log(data_s);
                    $('#servicos_proximo').append(`
                        <div class="row">${data_s}</div>
                    `);
                }
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_KEY')}}&callback=inicializar&v=weekly" async defer></script>
    <script>
        $(document).ready(function(){
            $( "#slider" ).slider({
                range: "max",
                min: 0,
                max: 200,
                value: 20,
                create: function() {
                    $( "#custom-handle" ).text( $( this ).slider( "value" ) + ' Km' );
                    $( "#formControlRange" ).val($( this ).slider( "value" ));
                },
                slide: function( event, ui ) {
                    $( "#custom-handle" ).text( ui.value + ' Km' );
                    $( "#formControlRange" ).val(ui.value);
                }
            });
            $('.btn-address-maps').on('click', function(e){
                // if(e.keyCode == 13){
                    if($('.address-maps').val() !== ''){
                        geocode({address: $('.address-maps').val()});
                    }
                    // geocode({location:{lat: -25.423749, lng: -49.197049}});
                // }
            });
        });
    </script> --}}
@endsection