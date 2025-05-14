<?php
require_once '../includes/db.php';

// Obtener datos para los select
$materias = obtenerDatos($conn, "SELECT id_Materia, n_Materia FROM materias ORDER BY n_Materia");
$laboratorios = obtenerDatos($conn, "SELECT id_Laboratorio, n_Laboratorio FROM laboratorio ORDER BY n_Laboratorio");
$grupos = obtenerDatos($conn, "SELECT id_Grupo, CONCAT(semestre, '-', grupo) AS nombre FROM grupos ORDER BY semestre, grupo");
$profesores = obtenerDatos($conn, "SELECT id_Usuario, nombre, apellido FROM usuarios WHERE id_TipoUsuario = 2 ORDER BY nombre");

function obtenerDatos($conn, $query) {
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}
?>

<form id="formNuevaPractica">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="materia" class="form-label">Materia</label>
            <select class="form-select" id="materia" name="materia" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($materias as $m): ?>
                    <option value="<?= $m['id_Materia'] ?>"><?= htmlspecialchars($m['n_Materia']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="laboratorio" class="form-label">Laboratorio</label>
            <select class="form-select" id="laboratorio" name="laboratorio" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($laboratorios as $lab): ?>
                    <option value="<?= $lab['id_Laboratorio'] ?>"><?= htmlspecialchars($lab['n_Laboratorio']) ?></option>
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
                    <option value="<?= $g['id_Grupo'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="profesor" class="form-label">Profesor</label>
            <select class="form-select" id="profesor" name="profesor" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($profesores as $p): ?>
                    <option value="<?= $p['id_Usuario'] ?>"><?= htmlspecialchars($p['nombre'].' '.$p['apellido']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="hora_inicio" class="form-label">Hora inicio</label>
            <select class="form-select" id="hora_inicio" name="hora_inicio" required>
                <option value="">Seleccionar...</option>
                <?php for ($h = 7; $h <= 16; $h++): ?>
                    <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00', $h) ?></option>
                    <option value="<?= sprintf('%02d:30', $h) ?>"><?= sprintf('%02d:30', $h) ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="hora_fin" class="form-label">Hora fin</label>
            <select class="form-select" id="hora_fin" name="hora_fin" required>
                <option value="">Seleccionar...</option>
                <?php for ($h = 7; $h <= 16; $h++): ?>
                    <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00', $h) ?></option>
                    <option value="<?= sprintf('%02d:30', $h) ?>"><?= sprintf('%02d:30', $h) ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Práctica</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#formNuevaPractica').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '../controllers/guardar_practica.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#nuevaPracticaModal').modal('hide');
                    window.location.href = response.redirect;
                } else {
                    alert(response.message);
                    // Mostrar errores específicos si existen
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key).after('<div class="invalid-feedback">' + value + '</div>');
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    });
});
</script>