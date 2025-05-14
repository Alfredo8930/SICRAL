<?php
/**
 * Script de recuperación de contraseña
 * Sistema de Registro en Laboratorios (SRL)
 */

// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'sral';

// Inicializar variables
$message = '';
$alertType = '';

// Verificar si se recibieron datos POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validar que se recibió el email
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        $message = 'Por favor, ingrese su correo electrónico.';
        $alertType = 'danger';
    } else {
        // Obtener y sanitizar email
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'El formato del correo electrónico no es válido.';
            $alertType = 'danger';
        } else {
            try {
                // Conectar a la base de datos
                $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Verificar si el correo existe
                $stmt = $conn->prepare("SELECT id, nombre, apellido FROM usuarios WHERE email = ? AND activo = 1");
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Generar token único
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Guardar token en la base de datos
                    $resetStmt = $conn->prepare("INSERT INTO password_reset (usuario_id, token, expiry, creado) VALUES (?, ?, ?, NOW())");
                    $resetStmt->execute([$user['id'], $token, $expiry]);
                    
                    // URL de restablecimiento
                    $resetUrl = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                    
                    // Preparar correo electrónico
                    $to = $email;
                    $subject = "Recuperación de contraseña - Sistema de Registro en Laboratorios";
                    
                    $message_body = "
                    <html>
                    <head>
                        <title>Recuperación de contraseña - SRL</title>
                    </head>
                    <body>
                        <h2>Sistema de Registro en Laboratorios</h2>
                        <p>Estimado/a {$user['nombre']} {$user['apellido']},</p>
                        <p>Recibimos una solicitud para restablecer su contraseña. Si usted no solicitó este cambio, puede ignorar este correo.</p>
                        <p>Para restablecer su contraseña, haga clic en el siguiente enlace:</p>
                        <p><a href=\"{$resetUrl}\">{$resetUrl}</a></p>
                        <p>Este enlace es válido por 1 hora.</p>
                        <p>Saludos cordiales,<br>Equipo del Sistema de Registro en Laboratorios</p>
                    </body>
                    </html>
                    ";
                    
                    // Cabeceras para envío de correo HTML
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: noreply@sistemalaboratorios.com" . "\r\n";
                    
                    // Enviar correo
                    if (mail($to, $subject, $message_body, $headers)) {
                        $message = "Se ha enviado un enlace de recuperación a su correo electrónico. Por favor revise su bandeja de entrada.";
                        $alertType = 'success';
                    } else {
                        $message = "No se pudo enviar el correo de recuperación. Por favor, contacte al administrador.";
                        $alertType = 'danger';
                    }
                    
                } else {
                    // No revelar si el correo existe o no por seguridad
                    $message = "Si su correo está registrado en nuestro sistema, recibirá un enlace para restablecer su contraseña.";
                    $alertType = 'info';
                }
                
            } catch (PDOException $e) {
                $message = "Error en el servidor. Por favor, intente más tarde.";
                $alertType = 'danger';
                error_log('Error de base de datos: ' . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Clave - Sistema de Registro en Laboratorios</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-header {
            background-color: #0d3b34;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .recovery-container {
            max-width: 450px;
            margin: 0 auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .logo {
            width: 100px;
            margin: 0 auto 20px;
            display: block;
        }
        .btn-submit {
            background-color: #6b513d;
            border-color: #6b513d;
            color: white;
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }
        .btn-submit:hover {
            background-color: #5a4433;
            border-color: #5a4433;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-header">
        <div class="container">
            <h1 class="text-center">Sistema de Registro en Laboratorios (SRL)</h1>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="recovery-container">
                    <h3 class="text-center mb-4">Recuperar Clave de Acceso</h3>
                    
                    <div class="text-center mb-4">
                        <img src="../assets/img/logo_itss.png" alt="Logo Institucional" class="logo">
                    </div>
                    
                    <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <p class="text-center mb-4">Ingrese su correo electrónico para recibir un enlace de recuperación.</p>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="mb-4">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@dominio.com" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-submit">Enviar Solicitud</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="../index.php" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Volver al inicio de sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="mt-5 text-center text-muted">
        <div class="container">
            <p>&copy; 2025 Sistema de Registro en Laboratorios. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap y Scripts JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>