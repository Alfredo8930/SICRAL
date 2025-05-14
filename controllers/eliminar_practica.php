<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

session_start();
if (!isset($_SESSION['user_email'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    // Verificar que se recibió el ID
    if (!isset($_GET['id'])) {
        throw new Exception('ID de práctica no proporcionado');
    }

    $id_practica = (int)$_GET['id'];

    // Validar que el ID sea numérico y positivo
    if ($id_practica <= 0) {
        throw new Exception('ID de práctica inválido');
    }

    // Iniciar transacción para asegurar integridad de datos
    mysqli_begin_transaction($conn);

    // 1. Primero eliminar de asistencias (por la restricción de clave foránea)
    $query = "DELETE FROM asistencias WHERE id_Practica = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $id_practica);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error al eliminar asistencias: ' . mysqli_stmt_error($stmt));
    }

    // 2. Luego eliminar de practicas_fechas
    $query = "DELETE FROM practicas_fechas WHERE id_Practica = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $id_practica);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error al eliminar horarios: ' . mysqli_stmt_error($stmt));
    }

    // 3. Finalmente eliminar de practicas
    $query = "DELETE FROM practicas WHERE id_Practica = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $id_practica);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error al eliminar práctica: ' . mysqli_stmt_error($stmt));
    }

    // Confirmar la transacción si todo fue bien
    mysqli_commit($conn);

    $response['success'] = true;
    $response['message'] = 'Práctica eliminada correctamente';

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if (isset($conn) && mysqli_thread_id($conn)) {
        mysqli_rollback($conn);
    }
    
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log('Error al eliminar práctica: ' . $e->getMessage());
}

echo json_encode($response);
?>