<!-- En tu archivo HTML -->
<div class="scanner-container">
    <div id="interactive" class="viewport"></div>
    <button id="startScanner" class="btn btn-primary">Iniciar Escáner</button>
    <button id="stopScanner" class="btn btn-danger" style="display:none;">Detener Escáner</button>
    <div id="result" class="alert alert-info mt-3" style="display:none;"></div>
</div>

<!-- Agrega esto en tu sección de lector de códigos -->
<audio id="barcodeSound" preload="auto">
    <source src="../assets/sounds/barcode-beep.mp3" type="audio/mpeg">
    <source src="../assets/sounds/barcode-beep.ogg" type="audio/ogg">
</audio>

<!-- Incluir QuaggaJS -->
<script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>

<style>
.viewport {
    width: 100%;
    max-width: 500px;
    height: 300px;
    margin: 0 auto;
    border: 2px dashed #ccc;
    position: relative;
    overflow: hidden;
}
.viewport canvas, .viewport video {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startBtn = document.getElementById('startScanner');
    const stopBtn = document.getElementById('stopScanner');
    const resultDiv = document.getElementById('result');
    
    startBtn.addEventListener('click', function() {
        startScanner();
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-block';
    });
    
    stopBtn.addEventListener('click', function() {
        Quagga.stop();
        startBtn.style.display = 'inline-block';
        stopBtn.style.display = 'none';
        resultDiv.style.display = 'none';
    });
    
    function startScanner() {
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive'),
                constraints: {
                    width: 480,
                    height: 320,
                    facingMode: "environment" // Usar cámara trasera
                },
            },
            decoder: {
                readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader"]
            },
        }, function(err) {
            if (err) {
                console.error(err);
                resultDiv.textContent = "Error al iniciar el escáner: " + err.message;
                resultDiv.style.display = 'block';
                return;
            }
            Quagga.start();
        });

        Quagga.onDetected(function(result) {
            const code = result.codeResult.code;
            resultDiv.textContent = "Código detectado: " + code;
            resultDiv.style.display = 'block';
            
            // Aquí puedes hacer algo con el código leído
            procesarCodigoBarras(code);
            
            // Opcional: Detener después de leer
            Quagga.stop();
            startBtn.style.display = 'inline-block';
            stopBtn.style.display = 'none';
        });
    }
    
    function procesarCodigoBarras(codigo) {
        // Implementa lo que necesites hacer con el código leído
        console.log("Procesando código:", codigo);
        
        // Ejemplo: Verificar asistencia
        fetch('../controllers/procesar_codigo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert("Asistencia registrada para: " + data.nombre);
            } else {
                alert("Error: " + data.message);
            }
        });
    }
});
</script>