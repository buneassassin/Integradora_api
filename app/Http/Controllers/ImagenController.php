<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImagenController extends Controller
{
    public function store(Request $request)
    {
        // Validar la imagen
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB máximo
        ]);
    
        // sacamos la imagen del request
        $image = $request->file('image');
    
        $imagePath = $image->store('images', 'public');
    
        // Guardar la ruta de la imagen en la base de datos
        $user = $request->user();
        $user->foto_perfil = $imagePath;
        $user->save();
     
        return response()->json(['message' => 'Imagen guardada correctamente.'], 200);

    }
    public function ver(Request $request) {
        $user = $request->user();
    
        // Asegúrate de que $user->foto_perfil tenga solo el nombre del archivo, sin rutas adicionales
        $imageUrl = url('storage/images/' . basename($user->foto_perfil));
    
        return response()->json([
            'user' => [
                'foto_perfil' => $imageUrl,
            ]
        ], 200);
    }
    
    
    
    

}
