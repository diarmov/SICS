<?php
// database/migrations/2024_01_01_000003_create_comites_vigilancia_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComitesVigilanciaTable extends Migration
{
    public function up()
    {
        Schema::create('comites_vigilancia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dependencia_id');
            $table->unsignedBigInteger('programa_id');
            $table->string('nombre');
            $table->unsignedBigInteger('id_estado')->nullable();
            $table->unsignedBigInteger('id_municipio')->nullable();
            $table->unsignedBigInteger('id_localidad')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('dependencia_id')->references('id')->on('dependencias');
            $table->foreign('programa_id')->references('id')->on('programas');
            $table->foreign('id_estado')->references('id_estado')->on('estados');
            $table->foreign('id_municipio')->references('id_municipio')->on('municipios');
            $table->foreign('id_localidad')->references('id_localidad')->on('localidades');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comites_vigilancia');
    }
}
