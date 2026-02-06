<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoApoyoToProgramasTable extends Migration
{
    public function up()
    {
        Schema::table('programas', function (Blueprint $table) {
            // Agregar la columna tipo_apoyo_id primero
            $table->foreignId('tipo_apoyo_id')->nullable()->after('dependencia_id');

            // Agregar las nuevas columnas para número de beneficiarios y monto vigilado
            $table->integer('numero_beneficiarios')->default(0)->after('periodo');
            $table->decimal('monto_vigilado', 15, 2)->default(0)->after('numero_beneficiarios');

            // Agregar la relación foreign key
            $table->foreign('tipo_apoyo_id')->references('id')->on('tipos_apoyo')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('programas', function (Blueprint $table) {
            // Eliminar la foreign key primero
            $table->dropForeign(['tipo_apoyo_id']);

            // Eliminar las columnas
            $table->dropColumn('tipo_apoyo_id');
            $table->dropColumn('numero_beneficiarios');
            $table->dropColumn('monto_vigilado');
        });
    }
}
