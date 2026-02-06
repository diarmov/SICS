<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElementosComiteTable extends Migration
{
    public function up()
    {
        Schema::create('elementos_comite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comite_vigilancia_id');
            $table->string('nombre_completo');
            $table->string('tipo_elemento');
            $table->timestamps();

            // Clave foránea corregida: referencia a 'comites_vigilancia'
            $table->foreign('comite_vigilancia_id')
                ->references('id')
                ->on('comites_vigilancia')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('elementos_comite');
    }
}
