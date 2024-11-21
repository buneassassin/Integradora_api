<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Nuevo Usuario</title>
    <style>
        /* Estilo general */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff; /* Fondo azul claro */
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff; /* Fondo blanco para el contenido */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        h1 {
            color: #0044cc; /* Azul oscuro */
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            color: #333333; /* Gris oscuro */
            font-size: 16px;
            line-height: 1.6;
        }
        a {
            color: #0066cc; /* Azul vivo */
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #666666; /* Gris claro */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hola usuario {{ $user->email }}</h1>
        <p>
            ¡Gracias por crear una cuenta en la plataforma **Tinacon**.
        </p>
        <p>
            Para autorizarlo, ingrese al siguiente enlace para proseguir con la activación de su cuenta:
        </p>
        <p>
            <a href="{{ $url }}/{{ $user->id }}/" target="_blank">Activar Cuenta</a>
        </p>
        <div class="footer">
            {{$url}}
        </div>
    </div>
</body>
</html>
