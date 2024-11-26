<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StrategySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $strategies = ['DESKTOP', 'MOBILE'];
    
        foreach ($strategies as $strategy) {
            DB::table('strategies')->insert([
                'name' => $strategy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
