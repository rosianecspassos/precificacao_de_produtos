{{-- resources/views/layouts/header.blade.php --}}
<nav class="navbar navbar-light bg-light mb-4">
    <div class="container">
        <header class="d-flex justify-content-end w-100 align-items-center">
            @auth
                {{-- Usuário Logado: Mostra link para Dashboard/Perfil --}}
                <a href="{{ route('dashboard') }}" class="btn btn-primary me-2">
                    Acessar Dashboard
                </a>

                {{-- Botão de Logout --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        Sair
                    </button>
                </form>
            @else
                {{-- Usuário Não Logado: --}}
                {{-- 1) Botão "Assinar Agora" só aparece se $plan existir --}}
                @isset($plan)
                    <a href="{{ route('plan.start_acquisition', ['plan' => $plan->id]) }}" class="btn btn-primary me-2">
                        Assinar Agora
                    </a>
                @endisset

                {{-- 2) Link de Login sempre aponta para a rota de login --}}
                <a href="{{ route('login') }}" class="btn btn-success me-2">
                    Login
                </a>
            @endauth
        </header>
    </div>
</nav>
