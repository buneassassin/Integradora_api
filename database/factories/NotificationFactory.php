<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * 
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Notification::class;
    public function definition()
    {
        return [
            'id_usuario' => Usuario::factory(),
            'type'       => $this->faker->randomElement(['info', 'alerta', 'warning']),
            'title'      => $this->faker->sentence,
            'message'    => $this->faker->paragraph,
            'is_read'    => $this->faker->boolean,
        ];
    }
}
