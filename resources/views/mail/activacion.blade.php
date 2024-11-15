<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Nuevo Usuario</title>
</head>
<body>
    <h1>Hola usuario {{ $user->email }}</h1>
    <p>Se ha intentado registrar un nuevo usuario en nuestro sistema de Tinacos-los-dantes.</p>
    <p>Para autorizarlo, ingrese a el siguiente enlace para proseguir con la activacion de su cuenta: <a href="{{ $url }}/{{ $user->id }}/">Activar Cuenta.</a></p>
    <br><br>
    {{$url}}

</body>
</html>