<?php
  session_start();
  if (!isset($_SESSION['user_email'])) {
      header("Location: login.php");
      exit();
  }
  require_once '../includes/db.php';
  
  // Obtener lista de materias
  $query = "SELECT m.id_Materia, m.n_Materia FROM materias m ORDER BY m.n_Materia";
  $result = mysqli_query($conn, $query);
  $materias = [];
  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $materias[] = $row;
    }
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRL - Materias</title>
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
              <h5 class="card-title mb-0">Listado de Materias</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaMateriaModal">
                <i class="fas fa-plus-circle me-1"></i> Nueva Materia
              </button>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre de la Materia</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($materias) > 0): ?>
                      <?php foreach ($materias as $materia): ?>
                        <tr>
                          <td><?= htmlspecialchars($materia['id_Materia']) ?></td>
                          <td><?= htmlspecialchars($materia['n_Materia']) ?></td>
                          <td>
                            <button class="btn btn-sm btn-warning edit-materia" data-id="<?= $materia['id_Materia'] ?>" data-nombre="<?= htmlspecialchars($materia['n_Materia']) ?>">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-materia" data-id="<?= $materia['id_Materia'] ?>">
                              <i class="fas fa-trash-alt"></i>
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="3" class="text-center">No hay materias registradas</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal Nueva Materia -->
        <div class="modal fade" id="nuevaMateriaModal" tabindex="-1" aria-labelledby="nuevaMateriaModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="nuevaMateriaModalLabel">Registrar Nueva Materia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="formNuevaMateria" action="../controllers/guardar_materia.php" method="POST">
                  <div class="mb-3">
                    <label for="nombreMateria" class="form-label">Nombre de la Materia</label>
                    <input type="text" class="form-control" id="nombreMateria" name="nombreMateria" required>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formNuevaMateria" class="btn btn-primary">Guardar</button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal Editar Materia -->
        <div class="modal fade" id="editarMateriaModal" tabindex="-1" aria-labelledby="editarMateriaModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editarMateriaModalLabel">Editar Materia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="formEditarMateria" action="../controllers/actualizar_materia.php" method="POST">
                  <input type="hidden" id="editMateriaId" name="materiaId">
                  <div class="mb-3">
                    <label for="editNombreMateria" class="form-label">Nombre de la Materia</label>
                    <input type="text" class="form-control" id="editNombreMateria" name="nombreMateria" required>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEditarMateria" class="btn btn-primary">Actualizar</button>
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
      // Editar materia
      document.querySelectorAll('.edit-materia').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const nombre = this.getAttribute('data-nombre');
          
          document.getElementById('editMateriaId').value = id;
          document.getElementById('editNombreMateria').value = nombre;
          
          const editModal = new bootstrap.Modal(document.getElementById('editarMateriaModal'));
          editModal.show();
        });
      });
      
      // Eliminar materia
      document.querySelectorAll('.delete-materia').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          if (confirm('¿Está seguro de eliminar esta materia?')) {
            window.location.href = `../controllers/eliminar_materia.php?id=${id}`;
          }
        });
      });
    });
  </script>
</body>
</html>