@extends('layouts.site')

@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    <div class="container my-5">
        <h2 class="text-center mb-5">QUEM SOMOS</h2>

        <div class="row mb-5">
            <div class="col-12 col-md-5">
                <img class="img-fluid" src="{{ asset('site/imgs/quem-somos-1.jpeg') }}" alt="PROJETO FEITO POR BIGUAÇU">
            </div>
            <div class="col-12 col-md-7">
                <h4><b>PROJETO FEITO POR BIGUAÇU</b></h4>
                <p>
                    É uma iniciativa de Fundação CITeB com apoio do Ministério da Ciência, Tecnologia e Inovação, 
                    Governo de Biguaçu e Empresa de Pesquisa Agropecuária e Extensão Rural de Santa Catarina - EPAGRI, 
                    que estimula o aprendizado, a inovação e o empreendedorismo para que os pequenos grandes negócios da região de Biguaçu prosperem, 
                    inserindo-os no ambiente online para comercialização dos seus produtos e serviços.
                </p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <h4><b>PARA QUEM É O "SHOPPING VIRTUAL" FEITO POR BIGUAÇU?</b></h4>
                <p>
                    No shopping virtual (marketplace), você pode ter sua loja online e vender pela internet. Se você produz na região de Biguaçu e quer aumentar suas vendas, esta iniciativa é para você!
                </p>
                <ul>
                    <li>Produtor rural</li>
                    <li>Artesão</li>
                    <li>Proprietário de pequena pousada rural</li>
                </ul>
                <p>
                    Aproveite a oportunidade de participar, exibindo seus produtos num ambiente online que vai valorizar o seu produto e dar a possibilidade de aumentar as suas vendas.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-7">
                <h4><b>INCLUSÃO, CAPACITAÇÃO E INOVAÇÃO</b></h4>
                <p>
                    O projeto tem como objetivo acessibilizar a esfera de comércio online aos pequenos produtores (rurais, artesãos) e empreendedores de forma inclusiva e participativa, 
                    além de capacitar os mesmos a elevar a maturidade do negócio.
                </p>

                <a href="https://api.whatsapp.com/send?phone=5547996003481&text=Quero%20Participar%20do%20Projeto%20Feito%20por%20Bigua%C3%A7u!" target="_blank" class="btn-whatsapp-quem-somos">
                    <img src="{{ asset('site/imgs/whatsapp.png') }}" alt="Whatsapp-(47) 99600-3481">
                    Quero me inscrever
                </a>
            </div>
            <div class="col-12 col-md-5">
                <img class="img-fluid" src="{{ asset('site/imgs/quem-somos-2.jpeg') }}" alt="INCLUSÃO, CAPACITAÇÃO E INOVAÇÃO">
            </div>
        </div>
    </div>
@endsection