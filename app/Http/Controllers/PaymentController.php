<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Payment;
use App\Models\User;

// IMPORTAÇÕES OBRIGATÓRIAS
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Configuração do Stripe
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Exception\CardException;

class PaymentController extends Controller
{
    /**
     * Define as chaves do Stripe globalmente no construtor.
     */
    public function __construct()
    {
        // =======================================================
        // GARANTIA: Prioriza a chave de TESTE (sk_test_...) para que 
        // cartões de teste sejam aceitos.
        // O valor deve ser a chave que você colocou em STRIPE_SECRET_TEST no seu .env.
        // =======================================================
        $secretKey = env('STRIPE_SECRET_TEST', env('STRIPE_SECRET'));
        Stripe::setApiKey($secretKey); 
    }

    /**
     * Exibe o formulário de pagamento para um plano específico.
     */
    public function showPaymentForm(Plan $plan)
    {
        // VERIFICAÇÃO: Se o usuário não estiver logado, redireciona para o login
        if (!Auth::check()) {
            Session::put('pending_plan_id', $plan->id);
            return redirect()->route('login');
        }

        $user = Auth::user(); 

        return view('payment.show', [
            'plan' => $plan,
            'user' => $user,
        ]);
    }

    /**
     * Processa o pagamento via Stripe (usando o token gerado pelo Elements).
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::find($request->plan_id);
        
        /** @var \App\Models\User $user */
        $user = Auth::user(); 

        $amountInCents = round($plan->price * 100);

        try {
            // 1. Tenta criar a cobrança (Charge) usando o token
            $charge = Charge::create([
                'amount' => $amountInCents,
                'currency' => 'brl', // Usamos BRL (Reais)
                'source' => $request->stripeToken,
                'description' => 'Assinatura do Plano: ' . $plan->name . ' - Usuário ID: ' . $user->id,
                'receipt_email' => $user->email,
            ]);

            if ($charge->status !== 'succeeded') {
                 throw new \Exception('O Stripe recusou a cobrança. Status: ' . $charge->status);
            }

            // 2. Salva o registro do pagamento no banco
            $payment = new Payment();
            $payment->user_id = $user->id; 
            $payment->plan_id = $plan->id;
            $payment->stripe_id = $charge->id;
            $payment->amount = $plan->price;
            $payment->status = 'completed'; 
            $payment->payment_method = 'card';
            $payment->save();

            // 3. Atualiza a data de expiração da assinatura do usuário
            $user->subscription_expires_at = now()->addDays($plan->duration_days); 
            $user->save(); 

            // 4. Limpa a sessão de plano pendente
            Session::forget('pending_plan_id');

            // 5. Redireciona para a Dashboard com mensagem de sucesso
            Session::flash('success', 'Pagamento realizado com sucesso! Sua assinatura está ativa.');
            return redirect()->route('dashboard');

        } catch (CardException $e) {
            // Trata erros de cartão, como recusa
            Session::flash('error', 'O seu cartão foi recusado: ' . $e->getMessage());
            return redirect()->route('payment.show', $plan->id);

        } catch (\Exception $e) {
            // Trata erros de conexão, chave, ou outros erros inesperados
            Log::error('Erro Stripe ao processar pagamento: ' . $e->getMessage());
            Session::flash('error', 'Ocorreu um erro inesperado no pagamento. Por favor, tente novamente mais tarde.');
            return redirect()->route('payment.show', $plan->id);
        }
    }

    // Rotas de retorno
    public function success()
    {
        return redirect()->route('dashboard')->with('success', 'Pagamento confirmado e assinatura ativada!');
    }

    public function cancel()
    {
        return redirect()->route('home')->with('error', 'O pagamento foi cancelado ou não foi concluído.');
    }
}