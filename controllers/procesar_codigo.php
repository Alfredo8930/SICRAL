<?php
// controllers/procesar_codigo.php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$codigo = $data['codigo'] ?? '';

if (empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Código no proporcionado']);
    exit();
}

// Buscar el usuario por código de barras
$query = "SELECT u.* FROM usuarios u 
          JOIN generador_barra gb ON u.id_Usuario = gb.id_Usuario
          WHERE gb.codigo_Barra = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    
    // Registrar asistencia (ejemplo)
    $insert = "INSERT INTO asistencias (id_Usuario, fecha, hora_entrada) 
               VALUES (?, CURDATE(), CURTIME())";
    $stmt2 = $conn->prepare($insert);
    $stmt2->bind_param("i", $usuario['id_Usuario']);
    $stmt2->execute();
    
    echo json_encode([
        'success' => true,
        'nombre' => $usuario['nombre'] . ' ' . $usuario['apellido'],
        'message' => 'Asistencia registrada'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
}

$stmt->close();
$conn->close();
?>