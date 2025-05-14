<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
verificarAutenticacion();
verificarTipoUsuario('Administrador');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Método no permitido', 405);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Datos JSON inválidos', 400);
    }
    
    $id = intval($data['id'] ?? 0);
    
    // Verificar si hay estudiantes en esta carrera
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM Usuarios WHERE id_Carrera = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['total'] > 0) {
        throw new Exception('No se puede eliminar la carrera porque tiene estudiantes asignados', 400);
    }
    
    $stmt = $conn->prepare("DELETE FROM Carrera WHERE id_Carrera = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Carrera eliminada correctamente'
        ]);
    } else {
        throw new Exception('Error al eliminar la carrera', 500);
    }
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>