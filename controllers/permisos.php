<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Obtener lista de permisos
$query = "SELECT pe.id_Permiso, m.n_Materia, u.nombre, u.apellido, 
          DATE_FORMAT(pe.fecha_Solicitud, '%d/%m/%Y %H:%i') as solicitud,
          DATE_FORMAT(pe.fecha_Ausencia, '%d/%m/%Y') as ausencia,
          pe.estado_Permiso, pe.justificacion
          FROM permisos pe
          JOIN practicas p ON pe.id_Practica = p.id_Practica
          JOIN materias m ON p.id_Materia = m.id_Materia
          JOIN usuarios u ON pe.id_Usuario = u.id_Usuario
          ORDER BY pe.fecha_Solicitud DESC";
$result = mysqli_query($conn, $query);
$permisos = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $permisos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRL - Permisos</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/styles-2.css">
</head>

<body>
  <div class="container-fluid p-0">
    <div class="row g-0">
      <!-- Sidebar -->
      <?php include "../views/nab_var.php"?>
      
      <!-- Main Content -->
      <div class="col-md-10 main-content">
        <div class="header">
          <h1 class="text-center">Sistema de Registro en Laboratorios (SRL)</h1>
        </div>
        
        <div class="container mt-3">
          <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">Solicitudes de Permisos</h5>
              <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                  <i class="fas fa-plus-circle me-1"></i> Nueva Solicitud
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="nuevo_permiso.php">Solicitud Individual</a></li>
                  <li><a class="dropdown-item" href="nuevo_permiso_grupo.php">Solicitud Grupal</a></li>
                </ul>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Alumno</th>
                      <th>Materia</th>
                      <th>Fecha Solicitud</th>
                      <th>Fecha Ausencia</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($permisos) > 0): ?>
                      <?php foreach ($permisos as $perm): ?>
                        <tr>
                          <td><?= htmlspecialchars($perm['id_Permiso']) ?></td>
                          <td><?= htmlspecialchars($perm['nombre'] . ' ' . $perm['apellido']) ?></td>
                          <td><?= htmlspecialchars($perm['n_Materia']) ?></td>
                          <td><?= htmlspecialchars($perm['solicitud']) ?></td>
                          <td><?= htmlspecialchars($perm['ausencia']) ?></td>
                          <td>
                            <?php 
                              $badgeClass = '';
                              $estadoText = '';
                              switch($perm['estado_Permiso']) {
                                case 'PEN': 
                                  $badgeClass = 'bg-warning text-dark';
                                  $estadoText = 'Pendiente';
                                  break;
                                case 'APR': 
                                  $badgeClass = 'bg-success';
                                  $estadoText = 'Aprobado';
                                  break;
                                case 'REC': 
                                  $badgeClass = 'bg-danger';
                                  $estadoText = 'Rechazado';
                                  break;
                                default: 
                                  $badgeClass = 'bg-secondary';
                                  $estadoText = 'Desconocido';
                              }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $estadoText ?></span>
                          </td>
                          <td>
                            <button class="btn btn-sm btn-primary view-perm" 
                                    data-id="<?= $perm['id_Permiso'] ?>" 
                                    data-justificacion="<?= htmlspecialchars($perm['justificacion']) ?>">
                              <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($_SESSION['user_type'] == 'admin' && $perm['estado_Permiso'] == 'PEN'): ?>
                              <button class="btn btn-sm btn-success approve-perm" data-id="<?= $perm['id_Permiso'] ?>">
                                <i class="fas fa-check"></i>
                              </button>
                              <button class="btn btn-sm btn-danger reject-perm" data-id="<?= $perm['id_Permiso'] ?>">
                                <i class="fas fa-times"></i>
                              </button>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="7" class="text-center">No hay solicitudes de permisos</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal para ver justificación -->
        <div class="modal fade" id="justificacionModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Justificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p id="justificacionText"></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
        
        <footer class="mt-5 text-center text-muted">
          <p>&copy; 2025 Sistema de Registro en Laboratorios. Todos los derechos reservados.</p>
        </footer>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Ver justificación
      document.querySelectorAll('.view-perm').forEach(button => {
        button.addEventListener('click', function() {
          const justificacion = this.getAttribute('data-justificacion');
          document.getElementById('justificacionText').textContent = justificacion;
          const modal = new bootstrap.Modal(document.getElementById('justificacionModal'));
          modal.show();
        });
      });
      
      // Aprobar permiso
      document.querySelectorAll('.approve-perm').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          if (confirm('¿Está seguro de aprobar este permiso?')) {
            fetch(`../controllers/aprobar_permiso.php?id=${id}`)
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  location.reload();
                } else {
                  alert('Error al aprobar el permiso');
                }
              });
          }
        });
      });
      
      // Rechazar permiso
      document.querySelectorAll('.reject-perm').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          if (confirm('¿Está seguro de rechazar este permiso?')) {
            fetch(`../controllers/rechazar_permiso.php?id=${id}`)
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  location.reload();
                } else {
                  alert('Error al rechazar el permiso');
                }
              });
          }
        });
      });
    });
  </script>
</body>
</html>