<?php

namespace Database\Factories;


use App\Models\Valor;
use App\Models\Sensor;
use App\Models\Tinaco;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Valor>
 */
class ValorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Valor::class;

    public function definition()
    {
        return [
            'sensor_id' => Sensor::factory(),
            'tinaco_id' => Tinaco::factory(),
            'value'     => $this->faker->randomFloat(2, 0, 100), // Asegúrate de que el campo se llame 'value' en el modelo o migración
        ];
    }
}
