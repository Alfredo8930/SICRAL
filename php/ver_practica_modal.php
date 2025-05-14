<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_GET['id'])) {
    die('ID no proporcionado');
}

$id_practica = (int)$_GET['id'];

// Obtener datos de la práctica
$query = "SELECT p.id_Practica, m.n_Materia, l.n_Laboratorio, 
          CONCAT(g.semestre, '-', g.grupo) AS grupo, 
          CONCAT(u.nombre, ' ', u.apellido) AS profesor,
          pf.fecha, pf.hora_Inicio, pf.hora_Fin
          FROM practicas p
          JOIN materias m ON p.id_Materia = m.id_Materia
          JOIN laboratorio l ON p.id_Laboratorio = l.id_Laboratorio
          JOIN grupos g ON p.id_Grupo = g.id_Grupo
          JOIN usuarios u ON p.id_Usuario = u.id_Usuario
          JOIN practicas_fechas pf ON p.id_Practica = pf.id_Practica
          WHERE p.id_Practica = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_practica);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$practica = mysqli_fetch_assoc($result);

if (!$practica) {
    die('Práctica no encontrada');
}
?>

<div class="row">
    <div class="col-md-6">
        <h5>Información General</h5>
        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>Materia:</strong> <?= htmlspecialchars($practica['n_Materia']) ?></li>
            <li class="list-group-item"><strong>Laboratorio:</strong> <?= htmlspecialchars($practica['n_Laboratorio']) ?></li>
            <li class="list-group-item"><strong>Grupo:</strong> <?= htmlspecialchars($practica['grupo']) ?></li>
            <li class="list-group-item"><strong>Profesor:</strong> <?= htmlspecialchars($practica['profesor']) ?></li>
        </ul>
    </div>
    <div class="col-md-6">
        <h5>Horario</h5>
        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($practica['fecha'])) ?></li>
            <li class="list-group-item"><strong>Hora Inicio:</strong> <?= date('H:i', strtotime($practica['hora_Inicio'])) ?></li>
            <li class="list-group-item"><strong>Hora Fin:</strong> <?= date('H:i', strtotime($practica['hora_Fin'])) ?></li>
            <li class="list-group-item"><strong>Duración:</strong> <?= calcularDuracion($practica['hora_Inicio'], $practica['hora_Fin']) ?></li>
        </ul>
    </div>
</div>

<div class="d-flex justify-content-between mt-3">
    <button class="btn btn-warning editar-desde-modal" data-id="<?= $practica['id_Practica'] ?>">
        <i class="fas fa-edit me-1"></i> Editar
    </button>
    <button class="btn btn-danger eliminar-desde-modal" data-id="<?= $practica['id_Practica'] ?>">
        <i class="fas fa-trash-alt me-1"></i> Eliminar
    </button>
</div>

<script>
// Script para manejar los botones dentro del modal
$(document).ready(function() {
    $('.editar-desde-modal').click(function() {
        const id = $(this).data('id');
        $('#verPracticaModal').modal('hide');
        setTimeout(() => {
            $(`button.editar-practica[data-id="${id}"]`).click();
        }, 500);
    });
    
    $('.eliminar-desde-modal').click(function() {
        const id = $(this).data('id');
        $('#verPracticaModal').modal('hide');
        setTimeout(() => {
            $(`button.eliminar-practica[data-id="${id}"]`).click();
        }, 500);
    });
});
</script>