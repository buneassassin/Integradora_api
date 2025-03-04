<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sensor;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sensor::create(['nombre' => 'Ultrasonico', 'modelo' => 'Ultrasónico JSN-SR04T-2.0', 'unidad_medida' => 'Cm', 'rango_min' => 0, 'rango_max' => 600]);
        Sensor::create(['nombre' => 'Temperatura', 'modelo' => 'Temperatura MAX6675 ', 'unidad_medida' => 'C°', 'rango_min' => 5, 'rango_max' => 45]);
        Sensor::create(['nombre' => 'PH','modelo' => 'Sensor de pH (PH4502-C)', 'unidad_medida' => 'PH', 'rango_min' => 0, 'rango_max' => 14]);
        Sensor::create(['nombre' => 'Turbidez', 'modelo' => 'Sensor de turbidez con salida analógica y digital', 'unidad_medida' => 'NTU', 'rango_min' => 0, 'rango_max' => 1000]);
        Sensor::create(['nombre' => 'TDS', 'modelo' => 'TDS con sonda sumergible', 'unidad_medida' => 'PPM', 'rango_min' => 0, 'rango_max' => 2000]);
    }
}
