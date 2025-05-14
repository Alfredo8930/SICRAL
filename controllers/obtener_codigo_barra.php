<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT codigo_Barra FROM generador_barra WHERE id_Usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['codigo' => $row['codigo_Barra']]);
} else {
    echo json_encode(['error' => 'Código no encontrado']);
}

$stmt->close();
$conn->close();
?>