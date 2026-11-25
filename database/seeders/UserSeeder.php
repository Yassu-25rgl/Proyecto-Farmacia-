<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::pluck('id', 'name');

        $users = [
            ['role' => 'Administrador', 'name' => 'Administrador', 'dni' => '12345678', 'phone' => '987654321', 'tuition' => null, 'email' => 'admin@sanjuan.com'],
            ['role' => 'Químico farmacéutico', 'name' => 'Rocío Quispe', 'dni' => '23456781', 'phone' => '912345678', 'tuition' => null, 'email' => 'rquispe@sanjuan.com'],
            ['role' => 'Cajero', 'name' => 'Carlos Mendoza', 'dni' => '34567812', 'phone' => '900000001', 'tuition' => null, 'email' => 'cmendoza@sanjuan.com'],
            ['role' => 'Cajero', 'name' => 'Lucía Torres', 'dni' => '45678123', 'phone' => '900000002', 'tuition' => null, 'email' => 'ltorres@sanjuan.com'],
        ];

        foreach ($users as $user) {
            if (!isset($roles[$user['role']])) {
                continue;
            }

            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'rol_id' => $roles[$user['role']],
                    'name' => $user['name'],
                    'dni' => $user['dni'],
                    'phone' => $user['phone'],
                    'tuition' => $user['tuition'],
                    'password' => bcrypt('password'),
                ]
            );
        }
    }
}