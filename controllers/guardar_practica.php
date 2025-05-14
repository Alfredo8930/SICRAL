<?php
require_once '../includes/db.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'errors' => []];

try {
    // Validar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar sesión
    session_start();
    if (!isset($_SESSION['user_email'])) {
        throw new Exception('No autorizado');
    }

    // Obtener y sanitizar datos
    $required = ['materia', 'laboratorio', 'grupo', 'profesor', 'fecha', 'hora_inicio', 'hora_fin'];
    $data = [];
    foreach ($required as $field) {
        $data[$field] = trim($_POST[$field] ?? '');
        if (empty($data[$field])) {
            $response['errors'][$field] = "Campo requerido";
        }
    }

    // Validaciones adicionales
    if (!empty($data['fecha']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['fecha'])) {
        $response['errors']['fecha'] = 'Formato de fecha inválido';
    }

    if (!empty($data['hora_inicio']) && !empty($data['hora_fin']) && $data['hora_inicio'] >= $data['hora_fin']) {
        $response['errors']['hora_fin'] = 'Debe ser posterior a la hora de inicio';
    }

    // Si hay errores, retornar
    if (!empty($response['errors'])) {
        $response['message'] = 'Corrija los errores';
        echo json_encode($response);
        exit;
    }

    // Verificar disponibilidad
    $disponible = verificarDisponibilidad($conn, $data);
    if (!$disponible['disponible']) {
        $response['message'] = 'Conflicto de horario';
        $response['conflictos'] = $disponible['conflictos'];
        echo json_encode($response);
        exit;
    }

    // Guardar en base de datos
    mysqli_begin_transaction($conn);

    // Insertar práctica principal
    $query = "INSERT INTO practicas (id_Materia, id_Laboratorio, id_Grupo, id_Usuario) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiii", $data['materia'], $data['laboratorio'], $data['grupo'], $data['profesor']);
    mysqli_stmt_execute($stmt);
    $id_practica = mysqli_insert_id($conn);

    // Insertar horario
    $query = "INSERT INTO practicas_fechas (id_Practica, fecha, hora_Inicio, hora_Fin) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isss", $id_practica, $data['fecha'], $data['hora_inicio'], $data['hora_fin']);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);

    $response['success'] = true;
    $response['message'] = 'Práctica registrada exitosamente';
    $response['redirect'] = 'listar_practicas.php';

} catch (Exception $e) {
    mysqli_rollback($conn);
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);

function verificarDisponibilidad($conn, $data) {
    $query = "SELECT m.n_Materia, CONCAT(u.nombre, ' ', u.apellido) AS profesor, 
              TIME_FORMAT(pf.hora_Inicio, '%H:%i') AS hora_inicio, 
              TIME_FORMAT(pf.hora_Fin, '%H:%i') AS hora_fin
              FROM practicas_fechas pf
              JOIN practicas p ON pf.id_Practica = p.id_Practica
              JOIN materias m ON p.id_Materia = m.id_Materia
              JOIN usuarios u ON p.id_Usuario = u.id_Usuario
              WHERE p.id_Laboratorio = ? AND pf.fecha = ?
              AND ((pf.hora_Inicio < ? AND pf.hora_Fin > ?) 
              OR (pf.hora_Inicio < ? AND pf.hora_Fin > ?) 
              OR (pf.hora_Inicio >= ? AND pf.hora_Fin <= ?))";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isssssss", 
        $data['laboratorio'], $data['fecha'],
        $data['hora_fin'], $data['hora_inicio'],
        $data['hora_fin'], $data['hora_inicio'],
        $data['hora_inicio'], $data['hora_fin']
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $conflictos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $conflictos[] = $row;
    }

    return [
        'disponible' => empty($conflictos),
        'conflictos' => $conflictos
    ];
}
?>