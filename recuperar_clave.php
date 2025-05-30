<?php
include("con_db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conex, $_POST['email']);

    // Buscar usuario por email
    $consulta = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conex, $consulta);

    if (mysqli_num_rows($resultado) > 0) {
        // Generar nueva contraseña aleatoria
        $nueva_clave = bin2hex(random_bytes(4)); // 8 caracteres hexadecimales
        $clave_encriptada = password_hash($nueva_clave, PASSWORD_DEFAULT);

        // Actualizar contraseña en la base de datos
        $update = "UPDATE usuarios SET password = '$clave_encriptada' WHERE email = '$email'";
        mysqli_query($conex, $update);

        // Mostrar la nueva clave (modo desarrollo)
        $mensaje = "<p style='color:green; text-align:center;'><strong>Contraseña restablecida correctamente.</strong></p>";
        $mensaje .= "<p style='text-align:center;'>Tu nueva contraseña es: <code>$nueva_clave</code></p>";
        $mensaje .= "<p style='text-align:center;'>Iniciá sesión y luego cámbiala si querés.</p>";
    } else {
        $mensaje = "<p style='color:red; text-align:center;'>Email no encontrado.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Recuperar Contraseña</title>
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
  input[type="email"] {
    width: 100%;
    padding: 12px;
    margin: 12px 0 20px 0;
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
  code {
    background-color: #eee;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
  }
</style>
</head>
<body>
<form method="POST" action="">
  <h2>Recuperar contraseña</h2>
  <input type="email" name="email" placeholder="Correo electrónico" required />
  <input type="submit" value="Restablecer contraseña" />
  <button type="button" onclick="history.back()">Volver</button>
  <?php
    if (isset($mensaje)) {
        echo $mensaje;
    }
  ?>
</form>
</body>
</html>
