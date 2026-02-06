<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumeroInformesToProgramasTable extends Migration
{
    public function up()
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->integer('numero_informes')->default(0)->after('periodo');
        });
    }

    public function down()
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn('numero_informes');
        });
    }
}
