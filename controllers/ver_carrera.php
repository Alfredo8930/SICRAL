<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
verificarAutenticacion();
verificarTipoUsuario('Administrador');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID de carrera no especificado', 400);
    }

    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT 
                            c.id_Carrera, 
                            c.n_Carrera,
                            COUNT(u.id_Usuario) as total_estudiantes
                          FROM Carrera c
                          LEFT JOIN Usuarios u ON c.id_Carrera = u.id_Carrera
                          WHERE c.id_Carrera = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Carrera no encontrada', 404);
    }
    
    $carrera = $result->fetch_assoc();
    
    // Obtener estudiantes de esta carrera
    $stmt = $conn->prepare("SELECT 
                            u.id_Usuario, 
                            u.nombre, 
                            u.apellido, 
                            u.correo,
                            g.grupo,
                            g.semestre
                          FROM Usuarios u
                          LEFT JOIN alumnos_grupos ag ON u.id_Usuario = ag.id_Usuario
                          LEFT JOIN grupos g ON ag.id_Grupo = g.id_Grupo
                          WHERE u.id_Carrera = ? AND u.id_TipoUsuario = (
                              SELECT id_TipoUsuario FROM tipo_Usuario WHERE nombre_Usuario = 'Estudiante'
                          )");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $estudiantes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'carrera' => $carrera,
        'estudiantes' => $estudiantes
    ]);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>