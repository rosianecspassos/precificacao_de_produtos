@extends('templates.home')

@php
    $stripePublicKey = config('services.stripe.key');
@endphp

@section('title', 'Checkout - ' . $plan->name)

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <h1 class="text-center mb-4 fw-bold">Finalizar Assinatura</h1>

            {{-- CARD DO PLANO --}}
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">{{ $plan->name }}</h3>
                    <p class="mb-0 fs-5">
                        R$ {{ number_format($plan->price, 2, ',', '.') }}
                    </p>
                </div>

                <div class="card-body">

                    {{-- ================= PIX (MERCADO PAGO) ================= --}}
                    <h4 class="mb-3">Pagar com Pix</h4>

                    <input type="hidden" id="plan_id" value="{{ $plan->id }}">

                    <button type="button" id="btnPix" class="btn btn-success w-100 mb-3">
                        Gerar QR Code PIX
                    </button>

                    <div id="pixContainer" style="display:none; text-align:center;">
                        <p class="fw-bold">Escaneie o QR Code:</p>

                        {{-- QR CODE --}}
                     <img id="pixQrCode" src="{{  $plan->qr_code }}" width="250" class="mb-3"> 


                        {{-- CÓDIGO COPIA E COLA --}}
                        <p class="fw-bold">Ou copie o código Pix:</p>
                        <textarea id="pixCode"
                                  class="form-control"
                                  rows="4"
                                  readonly></textarea>
                    </div>

                    <div id="statusBox" class="mt-3 p-2 rounded bg-light text-center">
                        Status: Aguardando…
                    </div>

                    <hr class="my-4">

                    {{-- ================= CARTÃO (STRIPE) ================= --}}
                    <h4 class="mb-3">Pagar com Cartão</h4>

                    <form id="payment-form" action="{{ route('payment.process') }}" method="POST">
                        @csrf

                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <input type="hidden" name="stripeToken" id="stripeTokenInput">

                        {{-- Nome --}}
                        <div class="mb-3">
                            <label class="form-label">Nome no Cartão</label>
                            <input type="text"
                                   id="card-holder-name"
                                   class="form-control"
                                   required>
                        </div>

                        {{-- Stripe Element --}}
                        <div class="mb-3">
                            <label class="form-label">Dados do Cartão</label>
                            <div id="card-element" class="stripe-element"></div>
                            <div id="card-errors" class="text-danger mt-2"></div>
                        </div>

                        <button type="submit"
                                id="card-button"
                                class="btn btn-primary w-100">
                            Pagar R$ {{ number_format($plan->price, 2, ',', '.') }}
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- ================= ESTILO ================= --}}
<style>
.stripe-element {
    padding: 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background: #fff;
}
</style>

{{-- ================= SCRIPTS ================= --}}
<script src="https://js.stripe.com/v3/"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ================= PIX — MERCADO PAGO ================= */

    const btnPix = document.getElementById("btnPix");

    btnPix.addEventListener("click", async () => {

        const planId = document.getElementById("plan_id").value;
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        document.getElementById("statusBox").innerText = "Status: Gerando PIX…";

        try {
            const response = await fetch("{{ route('payment.pix') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf,
                    "Accept": "application/json"
                },
                body: JSON.stringify({ plan_id: planId })
            });

            const data = await response.json();

            if (!data.qr_code_base64) {
                alert("Erro ao gerar PIX");
                return;
            }

            document.getElementById("pixContainer").style.display = "block";
            document.getElementById("pixQrCode").src =
                "data:image/png;base64," + data.qr_code_base64;

            document.getElementById("pixCode").value = data.qr_code;

            document.getElementById("statusBox").innerText =
                "Status: Aguardando pagamento…";

        } catch (e) {
            console.error(e);
            alert("Erro ao gerar PIX");
            document.getElementById("statusBox").innerText = "Status: Erro";
        }
    });

    /* ================= STRIPE ================= */

    const stripe = Stripe("{{ $stripePublicKey }}");
    const elements = stripe.elements();
    const card = elements.create("card");
    card.mount("#card-element");

    const form = document.getElementById("payment-form");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const { token, error } = await stripe.createToken(card, {
            name: document.getElementById("card-holder-name").value
        });

        if (error) {
            document.getElementById("card-errors").innerText = error.message;
            return;
        }

        document.getElementById("stripeTokenInput").value = token.id;
        form.submit();
    });

});
</script>

@endsection
