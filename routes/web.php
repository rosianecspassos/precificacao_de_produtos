<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanAcquisitionController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\CalculoController;
use App\Http\Controllers\RegistrationController;
use App\Http\Middleware\CheckSubscription;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/acquire/{plan}', [PlanAcquisitionController::class, 'start'])->name('plan.start_acquisition');

Route::get('/payment/{plan}', [PaymentController::class, 'showPaymentForm'])->name('payment.show');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');

Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

Route::get('/register/finalize', [RegistrationController::class, 'showRegistrationForm'])->name('registration.show');
Route::post('/register/finalize', [RegistrationController::class, 'registerAndFinalize'])->name('registration.finalize');

Route::middleware(['auth'])->group(function () {

    Route::get('/renovar', [ContentController::class, 'showRenewForm'])->name('renew.show');
    Route::post('/renovar/processar', [PaymentController::class, 'processRenewal'])->name('renewal.process');

    Route::middleware(CheckSubscription::class)->group(function () {
        Route::get('/dashboard', [ContentController::class, 'dashboard'])->name('dashboard');
         // Rota para o formulário de cálculo de preço
        Route::post('/calcular', [CalculoController::class, 'create'])->name('calcular');
    });
});
