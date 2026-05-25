<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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
        $reserva = Reserva::with('hospedaje')->findOrFail($reservaId);

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
        $reserva = Reserva::findOrFail($reservaId);

        if ($reserva->user_id !== session('user_data.id')) {
            return back()->with('error', 'No autorizado');
        }

        if ($reserva->cliente_confirmo_llegada) {
            return back()->with('error', 'Ya confirmaste tu llegada anteriormente');
        }

        if (now()->format('Y-m-d') < $reserva->fecha_inicio->format('Y-m-d')) {
            return back()->with('error', 'No puedes confirmar tu llegada antes de la fecha de inicio ('.$reserva->fecha_inicio->format('d/m/Y').')');
        }

        $reserva->update([
            'cliente_confirmo_llegada'   => true,
            'fecha_confirmacion_llegada' => now(),
        ]);

        return back()->with('success', '¡Llegada confirmada! El propietario ya puede liberar el pago.');
    }

    public function liberar($reservaId)
    {
        $reserva = Reserva::with(['hospedaje', 'cliente'])->findOrFail($reservaId);

        if ($reserva->pago_estado !== 'retenido') {
            return back()->with('error', 'El pago no está retenido');
        }

        if (!$reserva->cliente_confirmo_llegada) {
            return back()->with('error', 'El cliente aún no ha confirmado su llegada');
        }

        $reserva->update(['pago_estado' => 'liberado']);

        return back()->with('success', '¡Pago liberado exitosamente!');
    }
}