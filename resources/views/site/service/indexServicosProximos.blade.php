@extends('layouts.site')

@section('category-top')
    @include('components.category', ['bg' => '#ca5812', 'tipo_home' => 'home-rural'])
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
    {{-- <div class="container my-5">
        <div class="row">
            <div class="form-group col-3 d-none d-sm-block">
                <img src="{{asset('site/imgs/mapa-icon.png')}}" width="60px" alt="">
                <b>Perto de você</b>
            </div>
            <div class="form-group col-12 col-sm-5 d-flex align-items-center">
                <input type="hidden" class="latitude" value="{{session()->get('services_search_prox_geometry')['lat']??''}}">
                <input type="hidden" class="longitude" value="{{session()->get('services_search_prox_geometry')['lng']??''}}">
                <input type="text" class="form-control address-maps" value="{{session()->get('services_search_prox_address')['address']??''}}" placeholder="Insira a Cidade ou CEP" style="border-radius: 2rem;">
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
    <div class="container my-5">
        <div class="d-flex">
            <h3 class="border-bottom border-dark pb-2">SERVIÇOS LOCAIS</h3>
        </div>

        <div class="row">
            @foreach ($services as $service)
                @include('components.singleService', ['service' => $service, 'class' => 'col-md-3'])
            @endforeach
        </div>

        <div class="links mt-3">{{$services->links()}}</div>
    </div>

    <input type="hidden" class="services_search_prox_geometry" value="{{json_encode(session()->get('services_search_prox_geometry'))}}">
    <input type="hidden" class="services_search_prox" value="{{json_encode(session()->get('services_search_prox'))}}">
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
        let geometry;

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
                    geometry = JSON.parse(JSON.stringify(results[0].geometry.location));

                    $('.address-maps-search').html('Endereço Encontrado: '+results[0].formatted_address);
                    $('.latitude').val(geometry.lat);
                    $('.longitude').val(geometry.lng);
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
            let set_interval;
            $( "#slider" ).slider({
                range: "max",
                min: 0,
                max: 200,
                value: (parseInt(`{{session()->get('services_search_prox_address')['km_max'] ?? 20}}`)),
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
                        set_interval = setInterval(() => {
                            if(geometry){
                                $.ajax({
                                    url: '/getDistance-map',
                                    type: 'POST',
                                    data: {geometry, km_max: $('#formControlRange').val(), address_search: $('.address-maps').val()},
                                    success: (data)=>{window.location.reload();}
                                });
                                clearInterval(set_interval);
                            }
                        }, 800);
                    }
                    // geocode({location:{lat: -25.423749, lng: -49.197049}});
                // }
            });

            $(function(){
                var services_search_prox_geometry = JSON.parse($('.services_search_prox_geometry').val());
                var services_search_prox = JSON.parse($('.services_search_prox').val());
                geocode({location: {lat: parseFloat(services_search_prox_geometry.lat), lng: parseFloat(services_search_prox_geometry.lng)}})
                console.log(services_search_prox);
                var services_id= [];
                for(var latlng in services_search_prox){
                    services_id.push(services_search_prox[latlng].id);
                    var position = {
                        lat: parseFloat(services_search_prox[latlng].lat),
                        lng: parseFloat(services_search_prox[latlng].lng)
                    };
                    var marker_custom = new google.maps.Marker({
                        title: services_search_prox[latlng].loja,
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
                    }) (marker_custom, services_search_prox[latlng]));
                    marker_array_add.push(marker_custom);
                }
            });
        });
    </script> --}}
@endsection