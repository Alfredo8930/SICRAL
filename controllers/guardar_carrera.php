<?php
require_once '../includes/db.php'; // Asegúrate de que la ruta sea correcta
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Verificar autenticación y permisos
verificarAutenticacion();
verificarTipoUsuario('Administrador');

try {
    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Datos JSON inválidos', 400);
    }

    $nombre = trim($data['nombre'] ?? '');
    
    if (empty($nombre)) {
        throw new Exception('El nombre de la carrera es requerido', 400);
    }

    // Verificar si la carrera ya existe
    $check = $conn->prepare("SELECT id_Carrera FROM Carrera WHERE n_Carrera = ?");
    $check->bind_param("s", $nombre);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        throw new Exception('Esta carrera ya existe', 409);
    }

    // Insertar nueva carrera
    $stmt = $conn->prepare("INSERT INTO Carrera (n_Carrera) VALUES (?)");
    $stmt->bind_param("s", $nombre);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Carrera creada exitosamente',
            'carrera_id' => $stmt->insert_id
        ]);
    } else {
        throw new Exception('Error al ejecutar la consulta', 500);
    }
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
}
?>