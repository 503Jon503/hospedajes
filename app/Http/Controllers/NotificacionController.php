<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::where('user_id', session('user_data.id'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Marcar todas como leídas
        Notificacion::where('user_id', session('user_data.id'))
            ->where('leida', false)
            ->update(['leida' => true]);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida($id)
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('user_id', session('user_data.id'))
            ->firstOrFail();

        $notificacion->update(['leida' => true]);

        if ($notificacion->url) {
            return redirect($notificacion->url);
        }

        return back();
    }

    public function eliminar($id)
    {
        Notificacion::where('id', $id)
            ->where('user_id', session('user_data.id'))
            ->delete();

        return back()->with('success', 'Notificación eliminada');
    }

    public static function contar($userId)
    {
        return Notificacion::where('user_id', $userId)
            ->where('leida', false)
            ->count();
    }
}