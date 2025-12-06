<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Team oluştur (Tenant olarak)
        $team = Team::create([
            'name' => 'Örnek Restoran',
            'slug' => 'ornek-restoran',
        ]);

        // User seeder'ı çağır
        $this->call(UserSeeder::class);
        
        // Table seeder'ı çağır
        $this->call(TableSeeder::class);
        
        // Product seeder'ı çağır (kategoriler dahil)
        $this->call(ProductSeeder::class);
    }
}

