<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;
use Carbon\Carbon;

class NotificationController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $notifications = Notification::where('id_usuario', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                $createdTime = Carbon::parse($notification->created_at);
                $diffInMinutes = $createdTime->diffInMinutes(Carbon::now());

                if ($diffInMinutes < 60) {
                    $formattedTime = $diffInMinutes . ' minuto' . ($diffInMinutes > 1 ? 's' : '');
                } elseif ($diffInMinutes < 1440) { // 60 minutos * 24 horas
                    $hours = floor($diffInMinutes / 60);
                    $formattedTime = $hours . ' hora' . ($hours > 1 ? 's' : '');
                } elseif ($diffInMinutes < 525600) { // 1440 minutos * 365 días
                    $days = floor($diffInMinutes / 1440);
                    $formattedTime = $days . ' día' . ($days > 1 ? 's' : '');
                } else {
                    $years = floor($diffInMinutes / 525600); // 525600 minutos = 1 año
                    $formattedTime = $years . ' año' . ($years > 1 ? 's' : '');
                }

                $notification->formatted_created_at = $formattedTime;

                return $notification;
            });

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
    public function index2(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 10); // Número de notificaciones por página, por defecto 10

        $notifications = Notification::where('id_usuario', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Transformamos cada notificación para agregar el tiempo formateado
        $notifications->getCollection()->transform(function ($notification) {
            $createdTime = \Carbon\Carbon::parse($notification->created_at);
            $diffInMinutes = $createdTime->diffInMinutes(\Carbon\Carbon::now());

            if ($diffInMinutes < 60) {
                $formattedTime = $diffInMinutes . ' minuto' . ($diffInMinutes > 1 ? 's' : '');
            } elseif ($diffInMinutes < 1440) {
                $hours = floor($diffInMinutes / 60);
                $formattedTime = $hours . ' hora' . ($hours > 1 ? 's' : '');
            } elseif ($diffInMinutes < 525600) {
                $days = floor($diffInMinutes / 1440);
                $formattedTime = $days . ' día' . ($days > 1 ? 's' : '');
            } else {
                $years = floor($diffInMinutes / 525600);
                $formattedTime = $years . ' año' . ($years > 1 ? 's' : '');
            }

            $notification->formatted_created_at = $formattedTime;
            return $notification;
        });

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    public function countNotifications()
    {
        $user = Auth::user();
        $unreadCount = Notification::where('id_usuario', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
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
            'success' => true,
            'message' => 'Notificación eliminada correctamente.'
        ]);
    }
    // funciones de Admin
    public function EnviarNotificacionesGeneral(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'mesaje' => 'required',
            'type' => 'required',
            'title' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 400);
        }

        $usuario = Usuario::all();
        $mesaje = $request->mesaje;
        foreach ($usuario as $usuarios) {
            $notification = new Notification();
            $notification->id_usuario = $usuarios->id;
            $notification->type = $request->type;
            $notification->title = $request->title;
            $notification->message = $mesaje;
            $notification->is_read = false;
            $notification->save();
        }
        return response()->json(['message' => 'Notificaciones enviadas correctamente.'], 200);
    }
    public function gettype()
    {
        $type = ['info', 'alert'];
        return response()->json(['types' => $type], 200);
    }
}
