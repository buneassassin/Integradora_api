<?php

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
        Schema::create('sensor_tinaco', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained('sensor')->onDelete('cascade');
            $table->foreignId('tinaco_id')->constrained('tinaco')->onDelete('cascade');
            $table->foreignId('id_valor')->nullable()->constrained('valor')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor_tinaco');
    }
};
