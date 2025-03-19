<?php

namespace Database\Factories;

use App\Models\Cours;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmargementFactory extends Factory
{
    public function definition()
    {
        return [
            'cours_id' => Cours::factory(),
            'user_id' => User::factory()->etudiant(),
            'statut' => $this->faker->randomElement(['present', 'absent', 'retard']),
            'date_signature' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'commentaire' => $this->faker->optional()->sentence()
        ];
    }

    public function present()
    {
        return $this->state(function (array $attributes) {
            return [
                'statut' => 'present',
                'date_signature' => now()
            ];
        });
    }

    public function absent()
    {
        return $this->state(function (array $attributes) {
            return [
                'statut' => 'absent',
                'date_signature' => null
            ];
        });
    }

    public function retard()
    {
        return $this->state(function (array $attributes) {
            return [
                'statut' => 'retard',
                'date_signature' => now()->addMinutes(rand(5, 30))
            ];
        });
    }
} 