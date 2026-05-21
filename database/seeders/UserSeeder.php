<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nombre'   => 'Admin',
            'apellido' => 'Sistema',
            'email'    => 'admin@hospedajes.com',
            'telefono' => '77771111',
            'rol'      => 'admin',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'nombre'   => 'Carlos',
            'apellido' => 'Ramirez',
            'email'    => 'propietario@hospedajes.com',
            'telefono' => '77772222',
            'rol'      => 'propietario',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'nombre'   => 'Maria',
            'apellido' => 'Lopez',
            'email'    => 'cliente@hospedajes.com',
            'telefono' => '77773333',
            'rol'      => 'cliente',
            'password' => Hash::make('password123'),
        ]);
    }
}