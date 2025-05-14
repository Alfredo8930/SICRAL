<?php
require_once '../includes/db.php';

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$id_laboratorio = isset($_GET['laboratorio']) ? $_GET['laboratorio'] : '';

// Obtener laboratorios
if ($id_laboratorio) {
    $query_labs = "SELECT id_Laboratorio, n_Laboratorio FROM laboratorio WHERE id_Laboratorio = $id_laboratorio";
} else {
    $query_labs = "SELECT id_Laboratorio, n_Laboratorio FROM laboratorio ORDER BY n_Laboratorio";
}
$result_labs = mysqli_query($conn, $query_labs);
$laboratorios = mysqli_fetch_all($result_labs, MYSQLI_ASSOC);

// Generar horarios de 7:00 a 20:00 cada 30 minutos
$horarios = [];
for ($h = 7; $h <= 16; $h++) {
    $horarios[] = ['hora' => sprintf('%02d:00', $h)];
    if ($h < 16) {
        $horarios[] = ['hora' => sprintf('%02d:30', $h)];
    }
}

// Obtener prácticas existentes para la fecha
$query_ocupados = "SELECT l.id_Laboratorio, l.n_Laboratorio, 
                  m.n_Materia, CONCAT(u.nombre, ' ', u.apellido) AS profesor,
                  CONCAT(g.semestre, '-', g.grupo) AS grupo,
                  TIME(pf.hora_Inicio) AS hora_inicio, TIME(pf.hora_Fin) AS hora_fin
                  FROM practicas p
                  JOIN materias m ON p.id_Materia = m.id_Materia
                  JOIN laboratorio l ON p.id_Laboratorio = l.id_Laboratorio
                  JOIN grupos g ON p.id_Grupo = g.id_Grupo
                  JOIN practicas_fechas pf ON p.id_Practica = pf.id_Practica
                  JOIN usuarios u ON p.id_Usuario = u.id_Usuario
                  WHERE pf.fecha = '$fecha'";
if ($id_laboratorio) {
    $query_ocupados .= " AND p.id_Laboratorio = $id_laboratorio";
}
$result_ocupados = mysqli_query($conn, $query_ocupados);
$ocupados = mysqli_fetch_all($result_ocupados, MYSQLI_ASSOC);

// Preparar datos para el frontend
$ocupados_por_hora = [];
foreach ($ocupados as $ocupado) {
    $hora_actual = $ocupado['hora_inicio'];
    while ($hora_actual < $ocupado['hora_fin']) {
        $ocupados_por_hora[] = [
            'id_Laboratorio' => $ocupado['id_Laboratorio'],
            'hora' => substr($hora_actual, 0, 5),
            'materia' => $ocupado['n_Materia'],
            'profesor' => $ocupado['profesor'],
            'grupo' => $ocupado['grupo']
        ];
        
        // Avanzar 30 minutos
        $hora_actual = date('H:i:s', strtotime($hora_actual) + 1800);
    }
}

header('Content-Type: application/json');
echo json_encode([
    'laboratorios' => $laboratorios,
    'horarios' => $horarios,
    'ocupados' => $ocupados_por_hora
]);
?>