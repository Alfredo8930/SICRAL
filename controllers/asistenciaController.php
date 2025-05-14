<?php
include('../includes/db.php');
date_default_timezone_set('America/Mexico_City');

$practica = 1; // ID de la práctica actual
$hora_Salida = "09:00:00";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');

    if ($codigo === '') {
        die('Código inválido');
    }

    // Buscar usuario por código (con nombre)
    $stmt = $conn->prepare("SELECT u.id_Usuario, u.nombre, u.apellido 
                          FROM generador_barra g
                          JOIN Usuarios u ON g.id_Usuario = u.id_Usuario
                          WHERE g.codigo_Barra = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo "Usuario no encontrado.";
        exit;
    }

    $usuario = $resultado->fetch_assoc();
    $usuario_id = $usuario['id_Usuario'];
    $nombre = $usuario['nombre'] . ' ' . $usuario['apellido'];
    
    // Obtener hora actual
    $hora_actual = date('H:i:s');
    
    // Obtener fecha y horarios de la práctica
    $stmt_practica = $conn->prepare("SELECT fecha, hora_Inicio, hora_fin 
                                   FROM practicas_Fechas 
                                   WHERE id_Practica = ?");
    $stmt_practica->bind_param("i", $practica);
    $stmt_practica->execute();
    $practica_data = $stmt_practica->get_result()->fetch_assoc();
    
    if (!$practica_data) {
        die("Práctica no configurada correctamente");
    }
    
    $fecha_practica = $practica_data['fecha'];
    $hora_entrada_limite = $practica_data['hora_Inicio'];

    // Verificar asistencia existente
    $verificar = $conn->prepare("SELECT id_Asistencia FROM asistencias 
                               WHERE id_Usuario = ? 
                               AND id_Practica = ?");
    $verificar->bind_param("ii", $usuario_id, $practica);
    $verificar->execute();
    $verificar_resultado = $verificar->get_result();

    if ($verificar_resultado->num_rows === 0) {
        // Determinar estado
        $estado = ($hora_actual <= $hora_entrada_limite) ? "A TIEMPO" : "RETARDO";

        // Insertar asistencia (sin fecha, se relaciona con práctica)
        $insertar = $conn->prepare("INSERT INTO asistencias 
                                  (id_Usuario, id_Practica, hora_Entrada, hora_Salida, estado_Asistencia) 
                                  VALUES (?, ?, ?, ?, ?)");
        $insertar->bind_param("iisss", $usuario_id, $practica, $hora_actual, $hora_Salida, $estado);
        
        if ($insertar->execute()) {
            echo "$nombre registrado con éxito. Estado: $estado";
        } else {
            echo "Error al registrar asistencia: " . $conn->error;
        }
    } else {
        echo "$nombre ya tiene registro para esta práctica.";
    }

    // Cerrar conexiones
    $stmt->close();
    $stmt_practica->close();
    $verificar->close();
    if (isset($insertar)) $insertar->close();
    $conn->close();
}
?>