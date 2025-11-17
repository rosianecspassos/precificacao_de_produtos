<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    /**
     * Exibe o formulário de cadastro após um pagamento bem-sucedido.
     */
    public function showRegistrationForm(Request $request)
    {
        // Verifica se há dados de pagamento na sessão. Caso contrário, redireciona para a home.
        if (!$request->session()->has('payment_success_data')) {
            return redirect('/')->with('error', 'Nenhuma transação de pagamento encontrada. Por favor, inicie a compra novamente.');
        }

        return view('auth.post-payment-register');
    }

    /**
     * Finaliza o cadastro do usuário, associa a assinatura e faz o login.
     */
    public function registerAndFinalize(Request $request)
    {
        // 1. Validação dos dados
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Recupera os dados da transação
        $paymentData = $request->session()->get('payment_success_data');

        if (!$paymentData) {
            return redirect('/')->with('error', 'Dados de pagamento perdidos. Por favor, reinicie a aquisição.');
        }

        // 3. Cria o novo usuário
// 3. Cria o novo usuário
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
   'subscription_expires_at' => Carbon::now()->addDays($paymentData['plan_duration_days'] ?? 30),

]);

// Se houver um registro de pagamento, associe ao usuário
if (!empty($paymentData['payment_id'])) {
    $payment = \App\Models\Payment::find($paymentData['payment_id']);
    if ($payment) {
        $payment->user_id = $user->id;
        $payment->save();
    }
}


        // 4. Limpa a sessão e faz o login
        $request->session()->forget('payment_success_data');
        Auth::login($user);

        // 5. Redireciona para o dashboard
        return redirect()->route('dashboard')->with('success', 'Bem-vindo! Sua assinatura está ativa.');
    }
}

