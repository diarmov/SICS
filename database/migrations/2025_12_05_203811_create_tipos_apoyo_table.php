<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTiposApoyoTable extends Migration
{
    public function up()
    {
        Schema::create('tipos_apoyo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->date('fecha_alta')->nullable(); // Sin default en la columna
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar datos iniciales comunes
        $now = now();
        $today = date('Y-m-d');

        DB::table('tipos_apoyo')->insert([
            [
                'nombre' => 'Apoyo Alimentario',
                'fecha_alta' => $today,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nombre' => 'Apoyo Económico',
                'fecha_alta' => $today,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nombre' => 'Becas Educativas',
                'fecha_alta' => $today,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nombre' => 'Material Didáctico',
                'fecha_alta' => $today,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nombre' => 'Servicios de Salud',
                'fecha_alta' => $today,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nombre' => 'Infraestructura',
                'fecha_alta' => $today,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'nombre' => 'Capacitación',
                'fecha_alta' => $today,
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('tipos_apoyo');
    }
}
