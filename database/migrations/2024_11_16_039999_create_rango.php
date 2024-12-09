<?php

use App\Models\Rango;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

            Rango::create(['rango_min' => 1, 'rango_max' => 5]);
        });
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
