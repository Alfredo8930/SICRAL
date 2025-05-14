<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (!isset($_GET['id'])) {
    $response['message'] = 'ID no proporcionado';
    echo json_encode($response);
    exit();
}

$id_practica = (int)$_GET['id'];

try {
    // Validar y obtener datos del POST (similar a guardar_practica.php)
    
    mysqli_begin_transaction($conn);
    
    // Actualizar tabla practicas
    $query = "UPDATE practicas SET 
              id_Materia = ?, id_Laboratorio = ?, id_Grupo = ?, id_Usuario = ?
              WHERE id_Practica = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiiii", 
        $_POST['materia'], $_POST['laboratorio'], $_POST['grupo'], $_POST['profesor'], $id_practica);
    mysqli_stmt_execute($stmt);
    
    // Actualizar tabla practicas_fechas
    $query = "UPDATE practicas_fechas SET 
              fecha = ?, hora_Inicio = ?, hora_Fin = ?
              WHERE id_PracticaFecha = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssi", 
        $_POST['fecha'], $_POST['hora_inicio'], $_POST['hora_fin'], $id_practica);
    mysqli_stmt_execute($stmt);
    
    mysqli_commit($conn);
    
    $response['success'] = true;
    $response['message'] = 'Práctica actualizada correctamente';
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    $response['message'] = 'Error al actualizar: ' . $e->getMessage();
}

echo json_encode($response);
?>