<?php
require_once '../includes/db.php';

// Consulta para obtener usuarios con sus tipos y carreras
$query = "SELECT 
            u.id_Usuario, 
            u.nombre, 
            u.apellido, 
            u.correo, 
            u.telefono, 
            tu.nombre_Usuario AS tipo_usuario,
            c.n_Carrera AS carrera
          FROM usuarios u
          JOIN tipo_usuario tu ON u.id_TipoUsuario = tu.id_TipoUsuario
          LEFT JOIN carrera c ON u.id_Carrera = c.id_Carrera
          ORDER BY u.id_Usuario DESC";

$usuarios = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios - SRL</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../assets/css/styles-2.css">
    <style>
        .table-container {
            overflow-x: center;
        }
        .table th {
            background-color: #0d3b34;
            color: white;
        }
        .badge-estudiante {
            background-color: #28a745;
        }
        .badge-profesor {
            background-color: #007bff;
        }
        .badge-admin {
            background-color: #6f42c1;
        }
        .badge-tutor {
            background-color: #fd7e14;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
        .search-box {
            max-width: 300px;
            margin-bottom: 20px;
        }

        /* Estilos para el formulario de nuevo usuario */
        #formNuevoUsuario .form-control, 
        #formNuevoUsuario .form-select {
            margin-bottom: 0.5rem;
        }

        #formNuevoUsuario .was-validated .form-control:invalid,
        #formNuevoUsuario .was-validated .form-select:invalid {
            border-color: #dc3545;
        }

        #formNuevoUsuario .was-validated .form-control:valid,
        #formNuevoUsuario .was-validated .form-select:valid {
            border-color: #198754;
        }

        /* Toast notifications */
        .toast {
            margin-bottom: 10px;
        }

        /* Ajustes para selects */
        .form-select {
            cursor: pointer;
        }
    </style>
