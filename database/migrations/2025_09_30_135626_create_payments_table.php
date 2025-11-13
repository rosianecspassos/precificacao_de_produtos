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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // CORREÇÃO: Apenas cria a coluna user_id como UNSIGNED BIG INT.
            // A chave estrangeira será adicionada por último em outra migração.
            $table->unsignedBigInteger('user_id'); 
            
            $table->string('transaction_id')->nullable(); 
            $table->string('plan_slug');
            $table->decimal('amount', 8, 2);
            $table->string('status'); 
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
public function down(): void
{
    // CORREÇÃO: Usar dropIfExists para apagar a tabela inteira
    Schema::dropIfExists('payments'); 
}
};