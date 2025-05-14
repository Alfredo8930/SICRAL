<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/barcode.php'; // Incluir la librería de códigos de barras

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener el código de barras del usuario
$user_id = $_SESSION['user_id'];
$query = "SELECT codigo_Barra FROM generador_barra WHERE id_Usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $codigo_barra = $row['codigo_Barra'];
} else {
    // Generar un nuevo código si no existe
    $codigo_barra = 'STD' . str_pad($user_id, 6, '0', STR_PAD_LEFT);
    $insert = "INSERT INTO generador_barra (codigo_Barra, id_Usuario) VALUES (?, ?)";
    $stmt2 = $conn->prepare($insert);
    $stmt2->bind_param("si", $codigo_barra, $user_id);
    $stmt2->execute();
    $stmt2->close();
}
$stmt->close();

// Configurar parámetros para el código de barras
$size = isset($_GET['size']) ? $_GET['size'] : 40; // Tamaño del código
$orientation = 'horizontal'; // Orientación
$code_type = 'code128'; // Tipo de código
$print = false; // No imprimir texto debajo
$sizefactor = 1.5; // Factor de tamaño

// Generar el código de barras usando la librería
barcode('', $codigo_barra, $size, $orientation, $code_type, $print, $sizefactor);
?>