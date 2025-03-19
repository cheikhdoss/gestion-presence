<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'nom' => 'Admin',
            'prenom' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'), // Le mot de passe est "admin123"
            'role' => 'admin'
        ]);

        $this->command->info('Utilisateur admin créé :');
        $this->command->info('Email : admin@example.com');
        $this->command->info('Mot de passe : admin123');
    }
} 