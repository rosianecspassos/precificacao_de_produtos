<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanAcquisitionController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\CalculoController;
use App\Http\Controllers\RegistrationController;
use App\Http\Middleware\CheckSubscription; 
use Illuminate\Support\Facades\Route;

// Rota para a página inicial que exibe os planos (Acessível a convidados)
Route::get('/', [HomeController::class, 'index'])->name('home');

// ====================================================================
// FLUXO DE AQUISIÇÃO E PAGAMENTO (NÃO AUTENTICADO)
// ====================================================================

// 1. Inicia o funil: Salva o plano na sessão (ou em cookies)
Route::get('/acquire/{plan}', [PlanAcquisitionController::class, 'start'])->name('plan.start_acquisition');


// 2. Rotas de Pagamento (Convidado)
Route::get('/payment/{plan}', [PaymentController::class, 'showPaymentForm'])->name('payment.show');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

// 3. Rotas de Resultado (O sucesso agora vai para o cadastro)
// Estas rotas ainda são acessadas por convidados que acabaram de pagar
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success'); 
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');


// ====================================================================
// FLUXO PÓS-PAGAMENTO: CADASTRO DO USUÁRIO (NÃO AUTENTICADO)
// ====================================================================

// 4. Mostra o formulário de cadastro final (após o sucesso do pagamento)
Route::get('/register/finalize', [RegistrationController::class, 'showRegistrationForm'])->name('registration.show');

// 5. Cria o usuário e faz o login (TRANSFORMA CONVIDADO EM AUTENTICADO)
Route::post('/register/finalize', [RegistrationController::class, 'registerAndFinalize'])->name('registration.finalize');


// ====================================================================
// ROTAS PROTEGIDAS POR LOGIN E/OU ASSINATURA (AUTENTICADO)
// ====================================================================

// ...
Route::middleware(['auth'])->group(function () {
    
    // ROTA DE RENOVAÇÃO (Onde o Middleware CheckSubscription pode redirecionar se a assinatura expirar)
    Route::get('/renovar', [ContentController::class, 'showRenewForm'])->name('renew.show');
    Route::post('/renovar/processar', [PaymentController::class, 'processRenewal'])->name('renewal.process');


    // GRUPO DE ROTAS PROTEGIDAS PELA ASSINATURA
    // Estas rotas exigem que o usuário esteja logado E que a assinatura seja válida.
    Route::middleware(CheckSubscription::class)->group(function () {
        
        // Rota Protegida Principal (Dashboard)
        Route::get('/dashboard', [ContentController::class, 'dashboard'])->name('dashboard');

        // Rota para o formulário de cálculo de preço
        Route::post('/calcular', [CalculoController::class, 'create'])->name('calcular');
    });
});