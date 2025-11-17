<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

use Illuminate\Support\Facades\DB;

class PlansTableSeeder extends Seeder
{
    public function run()
    {
        // desabilitar verificação de chave estrangeira
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('plans')->truncate();

        // reativar verificação
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('plans')->insert([
            [
                'name' => 'Básico',
                'slug' => 'basico',
                'price' => 29.90,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 59.90,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'price' => 99.90,
            ],
        ]);
    }
}
