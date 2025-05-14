<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Escanear Código</title>
  <script src="https://unpkg.com/@ericblade/quagga2@1.2.6/dist/quagga.min.js"></script>

  <link rel="stylesheet" href="../assets/css/styles.css">
      <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container text-center mt-5">
    <h2>Escanea tu Credencial</h2>
    <div id="scanner"></div>
    <p id="resultado" class="mt-3 fw-bold text-success"></p>
  </div>

  <form id="formEscaneo" method="POST" action="http://localhost/SISTEMA_SRAL/controllers/asistenciaController.php">
    <input type="hidden" name="codigo" id="codigo">
  </form>

  <script>
    Quagga.init({
      inputStream: {
        name: "Live",
        type: "LiveStream",
        target: document.querySelector('#scanner'),
        constraints: {
          facingMode: "environment" // Usa cámara trasera si es posible
        },
      },
      decoder: {
        readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader"]
      }
    }, function (err) {
      if (err) {
        console.error(err);
        return;
      }
      Quagga.start();
    });

    Quagga.onDetected(function (data) {
      let codigo = data.codeResult.code;
      document.getElementById('resultado').textContent = "Código detectado: " + codigo;
      document.getElementById('codigo').value = codigo;

      // Detiene el escáner y envía el formulario
      Quagga.stop();
      document.getElementById('formEscaneo').submit();
    });
  </script>
</body>
</html>
