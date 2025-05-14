<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';


// Consulta para obtener grupos con conteo de alumnos
$query = "SELECT g.id_Grupo, g.semestre, g.grupo FROM grupos g ORDER BY g.semestre, g.grupo";

$grupos = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Grupos - SRL</title>
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
        
        /* Estilos para los modales */
        .modal-header {
            color: white;
        }
        #nuevoGrupoModal .modal-header {
            background-color: #0d6efd;
        }
        #editarGrupoModal .modal-header {
            background-color: #ffc107;
            color: #212529;
        }
        #eliminarGrupoModal .modal-header {
            background-color: #dc3545;
        }
        
        /* Badge para conteo de alumnos */
        .badge-alumnos {
            background-color: #6f42c1;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include "../views/nab_var.php"?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1 class="text-center">Lista de Grupos</h1>
        </div>
        
        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Todos los Grupos Registrados</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoGrupoModal">
                            <i class="fas fa-plus-circle me-2"></i> Nuevo Grupo
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group search-box">
                                <input type="text" class="form-control" placeholder="Buscar grupo..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="table table-striped table-hover" id="gruposTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Semestre</th>
                                    <th>Grupo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($grupo = $grupos->fetch_assoc()): ?>
                                <tr data-semestre="semestre-<?= $grupo['semestre'] ?>">
                                    <td><?= htmlspecialchars($grupo['id_Grupo']) ?></td>
                                    <td><?= htmlspecialchars($grupo['semestre']) ?></td>
                                    <td><?= htmlspecialchars($grupo['grupo']) ?></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-info btn-alumnos" 
                                                data-id="<?= $grupo['id_Grupo'] ?>"
                                                title="Ver alumnos">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning btn-editar" 
                                                data-id="<?= $grupo['id_Grupo'] ?>"
                                                data-semestre="<?= $grupo['semestre'] ?>"
                                                data-grupo="<?= htmlspecialchars($grupo['grupo']) ?>"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-eliminar" 
                                                data-id="<?= $grupo['id_Grupo'] ?>"
                                                data-grupo="<?= htmlspecialchars($grupo['semestre'] . $grupo['grupo']) ?>"
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
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

    <!-- Modal Nuevo Grupo -->
    <div class="modal fade" id="nuevoGrupoModal" tabindex="-1" aria-labelledby="nuevoGrupoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevoGrupoModalLabel">Registrar Nuevo Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoGrupo">
                        <div class="mb-3">
                            <label for="semestre" class="form-label">Semestre*</label>
                            <select class="form-select" id="semestre" name="semestre" required>
                                <option value="">Seleccione semestre</option>
                                <?php for($i = 1; $i <= 9; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione un semestre</div>
                        </div>
                        <div class="mb-3">
                            <label for="grupo" class="form-label">Grupo*</label>
                            <input type="text" class="form-control" id="grupo" name="grupo" 
                                   placeholder="Ej: A, B, C" maxlength="10" required>
                            <div class="invalid-feedback">Por favor ingrese un nombre de grupo</div>
                        </div>
                        <small class="text-muted">* Campos obligatorios</small>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarGrupo">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Grupo -->
    <div class="modal fade" id="editarGrupoModal" tabindex="-1" aria-labelledby="editarGrupoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarGrupoModalLabel">Editar Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarGrupo">
                        <input type="hidden" id="editGrupoId">
                        <div class="mb-3">
                            <label for="editSemestre" class="form-label">Semestre*</label>
                            <select class="form-select" id="editSemestre" name="semestre" required>
                                <?php for($i = 1; $i <= 9; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="invalid-feedback">Por favor seleccione un semestre</div>
                        </div>
                        <div class="mb-3">
                            <label for="editGrupo" class="form-label">Grupo*</label>
                            <input type="text" class="form-control" id="editGrupo" name="grupo" 
                                   maxlength="10" required>
                            <div class="invalid-feedback">Por favor ingrese un nombre de grupo</div>
                        </div>
                        <small class="text-muted">* Campos obligatorios</small>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning text-white" id="guardarEdicionGrupo">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Eliminar Grupo -->
    <div class="modal fade" id="eliminarGrupoModal" tabindex="-1" aria-labelledby="eliminarGrupoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarGrupoModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar el grupo <strong id="grupoAEliminarNombre"></strong>?</p>
                    <p class="text-danger">Esta acción eliminará también todos los alumnos asociados y no se puede deshacer.</p>
                    <input type="hidden" id="grupoAEliminarId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminacionGrupo">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    
    <script>
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

    // Guardar nuevo grupo
    document.getElementById('guardarGrupo').addEventListener('click', async function() {
        const form = document.getElementById('formNuevoGrupo');
        const btnGuardar = this;
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const grupoData = {
            semestre: document.getElementById('semestre').value,
            grupo: document.getElementById('grupo').value.trim()
        };
        
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        
        try {
            const response = await fetch('../controllers/guardar_grupo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(grupoData)
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Error al guardar el grupo');
            }
            
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('nuevoGrupoModal')).hide();
                showToast('success', 'Grupo creado', 'El grupo se ha registrado correctamente');
                setTimeout(() => location.reload(), 1500);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('danger', 'Error', error.message);
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = 'Guardar';
        }
    });

    // Editar grupo - llenar formulario
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const grupoId = this.getAttribute('data-id');
            const semestre = this.getAttribute('data-semestre');
            const grupo = this.getAttribute('data-grupo');
            
            document.getElementById('editGrupoId').value = grupoId;
            document.getElementById('editSemestre').value = semestre;
            document.getElementById('editGrupo').value = grupo;
            
            const modal = new bootstrap.Modal(document.getElementById('editarGrupoModal'));
            modal.show();
        });
    });

    // Guardar edición de grupo
    document.getElementById('guardarEdicionGrupo').addEventListener('click', async function() {
        const form = document.getElementById('formEditarGrupo');
        const btnGuardar = this;
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const grupoId = document.getElementById('editGrupoId').value;
        const grupoData = {
            semestre: document.getElementById('editSemestre').value,
            grupo: document.getElementById('editGrupo').value.trim()
        };
        
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        
        try {
            const response = await fetch(`../controllers/actualizar_grupo.php?id=${grupoId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(grupoData)
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Error al actualizar el grupo');
            }
            
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editarGrupoModal')).hide();
                showToast('success', 'Grupo actualizado', 'Los cambios se han guardado correctamente');
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

    // Eliminar grupo - confirmación
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const grupoId = this.getAttribute('data-id');
            const grupoNombre = this.getAttribute('data-grupo');
            
            document.getElementById('grupoAEliminarId').value = grupoId;
            document.getElementById('grupoAEliminarNombre').textContent = grupoNombre;
            
            const modal = new bootstrap.Modal(document.getElementById('eliminarGrupoModal'));
            modal.show();
        });
    });

    // Confirmar eliminación de grupo
    document.getElementById('confirmarEliminacionGrupo').addEventListener('click', async function() {
        const grupoId = document.getElementById('grupoAEliminarId').value;
        const btnEliminar = this;
        
        btnEliminar.disabled = true;
        btnEliminar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...';
        
        try {
            const response = await fetch(`../controllers/eliminar_grupo.php?id=${grupoId}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('success', 'Grupo eliminado', 'El grupo ha sido eliminado correctamente');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('danger', 'Error', data.message || 'Error al eliminar el grupo');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('danger', 'Error', 'Error en la comunicación con el servidor');
        } finally {
            bootstrap.Modal.getInstance(document.getElementById('eliminarGrupoModal')).hide();
        }
    });

    // Ver alumnos del grupo
    document.querySelectorAll('.btn-alumnos').forEach(btn => {
        btn.addEventListener('click', function() {
            const grupoId = this.getAttribute('data-id');
            window.location.href = `alumnos_grupo.php?grupo_id=${grupoId}`;
        });
    });

    // Filtrado por semestre
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Actualizar botones activos
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
            
            // Filtrar tabla
            const rows = document.querySelectorAll('#gruposTable tbody tr');
            rows.forEach(row => {
                if (filter === 'all' || row.getAttribute('data-semestre') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Búsqueda en tiempo real
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#gruposTable tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>