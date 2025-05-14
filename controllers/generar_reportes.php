<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Obtener datos para filtros
$materiasQuery = "SELECT id_Materia, n_Materia FROM materias ORDER BY n_Materia";
$materiasResult = mysqli_query($conn, $materiasQuery);
$materias = [];
if ($materiasResult) {
    while ($row = mysqli_fetch_assoc($materiasResult)) {
        $materias[] = $row;
    }
}

$laboratoriosQuery = "SELECT id_Laboratorio, n_Laboratorio FROM laboratorio ORDER BY n_Laboratorio";
$laboratoriosResult = mysqli_query($conn, $laboratoriosQuery);
$laboratorios = [];
if ($laboratoriosResult) {
    while ($row = mysqli_fetch_assoc($laboratoriosResult)) {
        $laboratorios[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRL - Reportes</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/styles-2.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <div class="card-header bg-light">
              <h5 class="card-title mb-0">Generar Reportes</h5>
            </div>
            <div class="card-body">
              <form id="reportForm" method="POST" action="../controllers/generar_reportes.php">
                <div class="row mb-4">
                  <div class="col-md-3">
                    <label for="tipoReporte" class="form-label">Tipo de Reporte</label>
                    <select class="form-select" id="tipoReporte" name="tipoReporte" required>
                      <option value="">Seleccione...</option>
                      <option value="asistencias">Asistencias</option>
                      <option value="permisos">Permisos</option>
                      <option value="practicas">Prácticas</option>
                      <option value="uso_laboratorios">Uso de Laboratorios</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" required>
                  </div>
                  <div class="col-md-3">
                    <label for="fechaFin" class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" id="fechaFin" name="fechaFin" required>
                  </div>
                  <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                      <i class="fas fa-file-export me-1"></i> Generar
                    </button>
                  </div>
                </div>
                
                <div class="row mb-4" id="filtrosAdicionales">
                  <!-- Filtros adicionales se cargarán dinámicamente según el tipo de reporte -->
                </div>
              </form>
              
              <div class="row">
                <div class="col-md-8">
                  <div class="card">
                    <div class="card-header bg-light">
                      <h5 class="card-title mb-0">Estadísticas de Asistencia</h5>
                    </div>
                    <div class="card-body">
                      <canvas id="asistenciaChart" height="250"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-header bg-light">
                      <h5 class="card-title mb-0">Uso de Laboratorios</h5>
                    </div>
                    <div class="card-body">
                      <canvas id="laboratorioChart" height="250"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="card mt-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0">Reporte Generado</h5>
                  <div>
                    <button class="btn btn-sm btn-success me-2" id="btnExportPDF">
                      <i class="fas fa-file-pdf me-1"></i> Exportar PDF
                    </button>
                    <button class="btn btn-sm btn-secondary" id="btnExportExcel">
                      <i class="fas fa-file-excel me-1"></i> Exportar Excel
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped" id="reportTable">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Materia</th>
                          <th>Laboratorio</th>
                          <th>Asistencias</th>
                          <th>Faltas</th>
                          <th>Porcentaje</th>
                        </tr>
                      </thead>
                      <tbody id="reportTableBody">
                        <!-- Datos del reporte se cargarán aquí -->
                      </tbody>
                    </table>
                  </div>
                </div>
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
  <!-- jsPDF with AutoTable plugin -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
  <!-- SheetJS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Inicializar fecha actual en los inputs de fecha
      const today = new Date();
      const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
      
      document.getElementById('fechaFin').valueAsDate = today;
      document.getElementById('fechaInicio').valueAsDate = firstDayOfMonth;
      
      // Gráfico de asistencias
      const ctxAsistencia = document.getElementById('asistenciaChart').getContext('2d');
      const asistenciaChart = new Chart(ctxAsistencia, {
        type: 'bar',
        data: {
          labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
          datasets: [{
            label: 'Asistencias',
            data: [65, 59, 80, 81, 56, 55],
            backgroundColor: 'rgba(13, 59, 52, 0.7)',
            borderColor: 'rgba(13, 59, 52, 1)',
            borderWidth: 1
          }, {
            label: 'Faltas',
            data: [5, 10, 4, 3, 8, 2],
            backgroundColor: 'rgba(139, 32, 66, 0.7)',
            borderColor: 'rgba(139, 32, 66, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
      
      // Gráfico de uso de laboratorios
      const ctxLaboratorio = document.getElementById('laboratorioChart').getContext('2d');
      const laboratorioChart = new Chart(ctxLaboratorio, {
        type: 'doughnut',
        data: {
          labels: ['Lab 1', 'Lab 2', 'Lab 3', 'Lab 4'],
          datasets: [{
            data: [30, 45, 25, 10],
            backgroundColor: [
              'rgba(13, 59, 52, 0.7)',
              'rgba(139, 32, 66, 0.7)',
              'rgba(107, 81, 61, 0.7)',
              'rgba(59, 89, 152, 0.7)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true
        }
      });
      
      // Cambiar filtros según tipo de reporte
      document.getElementById('tipoReporte').addEventListener('change', function() {
        const tipo = this.value;
        const filtrosDiv = document.getElementById('filtrosAdicionales');
        
        let html = '';
        if (tipo === 'asistencias' || tipo === 'practicas') {
          html = `
            <div class="col-md-4">
              <label for="materia" class="form-label">Materia</label>
              <select class="form-select" id="materia" name="materia">
                <option value="">Todas</option>
                <?php foreach ($materias as $mat): ?>
                  <option value="<?= $mat['id_Materia'] ?>"><?= htmlspecialchars($mat['n_Materia']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="laboratorio" class="form-label">Laboratorio</label>
              <select class="form-select" id="laboratorio" name="laboratorio">
                <option value="">Todos</option>
                <?php foreach ($laboratorios as $lab): ?>
                  <option value="<?= $lab['id_Laboratorio'] ?>"><?= htmlspecialchars($lab['n_Laboratorio']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="grupo" class="form-label">Grupo</label>
              <input type="text" class="form-control" id="grupo" name="grupo" placeholder="Opcional">
            </div>
          `;
        } else if (tipo === 'permisos') {
          html = `
            <div class="col-md-6">
              <label for="estadoPermiso" class="form-label">Estado</label>
              <select class="form-select" id="estadoPermiso" name="estadoPermiso">
                <option value="">Todos</option>
                <option value="PEN">Pendientes</option>
                <option value="APR">Aprobados</option>
                <option value="REC">Rechazados</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="usuario" class="form-label">Usuario (opcional)</label>
              <input type="text" class="form-control" id="usuario" name="usuario" placeholder="No. Control o Nombre">
            </div>
          `;
        } else if (tipo === 'uso_laboratorios') {
          html = `
            <div class="col-md-6">
              <label for="laboratorio" class="form-label">Laboratorio</label>
              <select class="form-select" id="laboratorio" name="laboratorio">
                <option value="">Todos</option>
                <?php foreach ($laboratorios as $lab): ?>
                  <option value="<?= $lab['id_Laboratorio'] ?>"><?= htmlspecialchars($lab['n_Laboratorio']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label for="tipoUso" class="form-label">Tipo de Uso</label>
              <select class="form-select" id="tipoUso" name="tipoUso">
                <option value="">Todos</option>
                <option value="clase">Clases</option>
                <option value="practica">Prácticas</option>
                <option value="extraordinario">Actividades Extraordinarias</option>
              </select>
            </div>
          `;
        }
        
        filtrosDiv.innerHTML = html;
      });
      
      // Manejar envío del formulario
      document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Obtener datos del formulario
        const formData = new FormData(this);
        
        // Realizar petición AJAX
        fetch('../controllers/generar_reportes.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            alert('Error: ' + data.error);
            return;
          }
          
          // Actualizar tabla con resultados
          actualizarTablaResultados(data.data);
          
          // Actualizar gráficas con nuevos datos
          if (data.graficos) {
            actualizarGraficos(data.graficos);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Ocurrió un error al generar el reporte.');
        });
      });
      
      // Función para actualizar la tabla de resultados
      function actualizarTablaResultados(data) {
        const tbody = document.getElementById('reportTableBody');
        tbody.innerHTML = '';
        
        if (data.length === 0) {
          const tr = document.createElement('tr');
          tr.innerHTML = '<td colspan="6" class="text-center">No se encontraron resultados</td>';
          tbody.appendChild(tr);
          return;
        }
        
        data.forEach(row => {
          const tr = document.createElement('tr');
          
          // Ajustar columnas según el tipo de reporte
          const tipoReporte = document.getElementById('tipoReporte').value;
          
          if (tipoReporte === 'asistencias' || tipoReporte === 'practicas') {
            tr.innerHTML = `
              <td>${formatDate(row.fecha)}</td>
              <td>${row.materia}</td>
              <td>${row.laboratorio}</td>
              <td>${row.asistencias}</td>
              <td>${row.faltas}</td>
              <td>${row.porcentaje}%</td>
            `;
          } else if (tipoReporte === 'permisos') {
            tr.innerHTML = `
              <td>${formatDate(row.fecha)}</td>
              <td>${row.usuario}</td>
              <td>${row.tipo_permiso}</td>
              <td>${getEstadoPermiso(row.estado)}</td>
              <td>${row.aprobador || '-'}</td>
              <td>${formatDate(row.fecha_respuesta) || '-'}</td>
            `;
          } else if (tipoReporte === 'uso_laboratorios') {
            tr.innerHTML = `
              <td>${formatDate(row.fecha)}</td>
              <td>${row.laboratorio}</td>
              <td>${row.tipo_uso}</td>
              <td>${row.horas_uso}</td>
              <td>${row.responsable}</td>
              <td>${row.capacidad_utilizada}%</td>
            `;
          }
          
          tbody.appendChild(tr);
        });
      }
      
      // Función para actualizar gráficos con nuevos datos
      function actualizarGraficos(data) {
        // Actualizar gráfico de asistencias
        if (data.asistencias) {
          asistenciaChart.data.labels = data.asistencias.labels;
          asistenciaChart.data.datasets[0].data = data.asistencias.asistencias;
          asistenciaChart.data.datasets[1].data = data.asistencias.faltas;
          asistenciaChart.update();
        }
        
        // Actualizar gráfico de uso de laboratorios
        if (data.laboratorios) {
          laboratorioChart.data.labels = data.laboratorios.labels;
          laboratorioChart.data.datasets[0].data = data.laboratorios.valores;
          laboratorioChart.update();
        }
      }
      
      // Función para formatear fechas
      function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('es-MX');
      }
      
      // Función para obtener texto de estado de permiso
      function getEstadoPermiso(estado) {
        const estados = {
          'PEN': '<span class="badge bg-warning">Pendiente</span>',
          'APR': '<span class="badge bg-success">Aprobado</span>',
          'REC': '<span class="badge bg-danger">Rechazado</span>'
        };
        return estados[estado] || estado;
      }
      
      // Exportar a PDF
      document.getElementById('btnExportPDF').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Título del documento
        const tipoReporte = document.getElementById('tipoReporte').value;
        const tituloReporte = getTituloReporte(tipoReporte);
        
        // Añadir información de cabecera
        doc.setFontSize(16);
        doc.text('Sistema de Registro en Laboratorios (SRL)', 105, 15, { align: 'center' });
        doc.setFontSize(14);
        doc.text(tituloReporte, 105, 25, { align: 'center' });
        
        // Fechas del reporte
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        doc.setFontSize(10);
        doc.text(`Periodo: ${formatDate(fechaInicio)} al ${formatDate(fechaFin)}`, 105, 35, { align: 'center' });
        
        // Añadir fecha de generación
        const today = new Date();
        doc.text(`Fecha de generación: ${today.toLocaleDateString('es-MX')}`, 105, 40, { align: 'center' });
        
        // Añadir tabla
        doc.autoTable({
          html: '#reportTable',
          startY: 45,
          theme: 'grid',
          headStyles: {
            fillColor: [13, 59, 52],
            textColor: 255
          }
        });
        
        // Nombre del archivo
        const fileName = `reporte_${tipoReporte}_${today.getTime()}.pdf`;
        doc.save(fileName);
      });
      
      // Función para obtener título según tipo de reporte
      function getTituloReporte(tipo) {
        const titulos = {
          'asistencias': 'Reporte de Asistencias',
          'permisos': 'Reporte de Permisos',
          'practicas': 'Reporte de Prácticas',
          'uso_laboratorios': 'Reporte de Uso de Laboratorios'
        };
        return titulos[tipo] || 'Reporte';
      }
      
      // Exportar a Excel
      document.getElementById('btnExportExcel').addEventListener('click', function() {
        const table = document.getElementById('reportTable');
        const tipoReporte = document.getElementById('tipoReporte').value;
        const tituloReporte = getTituloReporte(tipoReporte);
        
        // Crear libro y hoja
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(table);
        
        // Añadir hoja al libro
        XLSX.utils.book_append_sheet(wb, ws, "Reporte");
        
        // Nombre del archivo
        const fileName = `reporte_${tipoReporte}_${new Date().getTime()}.xlsx`;
        
        // Guardar archivo
        XLSX.writeFile(wb, fileName);
      });
      
      // Inicializar tabla con datos de ejemplo si no hay reporte cargado
      const datosEjemplo = [
        {
          fecha: '2025-05-01',
          materia: 'Programación Web',
          laboratorio: 'Laboratorio 1',
          asistencias: 25,
          faltas: 3,
          porcentaje: 89.3
        },
        {
          fecha: '2025-05-03',
          materia: 'Bases de Datos',
          laboratorio: 'Laboratorio 2',
          asistencias: 30,
          faltas: 0,
          porcentaje: 100
        },
        {
          fecha: '2025-05-05',
          materia: 'Inteligencia Artificial',
          laboratorio: 'Laboratorio 3',
          asistencias: 18,
          faltas: 2,
          porcentaje: 90
        }
      ];
      
      actualizarTablaResultados(datosEjemplo);
    });
  </script>
</body>
</html>