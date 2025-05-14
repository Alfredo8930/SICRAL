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
    $required = ['nombre', 'apellido', 'correo', 'tipo_usuario', 'codigo_barras', 'contrasena'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    // Verificar si el correo ya existe
    $stmt = $conn->prepare("SELECT id_Usuario FROM Usuarios WHERE correo = ?");
    $stmt->bind_param("s", $data['correo']);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        throw new Exception('El correo electrónico ya está registrado', 409);
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Insertar nuevo usuario (sin código de barras)
        $stmt = $conn->prepare("INSERT INTO usuarios (
            nombre, 
            apellido, 
            correo, 
            telefono, 
            id_TipoUsuario, 
            id_Carrera,
            contrasena
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $idCarrera = !empty($data['id_carrera']) ? $data['id_carrera'] : null;
        
        // En producción, usa password_hash(): password_hash($data['contrasena'], PASSWORD_DEFAULT)
        $stmt->bind_param(
            "ssssiis", 
            $data['nombre'],
            $data['apellido'],
            $data['correo'],
            $data['telefono'],
            $data['tipo_usuario'],
            $idCarrera,
            $data['contrasena'] // En producción, usar hash
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Error al crear el usuario: ' . $stmt->error, 500);
        }
        
        $userId = $stmt->insert_id;
        
        // Insertar código de barras en tabla generador_Barra
        $stmt = $conn->prepare("INSERT INTO generador_barra (codigo_Barra, id_Usuario) VALUES (?, ?)");
        $stmt->bind_param("is", $data['codigo_barras'], $userId);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al registrar código de barras: ' . $stmt->error, 500);
        }

        // Si es estudiante, asignar a grupo
        if (!empty($data['id_grupo']) && !empty($data['semestre'])) {
            $stmt = $conn->prepare("INSERT INTO alumnos_grupos (id_Usuario, id_Grupo) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $data['id_grupo']);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al asignar grupo: ' . $stmt->error, 500);
            }
        }
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'user_id' => $userId
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        throw $e;
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