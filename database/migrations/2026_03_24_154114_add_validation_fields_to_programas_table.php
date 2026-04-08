<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidationFieldsToProgramasTable extends Migration
{
    public function up()
    {
        Schema::table('programas', function (Blueprint $table) {
            // Campos para validación de guía operativa
            $table->boolean('guia_operativa_validada')->default(false);
            $table->text('guia_operativa_observaciones')->nullable();
            $table->unsignedBigInteger('guia_operativa_validada_por')->nullable();
            $table->timestamp('guia_operativa_fecha_validacion')->nullable();

            // Clave foránea
            $table->foreign('guia_operativa_validada_por')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropForeign(['guia_operativa_validada_por']);
            $table->dropColumn([
                'guia_operativa_validada',
                'guia_operativa_observaciones',
                'guia_operativa_validada_por',
                'guia_operativa_fecha_validacion'
            ]);
        });
    }
}
