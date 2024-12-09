<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImagenController extends Controller
{
    public function store(Request $request)
    {
        // Validar la imagen
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,avif,pdf|max:2048', 
        ]);

        // Obtener el archivo de la imagen del request
        $image = $request->file('image');

        // Subir la imagen a Cloudinary
        $uploadedImage = Cloudinary::upload($image->getRealPath(), [
            'folder' => 'images', // Opcional: carpeta en Cloudinary
        ]);

        // Obtener la URL segura de la imagen subida
        $imageUrl = $uploadedImage->getSecurePath();

        // Guardar la URL de la imagen en la base de datos
        $user = $request->user();
        $user->foto_perfil = $imageUrl;
        $user->save();

        return response()->json(['message' => 'Imagen guardada correctamente.', 'url' => $imageUrl], 200);
    }

    public function ver(Request $request)
    {
        $user = $request->user();
        $fotoDefault = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y';

        // Verificar si el usuario tiene una foto de perfil
        $fotoPerfil = $user->foto_perfil ?: $fotoDefault;

        return response()->json([
            'user' => [
                'foto_perfil' => $fotoPerfil,
            ]
        ], 200);
    }
}
