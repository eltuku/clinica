<?php
$conex = mysqli_connect("localhost", "root", "", "clinica");
if (!$conex) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>