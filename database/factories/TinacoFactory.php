<?php

namespace Database\Factories;

use App\Models\Tinaco;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tinaco>
 */
class TinacoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Tinaco::class;

    public function definition()
    {
        return [
            'id_usuario'     => Usuario::factory(),
            'name'           => $this->faker->word,
            'nivel_del_agua' => $this->faker->numberBetween(0, 100),
        ];
    }
}
