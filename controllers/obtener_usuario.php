<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
verificarAutenticacion();
verificarTipoUsuario('Administrador');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID de usuario no especificado', 400);
    }

    $id = intval($_GET['id']);
    
    // Consulta para obtener datos básicos del usuario
    $stmt = $conn->prepare("SELECT 
                        u.id_Usuario, 
                        u.nombre, 
                        u.apellido, 
                        u.correo, 
                        u.telefono,
                        u.id_TipoUsuario,
                        u.id_Carrera,
                        tu.nombre_Usuario AS tipo_usuario,
                        c.n_Carrera AS carrera,
                        g.id_Grupo AS id_Grupo,
                        g.grupo,
                        g.semestre,
                        gb.codigo_Barra
                      FROM usuarios u
                      JOIN tipo_usuario tu ON u.id_TipoUsuario = tu.id_TipoUsuario
                      LEFT JOIN carrera c ON u.id_Carrera = c.id_Carrera
                      LEFT JOIN alumnos_Grupos ag ON u.id_Usuario = ag.id_Usuario
                      LEFT JOIN grupos g ON ag.id_Grupo = g.id_Grupo
                      LEFT JOIN generador_Barra gb ON u.id_Usuario = gb.id_Usuario
                      WHERE u.id_Usuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Usuario no encontrado', 404);
    }
    
    $usuario = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'usuario' => $usuario
    ]);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>