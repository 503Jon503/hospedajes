<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Refund;

class PagoController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function checkout($reservaId)
    {
        $reserva = Reserva::with('hospedaje')->findOrFail($reservaId);

        if ($reserva->user_id !== session('user_data.id')) {
            return redirect()->route('reservas.index')->with('error', 'No autorizado');
        }

        if ($reserva->pago_estado === 'retenido') {
            return redirect()->route('reservas.index')->with('error', 'Esta reserva ya fue pagada');
        }

        return view('pagos.checkout', compact('reserva'));
    }

    public function procesar(Request $request, $reservaId)
    {
        $reserva = Reserva::with(['hospedaje', 'hospedaje.propietario'])->findOrFail($reservaId);

        try {
            $paymentIntent = PaymentIntent::create([
                'amount'   => $reserva->total * 100,
                'currency' => 'usd',
                'metadata' => [
                    'reserva_id' => $reserva->id,
                    'hospedaje'  => $reserva->hospedaje->nombre,
                    'cliente_id' => session('user_data.id'),
                ],
                'payment_method' => $request->payment_method_id,
                'confirm'        => true,
                'return_url'     => route('pagos.exitoso', $reserva->id),
            ]);

            $reserva->update([
                'pago_estado'           => 'retenido',
                'stripe_payment_intent' => $paymentIntent->id,
            ]);

            // Notificar al propietario que el cliente pagó
            Notificacion::enviar(
                $reserva->hospedaje->user_id,
                '💰 Pago recibido',
                session('user_data.nombre') . ' ' . session('user_data.apellido') . ' realizó el pago de $' . number_format($reserva->total, 2) . ' por ' . $reserva->hospedaje->nombre . '. El dinero está retenido hasta que el cliente confirme su llegada.',
                'success',
                route('reservas.index')
            );

            return redirect()->route('pagos.exitoso', $reserva->id);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function exitoso($reservaId)
    {
        $reserva = Reserva::with('hospedaje')->findOrFail($reservaId);
        return view('pagos.exitoso', compact('reserva'));
    }

    public function confirmarLlegada($reservaId)
    {
        $reserva = Reserva::with(['hospedaje', 'hospedaje.propietario'])->findOrFail($reservaId);

        if ($reserva->user_id !== session('user_data.id')) {
            return back()->with('error', 'No autorizado');
        }

        if ($reserva->cliente_confirmo_llegada) {
            return back()->with('error', 'Ya confirmaste tu llegada anteriormente');
        }

        if (now()->format('Y-m-d') < $reserva->fecha_inicio->format('Y-m-d')) {
            return back()->with('error', 'No puedes confirmar tu llegada antes de la fecha de inicio (' . $reserva->fecha_inicio->format('d/m/Y') . ')');
        }

        $reserva->update([
            'cliente_confirmo_llegada'   => true,
            'fecha_confirmacion_llegada' => now(),
            'pago_estado'                => 'liberado',
        ]);

        // Notificar al propietario que el pago fue liberado
        Notificacion::enviar(
            $reserva->hospedaje->user_id,
            '✅ Pago liberado',
            session('user_data.nombre') . ' ' . session('user_data.apellido') . ' confirmó su llegada a ' . $reserva->hospedaje->nombre . '. El pago de $' . number_format($reserva->total, 2) . ' ha sido liberado a tu cuenta bancaria.',
            'success',
            route('reservas.index')
        );

        return back()->with('success', '¡Llegada confirmada! El pago ha sido liberado automáticamente al propietario.');
    }

    public function reembolsar($reservaId)
    {
        $reserva = Reserva::with(['hospedaje', 'cliente'])->findOrFail($reservaId);

        if ($reserva->pago_estado !== 'retenido') {
            return back()->with('error', 'No se puede reembolsar este pago');
        }

        try {
            if ($reserva->stripe_payment_intent) {
                Refund::create([
                    'payment_intent' => $reserva->stripe_payment_intent,
                ]);
            }

            $reserva->update([
                'estado'      => 'cancelada',
                'pago_estado' => 'reembolsado',
            ]);

            // Notificar al cliente que fue reembolsado
            Notificacion::enviar(
                $reserva->user_id,
                '💸 Reserva cancelada y reembolso procesado',
                'El propietario canceló tu reserva en ' . $reserva->hospedaje->nombre . '. El monto de $' . number_format($reserva->total, 2) . ' será reembolsado a tu tarjeta en 5-10 días hábiles.',
                'warning',
                route('reservas.index')
            );

            return back()->with('success', '¡Reserva cancelada y pago reembolsado al cliente exitosamente!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al reembolsar: ' . $e->getMessage());
        }
    }
}