    <!-- Links de Autenticação no Topo -->
    <header class="d-flex justify-content-end  mb-4">
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary me-2">
                Acessar Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline-secondary me-2">
                Entrar
            </a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-success">
                    Cadastre-se
                </a>
            @endif
        @endauth
    </header>
