<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Obtener lista de asistencias
$query = "SELECT a.id_Asistencia, m.n_Materia, l.n_Laboratorio, 
          DATE_FORMAT(pf.fecha, '%d/%m/%Y') as fecha, 
          TIME_FORMAT(a.hora_Entrada, '%H:%i') as entrada, 
          TIME_FORMAT(a.hora_Salida, '%H:%i') as salida,
          a.estado_Asistencia, u.nombre, u.apellido, u.id_Usuario
          FROM asistencias a
          JOIN practicas p ON a.id_Practica = p.id_Practica
          JOIN practicas_fechas pf ON p.id_Practica = pf.id_PracticaFecha
          JOIN materias m ON p.id_Materia = m.id_Materia
          JOIN laboratorio l ON p.id_Laboratorio = l.id_Laboratorio
          JOIN usuarios u ON a.id_Usuario = u.id_Usuario
          ORDER BY pf.fecha DESC, a.hora_Entrada DESC";
$result = mysqli_query($conn, $query);
$asistencias = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $asistencias[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRL - Asistencias</title>
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
              <h5 class="card-title mb-0">Registro de Asistencias</h5>
              <div class="d-flex">
                <input type="date" id="fechaFiltro" class="form-control me-2" style="width: 180px;">
                <button id="btnFiltrar" class="btn btn-primary">
                  <i class="fas fa-filter me-1"></i> Filtrar
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>No. Control</th>
                      <th>Alumno</th>
                      <th>Materia</th>
                      <th>Laboratorio</th>
                      <th>Fecha</th>
                      <th>Entrada</th>
                      <th>Salida</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($asistencias) > 0): ?>
                      <?php foreach ($asistencias as $asis): ?>
                        <tr>
                          <td><?= htmlspecialchars($asis['id_Usuario']) ?></td>
                          <td><?= htmlspecialchars($asis['nombre'] . ' ' . $asis['apellido']) ?></td>
                          <td><?= htmlspecialchars($asis['n_Materia']) ?></td>
                          <td><?= htmlspecialchars($asis['n_Laboratorio']) ?></td>
                          <td><?= htmlspecialchars($asis['fecha']) ?></td>
                          <td><?= htmlspecialchars($asis['entrada']) ?></td>
                          <td><?= htmlspecialchars($asis['salida']) ?></td>
                          <td>
                            <span class="badge <?= ($asis['estado_Asistencia'] == 'A') ? 'bg-success' : 'bg-danger' ?>">
                              <?= ($asis['estado_Asistencia'] == 'A') ? 'Asistió' : 'Faltó' ?>
                            </span>
                          </td>
                          <td>
                            <button class="btn btn-sm btn-info toggle-asistencia" 
                                    data-id="<?= $asis['id_Asistencia'] ?>" 
                                    data-estado="<?= $asis['estado_Asistencia'] ?>">
                              <i class="fas fa-toggle-<?= ($asis['estado_Asistencia'] == 'A') ? 'on' : 'off' ?>"></i>
                            </button>
                            <a href="detalle_asistencia.php?id=<?= $asis['id_Asistencia'] ?>" class="btn btn-sm btn-primary">
                              <i class="fas fa-info-circle"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="9" class="text-center">No hay registros de asistencia</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
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
      // Cambiar estado de asistencia
      document.querySelectorAll('.toggle-asistencia').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const estado = this.getAttribute('data-estado');
          const nuevoEstado = (estado == 'A') ? 'F' : 'A';
          
          if (confirm('¿Está seguro de cambiar el estado de esta asistencia?')) {
            fetch(`../controllers/toggle_asistencia.php?id=${id}&estado=${nuevoEstado}`)
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  location.reload();
                } else {
                  alert('Error al actualizar la asistencia');
                }
              });
          }
        });
      });
      
      // Filtrar por fecha
      document.getElementById('btnFiltrar').addEventListener('click', function() {
        const fecha = document.getElementById('fechaFiltro').value;
        if (fecha) {
          window.location.href = `asistencias.php?fecha=${fecha}`;
        } else {
          window.location.href = `asistencias.php`;
        }
      });
    });
  </script>
</body>
</html>