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
            $table->string('nombre');
            $table->string('modelo')->nullable();
            $table->string('unidad_medida')->nullable();
            $table->timestamps();
        });
        Sensor::create(['nombre' => 'Ultrasónico JSN-SR04T-2.0', 'modelo' => 'Modelo 1', 'unidad_medida' => 'Unidad de Medida 1']);
        Sensor::create(['nombre' => '', 'modelo' => 'Modelo 2', 'unidad_medida' => 'Unidad de Medida 2']);
        Sensor::create(['nombre' => 'Sensor 3', 'modelo' => 'Modelo 3', 'unidad_medida' => 'Unidad de Medida 3']);
        Sensor::create(['nombre' => 'Sensor 4', 'modelo' => 'Modelo 4', 'unidad_medida' => 'Unidad de Medida 4']);
        Sensor::create(['nombre' => 'Sensor 5', 'modelo' => 'Modelo 5', 'unidad_medida' => 'Unidad de Medida 5']);
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
