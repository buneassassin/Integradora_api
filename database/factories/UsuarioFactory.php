<?php

namespace Database\Factories;

use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Usuario::class;

    public function definition()
    { 
        return [
            'id_persona'         => Persona::factory(),
            'usuario_nom'        => $this->faker->userName,
            'email'              => $this->faker->unique()->safeEmail,
            'email_verified_at'  => now(),
            'foto_perfil'        => $this->faker->imageUrl(200, 200, 'people'),
            'rol'                => 'Guest',
            'password'           => Hash::make('password'),
            'is_active'          => $this->faker->boolean,
            'is_Inactive'        => $this->faker->boolean,
            'remember_token'     => Str::random(10),
        ];
    }
}
