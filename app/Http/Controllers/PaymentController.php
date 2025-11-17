<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Payment;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

// Stripe
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Exception\CardException;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function __construct()
    {
        $secretKey = config('services.stripe.secret');

        if (empty($secretKey)) {
            Log::error('Chave secreta do Stripe não configurada. Verifique config/services.php e .env!');
        }

        Stripe::setApiKey($secretKey);
    }

    public function showPaymentForm(Plan $plan)
    {
        $user = Auth::user();

        return view('payment.show', [
            'plan' => $plan,
            'user' => $user,
        ]);
    }

    public function processPayment(Request $request)
    {
        $validationRules = [
            'stripeToken' => 'required',
            'plan_id' => 'required|exists:plans,id',
            'name' => Auth::check() ? 'nullable|string' : 'required|string|max:255',
        ];

        if (!Auth::check()) {
            $validationRules['email'] = 'required|email|unique:users,email';
        }

        $request->validate($validationRules);

        $plan = Plan::find($request->plan_id);

        $isGuest = !Auth::check();

        if (Auth::check()) {
            $user = Auth::user();
            $receiptEmail = $user->email;
        } else {
            $user = null;
            $receiptEmail = $request->email;
        }

        $amountInCents = round($plan->price * 100);

        try {
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

            // Salva o pagamento no banco (mesmo antes de criar usuário para histórico)
            $payment = new Payment();
            $payment->user_id = $user ? $user->id : null;
            $payment->plan_id = $plan->id;
            $payment->plan_slug = $plan->slug ?? null;
            $payment->stripe_id = $charge->id ?? null;
            $payment->amount = $plan->price;
            $payment->status = 'completed';
            $payment->payment_method = 'card';
            $payment->save();

            // Fluxo: convidado -> salvar dados na sessão e direcionar para registro final
            if ($isGuest) {
                // Dados que o RegistrationController irá usar para "atrelamento" do pagamento
              $paymentSessionData = [
    'payment_id' => $payment->id,
    'plan_id' => $plan->id,
    'plan_slug' => $plan->slug ?? null,
    'stripe_id' => $charge->id ?? null,
    'amount' => $plan->price,
    'email' => $receiptEmail,
    'plan_duration_days' => $plan->duration_days ?? 30, // ADICIONE ISTO!
];


                $request->session()->put('payment_success_data', $paymentSessionData);

                // Redireciona para o formulário de registro final
                return redirect()->route('registration.show')->with('success', 'Pagamento confirmado! Complete seu cadastro para ativar sua conta.');
            } else {
                // Usuário logado: atualiza subscription e redireciona para dashboard
      $user->subscription_expires_at = Carbon::now()->addDays($plan->duration_days ?? 30);
$user->save();



                $successMessage = 'Pagamento processado com sucesso! Sua assinatura foi atualizada.';
                return redirect()->route('dashboard')->with('success', $successMessage);
            }

        } catch (CardException $e) {
            Log::error('ERRO DE CARTÃO: ' . $e->getMessage());
            return back()->withInput($request->except('stripeToken'))->with('error', 'Cartão recusado: ' . $e->getMessage());
        } catch (ApiErrorException $e) {
            Log::error('ERRO STRIPE API: ' . $e->getMessage());
            return back()->withInput($request->except('stripeToken'))->with('error', 'Erro na comunicação com o Stripe.');
        } catch (\Exception $e) {
            Log::error('ERRO GERAL NO PROCESSAMENTO (Pós-Stripe): ' . $e->getMessage());
            return back()->withInput($request->except('stripeToken'))->with('error', 'Erro inesperado. Tente novamente.');
        }
    }

    /**
     * Página de sucesso (se você quiser exibir algo por GET)
     */
    public function success(Request $request)
    {
        // Opcional: leitura básica da sessão
        $data = $request->session()->get('payment_success_data', null);
        return view('payment.success', compact('data'));
    }

    /**
     * Página de cancelamento
     */
    public function cancel()
    {
        return view('payment.cancel');
    }

    /**
     * Processar renovação (rota: renewal.process)
     * (Exemplo simples — ajuste conforme sua lógica de renovação)
     */
    public function processRenewal(Request $request)
    {
        // Reaproveite processPayment ou um fluxo específico de renovação
        return $this->processPayment($request);
    }
}
