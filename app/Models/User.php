<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Os atributos que são atribuíveis em massa (Mass Assignable).
     * ESTA É A LINHA QUE PRECISA SER CORRIGIDA: adicione 'subscription_expires_at'
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'subscription_expires_at', // <--- ADICIONE ESTA LINHA
    ];

    /**
     * Os atributos que devem ser ocultados para serialização.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser 'cast' (convertidos) para tipos nativos.
     * É boa prática converter campos de data para 'datetime'.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime', // <--- ADICIONE ESTA LINHA TAMBÉM
        'password' => 'hashed',
    ];
}