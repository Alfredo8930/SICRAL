<?php
// controllers/total_usuario.php
require_once '../includes/db.php'; // Asegúrate de que la ruta sea correcta

try {

    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    $sql = "SELECT COUNT(id_Usuario) as total FROM usuarios";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error en la preparación: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    $total_usuarios = $data['total'] ?? 0; // Valor por defecto si hay error
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Error al contar usuarios: " . $e->getMessage());
    $total_usuarios = 0; // Valor por defecto en caso de error
}
?>