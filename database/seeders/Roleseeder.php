<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class Roleseeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'description' => 'Acceso total al sistema.'],
            ['name' => 'Químico farmacéutico', 'description' => 'Gestiona medicamentos y reportes.'],
            ['name' => 'Cajero', 'description' => 'Registra clientes y ventas.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}