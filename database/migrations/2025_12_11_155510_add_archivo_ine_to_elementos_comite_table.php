<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivoIneToElementosComiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('elementos_comite', function (Blueprint $table) {
            $table->string('archivo_ine')->nullable()->after('tipo_elemento');
        });
    }

    public function down()
    {
        Schema::table('elementos_comite', function (Blueprint $table) {
            $table->dropColumn('archivo_ine');
        });
    }
}
