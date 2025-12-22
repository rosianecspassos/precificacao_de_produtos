<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Stripe
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;

// Mercado Pago
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        // Mercado Pago
        MercadoPagoConfig::setAccessToken(
            config('services.mercadopago.access_token')
        );
    }

    /* =====================================================
     * FORMULÁRIO DE PAGAMENTO
     * ===================================================== */
    public function showPaymentForm(Plan $plan)
    {
        return view('payment.show', [
            'plan' => $plan,
            'user' => Auth::user(),
        ]);
    }

    /* =====================================================
     * CARTÃO DE CRÉDITO — STRIPE
     * ===================================================== */
    public function processPayment(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required',
            'plan_id' => 'required|exists:plans,id',
            'name' => 'required|string',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $user = Auth::user();

        try {
            $charge = Charge::create([
                'amount' => intval($plan->price * 100),
                'currency' => 'brl',
                'source' => $request->stripeToken,
                'description' => "Assinatura {$plan->name}",
                'receipt_email' => $user->email,
            ]);

            if ($charge->status !== 'succeeded') {
                throw new \Exception('Pagamento não aprovado');
            }

            Payment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'completed',
                'payment_method' => 'card',
                'stripe_id' => $charge->id,
            ]);

            $user->subscription_expires_at =
                Carbon::now()->addDays($plan->duration_days ?? 30);
            $user->save();

            return redirect()->route('dashboard')
                ->with('success', 'Pagamento realizado com sucesso');

        } catch (ApiErrorException $e) {
            Log::error('Stripe error', [
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Erro no pagamento com cartão');
        }
    }

    /* =====================================================
     * PIX — MERCADO PAGO (AUTOMÁTICO)
     * ===================================================== */
    public function createPix(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        // Usuário pode estar logado ou não
        $email = Auth::check()
            ? Auth::user()->email
            : 'test_user_' . uniqid() . '@email.com';

        try {
           
            
            $client = new PaymentClient();
        
            $payment = $client->create([
                "transaction_amount" => (float) $plan->price,
                "description" => "Assinatura {$plan->name}",
                "payment_method_id" => "pix",
                "payer" => [
                    "email" => $email,
                ],
            ]);

            return response()->json([
                'payment_id' => $payment->id,
                'qr_code' =>
                    $payment->point_of_interaction
                        ->transaction_data
                        ->qr_code,
                'qr_code_base64' =>
                    $payment->point_of_interaction
                        ->transaction_data
                        ->qr_code_base64,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro PIX Mercado Pago', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Erro ao gerar PIX'
            ], 500);
        }
    }

    /* =====================================================
     * CANCELAMENTO
     * ===================================================== */
    public function cancel()
    {
        return view('payment.cancel');
    }
}
