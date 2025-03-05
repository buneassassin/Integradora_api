<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sensor', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('modelo')->nullable();
            $table->string('unidad_medida')->nullable();
            $table->decimal('rango_min', 10, 2);
            $table->decimal('rango_max', 10, 2);
            $table->timestamps();
        });

        // Use DB::table() for inserting data instead of Eloquent model
        DB::table('sensor')->insert([
            [
                'nombre' => 'Ultrasonico',
                'modelo' => 'Ultrasónico JSN-SR04T-2.0',
                'unidad_medida' => 'Cm',
                'rango_min' => 0,
                'rango_max' => 600,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Temperatura',
                'modelo' => 'Temperatura MAX6675',
                'unidad_medida' => 'C°',
                'rango_min' => 5,
                'rango_max' => 45,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'PH',
                'modelo' => 'Sensor de pH (PH4502-C)',
                'unidad_medida' => 'PH',
                'rango_min' => 0,
                'rango_max' => 14,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Turbidez',
                'modelo' => 'Sensor de turbidez con salida analógica y digital',
                'unidad_medida' => 'NTU',
                'rango_min' => 0,
                'rango_max' => 1000,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'TDS',
                'modelo' => 'TDS con sonda sumergible',
                'unidad_medida' => 'PPM',
                'rango_min' => 0,
                'rango_max' => 2000,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('sensor');
    }
};
