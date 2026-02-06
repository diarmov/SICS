<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformesTable extends Migration
{
    public function up()
    {
        Schema::create('informes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programa_id')->constrained()->onDelete('cascade');
            $table->integer('numero_informe'); // 1, 2, 3, etc.
            $table->string('nombre');
            $table->string('archivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->boolean('entregado')->default(false);
            $table->timestamps();

            // Asegurar que no haya duplicados de numero_informe por programa
            $table->unique(['programa_id', 'numero_informe']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('informes');
    }
}
