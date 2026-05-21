<?php

namespace Database\Seeders;

use App\Models\Hospedaje;
use App\Models\User;
use Illuminate\Database\Seeder;

class HospedajeSeeder extends Seeder
{
    public function run(): void
    {
        $propietario = User::where('rol', 'propietario')->first();

        $hospedajes = [
            [
                'nombre'       => 'Rancho El Paraíso',
                'tipo'         => 'rancho',
                'descripcion'  => 'Hermoso rancho rodeado de naturaleza, ideal para descansar y disfrutar en familia. Cuenta con piscina, áreas verdes y parrilladas.',
                'ubicacion'    => 'Km 45 Carretera a Santa Ana',
                'departamento' => 'Santa Ana',
                'precio_noche' => 150.00,
                'capacidad'    => 20,
                'estado'       => 'disponible',
            ],
            [
                'nombre'       => 'Hotel Vista Verde',
                'tipo'         => 'hotel',
                'descripcion'  => 'Hotel moderno con vista al volcán, habitaciones climatizadas, restaurante y servicio de spa.',
                'ubicacion'    => 'Avenida Principal, Santa Ana',
                'departamento' => 'Santa Ana',
                'precio_noche' => 80.00,
                'capacidad'    => 4,
                'estado'       => 'disponible',
            ],
            [
                'nombre'       => 'Casa de Playa Los Cóbanos',
                'tipo'         => 'casa',
                'descripcion'  => 'Casa frente al mar con acceso directo a la playa, terraza, cocina equipada y área de hamacas.',
                'ubicacion'    => 'Los Cóbanos, Acajutla',
                'departamento' => 'Sonsonate',
                'precio_noche' => 120.00,
                'capacidad'    => 10,
                'estado'       => 'disponible',
            ],
            [
                'nombre'       => 'Apartamento Centro Histórico',
                'tipo'         => 'apartamento',
                'descripcion'  => 'Cómodo apartamento en el centro histórico de San Salvador, ideal para viajes de negocios o turismo cultural.',
                'ubicacion'    => 'Centro Histórico, San Salvador',
                'departamento' => 'San Salvador',
                'precio_noche' => 50.00,
                'capacidad'    => 2,
                'estado'       => 'disponible',
            ],
            [
                'nombre'       => 'Rancho Las Brumas',
                'tipo'         => 'rancho',
                'descripcion'  => 'Rancho en las montañas de Chalatenango con clima fresco, fogatas y actividades al aire libre.',
                'ubicacion'    => 'Cerro El Pital, La Palma',
                'departamento' => 'Chalatenango',
                'precio_noche' => 200.00,
                'capacidad'    => 30,
                'estado'       => 'disponible',
            ],
        ];

        foreach ($hospedajes as $hospedaje) {
            Hospedaje::create(array_merge($hospedaje, ['user_id' => $propietario->id]));
        }
    }
}