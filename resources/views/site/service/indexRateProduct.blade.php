@extends('layouts.site')

@section('container')
    <div class="container">
        @foreach ($services as $service)
            @if ($service->stars->count() == 0)
                <form action="{{route('perfil.rateService')}}" method="post">
                    <div class="row justify-content-center my-2 py-3 px-2 border rounded">
                        <div class="col-12 col-md-2 d-flex align-items-center">
                            <img class="img-fluid rounded" src="{{isset($service->service->images[0]) ? $service->service->images[0]->caminho : asset('site/imgs/logo.png')}}" alt="">
                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-center">
                            <span>{{$service->service_name}}</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="hidden" name="service_id" value="{{$service->service_id}}">
                            <div class="estrelas">
                                <label for="estrela_1" class="estrela_click"><i class="fas fa-star"></i></label>
                                <input type="radio" id="estrela_1" name="estrela" value="1">
        
                                <label for="estrela_2" class="estrela_click"><i class="fas fa-star"></i></label>
                                <input type="radio" id="estrela_2" name="estrela" value="2">
        
                                <label for="estrela_3" class="estrela_click"><i class="fas fa-star"></i></label>
                                <input type="radio" id="estrela_3" name="estrela" value="3">
        
                                <label for="estrela_4" class="estrela_click"><i class="fas fa-star"></i></label>
                                <input type="radio" id="estrela_4" name="estrela" value="4">
        
                                <label for="estrela_5" class="estrela_click"><i class="fas fa-star"></i></label>
                                <input type="radio" id="estrela_5" name="estrela" value="5">
                            </div>
                            <br>
                            <textarea name="comment" class="form-control"></textarea>
                        </div>
                        <div class="col-12 col-md-4 mt-2">
                            <button type="button" class="btn btn-block btn-primary btn-env-comment">Enviar Avaliação</button>
                        </div>
                    </div>
                </form>
            @endif
        @endforeach
    </div>
@endsection

@section('js')
@endsection
