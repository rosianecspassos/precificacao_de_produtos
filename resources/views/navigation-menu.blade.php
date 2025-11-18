{{-- resources/views/navigation-menu.blade.php --}}

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Menu Superior -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- Logo -->
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-10 w-auto fill-current text-gray-600" />
                    </a>
                </div>
            </div>

            <!-- Navegação do Usuário -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                {{-- Dropdown do Usuário --}}
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Link Dashboard -->
                            <x-dropdown-link href="{{ route('dashboard') }}">
                                Dashboard
                            </x-dropdown-link>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link 
                                    href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    Sair
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    <a href="{{ route('login') }}" 
                       class="text-sm text-gray-700 underline ms-4">
                       Login
                    </a>
                @endguest
            </div>

            <!-- Botão Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">

                        <path :class="{ 'hidden': open, 'inline-flex': !open }"
                            class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path :class="{ 'hidden': !open, 'inline-flex': open }"
                            class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />

                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">

        <!-- Link Dashboard -->
        @auth
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link href="{{ route('dashboard') }}">
                    Dashboard
                </x-responsive-nav-link>
            </div>

            <!-- Dados do Usuário -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ Auth::user()->email }}
                    </div>
                </div>

                <!-- Logout Mobile -->
                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link 
                            href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Sair
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth

        @guest
            <div class="pt-4 pb-1 border-t border-gray-200">
                <x-responsive-nav-link href="{{ route('login') }}">
                    Login
                </x-responsive-nav-link>
            </div>
        @endguest
    </div>
</nav>
