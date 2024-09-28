<?php

use App\Models\Order;

use App\Models\SignedPlan;
use App\Models\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FretesController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\AfiliadoController;
use App\Http\Controllers\CheckoutController;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PainelController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\IndexServiceController;
use App\Http\Controllers\Seller\StoreController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\EventHomeController;
use App\Http\Controllers\CheckoutServiceController;
use App\Http\Controllers\Seller\AuthSellerController;
use App\Http\Controllers\Seller\MelhorEnvioController;
use App\Http\Controllers\Seller\OwnTransportController;
use App\Http\Controllers\Seller\PainelSellerController;
use App\Http\Controllers\Seller\PedidoSellerController;
use App\Http\Controllers\Seller\LocalRetiradaController;
use App\Http\Controllers\Admin\LocalidadeRetiradaController;
use App\Http\Controllers\Seller\ResetUserPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['blockip'])->group(function(){
    Route::any('/sitemap', [IndexController::class, 'geraSiteMap']);
    
    Route::any('/cep/{cep}', [IndexController::class, 'cepConsulta']);
    Route::any('/cepCheckout/{cep}', [IndexController::class, 'cepCheckout']);
    
    Route::any('/modal-cep-session', [IndexController::class, 'ModalCepSession'])->name('ModalCepSession');
    
    Route::get('/', [IndexController::class, 'indexHome'])->name('home');
    Route::get('/todos-produtos', [IndexController::class, 'indexProducts'])->name('products');
    Route::get('/busca', [IndexController::class, 'indexSearch'])->name('search');
    Route::get('/categoria/{slug}', [IndexController::class, 'indexCategory'])->name('category');
    Route::get('produto/{slug}/{affiliate?}', [IndexController::class, 'indexProduct'])->name('product');
    Route::get('/quem-somos', [IndexController::class, 'indexWhoweare'])->name('whoweare');
    Route::get('/fale-conosco', [IndexController::class, 'indexContactus'])->name('contactus');
    Route::post('/faleconosco', [IndexController::class, 'sendContactus'])->name('send.contactus');
    Route::get('/loja-vendedor/{slug}', [IndexController::class, 'indexSellerStore'])->name('seller.store');
    Route::get('/loja-turismo/{slug}', [IndexServiceController::class, 'indexSellerStore'])->name('seller.turismo.store');
    Route::get('/blog', [IndexController::class, 'indexBlog'])->name('blog');
    Route::get('/termos-de-uso', [IndexController::class, 'indexTermsofuse'])->name('termsofuse');
    Route::get('/perguntas-frequentes', [IndexController::class, 'indexFaq'])->name('faq');
    Route::get('/trocas-e-devolucoes', [IndexController::class, 'indexExchangesreturns'])->name('exchangereturns');
    Route::get('/politica-de-privacidade', [IndexController::class, 'indexPrivacypolicy'])->name('privacypolicy');
    Route::get('/novidades', [IndexController::class, 'indexNew'])->name('indexnew');
    Route::get('/selecao-especial', [IndexController::class, 'indexSpecialSelection'])->name('specialselection');
    
    Route::get('/pagina/{slug}', [IndexController::class, 'indexPageView'])->name('indexPageView');
    
    // Newsletter
    Route::post('/inscricao-newsletter', [IndexController::class, 'registerNewsletter'])->name('registerNewsletter');
    Route::get('/cancelar-inscricao-newsletter', [IndexController::class, 'cancelNewsletter'])->name('cancelNewsletter');
    
    // Avise-me quando disponivel
    Route::post('/avise-me-register', [IndexController::class, 'aviseMeRegister'])->name('aviseMeRegister');
    
    // Turismo
    Route::get('/turismo', [IndexServiceController::class, 'indexRuralTourism'])->name('rural_tourism');
    Route::get('servico/{slug}/{affiliate?}', [IndexServiceController::class, 'indexService'])->name('service');
    Route::get('/categoria-servico/{slug}', [IndexServiceController::class, 'indexCategory'])->name('category.service');
    Route::get('/busca-servico', [IndexServiceController::class, 'indexSearch'])->name('search.service');
    Route::get('/add-services-favoritos/{service_id}', [IndexServiceController::class, 'addFavorites'])->name('addfavorites.service');
    
    Route::get('/add-favoritos/{product_id}', [IndexController::class, 'addFavorites'])->name('addfavorites');

    Route::get('/carrinho', [CartController::class, 'indexCart'])->name('cart');
    Route::post('/add-cart', [CartController::class, 'addCart'])->name('addCart');
    Route::post('/add-cart-qty', [CartController::class, 'addCartQty'])->name('addCartQty');
    Route::post('/remove-cart', [CartController::class, 'removeCart'])->name('removeCart');
    Route::get('/clear-cart', [CartController::class, 'clearCart'])->name('clearCart');

    Route::post('/create-session-cart', [CartController::class, 'createSessionCart'])->name('createSessionCart');

    Route::post('/freteCheckout', [FretesController::class, 'freteCheckout'])->name('freteCheckout');
    Route::any('/freteCheckoutProduto', [FretesController::class, 'freteCheckoutProduto'])->name('freteCheckoutProduto');
    Route::any('/freteCheckoutPlano', [FretesController::class, 'freteCheckoutPlano'])->name('freteCheckoutPlano');

    Route::get('/buscaEstado', [IndexController::class, 'buscaEstado']);
    Route::get('/buscaCidade/{id}', [IndexController::class, 'buscaCidade']);
    Route::get('/buscaBairro/{id}', [IndexController::class, 'buscaBairro']);

    Route::get('/produtores-locais', [IndexController::class, 'produtoresLocais'])->name('produtoresLocais');
    Route::get('/servicos-proximos', [IndexServiceController::class, 'servicosProximos'])->name('servicosProximos');
    
    Route::get('/agradecimento/pedido/{pedido?}', [CheckoutController::class, 'agradecimento'])->name('agradecimento');
    
    // Adicionando a uma sessão o serviço adquirido
    Route::post('/add-service-session', [CheckoutServiceController::class, 'seviceSession'])->name('service.session');

    Route::post('/add-plan-session', [CartController::class, 'cartSessionPlan'])->name('cartSessionPlan');
    
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/perfil', [IndexController::class, 'indexPerfil'])->name('perfil');
        Route::get('/perfil/pedidos/{order_number}', [IndexController::class, 'indexPedido'])->name('perfil.pedido');
    
        Route::get('/perfil-servicos/pedidos/{order_number}', [IndexController::class, 'indexPedidoServico'])->name('perfil.servico.pedido');
    
        Route::post('perfilSave', [PerfilController::class, 'perfilSave']);
        Route::post('senhaSave', [PerfilController::class, 'senhaSave']);
        Route::post('enderecoSave', [PerfilController::class, 'enderecoSave']);
        Route::get('apagarEndereco/{id}', [PerfilController::class, 'apagarEndereco']);
    
        // Chekout dos produtos
        Route::get('checkout-plano', [CheckoutController::class, 'checkoutSessionPlan'])->name('checkoutSessionPlan');
        Route::post('checkout-plano', [CheckoutController::class, 'checkoutSessionPlanPost'])->name('checkoutSessionPlan.post');
    
        // Chekout dos produtos
        Route::get('escolha-de-frete', [CheckoutController::class, 'indexModalidade'])->name('checkout.modalidade');
        Route::post('createSessionModalidade', [CheckoutController::class, 'createSessionModalidade'])->name('checkout.createSessionModalidade');
        Route::get('checkout', [CheckoutController::class, 'checkout'])->name('checkout');
        Route::post('checkout', [CheckoutController::class, 'finalizar'])->name('checkout.post');
    
        // Checkout do turismo rural
        Route::get('checkout-service', [CheckoutServiceController::class, 'checkout'])->name('checkout.service');
        Route::post('checkout-service', [CheckoutServiceController::class, 'finalizar'])->name('checkout.service.post');
    
        Route::post('finalizarOrder', [IndexController::class, 'finalizarOrder'])->name('finalizarOrder');
    
        Route::post('perfil/solicita-cancelamento-pedido', [PerfilController::class, 'solicitaCancelamentoPedido'])->name('perfil.solicitaCancelamentoPedido');
        Route::post('perfil/solicita-cancelamento-pedido-servico', [PerfilController::class, 'solicitaCancelamentoPedidoService'])->name('perfil.solicitaCancelamentoPedidoServico');
        Route::post('perfil/solicita-cancelamento-assinatura', [PerfilController::class, 'solicitaCancelamentoAssinatura'])->name('perfil.solicitaCancelamentoAssinatura');
    
        Route::get('perfil/avaliar-produto', [IndexController::class, 'rateProduct'])->name('perfil.rateProduct');
        Route::post('perfil/avaliar-produto', [PerfilController::class, 'rateProduct'])->name('perfil.rateProduct');
        Route::get('perfil/avaliar-servico', [IndexController::class, 'rateService'])->name('perfil.rateService');
        Route::post('perfil/avaliar-servico', [PerfilController::class, 'rateService'])->name('perfil.rateService');
    
        Route::get('perfil/assinatura', [IndexController::class, 'indexAssinatura'])->name('perfil.assinatura');
        Route::get('/perfil/assinatura/{id}', [IndexController::class, 'indexAssinaturaDetalhe'])->name('perfil.assinaturaDetalhe');
    
        Route::get('perfil/env-code-delete', [PerfilController::class, 'envCodeDelete'])->name('perfil.envCodeDelete');
        Route::post('perfil/confirm-code-delete', [PerfilController::class, 'confirmCodeDelete'])->name('perfil.confirmCodeDelete');
    
        Route::get('/favoritos', [IndexController::class, 'indexFavorites'])->name('favorites');
        Route::get('/servicos-favoritos', [IndexServiceController::class, 'indexFavorites'])->name('favorites.service');
    
        Route::get('/rastreio/{codigo}', [MelhorEnvioController::class, 'rastreio'])->name('rastreio');
    
        //Criação Afiliado
        Route::post('perfil/afiliado', [PerfilController::class, 'salvarAfiliado'])->name('perfil.salvarAfiliado');
        Route::post('perfil/excluir-afiliado', [PerfilController::class, 'excluirAfiliado'])->name('perfil.excluirAfiliado');
        Route::post('perfil/salvar-link-afiliado', [PerfilController::class, 'salvarLinkAfiliado'])->name('perfil.salvarLinkAfiliado');
    });
    
    Route::middleware(['auth.seller'])->name('seller.')->prefix('/vendedor')->group(function () {
        Route::get('/', [PainelSellerController::class, 'indexDashboard'])->name('dashboard');
        Route::get('/perfil', [PainelSellerController::class, 'indexPerfil'])->name('perfil');
        Route::get('/enderecos', [PainelSellerController::class, 'indexEnderecos'])->name('endereco');
        Route::get('/loja', [PainelSellerController::class, 'indexLoja'])->name('loja');
        Route::get('/contaParaVenda', [PainelSellerController::class, 'contaParaVenda'])->name('contaParaVenda');
    
        Route::get('/rastreio/{codigo}', [MelhorEnvioController::class, 'rastreio'])->name('rastreio');
    
        Route::get('env-code-delete', [PainelSellerController::class, 'envCodeDelete'])->name('envCodeDelete');
        Route::post('confirm-code-delete', [PainelSellerController::class, 'confirmCodeDelete'])->name('confirmCodeDelete');
    
        Route::prefix('/comercial')->group(function () {
            Route::get('/pedidos', [PedidoSellerController::class, 'indexPedido'])->name('pedidos');
            Route::post('/alterarStatusOrder', [PedidoSellerController::class, 'alterarStatusOrder'])->name('atualizar_status_order');
            Route::get('/pedidos-servicos', [PedidoSellerController::class, 'indexPedidoServico'])->name('pedidos.servicos');
            Route::post('/alterarStatusOrderService', [PedidoSellerController::class, 'alterarStatusOrderService'])->name('atualizar_status_order_service');
    
            Route::get('/ver_pedido/{order_number}', [PedidoSellerController::class, 'indexVerPedido'])->name('ver_pedido');
            Route::get('/ver_pedido_servico/{order_number}', [PedidoSellerController::class, 'indexVerPedidoServico'])->name('ver_pedido.servico');
    
            Route::get('/assinaturas', [PedidoSellerController::class, 'indexAssinatura'])->name('assinaturas');
            Route::get('/assinaturas/{id}', [PedidoSellerController::class, 'indexAssinaturaDetalhe'])->name('assinaturaDetalhe');
    
            Route::post('/codigoAdd', [PedidoSellerController::class, 'codigoAdd'])->name('codigo_add');
            Route::post('/codigoRemove', [PedidoSellerController::class, 'codigoRemove'])->name('codigo_remove');
    
            Route::post('/verificar-solicitacao-cancelamento', [PedidoController::class, 'verificarSolicitacaoCancelamento'])->name('verificarSolicitacaoCancelamento');
            Route::post('/confirmar-solicitacao-cancelamento', [PedidoController::class, 'confirmarSolicitacaoCancelamento'])->name('confirmarSolicitacaoCancelamento');
    
            Route::post('/verificar-solicitacao-cancelamento-servico', [PedidoController::class, 'verificarSolicitacaoCancelamentoServico'])->name('verificarSolicitacaoCancelamentoServico');
            Route::post('/confirmar-solicitacao-cancelamento-servico', [PedidoController::class, 'confirmarSolicitacaoCancelamentoServico'])->name('confirmarSolicitacaoCancelamentoServico');
    
            Route::post('/pedido-anexar-fiscal', [PedidoSellerController::class, 'orderAnexarFiscal'])->name('orderAnexarFiscal');
            Route::post('/pedido-desanexar-fiscal', [PedidoSellerController::class, 'orderDesanexarFiscal'])->name('orderDesanexarFiscal');
    
            Route::post('/pedido-servico-anexar-fiscal', [PedidoSellerController::class, 'orderServiceAnexarFiscal'])->name('orderServiceAnexarFiscal');
            Route::post('/pedido-servico-desanexar-fiscal', [PedidoSellerController::class, 'orderServiceDesanexarFiscal'])->name('orderServiceDesanexarFiscal');
    
            Route::get('/imprimir-pedido/{order_number}', [PedidoSellerController::class, 'impressaoPedido'])->name('impressaoPedido');
            Route::get('/imprimir-pedido-servico/{order_number}', [PedidoSellerController::class, 'impressaoPedidoServico'])->name('impressaoPedidoServico');
    
            Route::any("/reserva-manual/{function_slug?}", [ServicoController::class, 'reservaManual'])->name('reservaManual');
        });
    
        // entregas
        Route::prefix('/entregas')->group(function(){
            Route::get('/transporte-proprio', [OwnTransportController::class, 'ownTransport'])->name('ownTransport');
            Route::post('/transporte-proprio/store', [OwnTransportController::class, 'novoTransporte'])->name('novoTransporte');
            Route::post('/transporte-proprio/update', [OwnTransportController::class, 'editarTransporte'])->name('editarTransporte');
            Route::post('/transporte-proprio/destroy', [OwnTransportController::class, 'excluirTransporte'])->name('excluirTransporte');
    
            Route::get('/melhor_envio', [MelhorEnvioController::class, 'indexApi'])->name('melhor_envio');
            Route::post('/melhor_envio', [MelhorEnvioController::class, 'storeApi'])->name('melhor_envio');
            Route::post('/melhor_envio/purchase', [MelhorEnvioController::class, 'storePurchaseApi'])->name('melhor_envio.purchase');
            Route::get('/melhor_envio/dados/{id}', [MelhorEnvioController::class, 'dataApi'])->name('melhor_envio.data');
    
            Route::get('/melhor_envio/etiqueta/{id}', [MelhorEnvioController::class, 'etiquetaApi'])->name('melhor_envio.etiqueta');
            Route::get('/melhor_envio/etiquetaImp/{id}', [MelhorEnvioController::class, 'etiquetaImpApi'])->name('melhor_envio.etiqueta.imp');

            // Controle de cadastros de retiradas de locais
            Route::any('/{data_type?}', [LocalRetiradaController::class, 'dataConfig'])->name('localDeRetirada');
        });


        // Controlle da conta de recebimento
        Route::any('/conta-de-recebimento/{data_type}', [StoreController::class, 'dataConfig'])->name('contaRecebimento');

        // Store seller
        Route::post('/atualizarLoja', [StoreController::class, 'atualizarLoja'])->name('atualizarLoja');
        Route::post('/atualizarLojaLogoBanner', [StoreController::class, 'atualizarLojaLogoBanner'])->name('atualizarLojaLogoBanner');
    
        // Alteração de dados do perfil
        Route::post('/nomePerfil', [AuthSellerController::class, 'nomePerfil'])->name('nomePerfil');
        Route::post('/emailPerfil', [AuthSellerController::class, 'emailPerfil'])->name('emailPerfil');
        Route::post('/cnpjCpfPerfil', [AuthSellerController::class, 'cnpjCpfPerfil'])->name('cnpjCpfPerfil');
        Route::post('/phonePerfil', [AuthSellerController::class, 'phonePerfil'])->name('phonePerfil');
        Route::post('/senhaPerfil', [AuthSellerController::class, 'senhaPerfil'])->name('senhaPerfil');
        // Endereço pessoal
        Route::post('/novoEndereco', [AuthSellerController::class, 'novoEndereco'])->name('novoEndereco');
        Route::post('/atualizarEndereco', [AuthSellerController::class, 'atualizarEndereco'])->name('atualizarEndereco');
        Route::post('/excluirEndereco', [AuthSellerController::class, 'excluirEndereco'])->name('excluirEndereco');
    
        Route::prefix('/cadastro')->group(function () {
            // produtos
            Route::any('produto/{function_slug?}', [\App\Http\Controllers\ProdutoController::class, 'index'])->name('produto');
    
            // Serviços
            Route::any('servico/{function_slug?}', [\App\Http\Controllers\ServicoController::class, 'index'])->name('servico');
    
            // Cupons
            Route::get('cupons', [CouponController::class, 'index'])->name('coupon.index');
            Route::post('cupons/ativo', [CouponController::class, 'ativo'])->name('coupon.ativo');
            Route::post('cupons/store', [CouponController::class, 'store'])->name('coupon.store');
            Route::get('cupons/show/{id}', [CouponController::class, 'show'])->name('coupon.show');
            Route::post('cupons/edit', [CouponController::class, 'edit'])->name('coupon.edit');
            Route::post('cupons/destroy', [CouponController::class, 'destroy'])->name('coupon.destroy');
    
            Route::get('/atributos/{id?}', [PainelController::class, 'indexAtributo']);
            Route::post('/novo_atributo', [AttributeController::class, 'novoAtributo'])->name('vendedor.novoAtributo');
            Route::post('/atualizar_atributo', [AttributeController::class, 'atualizarAtributo'])->name('vendedor.atualizarAtributo');
            Route::post('/apagar_atributo', [AttributeController::class, 'apagarAtributo'])->name('vendedor.apagarAtributo');
            Route::get('/attributos/vendedor/{seller}', [AttributeController::class, 'todosAtributos'])->name('attributos.lista.por.vendedor');
        });
    
    });
    
    Route::middleware(['auth.admin'])->prefix('/admin')->group(function () {
        Route::get('/', [PainelController::class, 'indexDashboard'])->name('dashboard');
        Route::get('/perfil', [PainelController::class, 'indexPerfil']);
        Route::get('/contas', [PainelController::class, 'indexContas']);
    
        //Criação Afiliado
        Route::post('perfil/afiliado', [PerfilController::class, 'salvarAfiliado'])->name('admin.salvarAfiliado');
        Route::post('perfil/excluir-afiliado', [PerfilController::class, 'excluirAfiliado'])->name('admin.excluirAfiliado');
    
        // Alteraçãp de dados do perfil
        Route::post('/nomePerfil', [AuthController::class, 'nomePerfil'])->name('nomePerfil');
        Route::post('/emailPerfil', [AuthController::class, 'emailPerfil'])->name('emailPerfil');
        Route::post('/senhaPerfil', [AuthController::class, 'senhaPerfil'])->name('senhaPerfil');
        // Alteração de dados das contas
        Route::post('/novaConta', [AuthController::class, 'novaConta'])->name('novaConta');
        Route::post('/atualizarConta', [AuthController::class, 'atualizarConta'])->name('atualizarConta');
        Route::post('/excluirConta', [AuthController::class, 'excluirConta'])->name('excluirConta');
        Route::post('/atualizarSenha', [AuthController::class, 'atualizarSenha'])->name('atualizarSenha');
    
        Route::get('/rastreio/{codigo}', [MelhorEnvioController::class, 'rastreio'])->name('admin.rastreio');
    
        Route::prefix('/comercial')->group(function () {
            Route::get('/pedidos', [PedidoController::class, 'indexPedido'])->name('pedidos');
            Route::post('/alterarStatusOrder', [PedidoController::class, 'alterarStatusOrder'])->name('admin.atualizar_status_order');
            Route::get('/pedidos-servicos', [PedidoController::class, 'indexPedidoServico'])->name('pedidos.servicos');
            Route::post('/alterarStatusOrderService', [PedidoController::class, 'alterarStatusOrderService'])->name('admin.atualizar_status_order_service');
    
            Route::get('/ver_pedido/{order_number}', [PedidoController::class, 'indexVerPedido'])->name('ver_pedido');
            Route::get('/ver_pedido_servico/{order_number}', [PedidoController::class, 'indexVerPedidoServico'])->name('ver_pedido.servico');
    
            Route::get('/assinaturas', [PedidoController::class, 'indexAssinatura'])->name('assinaturas');
            Route::get('/assinaturas/{id}', [PedidoController::class, 'indexAssinaturaDetalhe'])->name('assinaturaDetalhe');
    
            Route::post('/notificar-vendedor', [PedidoController::class, 'notificarVendedor'])->name('admin.notificarVendedor');
            Route::post('/notificar-vendedor-service', [PedidoController::class, 'notificarVendedorService'])->name('admin.notificarVendedor.pedido-servico');
    
            Route::post('/verificar-solicitacao-cancelamento', [PedidoController::class, 'verificarSolicitacaoCancelamento'])->name('admin.verificarSolicitacaoCancelamento');
            Route::post('/confirmar-solicitacao-cancelamento', [PedidoController::class, 'confirmarSolicitacaoCancelamento'])->name('admin.confirmarSolicitacaoCancelamento');
    
            Route::post('/verificar-solicitacao-cancelamento-servico', [PedidoController::class, 'verificarSolicitacaoCancelamentoServico'])->name('admin.verificarSolicitacaoCancelamentoServico');
            Route::post('/confirmar-solicitacao-cancelamento-servico', [PedidoController::class, 'confirmarSolicitacaoCancelamentoServico'])->name('admin.confirmarSolicitacaoCancelamentoServico');
    
            Route::post('/verificar-solicitacao-cancelamento-plano', [PedidoController::class, 'verificarSolicitacaoCancelamentoPlan'])->name('admin.verificarSolicitacaoCancelamentoPlan');
            Route::post('/confirmar-solicitacao-cancelamento-plano', [PedidoController::class, 'confirmarSolicitacaoCancelamentoPlan'])->name('admin.confirmarSolicitacaoCancelamentoPlan');
    
            Route::post('/pedido-anexar-fiscal', [PedidoController::class, 'orderAnexarFiscal'])->name('admin.orderAnexarFiscal');
            Route::post('/pedido-desanexar-fiscal', [PedidoController::class, 'orderDesanexarFiscal'])->name('admin.orderDesanexarFiscal');
    
            Route::post('/pedido-servico-anexar-fiscal', [PedidoController::class, 'orderServiceAnexarFiscal'])->name('admin.orderServiceAnexarFiscal');
            Route::post('/pedido-servico-desanexar-fiscal', [PedidoController::class, 'orderServiceDesanexarFiscal'])->name('admin.orderServiceDesanexarFiscal');
    
            Route::post('/codigoAdd', [PedidoController::class, 'codigoAdd'])->name('admin.codigo_add');
            Route::post('/codigoRemove', [PedidoController::class, 'codigoRemove'])->name('admin.codigo_remove');
    
            Route::get('/imprimir-pedido/{order_number}', [PedidoController::class, 'impressaoPedido'])->name('impressaoPedido');
            Route::get('/imprimir-pedido-servico/{order_number}', [PedidoController::class, 'impressaoPedidoServico'])->name('impressaoPedidoServico');
    
            Route::any("/reserva-manual/{function_slug?}", [ServicoController::class, 'reservaManual'])->name('reservaManual');
        });
    
        Route::prefix('/cadastro')->group(function () {
            // produtos
            Route::any('produto/{function_slug?}', [\App\Http\Controllers\ProdutoController::class, 'index'])->name('produto');
    
            // Serviços
            Route::any('servico/{function_slug?}', [\App\Http\Controllers\ServicoController::class, 'index'])->name('servico');
    
            // Afiliados
            Route::get('comissao-afiliados', [AfiliadoController::class, 'index'])->name('admin.afiliado.index');
            Route::post('/nova-comissao', [AfiliadoController::class, 'novaComissao'])->name('novaComissao');
            Route::post('/atualizar-comissao', [AfiliadoController::class, 'atualizarComissao'])->name('atualizarComissao');
            Route::post('/excluir-comissao', [AfiliadoController::class, 'excluirComissao'])->name('excluirComissao');
    
            // Cupons
            Route::get('cupons', [CouponController::class, 'index'])->name('admin.coupon.index');
            Route::post('cupons/ativo', [CouponController::class, 'ativo'])->name('admin.coupon.ativo');
            Route::post('cupons/store', [CouponController::class, 'store'])->name('admin.coupon.store');
            Route::get('cupons/show/{id}', [CouponController::class, 'show'])->name('admin.coupon.show');
            Route::post('cupons/edit', [CouponController::class, 'edit'])->name('admin.coupon.edit');
            Route::post('cupons/destroy', [CouponController::class, 'destroy'])->name('admin.coupon.destroy');
    
            // Categorias
            Route::get('/categoria_menu/{type?}/{id?}', [PainelController::class, 'indexCategoria']);
            Route::post('/nova_categoria', [CategoryController::class, 'novaCategoria'])->name('novaCategoria');
            Route::post('/atualizar_categoria', [CategoryController::class, 'atualizarCategoria'])->name('atualizarCategoria');
            Route::post('/pesquisa_categoria', [CategoryController::class, 'pesquisaCategoria']);
            Route::post('/pesquisa_categoria_produto', [CategoryController::class, 'pesquisaCategoriaProduto']);
            Route::post('/excluir_categoria', [CategoryController::class, 'excluirCategoria']);
    
            Route::get('/atributos/{id?}', [PainelController::class, 'indexAtributo']);
            Route::post('/novo_atributo', [AttributeController::class, 'novoAtributo'])->name('novoAtributo');
            Route::post('/atualizar_atributo', [AttributeController::class, 'atualizarAtributo'])->name('atualizarAtributo');
            Route::post('/apagar_atributo', [AttributeController::class, 'apagarAtributo'])->name('apagarAtributo');
            Route::get('/attributos/vendedor/{seller}', [AttributeController::class, 'todosAtributos'])->name('attributos.lista.por.vendedor');
    
            Route::get('/bairros', [PainelController::class, 'bairros']);
            Route::post('/bairros/store', [OwnTransportController::class, 'bairrosStore'])->name('admin.bairro.store');
            Route::post('/bairros/edit', [OwnTransportController::class, 'bairrosEdit'])->name('admin.bairro.edit');
            Route::post('/bairros/destroy', [OwnTransportController::class, 'bairrosDestroy'])->name('admin.bairro.destroy');

            // Controle de cadastros de retiradas de locais
            Route::any('/{data_type?}', [LocalidadeRetiradaController::class, 'dataConfig'])->name('admin.localidadeRetirada');
        });
    
        Route::prefix('/cliente')->group(function () {
            Route::get('/clientes', [PainelController::class, 'indexClientes']);
            Route::get('/vendedores', [PainelController::class, 'indexVendedores']);
            Route::get('/afiliados', [PainelController::class, 'indexAfiliados']);
            Route::get('/enderecos-cliente/{id}', [PainelController::class, 'enderecosCliente']);
            Route::get('/loja-vendedor/{id}', [PainelController::class, 'lojaVendedor']);
    
            Route::post('/status-vendedor', [AccountController::class, 'updateStatusVendedor'])->name('admin.updateStatusVendedor');
    
            Route::get('/afiliados/pedido/{id}', [PainelController::class, 'pedidosAfiliados'])->name('pedidos.afiliados');
    
            // Vendedor
            Route::post('/novoVendedor', [AccountController::class, 'novoVendedor'])->name('novoVendedor');
            Route::post('/alterar-responsavel-vendedor', [AccountController::class, 'vendedorResponsavel'])->name('admin.atualizar_responsavel_vendedor');
            Route::post('/atualizarVendedor', [AccountController::class, 'atualizarVendedor'])->name('atualizarVendedor');
            Route::post('/excluirVendedor', [AccountController::class, 'excluirVendedor'])->name('excluirVendedor');
            Route::post('/atualizarSenhaVendedor', [AccountController::class, 'atualizarSenhaVendedor'])->name('atualizarSenhaVendedor');
            // -----
            Route::post('/atualizarSEOVendedor', [AccountController::class, 'atualizarSEOVendedor'])->name('atualizarSEOVendedor');
            Route::post('/atualizarIMGSVendedor', [AccountController::class, 'atualizarIMGSVendedor'])->name('atualizarIMGSVendedor');
    
            // Cliente
            Route::post('/novoCliente', [AccountController::class, 'novoCliente'])->name('novoCliente');
            Route::post('/atualizarCliente', [AccountController::class, 'atualizarCliente'])->name('atualizarCliente');
            Route::post('/excluirCliente', [AccountController::class, 'excluirCliente'])->name('excluirCliente');
            Route::post('/atualizarSenhaCliente', [AccountController::class, 'atualizarSenhaCliente'])->name('atualizarSenhaCliente');
            // -----
            Route::post('/novoEnderecoCliente', [AccountController::class, 'novoEnderecoCliente'])->name('novoEnderecoCliente');
            Route::post('/atualizarEnderecoCliente', [AccountController::class, 'atualizarEnderecoCliente'])->name('atualizarEnderecoCliente');
            Route::post('/excluirEnderecoCliente', [AccountController::class, 'excluirEnderecoCliente'])->name('excluirEnderecoCliente');
        });
    
        Route::prefix('/outros')->group(function (){
            Route::get('/aprovar-avaliacao-produto/{status?}', [PainelController::class, 'rateProduct'])->name('admin.rateProduct.apro');
            Route::post('/aprovar-avaliacao-produto', [PainelController::class, 'rateProductSend'])->name('admin.rateProduct.send');
    
            Route::get('/aprovar-avaliacao-servico', [PainelController::class, 'rateService'])->name('admin.rateService.apro');
            Route::post('/aprovar-avaliacao-servico', [PainelController::class, 'rateServiceSend'])->name('admin.rateService.send');
    
            Route::get('/contatos', [PainelController::class, 'contacts'])->name('admin.contatcs');
            Route::post('/alterar-responsavel-contato', [PainelController::class, 'contactsResponsavel'])->name('admin.atualizar_responsavel_contact');
            Route::post('/alterar-status-contato', [PainelController::class, 'contactsStatus'])->name('admin.atualizar_status_contact');
            Route::post('/alterar-remover-contato', [PainelController::class, 'contactsRemove'])->name('admin.remove_contact');
    
            Route::get('/configurar-seo', [PainelController::class, 'seoConfig'])->name('admin.seo_config');
            Route::post('/configurar-seo/register', [PainelController::class, 'seoRegister'])->name('admin.seo_config.register');
            Route::get('/configurar-seo/busca-info', [PainelController::class, 'seoBucaInfo'])->name('admin.seo_config.busca_info');
    
            Route::get('/banners', [BannerController::class, 'indexBanner'])->name('admin.banner');
            Route::post('/banners-store', [BannerController::class, 'bannerStore'])->name('admin.banner.store');
    
            Route::get('/evento-home', [EventHomeController::class, 'indexEventoHome'])->name('admin.eventoHome');
            Route::post('/evento-home-store', [EventHomeController::class, 'eventoHomeStore'])->name('admin.eventoHome.store');
    
            Route::get('/evento-home-rural', [EventHomeController::class, 'indexEventoHomeRural'])->name('admin.eventoHomeRural');
            Route::post('/evento-home-rural-store', [EventHomeController::class, 'eventoHomeRuralStore'])->name('admin.eventoHomeRural.store');
    
            Route::post('/regras-parcelamento', [PainelController::class, 'parcelamentoRegras'])->name('admin.parcelamentoRegras');
    
            Route::get('/paginas', [PagesController::class, 'indexPages'])->name('admin.pages');
            Route::get('/pagina-nova', [PagesController::class, 'indexPagenew'])->name('admin.page.new');
            Route::post('/pagina-salvar', [PagesController::class, 'storePage'])->name('admin.page.store');
            Route::get('/pagina-editar/{id}', [PagesController::class, 'indexPageEdit'])->name('admin.page.edit');
            Route::post('/pagina-atualizar', [PagesController::class, 'updatePage'])->name('admin.page.update');
            Route::post('/pagina-apagar', [PagesController::class, 'destroyPage'])->name('admin.page.destroy');
    
            Route::get('/faturamento', [PainelController::class, 'indexFaturamento'])->name('admin.faturamento');
            Route::post('/get-faturamento', [PainelController::class, 'getFaturamento'])->name('admin.getFaturamento');
    
            Route::get('/newsletter', [PainelController::class, 'indexNewsletter'])->name('admin.newsletter');
            Route::post('/cancelar-newsletter', [PainelController::class, 'cancelNewsletter'])->name('admin.cancelNewsletter');
    
            Route::get('/clientes-esperando-aviso', [PainelController::class, 'indexClienteAviseMeQD'])->name('admin.clienteAviseMeQD');
            Route::post('/cancelar-clientes-esperando-aviso', [PainelController::class, 'cancelClienteAviseMeQD'])->name('admin.clienteAviseMeQD');
    
            Route::post('/tabelaGeral', [PainelController::class, 'tabelaGeral'])->name('admin.tabelaGeral');
    
            Route::get('/carrinhos-de-clientes', [PainelController::class, 'client_cart'])->name('admin.client.cart');
            Route::get('/limpar-carrinho-de-cliente/{user_id}', [PainelController::class, 'client_cart_clean'])->name('admin.client.cart.remove');
            Route::get('/remover-item-do-carrinho-de-cliente/{item_id}', [PainelController::class, 'client_cart_remove_item'])->name('admin.client.cart.removeitem');
    
            Route::any('/logs-do-sistema-geral', [PainelController::class, 'logs_view'])->name('admin.logs');
            Route::get('/logs-do-sistema-geral/download', [PainelController::class, 'logs_download'])->name('admin.logs.download');
        });
    
        Route::get('admin-logar/{id}/{user}', function($id, $user){
            switch($user){
                case 'client':
                    \Auth::guard('admin')->logout();
                    \Auth::guard('web')->loginUsingId($id);
                    return redirect()->route('perfil');
                    break;
                case 'seller':
                    \Auth::guard('admin')->logout();
                    \Auth::guard('seller')->loginUsingId($id);
                    return redirect()->route('seller.dashboard');
                    break;
            }
        })->name('admin-logar');
    });
    
    // Auth do Admin
    Route::get('/admin/login', [AuthController::class, 'indexLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
    // Route::get('/admin/register', [AuthController::class, 'indexRegister'])->name('admin.register');
    // Route::post('/admin/register', [AuthController::class, 'register'])->name('admin.register.post');
    Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    // Auth do Seller
    Route::get('/vendedor/login', [AuthSellerController::class, 'indexLogin'])->name('seller.login');
    Route::post('/vendedor/login', [AuthSellerController::class, 'login'])->name('seller.login.post');
    Route::get('/vendedor/register', [AuthSellerController::class, 'indexRegister'])->name('seller.register');
    Route::post('/vendedor/register', [AuthSellerController::class, 'register'])->name('seller.register.post');
    Route::post('/vendedor/logout', [AuthSellerController::class, 'logout'])->name('seller.logout');
    Route::get('/vendedor/esqueceu-senha', [ResetuserPasswordController::class, 'forgotPassword'])->name('seller.forgot.password');
    Route::post('/vendedor/esqueceu-senha', [ResetuserPasswordController::class, 'forgotPasswordSend'])->name('seller.forgot.password');
    Route::get('/vendedor/redefinir-senha/{token?}', [ResetuserPasswordController::class, 'resetPassword'])->name('seller.reset.password');
    Route::post('/vendedor/redefinir-senha/{token?}', [ResetuserPasswordController::class, 'resetPasswordSend'])->name('seller.reset.password');
    
    Route::post('aplicar-cupom', [CouponController::class, 'aplicarCupom'])->name('aplicarCupom');
    Route::post('aplicar-cupom-service', [CouponController::class, 'aplicarCupomService'])->name('aplicarCupomService');
    
    Route::prefix('/asaas')->group(function () {
        // Route::post('/criarRecipients/{seller}', [\App\Http\Controllers\PagarMe\VendedorController::class, 'criarRecipients'])->name('pagarme.criarRecipients');
    
        Route::post('postback/pagamento', [CheckoutController::class, 'postback'])->name('pagarme.postback.pagamento');
        // Route::post('postback/pagamento-servico', [CheckoutController::class, 'postback'])->name('pagarme.postback.pagamento-servico');
    });
    Route::get('callback/melhor-envio', [MelhorEnvioController::class, 'callbackCode'])->name('callbackCode');
    
    // Route::post("/servico/calendar/{id}", [ServicoController::class, 'servicoCalendar'])->name('servico.calendar');
    // Route::post("/servico/reservar", [ServicoController::class, 'servicoReservar'])->name('servico.reservar');
    
    Route::post("/servico/variation/component", [ServicoController::class, 'servicoVariationComponent'])->name('servico.variation.component');
    Route::get("/service/variation/component/update/{id}", [ServicoController::class, 'servicoUpdateVariationComponent'])->name('servico.variation.update.component');
    Route::post("/servico/date-card/component", [ServicoController::class, 'servicoDateCardComponent'])->name('servico.datecard.component');
    
    Route::post("/produto/variations/index", [\App\Http\Controllers\VariationsProdutoController::class, 'index'])->name('variations.index');
    Route::get("/produto/variations/update/{produto}", [\App\Http\Controllers\VariationsProdutoController::class, 'update'])->name('variations.update');
    Route::post('/produto-attr/new', [AttributeController::class, 'attrnew']);
    Route::post('/geral/session-frete-cart', [IndexController::class, 'sessionFreteCart'])->name('sessionFreteCart');
    
    Route::get('/geral/tables-ajax', [PainelController::class, 'allTables'])->name('allTables');
    Route::post('/geral/busca-attrs', [PainelController::class, 'buscaAttrs'])->name('buscaAttrs');
    Route::post('/geral/busca-attrs-var', [PainelController::class, 'buscaAttrsVar'])->name('buscaAttrsVar');
    
    Route::post('/geral/busca-attrs-variations', [IndexController::class, 'buscaAttrVariations'])->name('buscaAttrVariations');
    Route::post('/geral/select-attrs-variations', [IndexController::class, 'selectAttrsVariations'])->name('selectAttrsVariations');
    Route::post('/geral/select-attrs-service-variations', [IndexServiceController::class, 'selectAttrsVariations'])->name('selectAttrsVariations');
    
    // Route::post('/getDistance-map', [IndexServiceController::class, 'getDistanceMap'])->name('getDistanceMap');
    
    Route::post('buscaAddressSeller', [PainelSellerController::class, 'buscaAddressSeller']);
    Route::post('getInfoDash', function (Request $request) {
        ini_set('max_execution_time', 10000);
        ini_set('memory_limit','32192M');
        return response()->json(getInfoDash($request));
    });
    
    Route::post('singleService', function(Request $request){
        $services = App\Models\Service::with(['images', 'variations', 'categories.category'])->whereIn('id',$request->services_id)->inRandomOrder()->limit(4)->get()->map(function($service) use($request){
            $class = 'col-md-3';
            $service_gtag_type = $request->service_gtag_type ?? null;
            return view('components.singleService', get_defined_vars())->render();
        })->join('');
        return response()->json($services);
    });
    
    Route::get('/offline', function(){return view('offline');});
    
    Route::get('/getToken', function () {
        $token = csrf_token();
        // \Log::info(['verificando o token' => $token]);
        return response()->json($token);
    })->name('getToken');
});