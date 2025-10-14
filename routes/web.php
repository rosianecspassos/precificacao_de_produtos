<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanAcquisitionController; // Novo Controller de Aquisição
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\CalculoController;

// Rota para a página inicial que exibe os planos
Route::get('/', [HomeController::class, 'index'])->name('home');

// ====================================================================
// FLUXO DE AQUISIÇÃO (NÃO PROTEGIDO POR 'auth')
// ====================================================================

// 1. Rota que inicia o funil: Salva o plano na sessão e redireciona para login/cadastro.
// Destino do botão "Assinar Agora" na Home.
Route::get('/acquire/{plan}', [PlanAcquisitionController::class, 'start'])->name('plan.start_acquisition');


// 2. Rota para exibir o formulário de pagamento (Stripe Checkout)
// AGORA CHAMA O MÉTODO CORRETO: showPaymentForm
Route::get('/payment/{plan}', [PaymentController::class, 'showPaymentForm'])->name('payment.show');

// 3. Rota para processar o pagamento do formulário (POST)
// AGORA CHAMA O MÉTODO CORRETO: processPayment
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

// 4. Rotas de Sucesso e Falha do Pagamento (Ainda precisam ser implementadas no Controller)
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');


// ====================================================================
// ROTAS PROTEGIDAS (REQUEREM LOGIN E ASSINATURA)
// ====================================================================

Route::middleware(['auth'])->group(function () {
    // Rota de Renovação:
    Route::get('/renew', [ContentController::class, 'showRenewForm'])->name('renew.subscription');

    // Rota Protegida (Dashboard):
    Route::get('/dashboard', [ContentController::class, 'dashboard'])
        ->middleware('check.subscription')
        ->name('dashboard');

    // Rota para o formulário de cálculo de preço
    Route::post('/calcular', [CalculoController::class, 'create'])->name('calcular');
});