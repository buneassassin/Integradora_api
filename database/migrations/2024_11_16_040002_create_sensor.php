<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Sensor;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rango')->constrained('rango')->onDelete('cascade');
            $table->string('nombre');
            $table->string('modelo')->nullable();
            $table->string('unidad_medida')->nullable();
            $table->timestamps();
        });
<<<<<<< HEAD
        Sensor::create(['nombre' => 'Ultrasonico', 'modelo' => 'Ultrasónico JSN-SR04T-2.0', 'unidad_medida' => 'Hz']);
        Sensor::create(['nombre' => 'Temperatura', 'modelo' => 'Temperatura MAX6675 ', 'unidad_medida' => 'C']);
        Sensor::create(['nombre' => 'PH', 'modelo' => 'Sensor de pH (PH4502-C)', 'unidad_medida' => 'Unidad de Medida 3']);
        Sensor::create(['nombre' => 'Turbidez', 'modelo' => 'Sensor de turbidez con salida analógica y digital', 'unidad_medida' => 'Unidad de Medida 4']);
        Sensor::create(['nombre' => 'TDS', 'modelo' => 'TDS con sonda sumergible', 'unidad_medida' => 'Unidad de Medida 5']);
=======
        Sensor::create(['nombre' => 'Turbidez', 'modelo' => 'Modelo 1', 'unidad_medida' => 'Unidad de Medida 1']);
        Sensor::create(['nombre' => 'TDS', 'modelo' => 'Modelo 2', 'unidad_medida' => 'Unidad de Medida 2']);
        Sensor::create(['nombre' => 'Temperatura', 'modelo' => 'Modelo 3', 'unidad_medida' => 'Unidad de Medida 3']);
        Sensor::create(['nombre' => 'Ph', 'modelo' => 'Modelo 4', 'unidad_medida' => 'Unidad de Medida 4']);
        Sensor::create(['nombre' => 'Ultrasonico', 'modelo' => 'Modelo 5', 'unidad_medida' => 'Unidad de Medida 5']);
>>>>>>> b43a1fc325c45a0e2fdf54cbec7aef2c1c103f80
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor');
    }
};
