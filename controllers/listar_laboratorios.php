<?php
  session_start();
  if (!isset($_SESSION['user_email'])) {
      header("Location: login.php");
      exit();
  }
  require_once '../includes/db.php';
  
  // Obtener lista de laboratorios
  $query = "SELECT l.id_Laboratorio, l.n_Laboratorio, l.capacidad, l.disponible FROM laboratorio l ORDER BY l.n_Laboratorio";
  $result = mysqli_query($conn, $query);
  $laboratorios = [];
  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $laboratorios[] = $row;
    }
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRL - Laboratorios</title>
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
              <h5 class="card-title mb-0">Listado de Laboratorios</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoLaboratorioModal">
                <i class="fas fa-plus-circle me-1"></i> Nuevo Laboratorio
              </button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Capacidad</th>
                      <th>Disponible</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($laboratorios) > 0): ?>
                      <?php foreach ($laboratorios as $lab): ?>
                        <tr>
                          <td><?= htmlspecialchars($lab['id_Laboratorio']) ?></td>
                          <td><?= htmlspecialchars($lab['n_Laboratorio']) ?></td>
                          <td><?= htmlspecialchars($lab['capacidad']) ?></td>
                          <td>
                            <span class="badge <?= ($lab['disponible'] == 1) ? 'bg-success' : 'bg-danger' ?>">
                              <?= ($lab['disponible'] == 1) ? 'Disponible' : 'No disponible' ?>
                            </span>
                          </td>
                          <td>
                            <button class="btn btn-sm btn-warning edit-lab" 
                                    data-id="<?= $lab['id_Laboratorio'] ?>" 
                                    data-nombre="<?= htmlspecialchars($lab['n_Laboratorio']) ?>" 
                                    data-capacidad="<?= htmlspecialchars($lab['capacidad']) ?>"
                                    data-disponible="<?= htmlspecialchars($lab['disponible']) ?>">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-info toggle-disponible" 
                                    data-id="<?= $lab['id_Laboratorio'] ?>" 
                                    data-disponible="<?= $lab['disponible'] ?>">
                              <i class="fas fa-toggle-<?= ($lab['disponible'] == 1) ? 'on' : 'off' ?>"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-lab" data-id="<?= $lab['id_Laboratorio'] ?>">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center">No hay laboratorios registrados</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal Nuevo Laboratorio -->
        <div class="modal fade" id="nuevoLaboratorioModal" tabindex="-1" aria-labelledby="nuevoLaboratorioModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="nuevoLaboratorioModalLabel">Registrar Nuevo Laboratorio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="formNuevoLaboratorio" action="../controllers/guardar_laboratorio.php" method="POST">
                  <div class="mb-3">
                    <label for="nombreLab" class="form-label">Nombre del Laboratorio</label>
                    <input type="text" class="form-control" id="nombreLab" name="nombreLab" required>
                  </div>
                  <div class="mb-3">
                    <label for="capacidad" class="form-label">Capacidad (personas)</label>
                    <input type="number" class="form-control" id="capacidad" name="capacidad" min="1" required>
                  </div>
                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="disponible" name="disponible" checked>
                    <label class="form-check-label" for="disponible">Disponible</label>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formNuevoLaboratorio" class="btn btn-primary">Guardar</button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal Editar Laboratorio -->
        <div class="modal fade" id="editarLaboratorioModal" tabindex="-1" aria-labelledby="editarLaboratorioModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editarLaboratorioModalLabel">Editar Laboratorio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="formEditarLaboratorio" action="../controllers/actualizar_laboratorio.php" method="POST">
                  <input type="hidden" id="editLabId" name="labId">
                  <div class="mb-3">
                    <label for="editNombreLab" class="form-label">Nombre del Laboratorio</label>
                    <input type="text" class="form-control" id="editNombreLab" name="nombreLab" required>
                  </div>
                  <div class="mb-3">
                    <label for="editCapacidad" class="form-label">Capacidad (personas)</label>
                    <input type="number" class="form-control" id="editCapacidad" name="capacidad" min="1" required>
                  </div>
                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="editDisponible" name="disponible">
                    <label class="form-check-label" for="editDisponible">Disponible</label>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarLaboratorio" class="btn btn-primary">Actualizar</button>
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
      // Editar laboratorio
      document.querySelectorAll('.edit-lab').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const nombre = this.getAttribute('data-nombre');
          const capacidad = this.getAttribute('data-capacidad');
          const disponible = this.getAttribute('data-disponible');
          
          document.getElementById('editLabId').value = id;
          document.getElementById('editNombreLab').value = nombre;
          document.getElementById('editCapacidad').value = capacidad;
          document.getElementById('editDisponible').checked = (disponible == 1);
          
          const editModal = new bootstrap.Modal(document.getElementById('editarLaboratorioModal'));
          editModal.show();
        });
      });
      
      // Cambiar disponibilidad
      document.querySelectorAll('.toggle-disponible').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const disponible = this.getAttribute('data-disponible');
          const nuevoEstado = (disponible == 1) ? 0 : 1;
          
          if (confirm('¿Está seguro de cambiar el estado de disponibilidad de este laboratorio?')) {
            fetch(`../controllers/toggle_disponible_lab.php?id=${id}&estado=${nuevoEstado}`)
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  location.reload();
                } else {
                  alert('Error al actualizar la disponibilidad');
                }
              });
          }
        });
      });
      
      // Eliminar laboratorio
      document.querySelectorAll('.delete-lab').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          
          if (confirm('¿Está seguro de eliminar este laboratorio? Esta acción no se puede deshacer.')) {
            fetch(`../controllers/eliminar_laboratorio.php?id=${id}`)
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  location.reload();
                } else {
                  alert('Error al eliminar el laboratorio');
                }
              });
          }
        });
      });
    });
  </script>
</body>
</html>