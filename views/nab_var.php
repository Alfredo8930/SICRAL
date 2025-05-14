<!-- Sidebar -->
<div class="col-md-2 sidebar animate-slide-down">
  <div class="logo-container">
    <img src="../assets/img/logo_itss.png" alt="Logo" class="logo">
  </div>
  
  <?php
  // Obtener la URL actual
  $current_url = basename($_SERVER['REQUEST_URI']);
  // Si hay parámetros GET, eliminarlos para la comparación
  $current_page = strtok($current_url, '?');
  
  // Función para verificar si el enlace debe estar activo
  function isActive($page, $current) {
    return $page === $current ? 'active' : '';
  }
  ?>
  
  <nav class="nav flex-column px-2">
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('admin.php', $current_page); ?>" href="../views/admin.php">
        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
      </a>
    </div>

    <div class="nav-item">
      <a class="nav-link <?php echo isActive('lista_usuarios.php', $current_page); ?>" href="../controllers/lista_usuarios.php">
        <i class="fas fa-users me-2"></i> Usuarios
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('carreras_inf.php', $current_page); ?>" href="../controllers/carreras_inf.php">
        <i class="fas fa-graduation-cap me-2"></i> Carreras
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('lista_materias.php', $current_page); ?>" href="../controllers/lista_materias.php">
        <i class="fas fa-book me-2"></i> Materias
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('listar_grupos.php', $current_page); ?>" href="../controllers/listar_grupos.php">
        <i class="fas fa-users-cog me-2"></i> Grupos
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('listar_practicas.php', $current_page); ?>" href="../controllers/menu_practicas.php">
        <i class="fas fa-flask me-2"></i> Prácticas
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('listar_tutores.php', $current_page); ?>" href="../controllers/listar_tutores.php">
        <i class="fas fa-chalkboard-teacher me-2"></i> Tutores
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('listar_laboratorios.php', $current_page); ?>" href="../controllers/listar_laboratorios.php">
        <i class="fas fa-vial me-2"></i> Laboratorios
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('asistencias.php', $current_page); ?>" href="../controllers/asistencias.php">
        <i class="fas fa-clipboard-check me-2"></i> Asistencias
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('permisos.php', $current_page); ?>" href="../controllers/permisos.php">
        <i class="fas fa-key me-2"></i> Permisos
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('generar_reportes.php', $current_page); ?>" href="../controllers/generar_reportes.php">
        <i class="fas fa-chart-bar me-2"></i> Reportes
      </a>
    </div>
    
    <div class="nav-item">
      <a class="nav-link <?php echo isActive('codigo_barra.php', $current_page); ?>" href="codigo_barra.php">
        <i class="fas fa-barcode me-2"></i> Código de barra
      </a>
    </div>
    
    <div class="nav-item mt-3">
      <a class="nav-link" href="../includes/logout.php">
        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
      </a>
    </div>
  </nav>
</div>