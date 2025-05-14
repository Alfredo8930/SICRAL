<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
verificarAutenticacion();
verificarTipoUsuario('Administrador');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Obtener datos del usuario para edición
        if (!isset($_GET['id'])) {
            throw new Exception('ID de usuario no especificado', 400);
        }
        
        $id = intval($_GET['id']);
        
        $query = "SELECT 
                    u.id_Usuario, 
                    u.nombre, 
                    u.apellido, 
                    u.correo, 
                    u.telefono,
                    u.id_TipoUsuario,
                    u.id_Carrera,
                    ag.id_Grupo,
                    ag.semestre,
                    gb.codigo_Barra
                FROM usuarios u
                LEFT JOIN alumnos_grupos ag ON u.id_Usuario = ag.id_Usuario
                LEFT JOIN generador_Barra gb ON u.id_Usuario = gb.id_Usuario
                WHERE u.id_Usuario = ?";
        
        $stmt = $conn->prepare($query);
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
        
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Actualizar usuario
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Datos JSON inválidos', 400);
        }
        
        if (!isset($_GET['id'])) {
            throw new Exception('ID de usuario no proporcionado', 400);
        }
        
        $userId = $_GET['id'];
        $conn->begin_transaction();

        try {
            // 1. Actualizar datos básicos del usuario
            $updateFields = [];
            $updateValues = [];
            $types = '';
            
            $fields = [
                'nombre' => 's',
                'apellido' => 's',
                'correo' => 's',
                'telefono' => 's',
                'id_TipoUsuario' => 'i',
                'id_Carrera' => 'i'
            ];
            
            foreach ($fields as $field => $type) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $updateValues[] = $data[$field];
                    $types .= $type;
                }
            }
            
            // Actualizar contraseña si se proporcionó
            if (!empty($data['contrasena'])) {
                $updateFields[] = "contrasena = ?";
                $updateValues[] = password_hash($data['contrasena'], PASSWORD_DEFAULT);
                $types .= 's';
            }
            
            if (!empty($updateFields)) {
                $sql = "UPDATE usuarios SET " . implode(', ', $updateFields) . " WHERE id_Usuario = ?";
                $types .= 'i';
                $updateValues[] = $userId;
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$updateValues);
                
                if (!$stmt->execute()) {
                    throw new Exception('Error al actualizar usuario: ' . $stmt->error, 500);
                }
            }

            // 2. Actualizar código de barras
            if (isset($data['codigo_Barra'])) {
                // Verificar si ya existe un código de barras
                $stmt = $conn->prepare("SELECT id_Generador FROM generador_barra WHERE id_Usuario = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $stmt->store_result();
                
                if ($stmt->num_rows > 0) {
                    // Actualizar existente
                    $stmt = $conn->prepare("UPDATE generador_barra SET codigo_Barra = ? WHERE id_Usuario = ?");
                    $stmt->bind_param("si", $data['codigo_Barra'], $userId);
                } else {
                    // Insertar nuevo
                    $stmt = $conn->prepare("INSERT INTO generador_barra (codigo_Barra, id_Usuario) VALUES (?, ?)");
                    $stmt->bind_param("si", $data['codigo_Barra'], $userId);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception('Error al actualizar código de barras: ' . $stmt->error, 500);
                }
            }

            // 3. Manejar grupo (solo para estudiantes)
            if (isset($data['id_TipoUsuario']) && $data['id_TipoUsuario'] == 1) { // 1 = Estudiante
                $idGrupo = $data['id_Grupo'] ?? null;
                $semestre = $data['semestre'] ?? null;
                
                // Verificar si ya está en un grupo
                $stmt = $conn->prepare("SELECT id_AlumnoGrupo FROM alumnos_grupos WHERE id_Usuario = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $stmt->store_result();
                
                if ($stmt->num_rows > 0) {
                    if ($idGrupo && $semestre) {
                        // Actualizar grupo y semestre
                        $stmt = $conn->prepare("UPDATE alumnos_grupos SET id_Grupo = ? WHERE id_Usuario = ?");
                        $stmt->bind_param("ii", $idGrupo, $userId);
                    } 
                    
                    if (isset($stmt) && !$stmt->execute()) {
                        throw new Exception('Error al actualizar grupo: ' . $stmt->error, 500);
                    }
                } elseif ($idGrupo && $semestre) {
                    // Insertar nuevo registro de grupo
                    $stmt = $conn->prepare("INSERT INTO alumnos_grupos (id_Usuario, id_Grupo) VALUES (?, ?)");
                    $stmt->bind_param("ii", $userId, $idGrupo);
                    
                    if (!$stmt->execute()) {
                        throw new Exception('Error al asignar grupo: ' . $stmt->error, 500);
                    }
                }
            } else {
                // Si no es estudiante, eliminar de grupos si existiera
                $stmt = $conn->prepare("DELETE FROM alumnos_grupos WHERE id_Usuario = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
            }

            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'user_id' => $userId
            ]);
            
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    } else {
        throw new Exception('Método no permitido', 405);
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