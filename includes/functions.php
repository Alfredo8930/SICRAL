<?php
/**
 * Archivo de funciones auxiliares para el sistema de gestión de prácticas
 */

/**
 * Convierte caracteres especiales a entidades HTML y elimina espacios en blanco
 * @param string $data Cadena a sanitizar
 * @return string Cadena sanitizada
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Valida una fecha en formato YYYY-MM-DD
 * @param string $date Fecha a validar
 * @return bool True si es válida, false si no
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Valida un horario en formato HH:MM
 * @param string $time Horario a validar
 * @return bool True si es válido, false si no
 */
function validateTime($time) {
    return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
}

/**
 * Verifica si un laboratorio está disponible en un horario específico
 * @param mysqli $conn Conexión a la base de datos
 * @param int $labId ID del laboratorio
 * @param string $date Fecha en formato YYYY-MM-DD
 * @param string $startTime Hora de inicio en formato HH:MM
 * @param string $endTime Hora de fin en formato HH:MM
 * @param int $excludePracticeId ID de práctica a excluir (para ediciones)
 * @return array Array con 'disponible' (bool) y 'conflictos' si los hay
 */
function checkLabAvailability($conn, $labId, $date, $startTime, $endTime, $excludePracticeId = null) {
    $response = [
        'disponible' => true,
        'conflictos' => []
    ];

    $query = "SELECT p.id_Practica, m.n_Materia, u.nombre, u.apellido, 
              TIME_FORMAT(pf.hora_Inicio, '%H:%i') AS hora_inicio, 
              TIME_FORMAT(pf.hora_Fin, '%H:%i') AS hora_fin
              FROM practicas_fechas pf
              JOIN practicas p ON pf.id_PracticaFecha = p.id_Practica
              JOIN materias m ON p.id_Materia = m.id_Materia
              JOIN usuarios u ON p.id_Usuario = u.id_Usuario
              WHERE pf.id_Laboratorio = ? 
              AND pf.fecha = ?
              AND (
                (pf.hora_Inicio < ? AND pf.hora_Fin > ?) OR
                (pf.hora_Inicio < ? AND pf.hora_Fin > ?) OR
                (pf.hora_Inicio >= ? AND pf.hora_Fin <= ?)
              )";

    if ($excludePracticeId) {
        $query .= " AND pf.id_PracticaFecha != ?";
    }

    $stmt = mysqli_prepare($conn, $query);
    
    if ($excludePracticeId) {
        mysqli_stmt_bind_param($stmt, "issssssi", 
            $labId, $date, 
            $endTime, $startTime,
            $endTime, $startTime,
            $startTime, $endTime,
            $excludePracticeId
        );
    } else {
        mysqli_stmt_bind_param($stmt, "issssss", 
            $labId, $date, 
            $endTime, $startTime,
            $endTime, $startTime,
            $startTime, $endTime
        );
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $response['disponible'] = false;
        while ($row = mysqli_fetch_assoc($result)) {
            $response['conflictos'][] = [
                'id' => $row['id_Practica'],
                'materia' => $row['n_Materia'],
                'profesor' => $row['nombre'] . ' ' . $row['apellido'],
                'hora_inicio' => $row['hora_inicio'],
                'hora_fin' => $row['hora_fin']
            ];
        }
    }

    return $response;
}

/**
 * Obtiene los detalles completos de una práctica
 * @param mysqli $conn Conexión a la base de datos
 * @param int $practiceId ID de la práctica
 * @return array|null Array con los datos de la práctica o null si no existe
 */
function getPracticeDetails($conn, $practiceId) {
    $query = "SELECT p.id_Practica, m.id_Materia, m.n_Materia, l.id_Laboratorio, l.n_Laboratorio, 
              g.id_Grupo, CONCAT(g.semestre, '-', g.grupo) AS grupo_nombre, 
              u.id_Usuario, CONCAT(u.nombre, ' ', u.apellido) AS profesor,
              pf.fecha, TIME_FORMAT(pf.hora_Inicio, '%H:%i') AS hora_inicio, 
              TIME_FORMAT(pf.hora_Fin, '%H:%i') AS hora_fin, 
              DAYNAME(pf.fecha) AS dia_semana
              FROM practicas p
              JOIN materias m ON p.id_Materia = m.id_Materia
              JOIN laboratorio l ON p.id_Laboratorio = l.id_Laboratorio
              JOIN grupos g ON p.id_Grupo = g.id_Grupo
              JOIN usuarios u ON p.id_Usuario = u.id_Usuario
              JOIN practicas_fechas pf ON p.id_Practica = pf.id_PracticaFecha
              WHERE p.id_Practica = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $practiceId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $practice = mysqli_fetch_assoc($result);
        
        // Calcular duración
        $start = new DateTime($practice['hora_inicio']);
        $end = new DateTime($practice['hora_fin']);
        $duration = $start->diff($end);
        $practice['duracion'] = $duration->format('%H:%I');
        
        return $practice;
    }

    return null;
}

/**
 * Redirige a una URL con un mensaje flash de sesión
 * @param string $url URL a redirigir
 * @param string $type Tipo de mensaje (success, error, warning, info)
 * @param string $message Mensaje a mostrar
 */
function redirectWithMessage($url, $type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
    header("Location: $url");
    exit();
}

/**
 * Muestra un mensaje flash y lo elimina de la sesión
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $message = $_SESSION['flash_message']['message'];
        unset($_SESSION['flash_message']);
        
        echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
}

/**
 * Verifica si el usuario tiene un rol específico
 * @param string $requiredRole Rol requerido
 * @return bool True si el usuario tiene el rol, false si no
 */
function checkUserRole($requiredRole) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    // Aquí puedes implementar lógica más compleja si tienes jerarquías de roles
    return $_SESSION['user_role'] === $requiredRole;
}

/**
 * Genera opciones para un select HTML a partir de un array de datos
 * @param array $data Array de datos (debe contener 'id' y 'nombre')
 * @param mixed $selectedValue Valor seleccionado (opcional)
 * @param string $defaultText Texto para la opción por defecto (opcional)
 * @return string HTML con las opciones generadas
 */
function generateSelectOptions($data, $selectedValue = null, $defaultText = 'Seleccionar') {
    $html = "<option value=''>$defaultText</option>";
    
    foreach ($data as $item) {
        $selected = ($item['id'] == $selectedValue) ? 'selected' : '';
        $html .= "<option value='{$item['id']}' $selected>{$item['nombre']}</option>";
    }
    
    return $html;
}

function calcularDuracion($hora_inicio, $hora_fin) {
    $inicio = new DateTime($hora_inicio);
    $fin = new DateTime($hora_fin);
    $intervalo = $inicio->diff($fin);
    
    // Formatear la duración como HH:MM
    $horas = $intervalo->h;
    $minutos = $intervalo->i;
    
    // Si hay horas y minutos
    if ($horas > 0 && $minutos > 0) {
        return "$horas horas y $minutos minutos";
    } 
    // Si solo hay horas
    elseif ($horas > 0) {
        return "$horas horas";
    }
    // Si solo hay minutos
    else {
        return "$minutos minutos";
    }
}

function obtenerDatos($conn, $query) {
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die('Error en la consulta: ' . mysqli_error($conn));
    }
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}


