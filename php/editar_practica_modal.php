<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_GET['id'])) {
    die('ID no proporcionado');
}

$id_practica = (int)$_GET['id'];

// Obtener datos actuales de la práctica (CONSULTA CORREGIDA)
$query = "SELECT p.*, pf.fecha, pf.hora_Inicio, pf.hora_Fin 
          FROM practicas p
          JOIN practicas_fechas pf ON p.id_Practica = pf.id_Practica  -- CORRECCIÓN IMPORTANTE
          WHERE p.id_Practica = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_practica);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$practica = mysqli_fetch_assoc($result);

if (!$practica) {
    die('Práctica no encontrada');
}

// Obtener listas para los select
$materias = obtenerDatos($conn, "SELECT id_Materia, n_Materia FROM materias ORDER BY n_Materia");
$laboratorios = obtenerDatos($conn, "SELECT id_Laboratorio, n_Laboratorio FROM laboratorio ORDER BY n_Laboratorio");
$grupos = obtenerDatos($conn, "SELECT id_Grupo, CONCAT(semestre, '-', grupo) AS nombre FROM grupos ORDER BY semestre, grupo");
$profesores = obtenerDatos($conn, "SELECT id_Usuario, nombre, apellido FROM usuarios WHERE id_TipoUsuario = 2 ORDER BY nombre");
?>

<form id="formEditarPractica" data-id="<?= $id_practica ?>">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="materia" class="form-label">Materia</label>
            <select class="form-select" id="materia" name="materia" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($materias as $m): ?>
                    <option value="<?= $m['id_Materia'] ?>" <?= ($m['id_Materia'] == $practica['id_Materia']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['n_Materia']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="laboratorio" class="form-label">Laboratorio</label>
            <select class="form-select" id="laboratorio" name="laboratorio" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($laboratorios as $lab): ?>
                    <option value="<?= $lab['id_Laboratorio'] ?>" <?= ($lab['id_Laboratorio'] == $practica['id_Laboratorio']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lab['n_Laboratorio']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label for="grupo" class="form-label">Grupo</label>
            <select class="form-select" id="grupo" name="grupo" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($grupos as $g): ?>
                    <option value="<?= $g['id_Grupo'] ?>" <?= ($g['id_Grupo'] == $practica['id_Grupo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="profesor" class="form-label">Profesor</label>
            <select class="form-select" id="profesor" name="profesor" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($profesores as $p): ?>
                    <option value="<?= $p['id_Usuario'] ?>" <?= ($p['id_Usuario'] == $practica['id_Usuario']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d', strtotime($practica['fecha'])) ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="hora_inicio" class="form-label">Hora inicio</label>
            <select class="form-select" id="hora_inicio" name="hora_inicio" required>
                <option value="">Seleccionar...</option>
                <?php for ($h = 7; $h <= 16; $h++): ?>
                    <option value="<?= sprintf('%02d:00', $h) ?>" <?= (sprintf('%02d:00', $h) == $practica['hora_Inicio']) ? 'selected' : '' ?>>
                        <?= sprintf('%02d:00', $h) ?>
                    </option>
                    <option value="<?= sprintf('%02d:30', $h) ?>" <?= (sprintf('%02d:30', $h) == $practica['hora_Inicio']) ? 'selected' : '' ?>>
                        <?= sprintf('%02d:30', $h) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="hora_fin" class="form-label">Hora fin</label>
            <select class="form-select" id="hora_fin" name="hora_fin" required>
                <option value="">Seleccionar...</option>
                <?php for ($h = 7; $h <= 16; $h++): ?>
                    <option value="<?= sprintf('%02d:00', $h) ?>" <?= (sprintf('%02d:00', $h) == $practica['hora_Fin']) ? 'selected' : '' ?>>
                        <?= sprintf('%02d:00', $h) ?>
                    </option>
                    <option value="<?= sprintf('%02d:30', $h) ?>" <?= (sprintf('%02d:30', $h) == $practica['hora_Fin']) ? 'selected' : '' ?>>
                        <?= sprintf('%02d:30', $h) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#formEditarPractica').on('submit', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const formData = $(this).serialize();
        
        $.ajax({
            url: `../controllers/actualizar_practica.php?id=${id}`,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#editarPracticaModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error de conexión');
            }
        });
    });
});
</script>