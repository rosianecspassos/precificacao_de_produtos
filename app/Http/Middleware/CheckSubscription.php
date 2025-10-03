<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // <-- Importação da Facade Auth
use Carbon\Carbon; // <-- Importação da biblioteca Carbon

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Garante que o usuário está logado. Se não estiver, o middleware 'auth' cuidará disso.
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        // 1. Assinatura ATIVA: Se a data de expiração estiver no futuro, permite o acesso.
        if ($user->subscription_expires_at && $user->subscription_expires_at->isFuture()) {
            return $next($request);
        }

        // --- LÓGICA DE BLOQUEIO / REDIRECIONAMENTO ---

        // 2. Evita loop de redirecionamento: Se o usuário estiver tentando acessar a própria página de renovação, permite.
        if ($request->routeIs('renew.subscription')) {
            return $next($request);
        }

        // 3. Usuário NOVO (Nunca assinou): subscription_expires_at é NULL.
        if ($user->subscription_expires_at === null) {
            // Redireciona para a página de Planos (home) para a primeira compra.
            return redirect()->route('home');
        }

        // 4. Usuário EXPIROU (Era assinante, mas a data é passada).
        // Redireciona para a página de renovação.
        return redirect()->route('renew.subscription');
    }
}
