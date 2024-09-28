<div class="py-5 footer-email">
    <form action="{{route('registerNewsletter')}}" method="post" id="form_newsletter">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-3">
                    <h4 class="text-white">FAÇA PARTE DE NOSSA COMUNIDADE EXCLUSIVA E RECEBA</h4>
                    <h4 class="text-white">NOVIDADES E PROMOÇÕES ANTES DE TODO MUNDO!</h4>
                </div>
                <div class="form-group col-12 col-sm-5">
                    <input type="text" name="name" class="form-control" placeholder="Seu Nome">
                </div>
                <div class="form-group col-12 col-sm-5">
                    <input type="email" name="email" class="form-control" placeholder="Seu Email">
                </div>
                <div class="form-group col-12 col-sm-2">
                    <button type="button" class="btn btn-light btn-block btn-newsletter">ENVIAR</button>
                </div>
            </div>
        </div>
    </form>
</div>