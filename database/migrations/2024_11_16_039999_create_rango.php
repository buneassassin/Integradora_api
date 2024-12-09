<?php

use App\Models\Rango;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('rango', function (Blueprint $table) {
            $table->id();
            $table->decimal('rango_min', 10, 2);
            $table->decimal('rango_max', 10, 2);
            $table->timestamps();

           
        });

        Rango::create(['rango_min' => 0.00, 'rango_max' => 200.00]);
        Rango::create(['rango_min' => 15.00, 'rango_max' => 50.00]);
        Rango::create(['rango_min' => 0.00, 'rango_max' => 14.00]);
        Rango::create(['rango_min' => 0.00, 'rango_max' => 1000.00]);
        Rango::create(['rango_min' => 0.00, 'rango_max' => 2000.00]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rango');
    }
};
