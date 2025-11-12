<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Altera a coluna 'transaction_id' para aceitar valores nulos.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Altera a coluna para aceitar nulos, resolvendo o erro 1364.
            $table->string('transaction_id')->nullable()->change(); 
        });
    }

    /**
     * Reverse the migrations.
     * Retorna a coluna ao estado original (não nula) se necessário.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Nota: Retornar ao estado NOT NULL pode exigir que a coluna esteja vazia.
            $table->string('transaction_id')->nullable(false)->change();
        });
    }
};