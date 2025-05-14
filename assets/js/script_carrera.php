    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Manejo del formulario simplificado de nueva carrera
    document.getElementById('guardarCarrera').addEventListener('click', async function() {
    const nombreCarrera = document.getElementById('nombreCarrera');
    const nombre = nombreCarrera.value.trim();
    const btnGuardar = this;
    
    if (!nombre) {
        nombreCarrera.classList.add('is-invalid');
        return;
    }
    
    nombreCarrera.classList.remove('is-invalid');
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
    
    try {
        const response = await fetch('../controllers/guardar_carrera.php', {
            method: 'POST',
            credentials: 'same-origin', // Importante para mantener la sesión
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ nombre: nombre })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            if (data.message.includes('autenticado') || data.message.includes('autorizado')) {
                // Redirigir a login si hay problemas de autenticación
                window.location.href = '../login.php?error=' + encodeURIComponent(data.message);
            } else {
                throw new Error(data.message || 'Error en la respuesta del servidor');
            }
            return;
        }
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('nuevaCarreraModal')).hide();
            alert('Carrera agregada correctamente');
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = 'Guardar';
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

    document.addEventListener('DOMContentLoaded', function() {
        // Filtrado por estado
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Actualizar botones activos
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filtrar tabla
                const rows = document.querySelectorAll('#careersTable tbody tr');
                rows.forEach(row => {
                    const hasStudents = row.getAttribute('data-has-students');
                    
                    if (filter === 'all' || 
                        (filter === 'con-estudiantes' && hasStudents === 'true') || 
                        (filter === 'sin-estudiantes' && hasStudents === 'false')) {
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
                const rows = document.querySelectorAll('#careersTable tbody tr');
                
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
        
        // Manejo de eliminación
        let careerIdToDelete = null;
        
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', function() {
                careerIdToDelete = this.getAttribute('data-id');
                const careerName = this.getAttribute('data-nombre');
                
                document.getElementById('careerToDeleteName').textContent = careerName;
                const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                modal.show();
            });
        });
        
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (careerIdToDelete) {
                    // Aquí iría la llamada AJAX para eliminar la carrera
                    fetch(`../controllers/eliminar_carrera.php?id=${careerIdToDelete}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error al eliminar la carrera: ' + (data.message || ''));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error en la comunicación con el servidor');
                    });
                }
            });
        }
    });






    // Función para mostrar detalles de la carrera
    function verCarrera(id) {
        fetch(`../controllers/ver_carrera.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const carrera = data.carrera;
                    const estudiantes = data.estudiantes;
                    
                    // Configurar el modal de visualización
                    document.getElementById('carreraNombre').textContent = carrera.n_Carrera;
                    document.getElementById('carreraEstudiantes').textContent = carrera.total_estudiantes;
                    
                    // Llenar tabla de estudiantes
                    const tbody = document.getElementById('estudiantesCarrera');
                    tbody.innerHTML = '';
                    
                    estudiantes.forEach(estudiante => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${estudiante.nombre} ${estudiante.apellido}</td>
                            <td>${estudiante.correo}</td>
                            <td>${estudiante.semestre || 'N/A'}</td>
                            <td>${estudiante.grupo || 'N/A'}</td>
                        `;
                        tbody.appendChild(row);
                    });
                    
                    // Mostrar modal
                    new bootstrap.Modal(document.getElementById('verCarreraModal')).show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al obtener los datos de la carrera');
            });
    }

    // Función para abrir modal de edición
    function editarCarrera(id) {
        fetch(`../controllers/editar_carrera.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const carrera = data.carrera;
                    
                    // Llenar formulario de edición
                    document.getElementById('editCarreraId').value = carrera.id_Carrera;
                    document.getElementById('editCarreraNombre').value = carrera.n_Carrera;
                    
                    // Mostrar modal
                    new bootstrap.Modal(document.getElementById('editarCarreraModal')).show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos para editar');
            });
    }

    // Función para guardar cambios
    document.getElementById('guardarEdicionCarrera').addEventListener('click', function() {
        const id = document.getElementById('editCarreraId').value;
        const nombre = document.getElementById('editCarreraNombre').value.trim();
        
        if (!nombre) {
            alert('El nombre de la carrera es requerido');
            return;
        }
        
        const btnGuardar = this;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
        
        fetch('../controllers/editar_carrera.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id, nombre: nombre })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Carrera actualizada correctamente');
                location.reload();
            } else {
                throw new Error(data.message || 'Error al actualizar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = 'Guardar Cambios';
        });
    });

    // Función para eliminar carrera
    function confirmarEliminarCarrera(id, nombre) {
        document.getElementById('carreraAEliminarId').value = id;
        document.getElementById('carreraAEliminarNombre').textContent = nombre;
        new bootstrap.Modal(document.getElementById('eliminarCarreraModal')).show();
    }

    // Función para ejecutar eliminación
    document.getElementById('confirmarEliminacionCarrera').addEventListener('click', function() {
        const id = document.getElementById('carreraAEliminarId').value;
        const btnEliminar = this;
        
        btnEliminar.disabled = true;
        btnEliminar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...';
        
        fetch('../controllers/eliminar_carrera.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Carrera eliminada correctamente');
                location.reload();
            } else {
                throw new Error(data.message || 'Error al eliminar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            btnEliminar.disabled = false;
            btnEliminar.innerHTML = 'Eliminar';
            bootstrap.Modal.getInstance(document.getElementById('eliminarCarreraModal')).hide();
        });
    });
    </script>