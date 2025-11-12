<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona a coluna 'subscription_expires_at' na tabela 'users'
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Coluna que armazena quando a assinatura expira. Nullable é importante!
            $table->timestamp('subscription_expires_at')->nullable()->after('email_verified_at'); 
        });
    }

    /**
     * Reverse the migrations.
     * Remove a coluna se a migração for revertida
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('subscription_expires_at');
        });
    }
};