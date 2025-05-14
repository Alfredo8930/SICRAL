<?php
header('Content-Type: application/json');
require_once 'includes/db.php';

$codigo = $_GET['codigo'] ?? null;

if (!$codigo || !preg_match('/^[a-zA-Z0-9\-]+$/', $codigo)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Código no válido']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT u.id_Usuario, u.nombre, u.apellido, u.correo, u.telefono, c.n_Carrera
        FROM generador_barra gb
        JOIN usuarios u ON gb.id_Usuario = u.id_Usuario
        JOIN carrera c ON u.id_Carrera = c.id_Carrera
        WHERE gb.codigo_barra = ?
        LIMIT 1
    ");
    $stmt->execute([$codigo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Alumno no encontrado']);
    } else {
        echo json_encode([
            'success' => true,
            'id_Usuario' => $usuario['id_Usuario'],
            'nombre' => $usuario['nombre'] . ' ' . $usuario['apellido'],
            'correo' => $usuario['correo'],
            'telefono' => $usuario['telefono'],
            'carrera' => $usuario['n_Carrera']
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en servidor']);
}