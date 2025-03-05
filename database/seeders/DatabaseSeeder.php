<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Usuario;
use App\Models\Valor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Crea 5 usuarios utilizando el factory de Usuario
        Usuario::factory(5)->create();
        Notification::factory(5)->create();
        Valor::factory(5)->create();
        

    }
}
