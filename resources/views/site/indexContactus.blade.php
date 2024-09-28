@extends('layouts.site')

@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('meta_tags')
    <script src="https://www.google.com/recaptcha/api.js?render={{config('recaptcha.v3.public_key')}}"></script>
@endsection

@section('container')
<div class="container my-5">
    <div class="row">
        {{-- Contatos --}}
        <div class="col-12 col-md-5">
            <div class="pb-2">
                <h2><b>FALE CONOSCO</b></h2>
            </div>
            <p>
                <strong>Whatsapp/Fone</strong><br>
                <span>(47) 9 9600-3481</span><br>
                <span>(47) 9 8847-6422</span>
            </p>

            <p>
                <strong>E-mail</strong><br>
                <span>contato@raeasy.com</span>
            </p>
        </div>

        {{-- Formulario de email --}}
        <div class="col-12 col-md-7 form-email">
            <form action="{{route('send.contactus')}}" data-grecaptcha-action="message" id="form_contact" method="post">
                @csrf
                <div class="form-row">
                    <div class="col-12 pb-2">
                        <h2><b>FORMULÁRIO DE CONTATO</b></h2>
                        <p>Preencha os campos abaixo e entraremos em contato com você</p>
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="name">Nome Completo</label>
                        <input type="text" name="name" class="form-control" placeholder="Nome Completo">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="email">E-Mail</label>
                        <input type="email" name="email" class="form-control" placeholder="exemplo@exemplo.com.br">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="phone">Whatsapp/Fone (com DDD)</label>
                        <input type="text" name="phone" class="form-control" placeholder="(00) 0000-0000">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label for="assunto">Assunto</label>
                        <select name="assunto" class="form-control">
                            <option value="Quero fazer compras no atacado">Quero fazer compras no atacado</option>
                            <option value="Quero vender e preciso de ajuda">Quero vender e preciso de ajuda</option>
                            <option value="Quero indicar um produtor">Quero indicar um produtor</option>
                            <option value="Estou com problemas em um pedido">Selecione um Assunto</option>
                            <option value="Reclamação/elogio">Reclamação/elogio</option>
                            <option value="Outro motivo">Outro motivo</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="mensagem">Mensagem</label>
                        <textarea name="mensagem" rows="4" class="form-control max-caracteres" data-max_caracteres="120" placeholder="Escreva sua mensagem"></textarea>
                    </div>
                    <div class="form-group col-12">
                        <button type="button" class="btn btn-primary btn-block btn-send-contactus">Enviar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection