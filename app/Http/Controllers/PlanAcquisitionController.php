<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class PlanAcquisitionController extends Controller
{
    /**
     * Salva o plano escolhido na sessão e redireciona para login/cadastro.
     * Este é o primeiro ponto do funil de aquisição.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function start(Plan $plan)
    {
        // 1. O usuário precisa estar logado para pagar. Se estiver, pula para o pagamento.
        if (Auth::check()) {
            return redirect()->route('payment.show', $plan);
        }

        // 2. Se não estiver logado, salva o ID do plano na sessão.
        // O RouteServiceProvider lerá esta sessão após o login/cadastro.
        session()->put('plan_to_acquire', $plan->id);

        // 3. Redireciona para a página de login.
        // O usuário pode então escolher Logar ou se Cadastrar.
        return redirect()->route('login');
    }
}