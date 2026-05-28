<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use App\Models\Reserva;
use Illuminate\Console\Command;

class ExpirarReservas extends Command
{
    protected $signature   = 'reservas:expirar';
    protected $description = 'Expira reservas pendientes cuya fecha de inicio ya pasó';

    public function handle()
    {
        $reservasExpiradas = Reserva::where('estado_propietario', 'pendiente')
            ->where('estado', 'pendiente')
            ->where('fecha_inicio', '<', now()->format('Y-m-d'))
            ->get();

        foreach ($reservasExpiradas as $reserva) {
            $reserva->update([
                'estado'             => 'cancelada',
                'estado_propietario' => 'rechazada',
            ]);

            // Notificar al cliente
            Notificacion::enviar(
                $reserva->user_id,
                '⏰ Reserva expirada',
                'Tu solicitud de reserva en ' . $reserva->hospedaje->nombre . ' expiró porque el propietario no respondió a tiempo.',
                'warning',
                route('reservas.index')
            );
        }

        $this->info("Se expiraron {$reservasExpiradas->count()} reservas.");
    }
}