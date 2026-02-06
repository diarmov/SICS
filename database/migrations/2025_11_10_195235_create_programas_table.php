<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgramasTable extends Migration
{
    public function up()
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dependencia_id')->constrained();
            $table->string('nombre');
            $table->string('archivo_pdf')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->year('periodo');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('programas');
    }
}
