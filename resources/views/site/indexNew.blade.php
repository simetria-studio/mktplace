@extends('layouts.site')

@section('category-top')
    @include('components.category', ['bg' => '#3D550C', 'tipo_home' => 'home'])
@endsection
@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    <div class="container my-1 mt-md-4">
        <div class="mb-2 d-flex">
            <h3 class="border-bottom border-dark mb-0 pb-2 d-none d-sm-block">O QUE HÁ DE NOVO POR AQUI</h3>
            <h6 class="border-bottom border-dark mb-0 pb-2 d-sm-none">O QUE HÁ DE NOVO POR AQUI</h6>
        </div>

        <div class="row">
            @foreach ($produtos as $produto)
                @include('components.singleProduct', ['produto' => $produto, 'class' => 'col-md-3', 'product_gtag_type' => 'Produtos Novos'])
            @endforeach
        </div>

        <div class="mb-4 mt-5 d-flex justify-content-center">
            {{$produtos->links()}}
        </div>
    </div>
@endsection
