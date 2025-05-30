<?php
include("con_db.php");

$usuario = $_POST['usuario'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($usuario) && !empty($password) && !empty($email)) {
    $password_cifrada = password_hash($password, PASSWORD_DEFAULT);
    $consulta = "INSERT INTO usuarios (usuario, email, password) VALUES ('$usuario', '$email', '$password_cifrada')";
    $resultado = mysqli_query($conex, $consulta);

    if ($resultado) {
        echo "<p style='color:green; text-align:center;'>¡Usuario registrado exitosamente!</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error al registrar el usuario.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Registro de Usuario</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f0f4f8;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }
  form {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    width: 320px;
  }
  h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
  }
  input[type="text"],
  input[type="email"],
  input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 12px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
  }
  input[type="submit"],
  button {
    background-color: #4CAF50;
    color: white;
    padding: 12px;
    width: 100%;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    margin-top: 12px;
  }
  input[type="submit"]:hover,
  button:hover {
    background-color: #45a049;
  }
  p {
    margin: 10px 0 0;
  }
</style>
</head>
<body>
<form method="POST" action="">
  <h2>Registro</h2>
  <input type="text" name="usuario" placeholder="Usuario" required />
  <input type="email" name="email" placeholder="Correo electrónico" required />
  <input type="password" name="password" placeholder="Contraseña" required />
  <input type="submit" value="Registrarse" />
  <button type="button" onclick="history.back()">Volver</button>
</form>
</body>
</html>
