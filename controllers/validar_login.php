<?php
session_start();
header('Content-Type: application/json');

try {
    require __DIR__ . '/../includes/db.php';
    
    if (!isset($conn)) {
        throw new Exception('Error de conexión a la base de datos');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Correo electrónico no válido', 400);
    }

    if (strlen($password) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres', 400);
    }

    // Consulta con el nombre correcto del campo "contraseña"
    $query = "SELECT u.id_Usuario, u.nombre, u.apellido, u.contraseña, 
                     t.nombre_Usuario as tipo_usuario
              FROM Usuarios u
              JOIN tipo_Usuario t ON u.id_TipoUsuario = t.id_TipoUsuario
              WHERE u.correo = ?";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error, 500);
    }

    $stmt->bind_param("s", $email);
    $executed = $stmt->execute();
    
    if (!$executed) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error, 500);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Credenciales incorrectas', 401);
    }

    $usuario = $result->fetch_assoc();

    // Verificación de contraseña (ajustar según cómo estén almacenadas)
    if (!password_verify($password, $usuario['contraseña'])) {
        throw new Exception('Credenciales incorrectas', 401);
    }

    $_SESSION['usuario'] = [
        'id' => $usuario['id_Usuario'],
        'nombre' => $usuario['nombre'],
        'apellido' => $usuario['apellido'],
        'correo' => $email,
        'tipo_usuario' => $usuario['tipo_usuario'],
        'ultimo_acceso' => time()
    ];

    $redirectMap = [
        'Administrador' => '../admin/dashboard.php',
        'Tutor' => '../tutor/inicio.php',
        'Alumno' => '../alumno/inicio.php'
    ];

    $redirect = $redirectMap[$usuario['tipo_usuario']] ?? '../index.php';

    echo json_encode([
        'success' => true,
        'redirect' => $redirect,
        'user' => [
            'nombre' => $usuario['nombre'],
            'tipo' => $usuario['tipo_usuario']
        ]
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode() ?: 500
    ]);
    
    error_log('['.date('Y-m-d H:i:s').'] Error en validar_login.php: ' . $e->getMessage());
}
?>