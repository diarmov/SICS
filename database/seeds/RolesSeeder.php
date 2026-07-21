<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Limpiar permisos y roles existentes (opcional - solo si quieres empezar desde cero)
        Permission::query()->delete();
        Role::query()->delete();

        // Crear permisos
        $permisos = [
            'usuarios.*',
            'usuarios.create',
            'usuarios.view',
            'usuarios.edit',
            'usuarios.delete',
            'dependencias.*',
            'comites.*',
            'programas.*',
            'beneficiarios.*',
            'usuarios.enlace'
        ];

        foreach ($permisos as $permiso) {
            Permission::create(['name' => $permiso]);
        }

        // Crear roles y asignar permisos
        $superUsuario = Role::create(['name' => 'SuperUsuario']);
        $superUsuario->givePermissionTo(Permission::all());

        $adminCS = Role::create(['name' => 'Organo_Estatal_de_Control']);
        $adminCS->givePermissionTo(Permission::all());

        $coordinador = Role::create(['name' => 'Instancia_Normativa']);
        $coordinador->givePermissionTo([
            'programas.*',
            'comites.*',
            'usuarios.create',
            'usuarios.view',
            'usuarios.enlace'
        ]);

        $enlace = Role::create(['name' => 'Instancia_Ejecutora']);
        $enlace->givePermissionTo(['programas.*', 'comites.*']);

        $this->command->info('Roles y permisos creados exitosamente!');
    }
}
