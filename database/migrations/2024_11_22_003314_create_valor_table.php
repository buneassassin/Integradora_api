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

     protected $connection = 'mongodb';
    public function up()
    {
        Schema::create('Valor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained('sensor')->onDelete('cascade');
            $table->foreignId('tinaco_id')->constrained('tinaco')->onDelete('cascade');
            $table->decimal('valor', 10, 2)->nullable();
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
        Schema::dropIfExists('Valor');
    }
};
