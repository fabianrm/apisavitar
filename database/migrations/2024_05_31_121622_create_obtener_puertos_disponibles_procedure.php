<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateObtenerPuertosDisponiblesProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primero, eliminar el procedimiento si ya existe
        $dropProcedure = "DROP PROCEDURE IF EXISTS obtenerPuertosDisponibles";
        DB::unprepared($dropProcedure);

        // Crear el nuevo procedimiento
        $procedure = "
            CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerPuertosDisponibles`(IN p_box_id INT)
            BEGIN
                SELECT p.id, p.port_number
                FROM (
                    SELECT b.id, n.num AS port_number
                    FROM (SELECT id, total_ports FROM boxes  WHERE id = p_box_id) AS b
                    CROSS JOIN numbers n
                    WHERE n.num <= b.total_ports
                ) AS p
                LEFT JOIN services s ON p.id = s.box_id AND p.port_number = s.port_number
                WHERE s.port_number IS NULL OR s.box_id IS NULL;
            END
        ";

        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar el procedimiento si existe
        $dropProcedure = "DROP PROCEDURE IF EXISTS obtenerPuertosDisponibles";
        DB::unprepared($dropProcedure);
    }
}
