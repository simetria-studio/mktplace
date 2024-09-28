@extends('layouts.site')

@section('receiver-news')
    @include('components.receiverNews')
@endsection

@section('container')
    <style>
        .header-s {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .header-s h1 {
            margin: 0;
        }

        section {
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        .faq-item {
            margin-bottom: 20px;
        }

        .faq-item h3 {
            margin: 0 0 10px 0;
            color: #0056b3;
        }

        .faq-item p {
            margin: 0;
        }

        a {
            color: #0056b3;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

    <section class="container my-3">
        <section class="header-s">
            <h1>Perguntas Frequentes - FAQ</h1>
            <p>Sabemos que algumas detalhes podem não ser tão claros num primeiro momento mas sempre podemos contar com mais informações e/ou perguntar como as coisas funcionam!!</p>
        </section>
        
        <section id="compradores">
            <h2>Para Compradores</h2>
            <div class="faq-item">
                <h3>Como Faço a Compra?</h3>
                <p>Para realizar a compra basta escolher os produtos, realizar o cadastro, selecionar o método de pagamento e finalizar a compra.</p>
            </div>
            <div class="faq-item">
                <h3>Quem entrega os produtos?</h3>
                <p>Cada vendedor faz o envio do produto vendido. Confira no carrinho ao finalizar a compra quais são os métodos de envio disponíveis.</p>
            </div>
            <div class="faq-item">
                <h3>Por que aparece mais que um frete no carrinho?</h3>
                <p>Isso acontece quando há produtos de mais de um vendedor no carrinho. Como cada vendedor faz o envio dos seus produtos, ocorre uma cobrança específica de frete para cada vendedor selecionado.</p>
            </div>
            <div class="faq-item">
                <h3>Quais são os meios de pagamento?</h3>
                <p>O Feito Por Biguaçu aceita pagamento por boleto bancário, cartão de crédito e Pix.</p>
            </div>
            <div class="faq-item">
                <h3>Prazo de entrega</h3>
                <p>O prazo de entrega é contado a partir da confirmação de pagamento. Ele é indicado por cada vendedor, devendo-se observar a descrição completa do produto além do método de envio escolhido.</p>
            </div>
        </section>
        
        <section id="vendedores">
            <h2>Para Vendedores</h2>
            <div class="faq-item">
                <h3>O que é necessário para vender no Feito Por Biguaçu?</h3>
                <p>É necessário que os produtos estejam alinhados com o Feito Por Biguaçu (conforme Termos de Uso), realizar o cadastro no Feito Por Biguaçu e possuir ou criar uma conta no PAGAR.ME (método gratuito) para recebimento e envio dos produtos. Caso seus produtos sejam enviados para todo o Brasil, o Feito Por Biguaçu disponibiliza a opção do Melhor Envio, sendo necessário possuir ou criar uma conta (método gratuito).</p>
            </div>
            <div class="faq-item">
                <h3>O Feito Por Biguaçu cobra mensalidade?</h3>
                <p>Não há cobrança de mensalidades para vender no Feito Por Biguaçu!</p>
            </div>
            <div class="faq-item">
                <h3>O Feito Por Biguaçu cobra para realizar o cadastro da minha loja?</h3>
                <p>Não há custo de adesão/setup. Faça já o seu cadastro de forma gratuita!</p>
            </div>
            <div class="faq-item">
                <h3>Tem alguma cobrança no Feito Por Biguaçu?</h3>
                <p>Sim, o Feito Por Biguaçu cobra uma taxa percentual por venda efetivada. Esses valores diferenciam entre Turismo Rural e Venda de Produtos. <a href="#">Clique aqui para saber mais detalhes</a>.</p>
            </div>
            <div class="faq-item">
                <h3>Como recebo os pedidos?</h3>
                <p>Ao receber um pedido, você é automaticamente avisado por e-mail. Os pedidos podem ser acompanhados pelo painel do vendedor.</p>
            </div>
            <div class="faq-item">
                <h3>Como os clientes me encontrarão?</h3>
                <p>O Feito Por Biguaçu faz a divulgação dos produtos nas redes sociais e mecanismos de busca. Além disso, cada vendedor possui um link próprio da sua loja que pode ser personalizada com seu logotipo e banner de capa.</p>
            </div>
            <p>Caso ainda tenha alguma dúvida não atendida nas Perguntas Frequentes, entre em contato via <a href="#">Fale Conosco</a>!</p>
        </section>
    </section>
@endsection