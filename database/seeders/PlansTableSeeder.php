<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlansTableSeeder extends Seeder
{
    /**
     * Preenche a tabela 'plans' com dados iniciais.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([
            // Plano Padrão
            [
                'name' => 'Plano Padrão',
                'slug' => 'plano_padrao',
                'price' => 19.90,
                'description' => 'Acesso essencial ao sistema, ideal para pequenos projetos.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Plano Premium
            [
                'name' => 'Plano Premium',
                'slug' => 'plano_premium',
                'price' => 49.90,
                'description' => 'Acesso total, recursos avançados e relatórios detalhados.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}