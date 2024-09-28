@extends('layouts.site')

@section('category-top')
    @include('components.category', ['bg' => '#3D550C', 'tipo_home' => 'home'])
@endsection
@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    <div class="container my-1 mt-md-4">
        <div><a class="btn btn-c-primary" href="{{route('favorites.service')}}">Ver Meus Serviços Favoritos</a></div>

        <div class="my-2 d-flex">
            <h3 class="border-bottom border-dark mb-0 pb-2 d-none d-sm-block">SEUS PRODUTOS FAVORITOS</h3>
            <h6 class="border-bottom border-dark mb-0 pb-2 d-sm-none">SEUS PRODUTOS FAVORITOS</h6>
        </div>

        <div class="row">
            @foreach ($produtos as $produto_novo)
                @include('components.singleProduct', ['produto' => $produto, 'class' => 'col-md-3'])
            @endforeach
        </div>

        <div class="mb-4 mt-5 d-flex justify-content-center">
            {{$produtos->links()}}
        </div>
    </div>
@endsection
