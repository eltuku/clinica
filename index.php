<?php
session_start();
include('config.php');

// Si ya hay sesión activa, redirige a sistema.php
if (isset($_SESSION['usuario']) || isset($_SESSION['access_token'])) {
    header("Location: sistema.php");
    exit();
}

// Botón de Google
$login_button = '';
if (!isset($_SESSION['access_token'])) {
    $login_button = '<a href="' . $google_client->createAuthUrl() . '" style="background: #dd4b39; border-radius: 5px; color: white; display: block; font-weight: bold; padding: 20px; text-align: center; text-decoration: none; width: 200px;">Login With Google</a>';
}

// Mostrar errores si existen
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
<div class="card p-4 shadow-lg" style="min-width: 350px;">
    <h3 class="text-center mb-4">Iniciar Sesión</h3>

    <?php if ($error): ?>
        <div class='alert alert-danger'><?= $error ?></div>
    <?php endif; ?>

    <form action="login.php" method="post">
        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        <p><a href="recuperar_clave.php">¿Olvidaste tu contraseña?</a></p>
    </form>

    <div class="mt-3 text-center">
        <?= $login_button ?>
    </div>

    <div class="mt-3 text-center">
        <a href="register.php">Registrarse</a>
    </div>
</div>
</body>
</html>
