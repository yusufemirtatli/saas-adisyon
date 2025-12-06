<?php

namespace Database\Seeders;

use App\Models\Table;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::first();

        // 2 Masa oluştur
        Table::create([
            'team_id' => $team->id,
            'name' => 'Masa 1',
        ]);

        Table::create([
            'team_id' => $team->id,
            'name' => 'Masa 2',
        ]);
    }
}

