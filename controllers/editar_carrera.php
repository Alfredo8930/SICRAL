<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
verificarAutenticacion();
verificarTipoUsuario('Administrador');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Obtener datos de la carrera
        if (!isset($_GET['id'])) {
            throw new Exception('ID de carrera no especificado', 400);
        }
        
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM Carrera WHERE id_Carrera = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Carrera no encontrada', 404);
        }
        
        echo json_encode([
            'success' => true,
            'carrera' => $result->fetch_assoc()
        ]);
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Actualizar carrera
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Datos JSON inválidos', 400);
        }
        
        $id = intval($data['id'] ?? 0);
        $nombre = trim($data['nombre'] ?? '');
        
        if (empty($nombre)) {
            throw new Exception('El nombre de la carrera es requerido', 400);
        }
        
        $stmt = $conn->prepare("UPDATE Carrera SET n_Carrera = ? WHERE id_Carrera = ?");
        $stmt->bind_param("si", $nombre, $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Carrera actualizada correctamente'
            ]);
        } else {
            throw new Exception('Error al actualizar la carrera', 500);
        }
    } else {
        throw new Exception('Método no permitido', 405);
    }
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>