<?php
  require_once '../controllers/total_usuario.php';
  session_start();
  if (!isset($_SESSION['user_email'])) {
      header("Location: login.php");
      exit();
  }
  ?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRL - Panel de Administración</title>
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
          <!-- Dashboard Section -->
          <div id="dashboard" class="section active">
            <div class="user-welcome">
              <h2>Bienvenido, <?= htmlspecialchars($_SESSION['user_name'] ?? 'admin') ?></h2>
              <p>Panel de administración del sistema.</p>
            </div>
            
            <div class="stats-cards mb-4">
              <div class="row">
                <div class="col-md-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="card-title">Total Usuarios</h6>
                          <h2 class="mb-0">
                            <?= isset($total_usuarios) ? htmlspecialchars($total_usuarios, ENT_QUOTES, 'UTF-8') : '<i class="fas fa-exclamation-triangle"></i> Error' ?>
                          </h2>
                        </div>
                        <div class="stats-icon">
                          <i class="fas fa-users"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="card-title">Prácticas Activas</h6>
                          <h2 class="mb-0">36</h2>
                        </div>
                        <div class="stats-icon">
                          <i class="fas fa-flask"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="card-title">Asistencias Hoy</h6>
                          <h2 class="mb-0">48</h2>
                        </div>
                        <div class="stats-icon">
                          <i class="fas fa-clipboard-check"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="card-title">Permisos Pendientes</h6>
                          <h2 class="mb-0">7</h2>
                        </div>
                        <div class="stats-icon">
                          <i class="fas fa-key"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-8">
                <div class="card">
                  <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Últimas Prácticas Registradas</h5>
                  </div>
                  <div class="card-body">
                    <div class="table-container">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Materia</th>
                            <th>Laboratorio</th>
                            <th>Grupo</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>143</td>
                            <td>Programación Web</td>
                            <td>Lab 3</td>
                            <td>G5</td>
                            <td>03/05/2025</td>
                            <td class="action-buttons">
                              <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                              <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            </td>
                          </tr>
                          <tr>
                            <td>142</td>
                            <td>Bases de Datos</td>
                            <td>Lab 2</td>
                            <td>G3</td>
                            <td>03/05/2025</td>
                            <td class="action-buttons">
                              <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                              <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            </td>
                          </tr>
                          <tr>
                            <td>141</td>
                            <td>Redes</td>
                            <td>Lab 1</td>
                            <td>G2</td>
                            <td>02/05/2025</td>
                            <td class="action-buttons">
                              <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                              <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="text-center mt-3">
                      <a href="listar_practicas.php" class="btn btn-sm btn-secondary">Ver todas las prácticas</a>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="card">
                  <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Últimos Usuarios Registrados</h5>
                  </div>
                  <div class="card-body">
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                          <div>Juan Pérez</div>
                          <small class="text-muted">Estudiante - 04/05/2025</small>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-user-edit"></i></a>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                          <div>María Rodríguez</div>
                          <small class="text-muted">Estudiante - 03/05/2025</small>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-user-edit"></i></a>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                          <div>Carlos Sánchez</div>
                          <small class="text-muted">Profesor - 02/05/2025</small>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-user-edit"></i></a>
                      </li>
                    </ul>
                    <div class="text-center mt-3">
                      <a href="listar_usuarios.php" class="btn btn-sm btn-secondary">Ver todos los usuarios</a>
                    </div>
                  </div>
                </div>
                
                <div class="card mt-4">
                  <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Acciones Rápidas</h5>
                  </div>
                  <div class="card-body">
                    <div class="d-grid gap-2">
                      <a href="nuevo_usuario.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
                      </a>
                      <a href="nueva_practica.php" class="btn btn-outline-primary">
                        <i class="fas fa-flask me-2"></i> Nueva Práctica
                      </a>
                      <a href="generar_reportes.php" class="btn btn-outline-primary">
                        <i class="fas fa-file-export me-2"></i> Generar Reporte
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card mt-4">
              <div class="card-header bg-light">
                <h5 class="card-title mb-0">Datos de Administrador</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3 row">
                      <label class="col-sm-4 col-form-label fw-bold">No. Control:</label>
                      <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '23') ?>">
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-sm-4 col-form-label fw-bold">Nombre:</label>
                      <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'admin') ?>">
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-sm-4 col-form-label fw-bold">Carrera:</label>
                      <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= htmlspecialchars($_SESSION['user_career'] ?? 'Ingeniería Informática') ?>">
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-sm-4 col-form-label fw-bold">Semestre:</label>
                      <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= htmlspecialchars($_SESSION['user_semester'] ?? 'No aplica') ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3 row">
                      <label class="col-sm-4 col-form-label fw-bold">Email:</label>
                      <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= htmlspecialchars($_SESSION['user_email'] ?? 'admin@gmail.com') ?>">
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-sm-4 col-form-label fw-bold">Apellido:</label>
                      <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= htmlspecialchars($_SESSION['user_lastname'] ?? 'admin') ?>">
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-sm-4 col-form-label fw-bold">Teléfono:</label>
                      <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= htmlspecialchars($_SESSION['user_phone'] ?? '111111111') ?>">
                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label class="col-sm-4 col-form-label fw-bold">Grupo:</label>
                      <div class="col-sm-8">
                        <input type="text" readonly class="form-control-plaintext" value="<?= htmlspecialchars($_SESSION['user_group'] ?? 'No aplica') ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end">
                  <button class="btn btn-secondary">Modificar</button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Other Sections -->

          <div id="listar-usuarios" class="section">
            <div class="card">
           
            </div>
          </div>

          <div id="modificar-usuario" class="section">
            <div class="card">
              <?php include_once "../horario.php"?>
            </div>
          </div>
          
          <div id="nuevo-usuario" class="section">
            <div class="card">
              <div class="card-header bg-light">
                <h5 class="mb-0">Código de barra</h5>
              </div>
              <div class="card-body text-center">
                <h4><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?> - <?= htmlspecialchars($_SESSION['user_id'] ?? 'ID') ?></h4>
                <div class="my-4">
                  <img src="/api/placeholder/300/100" alt="Código de Barras" class="img-fluid">
                </div>
                <button type="button" class="btn btn-success mt-3">Imprimir código</button>
              </div>
            </div>
          </div>
          
          <div id="yahs" class="section">
            <div class="card">
              <div class="card-header bg-light">
                <h5 class="mb-0">Asistencia</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr class="bg-success text-white">
                        <th>Materia</th>
                        <th>Fecha</th>
                        <th>Hora entrada</th>
                        <th>Hora salida</th>
                        <th>Estado</th>
                      </tr>
                    </thead>
                    <tbody style="background-color: #e9ecef;">
                      <tr>
                        <td>BASE DE DATOS</td>
                        <td>28/04/2025</td>
                        <td>7:05 am</td>
                        <td>10:00 am</td>
                        <td>Asistió</td>
                      </tr>
                      <tr>
                        <td>AUDITORÍA INF</td>
                        <td>29/04/2025</td>
                        <td>11:10 am</td>
                        <td>12:05 pm</td>
                        <td>Asistió</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          
          <div id="permisos" class="section">
            <div class="card">
              <div class="card-header bg-light">
                <h5 class="mb-0">Permisos</h5>
              </div>
              <div class="card-body">
                <form>
                  <div class="mb-3">
                    <label class="form-label">Materia</label>
                    <select class="form-select">
                      <option>BASE DE DATOS</option>
                      <option>INTERCONECTIVIDAD</option>
                      <option>AUDITORÍA INF</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" class="form-control">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Motivo</label>
                    <textarea class="form-control" rows="3"></textarea>
                  </div>
                  <div class="text-center">
                    <button type="button" class="btn btn-success">Solicitar permiso</button>
                  </div>
                </form>
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
  

</body>
</html>