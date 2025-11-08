<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısı oluştur
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);

        // Admin için bir team oluştur
        $team = Team::create([
            'name' => 'Admin Team',
            'slug' => 'admin-team',
        ]);

        // Admin'i team'e ekle
        $admin->teams()->attach($team->id);
    }
}
