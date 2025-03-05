<?php

namespace Database\Factories;


use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class PersonaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Persona::class;

    public function definition()
    {

        return [
            'nombres' => $this->faker->firstName(),
            'a_p' => $this->faker->lastName(),
            'a_m' => $this->faker->lastName(),
            'telefono' => $this->faker->phoneNumber(),
        ];
       
    }
}
