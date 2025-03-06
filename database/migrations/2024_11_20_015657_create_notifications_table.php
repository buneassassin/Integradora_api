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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuario')->onDelete('cascade');
            $table->string('type');           // Tipo de notificación (info, alerta, etc.)
            $table->string('title');          // Título de la notificación
            $table->text('message');          // Mensaje de la notificación
            $table->boolean('is_read')->default(false); // Estado de lectura
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
        Schema::dropIfExists('notifications');
    }
};
