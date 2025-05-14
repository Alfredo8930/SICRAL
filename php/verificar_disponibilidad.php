<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$response = ['disponible' => false, 'conflictos' => []];

try {
    $laboratorio = $_GET['laboratorio'] ?? null;
    $fecha = $_GET['fecha'] ?? null;
    $hora_inicio = $_GET['hora_inicio'] ?? null;
    $hora_fin = $_GET['hora_fin'] ?? null;

    if (!$laboratorio || !$fecha || !$hora_inicio || !$hora_fin) {
        throw new Exception('Datos incompletos');
    }

    $query = "SELECT m.n_Materia, CONCAT(u.nombre, ' ', u.apellido) AS profesor,
              TIME_FORMAT(pf.hora_Inicio, '%H:%i') AS hora_inicio,
              TIME_FORMAT(pf.hora_Fin, '%H:%i') AS hora_fin
              FROM practicas_fechas pf
              JOIN practicas p ON pf.id_PracticaFecha = p.id_Practica
              JOIN materias m ON p.id_Materia = m.id_Materia
              JOIN usuarios u ON p.id_Usuario = u.id_Usuario
              WHERE p.id_Laboratorio = ? AND pf.fecha = ?
              AND ((pf.hora_Inicio < ? AND pf.hora_Fin > ?)
              OR (pf.hora_Inicio < ? AND pf.hora_Fin > ?)
              OR (pf.hora_Inicio >= ? AND pf.hora_Fin <= ?))";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isssssss", 
        $laboratorio, $fecha,
        $hora_fin, $hora_inicio,
        $hora_fin, $hora_inicio,
        $hora_inicio, $hora_fin
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $response['conflictos'][] = $row;
    }

    $response['disponible'] = empty($response['conflictos']);

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>