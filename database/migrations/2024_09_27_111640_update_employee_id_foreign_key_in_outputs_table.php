<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmployeeIdForeignKeyInOutputsTable extends Migration
{
    public function up()
    {
        Schema::table('outputs', function (Blueprint $table) {
            // Eliminar la clave foránea existente
            //$table->dropForeign(['employee_id']);

            // Añadir la nueva clave foránea que referencia a la tabla users
            //$table->foreign('user_id')->references('id')->on('users');


            $table->dropForeign(['employee_id']); // Si había una clave foránea

            // Agregar la columna material_id
            $table->unsignedBigInteger('user_id')->after('id');
            $table->foreign('user_id')->references('id')->on('users');


        });
    }

    public function down()
    {
        Schema::table('outputs', function (Blueprint $table) {
            // Eliminar la nueva clave foránea
            $table->dropForeign(['employee_id']);

            // Restaurar la clave foránea original
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }
}
