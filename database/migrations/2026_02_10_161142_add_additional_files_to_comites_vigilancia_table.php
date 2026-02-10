<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFilesToComitesVigilanciaTable extends Migration
{
    public function up()
    {
        Schema::table('comites_vigilancia', function (Blueprint $table) {
            $table->string('lista_asistencia')->nullable()->after('archivo_minuta');
            $table->text('material_difusion')->nullable()->after('lista_asistencia'); // JSON
            $table->text('fotografias_reunion')->nullable()->after('material_difusion'); // JSON
        });
    }

    public function down()
    {
        Schema::table('comites_vigilancia', function (Blueprint $table) {
            $table->dropColumn(['lista_asistencia', 'material_difusion', 'fotografias_reunion']);
        });
    }
}
