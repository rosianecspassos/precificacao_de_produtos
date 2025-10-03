<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial com os planos disponíveis.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $plans = Plan::all();

        return view('conteudo.index', compact('plans'));
    }
}
