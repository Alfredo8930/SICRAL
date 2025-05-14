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
    
    // Manejo de eliminación
    let userIdToDelete = null;
    
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            userIdToDelete = this.getAttribute('data-id');
            const userName = this.getAttribute('data-nombre');
            
            document.getElementById('userToDeleteName').textContent = userName;
            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        });
    });
    
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (userIdToDelete) {
                // Aquí iría la llamada AJAX para eliminar el usuario
                fetch(`../controllers/eliminar_usuario.php?id=${userIdToDelete}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el usuario: ' + (data.message || ''));
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
                document.getElementById('usuarioGrupo').textContent = usuario.gruppo || 'N/A';
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
        
        
        // Guardar cambios en edición
        document.getElementById('guardarEdicionUsuario').addEventListener('click', async function() {
            const form = document.getElementById('formEditarUsuario');
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            const btnGuardar = this;
            const userId = document.getElementById('editUsuarioId').value;
            
            const userData = {
                nombre: document.getElementById('editNombreUsuario').value.trim(),
                apellido: document.getElementById('editApellidoUsuario').value.trim(),
                correo: document.getElementById('editCorreoUsuario').value.trim(),
                telefono: document.getElementById('editTelefonoUsuario').value.trim(),
                tipo_usuario: document.getElementById('editTipoUsuario').value,
                id_carrera: document.getElementById('editCarreraUsuario').value || null,
                semestre: document.getElementById('editSemestreUsuario').value || null,
                id_grupo: document.getElementById('editGrupoUsuario').value || null,
                codigo_barras: document.getElementById('editCodigoBarras').value.trim(),
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
    });
</script>