<?php
session_start();
include("con_db.php");
include("config.php");

// LOGIN CON GOOGLE
if (isset($_GET["code"])) {
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
    if (!isset($token['error'])) {
        $google_client->setAccessToken($token['access_token']);
        $_SESSION['access_token'] = $token['access_token'];

        $google_service = new Google_Service_Oauth2($google_client);
        $data = $google_service->userinfo->get();

        $_SESSION['user_first_name'] = $data['given_name'] ?? '';
        $_SESSION['user_last_name'] = $data['family_name'] ?? '';
        $_SESSION['user_email_address'] = $data['email'] ?? '';
        $_SESSION['user_gender'] = $data['gender'] ?? '';
        $_SESSION['user_image'] = $data['picture'] ?? '';

        header("Location: sistema.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Error al autenticar con Google.";
        header("Location: index.php");
        exit();
    }
}

// LOGIN LOCAL (usuario / contraseña)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    if (empty($usuario) || empty($password)) {
        $_SESSION['login_error'] = "Por favor completá todos los campos.";
        header("Location: index.php");
        exit();
    }

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conex->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['usuario'] = $usuario;
            header("Location: sistema.php");
            exit();
        }
    }

    $_SESSION['login_error'] = "Usuario o contraseña incorrectos.";
    header("Location: index.php");
    exit();
}
?>
