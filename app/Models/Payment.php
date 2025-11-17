<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plan_id', // CRÍTICO: Adicionado (foi o campo que deu erro no SQL)
        'stripe_id',
        'amount',
        'status',
        'plan_slug',
        'payment_method',
    ];

    /**
     * Relação: Um pagamento pertence a um usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relação: Um pagamento pertence a um plano
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}