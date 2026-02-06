<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReglasAndGuiaPdfToProgramasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programas', function (Blueprint $table) {
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('programas', 'reglas_operacion_pdf')) {
                $table->string('reglas_operacion_pdf')->nullable()->after('archivo_pdf');
            }

            if (!Schema::hasColumn('programas', 'guia_operativa_pdf')) {
                $table->string('guia_operativa_pdf')->nullable()->after('reglas_operacion_pdf');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programas', function (Blueprint $table) {
            // Eliminar solo si existen
            if (Schema::hasColumn('programas', 'reglas_operacion_pdf')) {
                $table->dropColumn('reglas_operacion_pdf');
            }

            if (Schema::hasColumn('programas', 'guia_operativa_pdf')) {
                $table->dropColumn('guia_operativa_pdf');
            }
        });
    }
}
