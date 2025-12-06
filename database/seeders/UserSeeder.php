<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::first();

        // Kullanıcı oluştur
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);

        // Kullanıcıyı team'e bağla
        if ($team) {
            $user->teams()->attach($team->id);
        }
    }
}
