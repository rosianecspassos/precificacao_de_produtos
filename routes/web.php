<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanAcquisitionController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\CalculoController;
use App\Http\Middleware\CheckSubscription; // IMPORTANTE: Importamos o Middleware pela classe
use Illuminate\Support\Facades\Route;

// Rota para a página inicial que exibe os planos
Route::get('/', [HomeController::class, 'index'])->name('home');

// ====================================================================
// FLUXO DE AQUISIÇÃO (NÃO PROTEGIDO POR MIDDLEWARE DE ASSINATURA)
// ====================================================================

// 1. Inicia o funil: Salva o plano na sessão e redireciona para login/cadastro.
Route::get('/acquire/{plan}', [PlanAcquisitionController::class, 'start'])->name('plan.start_acquisition');


// 2. Rota para exibir o formulário de pagamento (Checkout)
Route::get('/payment/{plan}', [PaymentController::class, 'showPaymentForm'])->name('payment.show');

// 3. Rota para processar o pagamento do formulário (POST)
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

// 4. Rotas de Sucesso e Falha do Pagamento
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');


// ====================================================================
// ROTAS PROTEGIDAS POR LOGIN E/OU ASSINATURA
// ====================================================================

Route::middleware(['auth'])->group(function () {
    
    // ROTA DE RENOVAÇÃO (Onde o Middleware redireciona)
    // O nome 'renew.show' é o que o Middleware CheckSubscription espera.
    Route::get('/renovar', [ContentController::class, 'showRenewForm'])->name('renew.show');
    
    // Rota de processamento do pagamento de renovação (POST)
    Route::post('/renovar/processar', [PaymentController::class, 'processRenewal'])->name('renewal.process');


    // GRUPO DE ROTAS PROTEGIDAS PELA ASSINATURA
    // USANDO A CLASSE DO MIDDLEWARE DIRETAMENTE!
    Route::middleware(CheckSubscription::class)->group(function () {

        // Rota Protegida (Dashboard)
        Route::get('/dashboard', [ContentController::class, 'dashboard'])->name('dashboard');

        // Rota para o formulário de cálculo de preço
        Route::post('/calcular', [CalculoController::class, 'create'])->name('calcular');
        
        // Adicione aqui outras rotas que exigem assinatura
    });
});