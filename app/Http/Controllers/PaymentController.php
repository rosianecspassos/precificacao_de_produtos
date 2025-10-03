<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Payment;
use App\Models\User; // Necessário se você usa $user->save()

// IMPORTAÇÕES OBRIGATÓRIAS
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- **ESTA** é a classe Auth correta
use Carbon\Carbon;

// Se você instalou o Stripe:
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    /**
     * Exibe o formulário de pagamento para um plano específico.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentForm(Plan $plan)
    {
        // VERIFICAÇÃO DE AUTENTICAÇÃO: Usando a Facade Auth::check()
        if (!Auth::check()) {
            // Se o usuário não estiver logado, redireciona para a rota de login
            return redirect()->route('login');
        }

        // Se estiver logado, obtém o usuário
        $user = Auth::user(); 

        return view('payment.show', [
            'plan' => $plan,
            'user' => $user,
        ]);
    }

    /**
     * Processa o pagamento via Stripe.
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::find($request->plan_id);
        
        // Tipagem para evitar erros de IDE, mas usando a chamada de runtime correta
        /** @var \App\Models\User $user */
        $user = Auth::user(); 

        // [Omitindo lógica do Stripe por ser um trecho grande, mas focando nas variáveis do erro]

        // Simulando sucesso do Stripe:

        // 5. Simulação de Salvar o pagamento
        $payment = new Payment();
        $payment->user_id = $user->id; // Aqui está o 'user' sendo usado corretamente
        // ... (restante dos campos)
        $payment->save();


        // 6. Atualiza a data de expiração da assinatura do usuário
        // Certifique-se que você rodou a migração para adicionar 'subscription_expires_at' na tabela users!
        $user->subscription_expires_at = now()->addDays($plan->duration_days); 
        $user->save(); // Aqui está o 'save' sendo usado no objeto User

        // [Omitindo redirecionamento]
    }
}
