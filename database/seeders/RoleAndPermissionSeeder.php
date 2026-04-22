<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear cache de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Usuarios
            'users.index',
            'users.show',
            'users.create',
            'users.update',
            'users.delete',

            // Roles
            'roles.index',
            'roles.show',
            'roles.create',
            'roles.update',
            'roles.delete',

            // Permisos
            'permissions.index',
            'permissions.assign',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear roles y asignar permisos
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisor->givePermissionTo([
            'users.index',
            'users.show',
        ]);

        $usuario = Role::firstOrCreate(['name' => 'usuario', 'guard_name' => 'web']);
    }
}
