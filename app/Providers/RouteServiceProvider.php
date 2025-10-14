<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    //public const HOME = '/'; // <-- ALTERADO para a sua página inicial (planos)
    // ... (restante do código)
    // Localize e substitua ou adicione este método.

/**
 * Defina o caminho para o qual os usuários devem ser redirecionados após a autenticação.
 *
 * @return string
 */
public static function redirectTo(): string
{
    // Verifica se existe um 'plan_to_acquire' na sessão.
    if (session()->has('plan_to_acquire')) {
        // Se sim, redireciona para a rota que inicia o pagamento com o plano salvo.
        $planId = session('plan_to_acquire');
        session()->forget('plan_to_acquire'); // Limpa a sessão após uso
        return route('payment.show', ['plan' => $planId], false);
    }
    
    // Caso contrário, usa o caminho padrão do Dashboard.
    return '/dashboard';
}


// public const HOME = '/dashboard'; 

### O Novo Fluxo (Caso de Uso Otimizado)

/*1.  **Usuário Clica em "Assinar":** O `PlanAcquisitionController@start` é acionado.
2.  **Controller Ação:** Salva o `plan_id` na sessão e redireciona para `/login`.
3.  **Usuário Loga/Cadastra:** Jetstream autentica.
4.  **Redirecionamento:** O `RouteServiceProvider` vê o `plan_id` na sessão, redireciona o usuário para `payment.show/{planId}` e limpa a sessão.
5.  **Usuário Paga:** A sessão de checkout do Stripe é iniciada imediatamente.*/

}
