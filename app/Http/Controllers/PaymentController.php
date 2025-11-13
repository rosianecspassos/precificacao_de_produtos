<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Payment;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException; // Importado para capturar o erro exato do DB

// Configuração do Stripe
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Exception\CardException;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    /**
     * Define as chaves do Stripe globalmente no construtor.
     */
    public function __construct()
    {
        $secretKey = env('STRIPE_SECRET_TEST', env('STRIPE_SECRET'));
        
        if (empty($secretKey)) {
             Log::error('Chave secreta do Stripe não configurada no .env!');
        }
        
        Stripe::setApiKey($secretKey); 
    }

    /**
     * Exibe o formulário de pagamento para um plano específico.
     */
    public function showPaymentForm(Plan $plan)
    {
        $user = Auth::user();

        return view('payment.show', [
            'plan' => $plan,
            'user' => $user, 
        ]);
    }

    /**
     * Processa o pagamento via Stripe.
     * Lógica: Logado -> Dashboard | Convidado -> Register
     */
    public function processPayment(Request $request)
    {
        // 1. Validação dos dados
        $validationRules = [
            'stripeToken' => 'required',
            'plan_id' => 'required|exists:plans,id',
            'name' => Auth::check() ? 'nullable|string' : 'required|string|max:255',
        ];

        if (!Auth::check()) {
             $validationRules['email'] = 'required|email'; 
        }
        
        $request->validate($validationRules);

        $plan = Plan::find($request->plan_id);
        
        // 2. Definição do Usuário/Email
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user(); 
            $receiptEmail = $user->email;
        } else {
            $user = null;
            $receiptEmail = $request->email; 
        }

        $amountInCents = round($plan->price * 100);

        try {
            // 3. Tenta criar a cobrança (Charge)
            $charge = Charge::create([
                'amount' => $amountInCents,
                'currency' => 'brl',
                'source' => $request->stripeToken,
                'description' => 'Assinatura do Plano: ' . $plan->name . ' - Nome: ' . $request->name,
                'receipt_email' => $receiptEmail,
            ]);

            if ($charge->status !== 'succeeded') {
                 throw new \Exception('O Stripe recusou a cobrança. Status: ' . $charge->status);
            }

            // 4. PÓS-PAGAMENTO BEM-SUCEDIDO
            if ($user) {
                // FLUXO 1: USUÁRIO AUTENTICADO (Renovação/Upgrade)
                
                $payment = new Payment();
                $payment->user_id = $user->id; 
                $payment->plan_id = $plan->id;
                $payment->stripe_id = $charge->id;
                $payment->amount = $plan->price;
                $payment->status = 'completed'; 
                $payment->payment_method = 'card';
                $payment->save();

                // Lógica de atualização da assinatura com try/catch isolado para DB
                try {
                    $user->subscription_expires_at = Carbon::now()->addDays($plan->duration_days); 
                    $user->save(); 

                    Session::flash('success', 'Pagamento realizado com sucesso! Sua assinatura está ativa.');
                    return redirect()->route('dashboard');

                } catch (QueryException $dbE) {
                    // SE ESTE BLOCO FOR ACIONADO, O PROBLEMA ESTÁ NO SEU BANCO DE DADOS
                    Log::error('ERRO CRÍTICO NO DB (SUBSCRIPTION SAVE): ' . $dbE->getMessage());
                    
                    // Exibe mensagem informativa, mas registra a falha no log
                    Session::flash('error', 'Pagamento realizado, mas houve um erro ao atualizar sua assinatura no banco de dados. Por favor, contate o suporte. (Código: DB-FAIL)');
                    return redirect()->route('dashboard');
                }
                
                // O código abaixo não será executado se o try/catch for acionado
                // Session::flash('success', 'Pagamento realizado com sucesso! Sua assinatura está ativa.');
                // return redirect()->route('dashboard');

            } else {
                // FLUXO 2: USUÁRIO CONVIDADO (Primeira Compra)
                
                $paymentSuccessData = [
                    'plan_id' => $plan->id,
                    'name' => $request->name, 
                    'email' => $receiptEmail, 
                    'transaction_id' => $charge->id,
                    'price' => $plan->price,
                ];

                $request->session()->put('payment_success_data', $paymentSuccessData);

                // Redireciona para a rota de REGISTRO (cadastro)
                return redirect()->route('register')->with('status', 'Pagamento confirmado! Crie sua conta para acessar.'); 
            }

        } catch (CardException $e) {
            Session::flash('error', 'O seu cartão foi recusado: ' . $e->getMessage());
            return back()->withInput($request->except('stripeToken')); 

        } catch (ApiErrorException $e) {
            Log::error('Erro Stripe API: ' . $e->getMessage());
            Session::flash('error', 'Ocorreu um erro na comunicação com o Stripe: ' . $e->getMessage());
            return back()->withInput($request->except('stripeToken'));
            
        } catch (\Exception $e) {
            Log::error('ERRO GERAL DE PROCESSAMENTO: ' . $e->getMessage());
            Session::flash('error', 'Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.');
            return back()->withInput($request->except('stripeToken'));
        }
    }
    
    public function cancel(Request $request) 
    {
        Session::flash('error', 'A transação foi cancelada ou falhou.');
        return redirect()->route('home'); 
    }

    public function processRenewal(Request $request)
    {
        Session::flash('success', 'Renovação processada com sucesso.');
        return redirect()->route('dashboard');
    }
}