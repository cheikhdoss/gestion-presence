<?php

namespace Database\Factories;

use App\Models\Salle;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalleFactory extends Factory
{
    protected $model = Salle::class;

    public function definition()
    {
        return [
            'nom' => 'Salle ' . $this->faker->unique()->numberBetween(100, 999),
            'capacite' => $this->faker->numberBetween(20, 100),
            'description' => $this->faker->optional()->sentence()
        ];
    }
} 