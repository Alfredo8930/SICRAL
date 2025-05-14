<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escáner de Asistencia - Laboratorio</title>
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <style>
        #scanner-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px;
        }
        #video {
            width: 100%;
            max-width: 500px;
            height: 300px;
            border: 2px solid #007bff;
            border-radius: 8px;
        }
        #result, #loading {
            margin-top: 15px;
            padding: 10px;
            font-size: 1.2em;
            background-color: #f8f9fa;
            border-radius: 4px;
            min-width: 300px;
            text-align: center;
        }
        .btn {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #loading {
            display: none;
            color: #555;
        }
    </style>
</head>
<body>
    <div id="scanner-container">
        <h2>Escanear Código del Alumno</h2>
        <video id="video"></video>
        <div id="result">📷 Acerca el código al escáner</div>
        <div id="loading">🔄 Buscando alumno...</div>
        <button id="toggleScanner" class="btn">Detener Escáner</button>
    </div>

    <audio id="beep" src="https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg" preload="auto"></audio>

    <script>
        const codeReader = new ZXing.BrowserMultiFormatReader();
        const videoElem = document.getElementById('video');
        const resultDiv = document.getElementById('result');
        const loadingDiv = document.getElementById('loading');
        const toggleBtn = document.getElementById('toggleScanner');
        const beepSound = document.getElementById('beep');

        let scannerActive = true;
        let lastScannedCode = "";

        // Formatos compatibles
        const formatsToSupport = [
            ZXing.BarcodeFormat.CODE_128,
            ZXing.BarcodeFormat.CODE_39,
            ZXing.BarcodeFormat.QR_CODE
        ];

        toggleBtn.addEventListener('click', () => {
            if (scannerActive) {
                stopScanner();
                toggleBtn.textContent = "Iniciar Escáner";
            } else {
                startScanner();
                toggleBtn.textContent = "Detener Escáner";
            }
            scannerActive = !scannerActive;
        });

        function startScanner() {
            codeReader.decodeFromVideoDevice(
                null,
                'video',
                (result, err) => {
                    if (result) {
                        const scannedCode = result.text.trim();
                        if (scannedCode !== lastScannedCode) {
                            lastScannedCode = scannedCode;
                            beepSound.play();
                            resultDiv.textContent = `✅ Código detectado: ${scannedCode}`;
                            processScannedCode(scannedCode);
                        }
                    }
                    if (err && !(err instanceof ZXing.NotFoundException)) {
                        console.error(err);
                        resultDiv.textContent = "⚠️ Error: " + err.message;
                    }
                },
                {
                    formats: formatsToSupport,
                    tryHarder: true,
                    constraints: {
                        facingMode: "environment",
                        focusMode: "continuous"
                    }
                }
            ).then(() => {
                console.log("Escáner iniciado");
            }).catch(err => {
                console.error(err);
                resultDiv.textContent = "❌ Error al iniciar cámara: " + err.message;
            });
        }

        function stopScanner() {
            codeReader.reset();
            console.log("Escáner detenido");
        }

// Modificar processScannedCode para mostrar loading
function processScannedCode(code) {
    loadingDiv.style.display = 'block';
    resultDiv.textContent = `✅ Código detectado: ${code}`;
    
    fetch(`../controllers/buscar_alumno.php?codigo=${encodeURIComponent(code)}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la red');
            return response.json();
        })
        .then(data => {
            loadingDiv.style.display = 'none';
            if (data.success) {
                resultDiv.innerHTML = `
                    <strong>Nombre:</strong> ${data.nombre}<br>
                    <strong>Correo:</strong> ${data.correo}<br>
                    <strong>Teléfono:</strong> ${data.telefono}<br>
                    <strong>Carrera:</strong> ${data.carrera}
                `;
            } else {
                resultDiv.textContent = data.message || "Alumno no encontrado";
            }
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            console.error("Error:", error);
            resultDiv.textContent = "Error al buscar alumno";
        });
}

// Añadir verificación de cámara al inicio
navigator.mediaDevices.getUserMedia({ video: true })
    .then(() => startScanner())
    .catch(err => {
        console.error(err);
        resultDiv.textContent = "Error: No se pudo acceder a la cámara. Verifica los permisos.";
        toggleBtn.disabled = true;
    });


        // Iniciar escáner al cargar
        window.addEventListener('load', () => {
            startScanner();
        });

        // Detener escáner al cerrar
        window.addEventListener('beforeunload', () => {
            if (scannerActive) {
                stopScanner();
            }
        });
    </script>
</body>
</html>
