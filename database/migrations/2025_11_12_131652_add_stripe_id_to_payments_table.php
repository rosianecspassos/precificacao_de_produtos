<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // CORREÇÃO: Usamos 'plan_slug' em vez de 'plan_id',
            // pois 'plan_slug' é a coluna existente na tabela payments.
            $table->string('stripe_id')->nullable()->after('plan_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('stripe_id');
        });
    }
};