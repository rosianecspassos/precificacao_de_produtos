<!-- resources/views/payment/show.blade.php -->
@extends('templates.home')

@section('title', 'Checkout - ' . $plan->name)

@section('content')

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h1 class="text-center mb-4 text-2xl font-bold text-gray-800">Finalizar Assinatura</h1>
            
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="card shadow-lg border-0 rounded-xl">
                <div class="card-header bg-primary text-white p-4 rounded-t-xl">
                    <h2 class="card-title text-xl font-semibold">{{ $plan->name }}</h2>
                    <p class="card-subtitle lead mb-0">R$ {{ number_format($plan->price, 2, ',', '.') }}</p>
                </div>
                
                <div class="card-body p-5">
                    
                    <form id="payment-form" action="{{ route('payment.process') }}" method="POST">
                        @csrf

                        <!-- Campos Escondidos -->
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        
                        <!-- 1. CAMPO NOME COMPLETO (Corrigido para name="name" para validação do Laravel) -->
                        <div class="mb-3">
                            <label for="card-holder-name" class="form-label font-medium">Nome no Cartão</label>
                            <input type="text" 
                                name="name" 
                                id="card-holder-name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name', $user ? $user->name : '') }}" 
                                required 
                                autocomplete="cc-name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 2. CAMPO DO CARTÃO DE CRÉDITO (STRIPE ELEMENTS: CAMPO ÚNICO) -->
                        <div class="mb-4">
                            <label for="card-element" class="form-label font-medium">Detalhes do Cartão de Crédito</label>
                            <!-- O campo único do Stripe será montado neste div -->
                            <div id="card-element" class="stripe-element-field">
                                <!-- Elementos de formulário do Stripe serão inseridos aqui. -->
                            </div>
                            <!-- Usado para exibir erros do cartão -->
                            <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                        </div>

                        <!-- 3. MENSAGEM DE REDIRECIONAMENTO -->
                        @if (!$user)
                        <div class="alert alert-info small mb-4">Você será redirecionado para o Cadastro após o pagamento ser confirmado.</div>
                        @endif
                        
                        <!-- Botão de Submissão -->
                        <button type="submit" class="btn btn-success w-100 py-3 text-lg font-bold" id="card-button">
                            Pagar R$ {{ number_format($plan->price, 2, ',', '.') }}
                        </button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- INCLUSÃO OBRIGATÓRIA DO SCRIPT DO STRIPE -->
<script src="https://js.stripe.com/v3/"></script>

<!-- BLOCO DE INICIALIZAÇÃO DO STRIPE (GLOBAL) -->
<script>
    // Variáveis globais para o Stripe
    var stripe;
    var elements;
    
    // CRÍTICO: Tenta carregar a chave de API
    const stripePublicKey = "{{ env('STRIPE_KEY_PUBLIC') }}";
    
    if (!stripePublicKey || stripePublicKey === '') {
        console.error("ERRO CRÍTICO: A chave pública 'STRIPE_KEY_PUBLIC' não está carregando. Verifique seu .env e execute 'php artisan config:clear'.");
        
        const cardErrors = document.getElementById('card-errors');
        if (cardErrors) cardErrors.textContent = "ERRO: Chave de pagamento ausente. Execute 'php artisan config:clear'.";
        
        const cardButton = document.getElementById('card-button');
        if (cardButton) cardButton.disabled = true;
        
    } else {
        // Inicializa o Stripe e Elements apenas se a chave estiver presente
        stripe = Stripe(stripePublicKey); 
        elements = stripe.elements();
    }
</script>

<!-- BLOCO DE ESTILO E LÓGICA DE PAGAMENTO -->
<style>
    /* Aplicando o estilo de input do Bootstrap/Tailwind aos campos do Stripe */
    .stripe-element-field {
        /* Aumenta a altura e o padding para parecer um campo maior */
        height: 50px; /* Altura aumentada */
        padding: 0.75rem 1rem; /* Padding maior */
        
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        
        /* Z-INDEX EXTREMO (MANTIDO) para garantir clique */
        z-index: 9999999 !important; 
        position: relative !important;
        transform: translateZ(0); 
    }

    .stripe-element-field:focus-within {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Se o Stripe não foi inicializado (chave falhou), não prossiga.
        if (typeof stripe === 'undefined' || typeof elements === 'undefined') {
            return;
        }

        const formattedPrice = `R$ {{ number_format($plan->price, 2, ',', '.') }}`;
        const cardErrors = document.getElementById('card-errors');
        
        const style = {
            base: {
                fontSize: '16px',
                color: '#495057',
                fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#dc3545', /* Cor de erro do Bootstrap */
                iconColor: '#dc3545',
            }
        };

        // 1. Cria o elemento único do cartão
        const card = elements.create('card', { 
            style: style,
            hidePostalCode: true // CRÍTICO: Remove o campo CEP/Postal Code
        });
        card.mount('#card-element');


        // 2. Lida com erros em tempo real
        card.on('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
            } else {
                cardErrors.textContent = '';
            }
        });

        // 3. Lida com o envio do formulário
        const form = document.getElementById('payment-form');
        const cardHolderNameInput = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('card-button');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            cardButton.disabled = true;
            cardButton.textContent = 'Processando...';

            // Captura o valor do campo de nome corrigido
            const nameValue = cardHolderNameInput.value;

            // Cria o Token de Pagamento 
            const { token, error } = await stripe.createToken(card, {
                name: nameValue,
            });

            if (error) {
                // Exibe erro para o usuário e reabilita o botão
                cardErrors.textContent = error.message;
                cardButton.disabled = false;
                cardButton.textContent = `Pagar ${formattedPrice}`;
            } else {
                // Adiciona o token ao formulário e o envia para o Laravel
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Envia o formulário
                form.submit();
            }
        });
        
        // Foca no campo do cartão
        card.focus();
    });
</script>
@endsection