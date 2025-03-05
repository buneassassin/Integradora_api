<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sensor>
 */

use App\Models\Sensor;

class SensorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Sensor::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'modelo' => $this->faker->word,
            'unidad_medida' => $this->faker->randomElement(['cm', 'm', 'kg', '°C']),
            'rango_min' => $this->faker->randomFloat(2, 0, 50),
            'rango_max' => $this->faker->randomFloat(2, 50, 100),
        ];
    }
}
