<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class SuperUsuarioSeeder extends Seeder
{
    public function run()
    {
        // Verificar si ya existe un SuperUsuario
        $existingUser = User::where('email', 'superusuario@sics.com')->first();

        if ($existingUser) {
            $this->command->info('El SuperUsuario ya existe!');
            return;
        }

        // Crear el usuario SuperUsuario
        $user = User::create([
            'name' => 'Super',
            'nombre' => 'Super',
            'apellido_paterno' => 'Usuario',
            'apellido_materno' => 'Sistema',
            'email' => 'superusuario@sics.com',
            'password' => Hash::make('password'),
            'dependencia_id' => 1,
            'activo' => true,
        ]);

        // Asignar el rol
        $user->assignRole('SuperUsuario');

        $this->command->info('SuperUsuario creado exitosamente!');
        $this->command->info('Email: superusuario@sics.com');
        $this->command->info('Contraseña: password');
    }
}
