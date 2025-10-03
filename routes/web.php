<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\CalculoController;
// Rota para a página inicial que exibe os planos
Route::get('/', [HomeController::class, 'index'])->name('home');


// Rota para exibir o formulário de pagamento de um plano específico (GET)
// Chamada pela view quando o usuário clica em "Assinar"
Route::get('/payment/{plan}', [PaymentController::class, 'showPaymentForm'])->name('payment.show');

// Rota para processar o pagamento do formulário (POST)
// Chamada quando o usuário envia os dados do cartão via Stripe
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

// Rotas de Autenticação


Route::middleware(['auth'])->group(function () {
    // 1. Rota de Renovação:
    // O middleware CheckSubscription redireciona para esta rota quando expirado.
    Route::get('/renew', [ContentController::class, 'showRenewForm'])->name('renew.subscription');

    // 2. Rota Protegida (Dashboard):
    // Rota principal que requer o login E a assinatura ativa.
    Route::get('/dashboard', [ContentController::class, 'dashboard'])
        ->middleware('check.subscription')
        ->name('dashboard');

    // Rota para o formulário de cálculo de preço
      Route::post('/calcular', [CalculoController::class, 'create'])->name('calcular');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
