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
  </script>