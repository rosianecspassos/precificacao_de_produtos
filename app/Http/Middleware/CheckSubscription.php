<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. O usuário precisa estar logado
        if (!$user) {
            return redirect()->route('login');
        }

        // 2. Verifica se a assinatura está expirada
        // A coluna 'subscription_expires_at' deve existir e ser do tipo timestamp/datetime (nullable)
        if ($user->subscription_expires_at === null || $user->subscription_expires_at->isPast()) {
            
            // Se a assinatura for nula (nunca pagou) OU a data for no passado (expirou)
            
            // Verifica se a rota ATUAL é a rota de renovação/compra. 
            // Se o usuário já estiver na tela de renovação, não redireciona novamente
            if ($request->routeIs('renew.show')) {
                return $next($request); 
            }

            // Redireciona para a página de renovação, impedindo o acesso à Dashboard.
            return redirect()->route('renew.show');
        }

        // 3. Assinatura válida: permite o acesso
        return $next($request);
    }
}