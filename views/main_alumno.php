<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> SRL </title>
  <!-- Bootstrap CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
  <div class="container-fluid p-0">
    <div class="row g-0">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar">
        <div class="text-center py-3">
          <img src="../assets/img/logo_itss.png" alt="Logo" class="img-fluid" style="max-height: 80px;">
        </div>
        <nav class="nav flex-column text-center">
          <a class="nav-link active" href="#" data-section="datos">
            <i class="fas fa-user me-2"></i>Datos
          </a>
          <a class="nav-link" href="#" data-section="horario">
            <i class="fas fa-clock me-2"></i>Horario
          </a>
          <a class="nav-link" href="#" data-section="codigo">
            <i class="fas fa-barcode me-2"></i>Código de barra
          </a>
          <a class="nav-link" href="#" data-section="asistencia">
            <i class="fas fa-clipboard-check me-2"></i>Asistencia
          </a>
          <a class="nav-link" href="#" data-section="permisos">
            <i class="fas fa-key me-2"></i>Permisos
          </a>
          <a class="dropdown-item" href="../includes/logout.php">
            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
          </a>
        </nav>
      </div>
    
      <!-- Main Content -->
      <div class="col-md-10">
        <div class="header">
          <h2>Sistema de Registro en Laboratorios (SRL)</h2>
        </div>
        
        <div class="container mt-4">
          <div class="row mb-3">
            <div class="col-md-12 text-center">
              <h2>Bienvenido <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?></h2>
            </div>
          </div>
          <!-- SECCIÓN DE DATOS -->
          <div id="datos" class="section active">
            <div class="card">
              <div class="card-header bg-light text-center">
                <h5 class="mb-0">Datos Generales</h5>
              </div>
              
              <div class="card-body">
                <form>
                  <div class="row mb-3 justify-content-evenly">
                    <div class="col-md-5">
                      <div class="row">
                        <label class="col-md-3 col-form-label">NO. Control:</label>
                        <div class="col-md-2">
                          <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_id'] ?? 'Usuario') ?>" readonly>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-5">
                      <div class="row">
                        <label class="col-md-3 col-form-label">Email:</label>
                        <div class="col-md-6">
                          <input type="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user_email'] ?? 'Usuario') ?>" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row mb-3 justify-content-evenly">
                    <div class="col-md-5">
                      <div class="row">
                        <label class="col-md-3 col-form-label">Nombre:</label>
                        <div class="col-md-6">
                          <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>" readonly>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <div class="row">
                        <label class="col-md-3 col-form-label">Apellido:</label>
                        <div class="col-md-6">
                          <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_lastname'] ?? 'Usuario') ?>" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row mb-3 justify-content-evenly">
                    <div class="col-md-5">
                      <div class="row">
                        <label class="col-md-3 col-form-label">Carrera:</label>
                        <div class="col-md-6">
                          <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_career'] ?? 'Usuario') ?>" readonly>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <div class="row">
                        <label class="col-md-3 col-form-label">Telefono:</label>
                        <div class="col-md-6">
                          <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_phone'] ?? 'Usuario') ?>" readonly>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3 justify-content-evenly">
                    <div class="col-md-5">
                      <div class="row">
                        <label class="col-md-3 col-form-label">Semestre:</label>
                        <div class="col-md-2">
                          <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_semester'] ?? 'No registrado') ?>" readonly>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-5">
                      <div class="row">
                        <label class="col-md-3 col-form-label">Grupo:</label>
                        <div class="col-md-2">
                          <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_group'] ?? 'No registrado') ?>" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row mt-4">
                    <div class="col-12 text-center">
                      <button type="button" class="btn btn-modify px-4" id="btnModificar">Modificar</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          
          <!-- SECCIÓN DE HORARIO -->
          <div id="asistencia" class="section">
            <div class="card">
            </div>
          </div>

          <div id="horario" class="section">
            <div class="card">
            <?php include "../horario.php"?>
            </div>
          </div>
          
      <!-- SECCIÓN DE CÓDIGO DE BARRA -->
      <div id="codigo" class="section">
          <div class="card">
              <div class="card-header bg-light text-center">
                  <h5 class="mb-0">Código de barra</h5>
              </div>
              <div class="card-body text-center">
                  <h4><?= htmlspecialchars(($_SESSION['user_name'] ?? '') . ' ' . ($_SESSION['user_lastname'] ?? '')) ?></h4>
                  <p class="text-muted mb-3">Código: <span id="codigoBarrasText"><?= $_SESSION['codigo_barras'] ?? 'No disponible' ?></span></p>
                  
                  <div class="my-4 d-flex justify-content-center">
                      <svg id="barcode" class="border p-2 bg-white" style="max-width: 100%; height: auto;"></svg>
                  </div>
                  
                  <div class="d-flex justify-content-center gap-3 mt-3">
                      <button type="button" class="btn btn-success" id="btnImprimirCodigo">
                          <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                      </button>
                      <button type="button" class="btn btn-primary" id="btnDescargarCodigo">
                          <i class="fas fa-download me-2"></i>Descargar Imagen
                      </button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Canvas oculto para conversión a imagen -->
      <canvas id="barcodeCanvas" style="display: none;"></canvas>
          
          <!-- SECCIÓN DE ASISTENCIA (Placeholder) -->
          <div id="otro" class="section">
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
                      <tr>
                        <td>DESARROLLO WEB</td>
                        <td>30/04/2025</td>
                        <td>1:15 pm</td>
                        <td>2:05 pm</td>
                        <td>Asistió</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          
          <!-- SECCIÓN DE PERMISOS (Placeholder) -->
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
                      <option>DESARROLLO WEB</option>
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
      </div>
    </div>
  </div>

  <!-- SECCIÓN DE LECTURA DE CÓDIGOS -->
  <div id="lector" class="section">
      <div class="card">
          <div class="card-header bg-light text-center">
              <h5 class="mb-0">Registrar asistencia</h5>
          </div>
          <div class="card-body text-center">
              <div class="scanner-container">
                  <div id="interactive" class="viewport"></div>
                  <button id="startScanner" class="btn btn-primary mt-3">
                      <i class="fas fa-camera me-2"></i>Iniciar Escáner
                  </button>
                  <button id="stopScanner" class="btn btn-danger mt-3" style="display:none;">
                      <i class="fas fa-stop me-2"></i>Detener Escáner
                  </button>
                  <div id="result" class="alert alert-info mt-3" style="display:none;"></div>
              </div>
              
              <div class="mt-4">
                  <h5>O usar lector USB</h5>
                  <p class="text-muted">Enfoca el código de barras o escanea con un lector USB</p>
                  <input type="text" id="barcodeInput" class="form-control text-center" 
                        placeholder="El código aparecerá aquí automáticamente">
              </div>
          </div>
      </div>
  </div>

  <!-- Bootstrap Bundle with Popper -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
  <!-- Incluir jsPDF para generación de PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  <!-- Incluir JsBarcode desde CDN -->
      <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
  
  <!-- Custom JavaScript for navigation -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const navLinks = document.querySelectorAll('.nav-link');
      
      navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Remove active class from all links and sections
          navLinks.forEach(item => item.classList.remove('active'));
          document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
          
          // Add active class to clicked link
          this.classList.add('active');
          
          // Show corresponding section
          const sectionId = this.getAttribute('data-section');
          document.getElementById(sectionId).classList.add('active');
        });
      });
    });




  // Función para mostrar la sección activa al cargar la página
  document.addEventListener('DOMContentLoaded', function() {
      // Configuración inicial del código de barras
      const codigoBarra = '<?= $_SESSION['codigo_barras'] ?? '' ?>';
      
      if (codigoBarra) {
          generarCodigoBarras(codigoBarra);
      } else {
          mostrarErrorCodigo();
      }
      
      // Event listeners para los botones
      document.getElementById('btnImprimirCodigo').addEventListener('click', descargarPDF);
      document.getElementById('btnDescargarCodigo').addEventListener('click', descargarImagen);
  });

  function generarCodigoBarras(codigo) {
      try {
          JsBarcode("#barcode", codigo, {
              format: "CODE128",
              lineColor: "#000",
              width: 2,
              height: 80,
              displayValue: true,
              fontSize: 14,
              margin: 10,
              textMargin: 5
          });
      } catch (error) {
          console.error("Error al generar código de barras:", error);
          mostrarErrorCodigo();
      }
  }

  function mostrarErrorCodigo() {
      const barcodeContainer = document.getElementById('barcode');
      barcodeContainer.innerHTML = `
          <text x="50%" y="50%" fill="red" text-anchor="middle" 
                font-family="Arial" font-size="14">
              Error al generar código de barras
          </text>`;
  }

  function descargarPDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();
      
      const nombre = '<?= ($_SESSION['user_name'] ?? '') . ' ' . ($_SESSION['user_lastname'] ?? '') ?>';
      const codigo = document.getElementById('codigoBarrasText').textContent;
      const fecha = new Date().toLocaleDateString();
      
      // Convertir SVG a imagen
      const svg = document.getElementById('barcode');
      const canvas = document.getElementById('barcodeCanvas');
      const ctx = canvas.getContext('2d');
      const data = new XMLSerializer().serializeToString(svg);
      const img = new Image();
      
      img.onload = function() {
          canvas.width = img.width;
          canvas.height = img.height;
          ctx.drawImage(img, 0, 0);
          
          // Añadir imagen al PDF
          const imgData = canvas.toDataURL('image/png');
          
          // Configuración del PDF
          doc.setFont('helvetica', 'normal');
          doc.setFontSize(16);
          doc.text('Sistema de Registro en Laboratorios (SRL)', 105, 15, { align: 'center' });
          
          doc.setFontSize(12);
          doc.text('Código de Barras - ' + fecha, 105, 25, { align: 'center' });
          
          // Añadir información del usuario
          doc.setFontSize(14);
          doc.text('Nombre: ' + nombre, 15, 40);
          doc.text('Código: ' + codigo, 15, 50);
          
          // Añadir imagen del código de barras
          doc.addImage(imgData, 'PNG', 50, 60, 110, 50);
          
          // Añadir pie de página
          doc.setFontSize(10);
          doc.setTextColor(100);
          doc.text('Generado automáticamente por el SRL', 105, 150, { align: 'center' });
          
          // Descargar el PDF
          doc.save('codigo_barras_' + nombre.replace(/ /g, '_') + '.pdf');
      };
      
      img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(data)));
  }

  function descargarImagen() {
      const codigo = '<?= $_SESSION['codigo_barras'] ?? '' ?>';
      const nombre = '<?= ($_SESSION['user_name'] ?? 'Usuario') ?>';
      
      if (!codigo) {
          alert('No se encontró código de barras para descargar');
          return;
      }

      const svg = document.getElementById('barcode');
      const canvas = document.getElementById('barcodeCanvas');
      const ctx = canvas.getContext('2d');
      const data = new XMLSerializer().serializeToString(svg);
      const img = new Image();
      
      img.onload = function() {
          canvas.width = img.width;
          canvas.height = img.height;
          ctx.drawImage(img, 0, 0);
          
          const link = document.createElement('a');
          link.download = `codigo_barras_${nombre}_${codigo}.png`;
          link.href = canvas.toDataURL('image/png');
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
      };
      
      img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(data)));
  }

  </script>
</body>
</html>