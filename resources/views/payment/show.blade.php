@extends('templates.home')
@section('title', 'Finalizar Pagamento')

{{-- Define a chave pública do Stripe no JavaScript --}}
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Usa a chave pública do .env. Laravel injeta essa variável automaticamente.
    const stripe = Stripe("{{ env('STRIPE_KEY') }}");
    const elements = stripe.elements();
</script>

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Finalizar Assinatura</h1>

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Resumo do Pedido</h4>
                </div>
                <div class="card-body">
                    <p class="h5">Plano Selecionado: <strong>{{ $plan->name }}</strong></p>
                    <p class="h5">Preço: <strong>R$ {{ number_format($plan->price, 2, ',', '.') }}</strong></p>
                    <hr>
                    <p class="lead">Seu acesso será liberado por {{ $plan->duration_days }} dias.</p>

                    {{-- 
                        O Formulário POST será enviado para a rota 'payment.process' que criamos. 
                        Ele contém o token de segurança CSRF e o ID do plano.
                    --}}
                    <form action="{{ route('payment.process') }}" method="POST" id="payment-form">
                        @csrf

                        {{-- Campo escondido para enviar o ID do plano ao Controller --}}
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                        <div class="form-group mb-3">
                            <label for="card-holder-name">Nome no Cartão</label>
                            <input type="text" id="card-holder-name" class="form-control" required>
                        </div>

                        {{-- Elemento Stripe para inserir o cartão --}}
                        <div class="form-group mb-4">
                            <label for="card-element">Informações do Cartão</label>
                            <div id="card-element" class="form-control" style="height: 40px; padding-top: 10px;">
                                <!-- Um Stripe Element será inserido aqui. -->
                            </div>
                            <!-- Usado para exibir erros do cartão -->
                            <div id="card-errors" class="invalid-feedback d-block"></div>
                        </div>

                        {{-- Botão de Pagamento --}}
                        <button id="card-button" class="btn btn-success btn-lg w-100" data-secret="{{ $plan->id }}">
                            Pagar R$ {{ number_format($plan->price, 2, ',', '.') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Configurações do Stripe Elements e Processamento do Formulário
    document.addEventListener('DOMContentLoaded', function () {
        const style = {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a',
            }
        };

        // 1. Cria o elemento do cartão
        const card = elements.create('card', { style: style });
        card.mount('#card-element');

        // 2. Lida com erros em tempo real no campo do cartão
        const cardErrors = document.getElementById('card-errors');
        card.addEventListener('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
            } else {
                cardErrors.textContent = '';
            }
        });

        // 3. Lida com o envio do formulário
        const form = document.getElementById('payment-form');
        const cardHolderName = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('card-button');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            cardButton.disabled = true; // Desabilita o botão para evitar cliques duplos
            cardButton.textContent = 'Processando...';

            // Cria um Token de Pagamento (não o pagamento em si)
            const { token, error } = await stripe.createToken(card, {
                name: cardHolderName.value,
            });

            if (error) {
                // Exibe erro para o usuário e reabilita o botão
                cardErrors.textContent = error.message;
                cardButton.disabled = false;
                cardButton.textContent = `Pagar R$ {{ number_format($plan->price, 2, ',', '.') }}`;
            } else {
                // Adiciona o token ao formulário e o envia para o Laravel
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Envia o formulário para o PaymentController
                form.submit();
            }
        });
    });
</script>
@endsection
