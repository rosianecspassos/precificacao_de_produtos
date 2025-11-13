<!-- layouts/header.blade.php (Contém a Navbar Corrigida) -->

<!-- Links de Autenticação no Topo -->
<nav class="navbar navbar-light bg-light mb-4">
    <div class="container">
        <header class="d-flex justify-content-end w-100">
            @auth
                {{-- Usuário Logado: Mostra link para Dashboard/Perfil --}}
                <a href="{{ route('dashboard') }}" class="btn btn-primary me-2">
                    Acessar Dashboard
                </a>
                {{-- Adicione o botão de Logout para usuários logados --}}
                <a href="{{ route('logout') }}" class="btn btn-secondary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sair
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                {{-- Usuário Não Logado: --}}
                
                {{-- 1. CORREÇÃO FINAL PARA O ERRO DE VARIÁVEL INDEFINIDA: 
                   Este link só aparece se a variável $plan (singular) for enviada pelo Controller. --}}
                @if (isset($plan))
                    <a href="{{ route('plan.start_acquisition', ['plan' => $plan->slug]) }}" 
                        class="btn btn-primary me-2">
                        Assinar Agora
                    </a>
                    Adquira o Plano
                @endif

                {{-- 2. ÚNICO LINK PERMITIDO NA PÁGINA DE PLANOS (Regra de Negócio) --}}
                <a href="{{ route('login') }}" class="btn btn-success me-2">
                    Login
                </a>
                
                {{-- O link de Cadastro e 'Dashboard' foram removidos para seguir seu fluxo. --}}
            @endauth
        </header>
    </div>
</nav>