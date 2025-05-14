<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

try {
    verificarAutenticacion();
    verificarTipoUsuario('Administrador');

    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Método no permitido', 405);
    }

    // Obtener ID del grupo desde query parameters
    if (!isset($_GET['id'])) {
        throw new Exception('ID de grupo no proporcionado', 400);
    }

    $grupoId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($grupoId === false || $grupoId < 1) {
        throw new Exception('ID de grupo no válido', 400);
    }

    // Iniciar transacción para eliminación en cascada
    $conn->begin_transaction();

    try {
        // 1. Eliminar alumnos del grupo (relación alumnos_Gruppo)
        $deleteAlumnos = "DELETE FROM alumnos_grupos WHERE id_Grupo = ?";
        $stmt = $conn->prepare($deleteAlumnos);
        $stmt->bind_param("i", $grupoId);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar alumnos del grupo: ' . $stmt->error, 500);
        }

        // 2. Eliminar tutores del grupo (relación Tutores)
        $deleteTutores = "DELETE FROM tutores WHERE id_Grupo = ?";
        $stmt = $conn->prepare($deleteTutores);
        $stmt->bind_param("i", $grupoId);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar tutores del grupo: ' . $stmt->error, 500);
        }

        // 3. Eliminar el grupo
        $deleteGrupo = "DELETE FROM grupos WHERE id_Grupo = ?";
        $stmt = $conn->prepare($deleteGrupo);
        $stmt->bind_param("i", $grupoId);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al eliminar el grupo: ' . $stmt->error, 500);
        }

        if ($stmt->affected_rows === 0) {
            throw new Exception('El grupo no existe', 404);
        }

        // Confirmar transacción
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Grupo eliminado exitosamente',
            'grupo_id' => $grupoId
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