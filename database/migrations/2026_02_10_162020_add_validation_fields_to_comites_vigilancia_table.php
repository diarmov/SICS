<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidationFieldsToComitesVigilanciaTable extends Migration
{
    public function up()
    {
        Schema::table('comites_vigilancia', function (Blueprint $table) {
            $table->boolean('validado')->default(false)->after('activo');
            $table->unsignedBigInteger('validado_por')->nullable()->after('validado');
            $table->timestamp('fecha_validacion')->nullable()->after('validado_por');

            // Clave foránea opcional
            $table->foreign('validado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('comites_vigilancia', function (Blueprint $table) {
            $table->dropForeign(['validado_por']);
            $table->dropColumn(['validado', 'validado_por', 'fecha_validacion']);
        });
    }
}
