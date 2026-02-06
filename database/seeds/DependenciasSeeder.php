<?php

use Illuminate\Database\Seeder;
use App\Dependencia;

class DependenciasSeeder extends Seeder
{
    public function run()
    {
        $dependencias = [
            ['dependencia' => 'Secretaría de la Función Pública', 'siglas' => 'SFP', 'activo' => true],
            ['dependencia' => 'Secretaría de Salud', 'siglas' => 'SSA', 'activo' => true],
            ['dependencia' => 'Secretaría de Desarrollo Social', 'siglas' => 'SEDESOL', 'activo' => true],
            ['dependencia' => 'Secretaría de Economía', 'siglas' => 'SE', 'activo' => true],
        ];

        foreach ($dependencias as $dependencia) {
            Dependencia::firstOrCreate(
                ['siglas' => $dependencia['siglas']],
                $dependencia
            );
        }

        $this->command->info('Dependencias creadas exitosamente!');
    }
}