</head>
<body>
          <!-- Sidebar -->
    <?php include "../views/nab_var.php"?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1 class="text-center">Lista de Usuarios</h1>
        </div>
        
        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Todos los Usuarios Registrados</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                                <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
                            </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group search-box">
                                <input type="text" class="form-control" placeholder="Buscar usuario..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary active filter-btn" data-filter="all">Todos</button>
                                <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="estudiante">Estudiantes</button>
                                <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="profesor">Profesores</button>
                                <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="administrador">Administrador</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="table table-striped table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Tipo</th>
                                    <th>Carrera</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($usuario = $usuarios->fetch_assoc()): ?>
                                <tr data-type="<?= strtolower(str_replace(' ', '-', $usuario['tipo_usuario'])) ?>">
                                    <td><?= htmlspecialchars($usuario['id_Usuario']) ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></td>
                                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                    <td>
                                        <?php 
                                            $badgeClass = '';
                                            switch(strtolower($usuario['tipo_usuario'])) {
                                                case 'estudiante': $badgeClass = 'badge-estudiante'; break;
                                                case 'profesor': $badgeClass = 'badge-profesor'; break;
                                                case 'administrador': $badgeClass = 'badge-admin'; break;
                                                case 'tutor': $badgeClass = 'badge-tutor'; break;
                                                default: $badgeClass = 'bg-secondary';
                                            }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($usuario['tipo_usuario']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($usuario['carrera'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-primary btn-ver" 
                                                data-id="<?= $usuario['id_Usuario'] ?>" 
                                                title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning btn-editar" 
                                                data-id="<?= $usuario['id_Usuario'] ?>" 
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-eliminar" 
                                                data-id="<?= $usuario['id_Usuario'] ?>" 
                                                data-nombre="<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>"
                                                title="Eliminar">
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

    <!-- Modal para Eliminar Usuario -->
    <div class="modal fade" id="eliminarUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar al usuario <strong id="usuarioAEliminarNombre"></strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer y eliminará todos los datos asociados.</p>
                    <input type="hidden" id="usuarioAEliminarId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminacionUsuario">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Usuario -->
    <div class="modal fade" id="verUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalles del Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">ID:</label>
                        <span id="usuarioId"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Nombre:</label>
                        <span id="usuarioNombre"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Apellido:</label>
                        <span id="usuarioApellido"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Correo:</label>
                        <span id="usuarioCorreo"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Teléfono:</label>
                        <span id="usuarioTelefono"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Tipo de Usuario:</label>
                        <span id="usuarioTipo"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Carrera:</label>
                        <span id="usuarioCarrera"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Grupo:</label>
                        <span id="usuarioGrupo"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Semestre:</label>
                        <span id="usuarioSemestre"></span>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Código de Barras:</label>
                        <span id="usuarioCodigoBarras"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarUsuario">
                        <input type="hidden" id="editUsuarioId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editNombreUsuario" class="form-label">Nombre*</label>
                                <input type="text" class="form-control" id="editNombreUsuario" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editApellidoUsuario" class="form-label">Apellido*</label>
                                <input type="text" class="form-control" id="editApellidoUsuario" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editCorreoUsuario" class="form-label">Correo Electrónico*</label>
                                <input type="email" class="form-control" id="editCorreoUsuario" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editTelefonoUsuario" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="editTelefonoUsuario">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editTipoUsuario" class="form-label">Tipo de Usuario*</label>
                                <select class="form-select" id="editTipoUsuario" required>
                                    <option value="">Seleccionar tipo</option>
                                    <?php
                                    $tipos = $conn->query("SELECT * FROM tipo_usuario");
                                    while($tipo = $tipos->fetch_assoc()): ?>
                                    <option value="<?= $tipo['id_TipoUsuario'] ?>"><?= htmlspecialchars($tipo['nombre_Usuario']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editCarreraUsuario" class="form-label">Carrera</label>
                                <select class="form-select" id="editCarreraUsuario">
                                    <option value="">Seleccionar carrera</option>
                                    <?php
                                    $carreras = $conn->query("SELECT * FROM carrera");
                                    while($carrera = $carreras->fetch_assoc()): ?>
                                    <option value="<?= $carrera['id_Carrera'] ?>"><?= htmlspecialchars($carrera['n_Carrera']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editSemestreUsuario" class="form-label">Semestre</label>
                                <select class="form-select" id="editSemestreUsuario">
                                    <option value="">Seleccionar semestre</option>
                                    <?php for($i=1; $i<=12; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editGrupoUsuario" class="form-label">Grupo</label>
                                <select class="form-select" id="editGrupoUsuario">
                                    <option value="">Seleccionar grupo</option>
                                    <?php
                                    $grupos = $conn->query("SELECT * FROM grupos");
                                    while($grupo = $grupos->fetch_assoc()): ?>
                                    <option value="<?= $grupo['id_Grupo'] ?>"><?= htmlspecialchars($grupo['grupo']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editCodigoBarras" class="form-label">Código de Barras*</label>
                                <input type="text" class="form-control" id="editCodigoBarras" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editContrasenaUsuario" class="form-label">Contraseña (dejar vacío para no cambiar)</label>
                                <input type="password" class="form-control" id="editContrasenaUsuario">
                            </div>
                        </div>
                        
                        <small class="text-muted">* Campos obligatorios</small>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning text-white" id="guardarEdicionUsuario">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo Usuario -->
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-labelledby="nuevoUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="nuevoUsuarioModalLabel">Agregar Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoUsuario">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombreUsuario" class="form-label">Nombre*</label>
                                <input type="text" class="form-control" id="nombreUsuario" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidoUsuario" class="form-label">Apellido*</label>
                                <input type="text" class="form-control" id="apellidoUsuario" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="correoUsuario" class="form-label">Correo Electrónico*</label>
                                <input type="email" class="form-control" id="correoUsuario" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefonoUsuario" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefonoUsuario">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipoUsuario" class="form-label">Tipo de Usuario*</label>
                                <select class="form-select" id="tipoUsuario" required>
                                    <option value="">Seleccionar tipo</option>
                                    <?php
                                    $tipos = $conn->query("SELECT * FROM tipo_Usuario");
                                    while($tipo = $tipos->fetch_assoc()): ?>
                                    <option value="<?= $tipo['id_TipoUsuario'] ?>"><?= htmlspecialchars($tipo['nombre_Usuario']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="carreraUsuario" class="form-label">Carrera</label>
                                <select class="form-select" id="carreraUsuario">
                                    <option value="">Seleccionar carrera</option>
                                    <?php
                                    $carreras = $conn->query("SELECT * FROM Carrera");
                                    while($carrera = $carreras->fetch_assoc()): ?>
                                    <option value="<?= $carrera['id_Carrera'] ?>"><?= htmlspecialchars($carrera['n_Carrera']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="semestreUsuario" class="form-label">Semestre</label>
                                <select class="form-select" id="semestreUsuario">
                                    <option value="">Seleccionar semestre</option>
                                    <?php for($i=1; $i<=9; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="grupoUsuario" class="form-label">Grupo</label>
                                <select class="form-select" id="grupoUsuario">
                                    <option value="">Seleccionar grupo</option>
                                    <?php
                                    $grupos = $conn->query("SELECT * FROM grupos");
                                    while($grupo = $grupos->fetch_assoc()): ?>
                                    <option value="<?= $grupo['id_Grupo'] ?>"><?= htmlspecialchars($grupo['grupo']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="codigoBarras" class="form-label">Número para Código de Barras*</label>
                                <input type="text" class="form-control" id="codigoBarras" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contrasenaUsuario" class="form-label">Contraseña*</label>
                                <input type="password" class="form-control" id="contrasenaUsuario" required>
                            </div>
                        </div>
                        
                        <small class="text-muted">* Campos obligatorios</small>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarUsuario">Guardar Usuario</button>
                </div>
            </div>
        </div>
    </div>



<!-- Bootstrap Bundle with Popper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
  
<script>
// Script para manejar la interacción en la lista de usuarios
document.addEventListener('DOMContentLoaded', function() {
    // Filtrado por tipo de usuario
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Actualizar botones activos
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            
            // Filtrar tabla
            const rows = document.querySelectorAll('#usersTable tbody tr');
            rows.forEach(row => {
                if (filter === 'all' || row.getAttribute('data-type').includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Búsqueda en tiempo real
    const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#usersTable tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });

    





    document.getElementById('guardarUsuario').addEventListener('click', async function() {
        const btnGuardar = this;
        const form = document.getElementById('formNuevoUsuario');
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const userData = {
            nombre: document.getElementById('nombreUsuario').value.trim(),
            apellido: document.getElementById('apellidoUsuario').value.trim(),
            correo: document.getElementById('correoUsuario').value.trim(),
            telefono: document.getElementById('telefonoUsuario').value.trim(),
            tipo_usuario: document.getElementById('tipoUsuario').value,
            id_carrera: document.getElementById('carreraUsuario').value || null,
            semestre: document.getElementById('semestreUsuario').value || null,
            id_grupo: document.getElementById('grupoUsuario').value || null,
            codigo_barras: document.getElementById('codigoBarras').value.trim(),
            contrasena: document.getElementById('contrasenaUsuario').value
        };
        
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        
        try {
            const response = await fetch('../controllers/guardar_usuario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Error al guardar el usuario');
            }
            
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('nuevoUsuarioModal')).hide();
                showToast('success', 'Usuario creado', 'El usuario se ha registrado correctamente');
                setTimeout(() => location.reload(), 1500);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('danger', 'Error', error.message);
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = 'Guardar Usuario';
        }
    });

    // Función para mostrar notificaciones toast
    function showToast(type, title, message) {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
        
        // Eliminar el toast después de que se oculte
        toastEl.addEventListener('hidden.bs.toast', function() {
            toastEl.remove();
        });
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '1100';
        document.body.appendChild(container);
        return container;
    }

    // Mostrar/ocultar campos según tipo de usuario
    document.getElementById('tipoUsuario').addEventListener('change', function() {
        const tipoUsuario = this.value;
        const esEstudiante = tipoUsuario === '1'; // Ajusta según tu DB
        
        document.getElementById('carreraUsuario').required = esEstudiante;
        document.getElementById('semestreUsuario').required = esEstudiante;
        document.getElementById('grupoUsuario').required = esEstudiante;
    });




    // Función para mostrar detalles del usuario
    async function verUsuario(id) {
        try {
            const response = await fetch(`../controllers/obtener_usuario.php?id=${id}`);
            const data = await response.json();
            
            if (data.success) {
                const usuario = data.usuario;
                
                // Llenar información básica en el modal
                document.getElementById('usuarioId').textContent = usuario.id_Usuario;
                document.getElementById('usuarioNombre').textContent = usuario.nombre;
                document.getElementById('usuarioApellido').textContent = usuario.apellido;
                document.getElementById('usuarioCorreo').textContent = usuario.correo;
                document.getElementById('usuarioTelefono').textContent = usuario.telefono || 'N/A';
                document.getElementById('usuarioTipo').textContent = usuario.tipo_usuario;
                document.getElementById('usuarioCarrera').textContent = usuario.carrera || 'N/A';
                document.getElementById('usuarioGrupo').textContent = usuario.grupo || 'N/A';
                document.getElementById('usuarioSemestre').textContent = usuario.semestre || 'N/A';
                document.getElementById('usuarioCodigoBarras').textContent = usuario.codigo_Barra || 'N/A';
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('verUsuarioModal'));
                modal.show();
            } else {
                showToast('danger', 'Error', data.message || 'Error al obtener datos del usuario');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('danger', 'Error', 'Error al cargar los datos del usuario');
        }
    }

    async function editarUsuario(id) {
        try {
            const response = await fetch(`../controllers/obtener_usuario.php?id=${id}`);
            const data = await response.json();
            
            if (data.success) {
                const usuario = data.usuario;
                
                // Llenar el formulario de edición
                document.getElementById('editUsuarioId').value = usuario.id_Usuario;
                document.getElementById('editNombreUsuario').value = usuario.nombre;
                document.getElementById('editApellidoUsuario').value = usuario.apellido;
                document.getElementById('editCorreoUsuario').value = usuario.correo;
                document.getElementById('editTelefonoUsuario').value = usuario.telefono || '';
                document.getElementById('editTipoUsuario').value = usuario.id_TipoUsuario;
                document.getElementById('editCarreraUsuario').value = usuario.id_Carrera || '';
                document.getElementById('editSemestreUsuario').value = usuario.semestre || '';
                document.getElementById('editGrupoUsuario').value = usuario.id_Grupo || '';
                document.getElementById('editCodigoBarras').value = usuario.codigo_Barra || '';
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'));
                modal.show();
            } else {
                showToast('danger', 'Error', data.message || 'Error al obtener datos del usuario');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('danger', 'Error', 'Error al cargar los datos del usuario');
        }
    }

    // Función para cargar datos para editar usuario
    document.getElementById('guardarEdicionUsuario').addEventListener('click', async function() {
        const form = document.getElementById('formEditarUsuario');
        const btnGuardar = this;
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const userId = document.getElementById('editUsuarioId').value;
        const userData = {
            nombre: document.getElementById('editNombreUsuario').value.trim(),
            apellido: document.getElementById('editApellidoUsuario').value.trim(),
            correo: document.getElementById('editCorreoUsuario').value.trim(),
            telefono: document.getElementById('editTelefonoUsuario').value.trim(),
            id_TipoUsuario: document.getElementById('editTipoUsuario').value,
            id_Carrera: document.getElementById('editCarreraUsuario').value || null,
            semestre: document.getElementById('editSemestreUsuario').value || null,
            id_Grupo: document.getElementById('editGrupoUsuario').value || null,
            codigo_Barra: document.getElementById('editCodigoBarras').value.trim(),
            contrasena: document.getElementById('editContrasenaUsuario').value || null
        };
        
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        
        try {
            const response = await fetch(`../controllers/actualizar_usuario.php?id=${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Error al actualizar el usuario');
            }
            
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editarUsuarioModal')).hide();
                showToast('success', 'Usuario actualizado', 'Los cambios se han guardado correctamente');
                setTimeout(() => location.reload(), 1500);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('danger', 'Error', error.message);
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = 'Guardar Cambios';
        }
    });

    // Función para confirmar eliminación de usuario
    function confirmarEliminarUsuario(id, nombre) {
        document.getElementById('usuarioAEliminarId').value = id;
        document.getElementById('usuarioAEliminarNombre').textContent = nombre;
        
        const modal = new bootstrap.Modal(document.getElementById('eliminarUsuarioModal'));
        modal.show();
    }

    // Event listeners para los botones
    document.addEventListener('DOMContentLoaded', function() {
        // Ver usuario
        document.querySelectorAll('.btn-ver').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                verUsuario(userId);
            });
        });
        
        // Editar usuario
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                editarUsuario(userId);
            });
        });
        
        // Eliminar usuario
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const userName = this.getAttribute('data-nombre');
                confirmarEliminarUsuario(userId, userName);
            });
        });
        
        // Confirmar eliminación
        document.getElementById('confirmarEliminacionUsuario').addEventListener('click', async function() {
            const userId = document.getElementById('usuarioAEliminarId').value;
            
            try {
                const response = await fetch(`../controllers/eliminar_usuario.php?id=${userId}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                
                if (data.success) {
                    showToast('success', 'Usuario eliminado', 'El usuario ha sido eliminado correctamente');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('danger', 'Error', data.message || 'Error al eliminar el usuario');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('danger', 'Error', 'Error en la comunicación con el servidor');
            }
            
            bootstrap.Modal.getInstance(document.getElementById('eliminarUsuarioModal')).hide();
        });
    });
</script>
</body>
</html>