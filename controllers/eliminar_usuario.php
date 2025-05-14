<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

try {
    verificarAutenticacion();
    verificarTipoUsuario('Administrador');

    // Verificar si la solicitud es DELETE
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Método no permitido', 405);
    }

    // Obtener el ID del usuario desde la URL
    $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
    if (!$id || $id <= 0) {
        throw new Exception('ID de usuario no válido', 400);
    }

    // Iniciar transacción para eliminación en cascada
    $conn->begin_transaction();

    try {
        // 1. Eliminar código de barras asociado
        $stmt = $conn->prepare("DELETE FROM generador_Barra WHERE id_Usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // 2. Eliminar de alumnos_Gruppo si es estudiante
        $stmt = $conn->prepare("DELETE FROM alumnos_grupos WHERE id_Usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // 3. Eliminar de Tutores si es tutor
        $stmt = $conn->prepare("DELETE FROM tutores WHERE id_Usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // 4. Eliminar permisos asociados
        $stmt = $conn->prepare("DELETE FROM permisos WHERE id_Usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // 5. Eliminar asistencias asociadas
        $stmt = $conn->prepare("DELETE FROM asistencias WHERE id_Usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // 6. Finalmente eliminar el usuario
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_Usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception('Usuario no encontrado', 404);
        }

        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuario eliminado correctamente',
            'user_id' => $id
        ]);

    } catch (Exception $e) {
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

// No es necesario cerrar $conn si usas conexión persistente
?>