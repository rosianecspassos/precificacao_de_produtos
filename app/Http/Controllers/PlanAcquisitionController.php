<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Routing\Controller;
use App\Http\Middleware\Authenticate; // IMPORTANTE: Importação necessária para Authenticate::class

class PlanAcquisitionController extends Controller
{
    /**
     * Garante que APENAS o método 'start' esteja acessível a convidados,
     * permitindo que o fluxo de assinatura comece sem login.
     */
    public function __construct()
    {
        // Referencia o middleware pela CLASSE e aplica a exceção.
        $this->middleware(Authenticate::class)->except(['start']); 
    }

    /**
     * Inicia o processo de aquisição do plano.
     * Deve ser acessível a convidados.
     * @param \App\Models\Plan $plan
     */
    public function start(Plan $plan)
    {
        // 1. Salva o plano na sessão para ser recuperado na página de pagamento.
        session(['selected_plan_id' => $plan->id]);

        // 2. Redireciona para o formulário de pagamento.
        return redirect()->route('payment.show', $plan);
    }
}