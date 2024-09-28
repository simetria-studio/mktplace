@extends('layouts.site')

@section('container')

    <div class="container seller-logo-banner">
        <div class="row mt-3 mb-5">
            <div class="col-4">
                <img src="{{asset('storage/'.$store->logo_path)}}" alt="Logo Tipo" class="img-fluid rounded logo">
            </div>
            <div class="col-8 text-center">
                <img src="{{isset($store->banner_path) ? asset('storage/'.$store->banner_path) : ''}}" alt="Banner da Loja" class="img-fluid rounded banner">
            </div>
        </div>

        <div class="row mb-3 laze_load">
            @foreach ($services as $service)
                @include('components.singleService', ['service' => $service, 'class' => 'col-md-3'])
            @endforeach
        </div>

        <div class="my-5 loading d-none justify-content-center">
            <div class="spinner-border d-none" style="width: 5rem; height: 5rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        {{-- <div class="row my-2 justify-content-center">
            <div class="col-12 col-md-6 text-center">
                <a class="btn btn-c-orange" href="{{route('contactus')}}">TENHO INTERESSE EM FAZER COMPRA NO ATACADO</a>
            </div>
        </div> --}}
    </div>
@endsection