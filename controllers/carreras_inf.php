<?php
require_once '../includes/db.php';

// Consulta para obtener todas las carreras
$query = "SELECT 
            c.id_Carrera, 
            c.n_Carrera,
            COUNT(u.id_Usuario) AS total_estudiantes
          FROM Carrera c
          LEFT JOIN Usuarios u ON c.id_Carrera = u.id_Carrera AND u.id_TipoUsuario = (
              SELECT id_TipoUsuario FROM tipo_Usuario WHERE nombre_Usuario = 'Estudiante'
          )
          GROUP BY c.id_Carrera
          ORDER BY c.n_Carrera ASC";

$carreras = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Carreras - SRL</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../assets/css/styles-2.css">
    <style>
        .table-container {
            overflow-x: auto;
        }
        .table th {
            background-color: #0d3b34;
            color: white;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
        .search-box {
            max-width: 300px;
            margin-bottom: 20px;
        }
        .badge-estudiantes {
            background-color: #28a745;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include "../views/nab_var.php"?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1 class="text-center">Lista de Carreras</h1>
        </div>
        
        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Todas las Carreras Registradas</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaCarreraModal">
                                <i class="fas fa-plus me-2"></i> Nueva Carrera
                            </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group search-box">
                                <input type="text" class="form-control" placeholder="Buscar carrera..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary active filter-btn" data-filter="all">Todas</button>
                                <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="con-estudiantes">Con estudiantes</button>
                                <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="sin-estudiantes">Sin estudiantes</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="table table-striped table-hover" id="careersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre de la Carrera</th>
                                    <th>Estudiantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($carrera = $carreras->fetch_assoc()): ?>
                                <tr data-has-students="<?= $carrera['total_estudiantes'] > 0 ? 'true' : 'false' ?>">
                                    <td><?= htmlspecialchars($carrera['id_Carrera']) ?></td>
                                    <td><?= htmlspecialchars($carrera['n_Carrera']) ?></td>
                                    <td>
                                        <span class="badge badge-estudiantes">
                                            <?= htmlspecialchars($carrera['total_estudiantes']) ?> estudiantes
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-primary" onclick="verCarrera(<?= $carrera['id_Carrera'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarCarrera(<?= $carrera['id_Carrera'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="confirmarEliminarCarrera(<?= $carrera['id_Carrera'] ?>, '<?= htmlspecialchars($carrera['n_Carrera']) ?>')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Anterior</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        
        <footer class="mt-5 text-center text-muted">
            <p>&copy; 2025 Sistema de Registro en Laboratorios. Todos los derechos reservados.</p>
        </footer>
    </div>

    <!-- Modal Simplificado para Nueva Carrera -->
    <div class="modal fade" id="nuevaCarreraModal" tabindex="-1" aria-labelledby="nuevaCarreraModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="nuevaCarreraModalLabel">Agregar Nueva Carrera</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaCarrera">
                        <div class="mb-3">
                            <label for="nombreCarrera" class="form-label">Nombre de la Carrera*</label>
                            <input type="text" class="form-control" id="nombreCarrera" required>
                            <div class="invalid-feedback">Por favor ingresa el nombre de la carrera</div>
                        </div>
                    </form>
                    <small class="text-muted">* Campo obligatorio</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarCarrera">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Ver Carrera -->
    <div class="modal fade" id="verCarreraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalles de la Carrera</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 id="carreraNombre"></h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-info">
                                <i class="fas fa-users me-1"></i> 
                                <span id="carreraEstudiantes"></span> estudiantes
                            </span>
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Estudiantes Inscritos</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Semestre</th>
                                    <th>Grupo</th>
                                </tr>
                            </thead>
                            <tbody id="estudiantesCarrera">
                                <!-- Datos se llenarán con JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Carrera -->
    <div class="modal fade" id="editarCarreraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Editar Carrera</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarCarrera">
                        <input type="hidden" id="editCarreraId">
                        <div class="mb-3">
                            <label for="editCarreraNombre" class="form-label">Nombre de la Carrera</label>
                            <input type="text" class="form-control" id="editCarreraNombre" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning text-white" id="guardarEdicionCarrera">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Carrera -->
    <div class="modal fade" id="eliminarCarreraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar la carrera <strong id="carreraAEliminarNombre"></strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                    <input type="hidden" id="carreraAEliminarId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminacionCarrera">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include "../assets/js/script_carrera.php"?>
    
</body>
</html>