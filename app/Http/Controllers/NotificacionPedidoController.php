<?php

namespace App\Http\Controllers;

use App\Models\NotificacionPedido;
use Illuminate\Http\Request;

class NotificacionPedidoController extends Controller
{
    public function pendientes(Request $request)
    {
        $notificaciones = NotificacionPedido::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('leida_at')
            ->orderBy('id')
            ->get(['id', 'tipo', 'mensaje']);

        return response()->json(['data' => $notificaciones]);
    }

    public function marcarLeida(Request $request, NotificacionPedido $notificacion)
    {
        abort_unless($notificacion->user_id === $request->user()->id, 403);

        $notificacion->update(['leida_at' => now()]);

        return response()->json(['message' => 'Notificación cerrada.']);
    }
}
