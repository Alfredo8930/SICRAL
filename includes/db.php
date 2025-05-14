<?php
$host = 'localhost';       // o IP del servidor
$user = 'root';            // tu usuario de MySQL
$pass = '';                // tu contraseña de MySQL
$dbname = 'sral';   // el nombre de tu base de datos

$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
