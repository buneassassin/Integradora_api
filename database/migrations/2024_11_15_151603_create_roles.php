<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role1 = Role::create(['name' => 'guest']);
        $role2 = Role::create(['name' => 'usuario']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          
    }
};
