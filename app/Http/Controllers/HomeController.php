<?php

namespace App\Http\Controllers;

use App\Models\Plan; // Importa o modelo Plan
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial com a lista de planos disponíveis.
     */
    public function index()
    {
        // CORREÇÃO: Busca TODOS os planos usando ::all() para evitar o 'where active'
        $plans = Plan::all(); // Busca todos os planos

        // Retorna a view 'home' (ou 'welcome', ajuste conforme seu template)
        // e PASSA a variável $planos para a view.
        // A view deve estar esperando a variável $planos (plural).
        return view('conteudo.index') // Altere 'templates.home' se a sua view raiz for outra
                    ->with('plans', $plans);
    }
}