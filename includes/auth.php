<?php
// includes/auth.php
session_start();
require_once 'db.php';

function verificarAutenticacion() {
    if (!isset($_SESSION['user_id'])) {
        if (basename($_SERVER['PHP_SELF']) != 'login.php') {
            header("Location: ../index.php?error=Por favor inicie sesión");
            exit();
        }
    }
}

function verificarTipoUsuario($tipoRequerido) {
    verificarAutenticacion();
    if ($_SESSION['user_type'] != $tipoRequerido) {
        header("Location: ../index.php?error=Acceso no autorizado");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correo'])) {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    $sql = "SELECT u.*, c.n_Carrera as carrera_nombre, 
                   t.nombre_Usuario as tipo_usuario,
                   g.grupo as grupo_nombre,
                   g.semestre as semestre_nombre,
                   gb.codigo_Barra as codigo_barras
            FROM usuarios u
            LEFT JOIN carrera c ON u.id_Carrera = c.id_Carrera
            LEFT JOIN tipo_usuario t ON u.id_TipoUsuario = t.id_TipoUsuario
            LEFT JOIN alumnos_grupos ag ON u.id_Usuario = ag.id_Usuario
            LEFT JOIN grupos g ON ag.id_Grupo = g.id_Grupo
            LEFT JOIN generador_barra gb ON u.id_Usuario = gb.id_Usuario
            WHERE u.correo = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if ($contrasena === $user['contrasena']) {
            $_SESSION['user_id'] = $user['id_Usuario'];
            $_SESSION['user_email'] = $user['correo'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_lastname'] = $user['apellido'];
            $_SESSION['user_phone'] = $user['telefono'];
            $_SESSION['user_type'] = $user['tipo_usuario'];
            $_SESSION['user_career'] = $user['carrera_nombre'];
            $_SESSION['user_group'] = $user['grupo_nombre'];
            $_SESSION['user_semester'] = $user['semestre_nombre'];
            $_SESSION['codigo_barras'] = $user['codigo_barras'] ?? null;
            
            // Generar código de barras si no existe
            if (empty($_SESSION['codigo_barras'])) {
                $codigo_barra = 'STD' . str_pad($user['id_Usuario'], 6, '0', STR_PAD_LEFT);
                
                $insert_sql = "INSERT INTO generador_barra (codigo_Barra, id_Usuario) VALUES (?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("si", $codigo_barra, $user['id_Usuario']);
                if ($insert_stmt->execute()) {
                    $_SESSION['codigo_barras'] = $codigo_barra;
                }
                $insert_stmt->close();
            }
            
            switch($user['tipo_usuario']) {
                case 'Administrador':
                    header("Location: ../views/admin.php");
                    break;
                case 'Profesor':
                    header("Location: ../views/profesor.php");
                    break;
                default:
                    header("Location: ../views/main_alumno.php");
            }
            exit();
        } else {
            header("Location: ../index.php?error=Contraseña incorrecta");
            exit();
        }
    } else {
        header("Location: ../index.php?error=Usuario no encontrado");
        exit();
    }
    
    $stmt->close();
}
?>