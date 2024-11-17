<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Contenedor principal del email */
        .email-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: #4CAF50;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 0.8rem 1.2rem;
            border-radius: 5px;
            font-size: 1rem;
            margin-top: 1rem;
        }

        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Recuperar Contraseña</h1>
        <p>Hola {{ $user->name }},</p>
        <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
        <a href="{{ $url }}">Restablecer Contraseña</a>
        <p>Este enlace expirará en 5 minutos.</p>
    </div>
</body>
</html>
