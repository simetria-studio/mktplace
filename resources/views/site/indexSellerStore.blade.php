@extends('layouts.site')

@section('container')

    <div class="container seller-logo-banner">
        <div class="row mt-3 mb-5">
            <div class="col-4">
                <img src="{{isset($store->logo_path) ? asset('storage/'.$store->logo_path) : asset('site/imgs/logo.png')}}" alt="Logo Tipo" class="img-fluid rounded logo">
            </div>
            <div class="col-8 text-center">
                <img src="{{isset($store->banner_path) ? asset('storage/'.$store->banner_path) : asset('site/imgs/banner-vendedor-faltante.png')}}" alt="Banner da Loja" class="img-fluid rounded banner">
            </div>
        </div>

        <div class="d-flex justify-content-end w-100 my-3">
            <div class="compartilhar">
                <span class="mr-2">Compartilhar Loja:</span> <span class="d-md-none"><br></span>
                <a target="_blank" href=" https://www.facebook.com/sharer/sharer.php?u={{route('seller.store', $store->store_slug ?? '0')}}"><i class="fab fa-facebook-f"></i></a>
                <a target="_blank" href="https://twitter.com/intent/tweet?text={{route('seller.store', $store->store_slug ?? '0')}}"><i class="fab fa-twitter"></i></a>
                <a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url={{route('seller.store', $store->store_slug ?? '0')}}"><i class="fab fa-linkedin"></i></a>
                <a target="_blank" href="https://api.whatsapp.com/send?text={{route('seller.store', $store->store_slug ?? '0')}}&hl=pt-br"><i class="fab fa-whatsapp"></i></a>
                <a class="copy-link" href="{{route('seller.store', $store->store_slug ?? '0')}}"><i class="fas fa-link"></i></a>
            </div>
        </div>

        <div class="row mb-3 laze_load">
            @foreach ($produtos as $produto)
                @include('components.singleProduct', ['produto' => $produto, 'class' => 'col-md-3', 'product_gtag_type' => 'Loja do Vendedor'])
            @endforeach
        </div>

        <div class="my-5 loading d-none justify-content-center">
            <div class="spinner-border d-none" style="width: 5rem; height: 5rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="row my-2 justify-content-center">
            <div class="col-12 col-md-6 text-center">
                <a class="btn btn-c-orange" href="{{route('contactus')}}">TENHO INTERESSE EM FAZER COMPRA NO ATACADO</a>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            if(parseInt(`{{$produtos_locais}}`) > 0){
                $(function(){
                    setTimeout(() => {
                        if(!getCookie('cep_consulta')){
                            $('#ModalCepSession').removeClass('d-none');
                            setTimeout(() => {
                                $('#ModalCepSession').css({
                                    'top': '0',
                                    'transition': 'top .8s'
                                });
                            }, 100);
                        }
                    }, 1000);
                });
            }
        });
    </script>
@endsection