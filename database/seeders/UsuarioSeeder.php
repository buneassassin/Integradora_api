<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Roles;
use Database\Factories\UsuarioFactory;
use Illuminate\Support\Str;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $persona = Persona::where('nombres', 'Dante')->first();
        $adminRole = Roles::where('name', 'admin')->first();
        $userRole = Roles::where('name', 'usuario')->first();

        $adminUser = Usuario::create([
            'usuario_nom' => 'Dante',
            'email' => 'buneassassin@gmail.com',
            'email_verified_at' => now(),
            'foto_perfil' => 'default.jpg',
            'password' => bcrypt('1234567890'),
            'remember_token' => Str::random(10),
            'id_persona' => $persona->id,
        ]);
        $adminUser->roles()->attach($adminRole);

        for ($i = 0; $i < 5; $i++) {
            $persona = Persona::factory()->create();
            $user = Usuario::factory()->make(['id_persona' => $persona->id]);
            $user->save();
            $user->roles()->attach($userRole);
        }
    }
}
