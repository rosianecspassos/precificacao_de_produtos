<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona a coluna 'stripe_id' na tabela 'payments'
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // O stripe_id é um ID de string longo e precisa ser nullable caso
            // o pagamento falhe antes de gerar o ID (embora no nosso fluxo não deva ser o caso).
            $table->string('stripe_id')->nullable()->after('plan_id'); 
        });
    }

    /**
     * Reverse the migrations.
     * Remove a coluna se a migração for revertida
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('stripe_id');
        });
    }
};