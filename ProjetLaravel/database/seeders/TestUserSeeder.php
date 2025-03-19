<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Professeur Test',
            'email' => 'prof@test.com',
            'password' => Hash::make('password'),
            'role' => 'professeur'
        ]);

        User::create([
            'name' => 'Étudiant Test',
            'email' => 'etudiant@test.com',
            'password' => Hash::make('password'),
            'role' => 'etudiant'
        ]);
    }
} 