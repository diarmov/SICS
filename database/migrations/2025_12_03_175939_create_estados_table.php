<?php
// database/migrations/2024_01_01_000007_create_estados_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadosTable extends Migration
{
    public function up()
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->id('id_estado');
            $table->string('nombre', 100);
            $table->string('clave', 10)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('usuario_creacion')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('usuario_modificacion')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('usuario_creacion')->references('id')->on('users');
            $table->foreign('usuario_modificacion')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('estados');
    }
}
