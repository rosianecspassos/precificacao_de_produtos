<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalculoController extends Controller
{
    public function create(Request $request)
    {
        $nome_produto = $request->input('nome_produto', '');
        $valor_produto = null;
        
        // Inicializa as variáveis com arrays ou nulo
        $custos = is_array($request->input('custo')) ? array_filter($request->input('custo'), fn($value) => $value !== null && $value !== '') : [];
        $taxas = is_array($request->input('taxa')) ? array_filter($request->input('taxa'), fn($value) => $value !== null && $value !== '') : [];
        $lucro = $request->input('lucro');

        $custo_total = 0;
        $taxa_total = 0;

        // Verifica se há dados para calcular
        if (!empty($custos) && !empty($taxas) && $lucro !== null) {
            
            // Realiza os cálculos
            $custo_total = array_sum($custos);
            $taxa_total = array_sum($taxas);
            
            $taxa_decimal = $taxa_total / 100;
            $lucro_decimal = $lucro / 100;
            
            $denominador = 1 - $taxa_decimal - $lucro_decimal;
            
            if ($denominador > 0) {
                $valor_produto = $custo_total / $denominador;
            } else {
                $valor_produto = 0;
            }
        }
        
        // Adiciona um valor vazio ao array se estiver vazio, para repopular um campo vazio no Blade
        $custos_para_sessao = !empty($custos) ? $custos : [''];
        $taxas_para_sessao = !empty($taxas) ? $taxas : [''];


        // Retorna os dados, garantindo que todas as chaves estejam presentes
        return redirect()->route('dashboard')->with([
            'nome_produto' => $nome_produto,
            'custos' => $custos_para_sessao, // Array de custos para repopular os campos
            'taxas' => $taxas_para_sessao,   // Array de taxas para repopular os campos
            'custo' => $custo_total,         // Valor total do custo (escalar)
            'taxa' => $taxa_total,           // Valor total da taxa (escalar)
            'lucro' => $lucro ?? 0,
            'valor_produto' => $valor_produto
        ]);
    }
}
