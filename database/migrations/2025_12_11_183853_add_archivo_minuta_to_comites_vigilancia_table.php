<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivoMinutaToComitesVigilanciaTable extends Migration
{
    public function up()
    {
        Schema::table('comites_vigilancia', function (Blueprint $table) {
            $table->string('archivo_minuta')->nullable()->after('activo');
        });
    }

    public function down()
    {
        Schema::table('comites_vigilancia', function (Blueprint $table) {
            $table->dropColumn('archivo_minuta');
        });
    }
}
