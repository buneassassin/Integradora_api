<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tinaco;
use App\Models\Usuario;
use Database\Factories\TinacoFactory;

class TinacoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = Usuario::where('email', 'buneassassin@gmail.com')->first();

        $adminTinaco = Tinaco::create([
            'name' => "Casa",
            'nivel_del_agua' => fake()->numberBetween(0, 100),
            'id_usuario' => $adminUser->id
        ]);

        Tinaco::factory(5)->create()->each(function ($tinaco) {
            $user = Usuario::inRandomOrder()->first();
            $tinaco->usuario()->associate($user);
            $tinaco->save();
        });
        
        Usuario::where('email', '!=', 'buneassassin@gmail.com')->take(5)->get()->each(function ($user) {
            Tinaco::factory()->create(['id_usuario' => $user->id]);
        });
    }
}
