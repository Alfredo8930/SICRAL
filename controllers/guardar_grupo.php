<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

try {
    verificarAutenticacion();
    verificarTipoUsuario('Administrador');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Datos JSON inválidos', 400);
    }

    // Validar datos requeridos
    $required = ['semestre', 'grupo'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    // Validar semestre (1-12)
    $semestre = filter_var($data['semestre'], FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'max_range' => 12
        ]
    ]);
    
    if ($semestre === false) {
        throw new Exception('El semestre debe ser un número entre 1 y 12', 400);
    }

    // Sanitizar nombre de grupo
    $grupo = trim($data['grupo']);
    if (strlen($grupo) > 10) {
        throw new Exception('El nombre del grupo no puede exceder 10 caracteres', 400);
    }

    // Verificar si el grupo ya existe
    $stmt = $conn->prepare("SELECT id_Grupo FROM grupos WHERE semestre = ? AND grupo = ?");
    $stmt->bind_param("is", $semestre, $grupo);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        throw new Exception('Este grupo ya existe para el semestre especificado', 409);
    }

    // Insertar nuevo grupo
    $stmt = $conn->prepare("INSERT INTO grupos (semestre, grupo) VALUES (?, ?)");
    $stmt->bind_param("is", $semestre, $grupo);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al crear el grupo: ' . $stmt->error, 500);
    }
    
    $grupoId = $stmt->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Grupo creado exitosamente',
        'grupo_id' => $grupoId,
        'data' => [
            'semestre' => $semestre,
            'grupo' => $grupo
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
}
?>