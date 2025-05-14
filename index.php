<?php
session_start();
if (isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SRL </title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="assets/css/style-login.css">
</head>

<body>
    <div class="login-header">
        <div class="container">
            <h2 class="text-center">Sistema de Registro en Laboratorios (SRL)</h2>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <h3 class="text-center mb-4">Iniciar Sesión</h3>
                    
                    <div class="text-center mb-4">
                        <img src="assets/img/logo_itss.png" alt="Logo Institucional" class="logo">
                    </div>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="includes/auth.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico*</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="correo" placeholder="ejemplo@dominio.com" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña*</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="1234567890"required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-login">Aceptar</button>
                        </div>
                    </form>
                    
                    <div class="forgot-password">
                        <a href="controllers/recuperar_clave.php" class="text-decoration-none">Recuperar Clave de Acceso</a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar botón de toggle de contraseña
            const toggleBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('contrasena');
            
            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
            
            // Cierre automático de alertas después de 5 segundos
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>