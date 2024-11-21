<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        //filtramos las notificaciones por el id del usuario por fecha mas reciente
        $notifications = Notification::where('id_usuario', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    // Marcar una notificación como leída
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        // Cambiar el estado a leído
        $notification->is_read = true;
        $notification->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Notificación marcada como leída.',
            'data' => $notification
        ]);
    }

    // Eliminar una notificación
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        // Eliminar la notificación
        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notificación eliminada correctamente.'
        ]);
    }
    // funciones de Admin
    public function EnviarNotificacionesGeneral(Request $request)
    {
        $usuario = Usuario::all();
        $mesaje = $request->mesaje;
        foreach ($usuario as $usuarios) {
            $notification = new Notification();
            $notification->id_usuario = $usuarios->id;
            $notification->type = $request->type;
            $notification->mesaje = $mesaje;
            $notification->is_read = false;
            $notification->save();
        }
        return response()->json(['message' => 'Notificaciones enviadas correctamente.'], 200);
    }
}
