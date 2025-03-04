<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Persona::create([
            'nombres' => 'Dante',
            'a_p' => 'Basurto',
            'a_m' => 'X',
            'telefono' => '1234567890'
        ]);

        /*Persona::factory(5)->create();*/
    }
}
