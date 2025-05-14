<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Obtener lista de prácticas (tu código actual aquí)
$query = "SELECT p.id_Practica, m.n_Materia, l.n_Laboratorio, g.Grupo, 
          pf.fecha, pf.hora_Inicio, pf.hora_Fin, u.nombre, u.apellido,
          CONCAT(g.semestre, '-', g.grupo) AS smt,
          DAYNAME(pf.fecha) AS dia_semana
          FROM practicas p
          JOIN materias m ON p.id_Materia = m.id_Materia
          JOIN laboratorio l ON p.id_Laboratorio = l.id_Laboratorio
          JOIN grupos g ON p.id_Grupo = g.id_Grupo
          JOIN practicas_fechas pf ON p.id_Practica = pf.id_Practica
          JOIN usuarios u ON p.id_Usuario = u.id_Usuario
          ORDER BY pf.fecha, pf.hora_Inicio";
$result = mysqli_query($conn, $query);
$practicas_por_dia = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $fecha = $row['fecha'];
        $dia_semana = $row['dia_semana'];
        $practicas_por_dia[$fecha]['dia_semana'] = $dia_semana;
        $practicas_por_dia[$fecha]['practicas'][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRL - Prácticas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles-2.css">
    <style>
        /* Estilos adicionales para los modales */
        .modal-lg-custom {
            max-width: 800px;
        }
        .practica-img {
            max-height: 200px;
            object-fit: contain;
        }

    .dia-header {
      background-color: #f8f9fa;
      padding: 10px;
      margin-top: 20px;
      border-radius: 5px;
      font-weight: bold;
    }
    .horario-cell {
      white-space: nowrap;
    }
    .laboratorio-card {
      border: 1px solid #dee2e6;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    .laboratorio-header {
      background-color: #0d6efd;
      color: white;
      padding: 10px;
      border-radius: 5px 5px 0 0;
    }
    .disponible {
      background-color: #d4edda;
    }
    .ocupado {
      background-color: #f8d7da;
    } 
    </style>
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
              <h5 class="card-title mb-0">Horario de Prácticas</h5>
              <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaPracticaModal">
                  <i class="fas fa-plus-circle me-1"></i> Nueva Práctica
                </button>
                <button class="btn btn-success" id="btnVerDisponibilidad">
                  <i class="fas fa-calendar-check me-1"></i> Ver Disponibilidad
                </button>
              </div>
            </div>
            <div class="card-body">
              <?php if (count($practicas_por_dia) > 0): ?>
                <?php foreach ($practicas_por_dia as $fecha => $dia_data): ?>
                  <div class="dia-header">
                    <?= ucfirst($dia_data['dia_semana']) ?> - <?= date('d/m/Y', strtotime($fecha)) ?>
                  </div>
                  
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>Laboratorio</th>
                          <th>Materia</th>
                          <th>Grupo</th>
                          <th>Profesor</th>
                          <th>Horario</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($dia_data['practicas'] as $prac): ?>
                          <tr>
                            <td><?= htmlspecialchars($prac['n_Laboratorio']) ?></td>
                            <td><?= htmlspecialchars($prac['n_Materia']) ?></td>
                            <td><?= htmlspecialchars($prac['smt']) ?></td>
                            <td><?= htmlspecialchars($prac['nombre'] . ' ' . $prac['apellido']) ?></td>
                            <td class="horario-cell">
                              <?= date('H:i', strtotime($prac['hora_Inicio'])) . ' - ' . date('H:i', strtotime($prac['hora_Fin'])) ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary ver-practica" data-id="<?= $prac['id_Practica'] ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning editar-practica" data-id="<?= $prac['id_Practica'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger eliminar-practica" data-id="<?= $prac['id_Practica'] ?>">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="alert alert-info">
                  No hay prácticas registradas
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

            <!-- Modal de Disponibilidad -->
            <div class="modal fade" id="disponibilidadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disponibilidad de Laboratorios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                        <label for="fechaConsulta" class="form-label">Fecha:</label>
                        <input type="date" class="form-control" id="fechaConsulta">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                        <label for="laboratorioConsulta" class="form-label">Laboratorio:</label>
                        <select class="form-select" id="laboratorioConsulta">
                            <option value="">Todos los laboratorios</option>
                            <?php
                            $query_labs = "SELECT id_Laboratorio, n_Laboratorio FROM laboratorio ORDER BY n_Laboratorio";
                            $result_labs = mysqli_query($conn, $query_labs);
                            while ($lab = mysqli_fetch_assoc($result_labs)) {
                            echo '<option value="'.$lab['id_Laboratorio'].'">'.$lab['n_Laboratorio'].'</option>';
                            }
                            ?>
                        </select>
                        </div>
                    </div>
                    </div>
                    <button id="btnConsultarDisponibilidad" class="btn btn-primary mb-3">
                    <i class="fas fa-search me-1"></i> Consultar
                    </button>
                    <div id="resultadoDisponibilidad"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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


      <!-- Agrega este modal al final del documento, antes de los scripts -->
    <div class="modal fade" id="nuevaPracticaModal" tabindex="-1" aria-labelledby="nuevaPracticaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="nuevaPracticaModalLabel">Registrar Nueva Práctica</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <!-- El formulario se cargará aquí dinámicamente -->
            <div id="modalFormContainer">
                <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Modal para Ver Detalles -->
    <div class="modal fade" id="verPracticaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-lg-custom">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalles de la Práctica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detallesPractica">
                    <!-- Contenido cargado por AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar -->
    <div class="modal fade" id="editarPracticaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Editar Práctica</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="formularioEditar">
                    <!-- Contenido cargado por AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar -->
    <div class="modal fade" id="eliminarPracticaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Eliminar Práctica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de eliminar esta práctica? Esta acción no se puede deshacer.</p>
                    <input type="hidden" id="practicaIdEliminar">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Modal para Ver Detalles
        $('.ver-practica').click(function() {
            const id = $(this).data('id');
            $('#verPracticaModal').modal('show');
            
            $.get(`../php/ver_practica_modal.php?id=${id}`, function(data) {
                $('#detallesPractica').html(data);
            }).fail(function() {
                $('#detallesPractica').html('<div class="alert alert-danger">Error al cargar los detalles</div>');
            });
        });

        // Modal para Editar
        $('.editar-practica').click(function() {
            const id = $(this).data('id');
            $('#editarPracticaModal').modal('show');
            
            $.get(`../php/editar_practica_modal.php?id=${id}`, function(data) {
                $('#formularioEditar').html(data);
            }).fail(function() {
                $('#formularioEditar').html('<div class="alert alert-danger">Error al cargar el formulario</div>');
            });
        });

        // Modal para Eliminar
        $('.eliminar-practica').click(function() {
            const id = $(this).data('id');
            $('#practicaIdEliminar').val(id);
            $('#eliminarPracticaModal').modal('show');
        });

        // Confirmar Eliminación
        $('#confirmarEliminar').click(function() {
            const id = $('#practicaIdEliminar').val();
            
            $.ajax({
                url: '../controllers/eliminar_practica.php',
                method: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error de conexión');
                }
            });
        });

        // Cerrar modales al hacer clic fuera
        $('.modal').click(function(e) {
            if ($(e.target).hasClass('modal')) {
                $(this).modal('hide');
            }
        });
    });









    document.addEventListener('DOMContentLoaded', function() {      
      // Mostrar modal de disponibilidad
      document.getElementById('btnVerDisponibilidad').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('disponibilidadModal'));
        modal.show();
      });
      
      // Consultar disponibilidad
      document.getElementById('btnConsultarDisponibilidad').addEventListener('click', function() {
        const fecha = document.getElementById('fechaConsulta').value;
        const laboratorio = document.getElementById('laboratorioConsulta').value;
        
        if (!fecha) {
          alert('Por favor seleccione una fecha');
          return;
        }
        
        fetch(`../php/consultar_disponibilidad.php?fecha=${fecha}&laboratorio=${laboratorio}`)
          .then(response => response.json())
          .then(data => {
            let html = '<div class="table-responsive"><table class="table table-bordered">';
            html += '<thead><tr><th>Hora</th>';
            
            // Encabezados de laboratorios
            data.laboratorios.forEach(lab => {
              html += `<th>${lab.n_Laboratorio}</th>`;
            });
            
            html += '</tr></thead><tbody>';
            
            // Filas de horarios
            data.horarios.forEach(hora => {
              html += `<tr><td>${hora.hora}</td>`;
              
              data.laboratorios.forEach(lab => {
                const ocupado = data.ocupados.some(o => 
                  o.id_Laboratorio == lab.id_Laboratorio && o.hora == hora.hora
                );
                
                html += `<td class="${ocupado ? 'ocupado' : 'disponible'}">`;
                if (ocupado) {
                  const practica = data.ocupados.find(o => 
                    o.id_Laboratorio == lab.id_Laboratorio && o.hora == hora.hora
                  );
                  html += `${practica.materia}<br>${practica.profesor}<br>${practica.grupo}`;
                } else {
                  html += 'Disponible';
                }
                html += '</td>';
              });
              
              html += '</tr>';
            });
            
            html += '</tbody></table></div>';
            document.getElementById('resultadoDisponibilidad').innerHTML = html;
          });
      });
    });

    // Cargar el formulario en el modal cuando se abre
    document.getElementById('nuevaPracticaModal').addEventListener('show.bs.modal', function() {
      fetch('../php/nueva_practica.php')
        .then(response => response.text())
        .then(html => {
          document.getElementById('modalFormContainer').innerHTML = html;
          // Inicializar cualquier script necesario del formulario
          initFormScripts();
        });
    });








        function initFormScripts() {
      // Verificar disponibilidad
      const btnVerificar = document.getElementById('btnVerificarDisponibilidad');
      if (btnVerificar) {
        btnVerificar.addEventListener('click', function() {
          const laboratorio = document.getElementById('laboratorio').value;
          const fecha = document.getElementById('fecha').value;
          const horaInicio = document.getElementById('hora_inicio').value;
          const horaFin = document.getElementById('hora_fin').value;
          
          if (!laboratorio || !fecha || !horaInicio || !horaFin) {
            alert('Por favor complete todos los campos para verificar disponibilidad');
            return;
          }
          
          if (horaInicio >= horaFin) {
            alert('La hora de inicio debe ser anterior a la hora de fin');
            return;
          }
          
          fetch(`../php/verificar_disponibilidad.php?laboratorio=${laboratorio}&fecha=${fecha}&hora_inicio=${horaInicio}&hora_fin=${horaFin}`)
            .then(response => response.json())
            .then(data => {
              const resultado = document.getElementById('resultadoVerificacion');
              if (data.disponible) {
                resultado.innerHTML = `<div class="alert alert-success">El laboratorio está disponible en el horario seleccionado</div>`;
              } else {
                let mensaje = `<div class="alert alert-danger">El laboratorio no está disponible en ese horario. Conflicto con:<ul>`;
                data.conflictos.forEach(conflicto => {
                  mensaje += `<li>${conflicto.materia} (${conflicto.hora_inicio} - ${conflicto.hora_fin})</li>`;
                });
                mensaje += `</ul></div>`;
                resultado.innerHTML = mensaje;
              }
            });
        });
      }
      
      // Manejar el envío del formulario
      const form = document.getElementById('formNuevaPractica');
      if (form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          
          fetch('../controllers/guardar_practica.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(data.message);
              // Cerrar el modal y recargar la página
              const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaPracticaModal'));
              modal.hide();
              location.reload();
            } else {
              if (data.errors) {
                for (const [field, message] of Object.entries(data.errors)) {
                  const fieldElement = document.getElementById(field);
                  if (fieldElement) {
                    fieldElement.classList.add('is-invalid');
                    let errorElement = fieldElement.nextElementSibling;
                    if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
                      errorElement = document.createElement('div');
                      errorElement.className = 'invalid-feedback';
                      fieldElement.parentNode.insertBefore(errorElement, fieldElement.nextSibling);
                    }
                    errorElement.textContent = message;
                  }
                }
              }
              alert(data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al procesar la solicitud');
          });
        });
      }
      
      // Limpiar errores al cambiar los campos
      document.querySelectorAll('#formNuevaPractica input, #formNuevaPractica select').forEach(element => {
        element.addEventListener('change', function() {
          this.classList.remove('is-invalid');
          const errorElement = this.nextElementSibling;
          if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
          }
        });
      });
    }
    </script>
</body>
</html>