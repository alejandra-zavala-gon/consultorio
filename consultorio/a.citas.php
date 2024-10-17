<?php
require 'conect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar_cita') {
        $id_cita = $_POST['id_cita'];
        $id_paciente = $_POST['id_paciente'] === '' ? null : $_POST['id_paciente'];
        $motivo_cita = $_POST['motivo_cita'] === '' ? null : $_POST['motivo_cita'];
        $id_doctor = $_POST['id_doctor'] === '' ? null : $_POST['id_doctor'];
        $fecha = $_POST['fecha'] === '' ? null : date('Y-m-d', strtotime($_POST['fecha']));
        $hora = $_POST['hora'] === '' ? null : $_POST['hora'];
        $am_pm = (strtotime($hora) < strtotime('12:00')) ? 'AM' : 'PM';
        $hora_valida = validateHour($hora);
        if (!$hora_valida) {
            echo json_encode(['status' => 'error', 'message' => "La hora debe estar entre las 08:00 y 19:30, en intervalos de media hora."]);
            exit;
        }
        $stmt = $conn->prepare("SELECT COUNT(*) FROM citas WHERE fecha = ? AND hora = ? AND id_cita != ?");
        $stmt->bind_param("ssi", $fecha, $hora, $id_cita);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            echo json_encode(['status' => 'error', 'message' => "Ya existe una cita agendada a esta hora en la misma fecha."]);
            exit;
        }
        $sql = "UPDATE citas SET id_paciente=?, motivo_cita=?, id_doctor=?, fecha=?, hora=?, am_pm=? WHERE id_cita=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isisssi", $id_paciente, $motivo_cita, $id_doctor, $fecha, $hora, $am_pm, $id_cita);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => "Cita actualizada correctamente."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Error al actualizar: " . $conn->error]);
        }
        $stmt->close();
        exit;
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar_cita') {
        $id_cita = $_POST['id_cita'];
        $sql = "DELETE FROM citas WHERE id_cita = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_cita);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => "Cita eliminada exitosamente."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Error al eliminar: " . $conn->error]);
        }
        $stmt->close();
        exit;
    }
}

function validateHour($hora) {
    return ($hora >= '08:00' && $hora <= '19:30' && (strpos($hora, ':00') !== false || strpos($hora, ':30') !== false));
}

$sql_pacientes = "SELECT id_paciente, nombre, ap_pat FROM pacientes";
$result_pacientes = $conn->query($sql_pacientes);
$pacientes = [];
if ($result_pacientes->num_rows > 0) {
    while ($row = $result_pacientes->fetch_assoc()) {
        $pacientes[] = $row;
    }
}

$sql_doctores = "SELECT id_doctor, nombre, ap_pat FROM doctores";
$result_doctores = $conn->query($sql_doctores);
$doctores = [];
if ($result_doctores->num_rows > 0) {
    while ($row = $result_doctores->fetch_assoc()) {
        $doctores[] = $row;
    }
}

$sql = "SELECT * FROM citas"; 
$result = $conn->query($sql);
$citas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $citas[] = $row;
    }
} else {
    echo "No se encontraron registros.";
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Un Rayito de Luz - Citas</title>
    <link rel="stylesheet" href="css/estilo3.css">
    <link rel="stylesheet" href="css/estilo4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
</head>
<body>
    <div class="page-container">
        <header>
            <h1>Un Rayito de Luz</h1>
        </header>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="consultas.html">Consultar Datos</a></li>
                <li><a href="agregar.html">Agregar Datos</a></li>
                <li><a href="horarios.php">Horarios de especialidades</a></li>
                <li><a href="a.paciente.php">Pacientes</a></li>
                <li><a href="a.citas.php">Citas</a></li>
                <li><a href="a.doctor.php">Doctores</a></li>
            </ul>
        </nav>
        <main>
            <h2>Lista de Citas</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Cita</th>
                            <th>ID Paciente</th>
                            <th>Motivo de Cita</th>
                            <th>ID Doctor</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Actualizar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($citas as $cita) {
                            $hora_formateada = date("h:i A", strtotime($cita['hora']));
                            echo "<tr>";
                            echo "<td>{$cita['id_cita']}</td>";
                            echo "<td><select name='id_paciente_{$cita['id_cita']}'>";
                            foreach ($pacientes as $paciente) {
                                $selected = $cita['id_paciente'] == $paciente['id_paciente'] ? 'selected' : '';
                                echo "<option value='{$paciente['id_paciente']}' $selected>{$paciente['nombre']} {$paciente['ap_pat']}</option>";
                            }
                            echo "</select></td>";
                            echo "<td><input type='text' name='motivo_cita_{$cita['id_cita']}' value='{$cita['motivo_cita']}' /></td>";
                            echo "<td><select name='id_doctor_{$cita['id_cita']}'>";
                            foreach ($doctores as $doctor) {
                                $selected = $cita['id_doctor'] == $doctor['id_doctor'] ? 'selected' : '';
                                echo "<option value='{$doctor['id_doctor']}' $selected>{$doctor['nombre']} {$doctor['ap_pat']}</option>";
                            }
                            echo "</select></td>";
                            echo "<td><input type='date' name='fecha_{$cita['id_cita']}' value='{$cita['fecha']}' /></td>";
                            echo "<td><select name='hora_{$cita['id_cita']}'>";
                            for ($hour = 8; $hour <= 19; $hour++) {
                                foreach (['00', '30'] as $minute) {
                                    $time = str_pad($hour, 2, '0', STR_PAD_LEFT) . ":$minute";
                                    $time_full = $time . ":00";
                                    $time_am_pm = date("h:i A", strtotime($time_full));
                                    echo "<option value='$time_full'" . ($cita['hora'] == $time_full ? ' selected' : '') . ">$time_am_pm</option>";
                                }
                            }
                            echo "</select></td>";
                            echo "<td><button class='actualizar' data-id='{$cita['id_cita']}'>Actualizar</button></td>";
                            echo "<td><button class='eliminar' data-id='{$cita['id_cita']}'>Eliminar</button></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
        <footer>
            <p>&copy; 2024 Un Rayito de Luz. Todos los derechos reservados.</p>
            <p>Alejandra Zavala Gonzalez</p>
        </footer>
    </div>

    <script>
$('.actualizar').click(function() {
    const idCita = $(this).data('id');
    const idPaciente = $(`[name='id_paciente_${idCita}']`).val();
    const motivoCita = $(`[name='motivo_cita_${idCita}']`).val();
    const idDoctor = $(`[name='id_doctor_${idCita}']`).val();
    const fecha = $(`[name='fecha_${idCita}']`).val();
    const hora = $(`[name='hora_${idCita}']`).val();
    
    $.ajax({
        type: 'POST',
        url: '',
        data: {
            accion: 'actualizar_cita',
            id_cita: idCita,
            id_paciente: idPaciente,
            motivo_cita: motivoCita,
            id_doctor: idDoctor,
            fecha: fecha,
            hora: hora
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                swal({
                    title: "¡Éxito!",
                    text: response.message,
                    type: "success",
                    confirmButtonText: "OK"
                });
            } else {
                swal({
                    title: "Error",
                    text: response.message,
                    type: "error",
                    confirmButtonText: "OK"
                });
            }
        },
        error: function() {
            swal({
                title: "Error",
                text: "Error en la solicitud",
                type: "error",
                confirmButtonText: "OK"
            });
        }
    });
});

$('.eliminar').click(function() {
    const idCita = $(this).data('id');
    
    $.ajax({
        type: 'POST',
        url: '',
        data: {
            accion: 'eliminar_cita',
            id_cita: idCita
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                swal({
                    title: "¡Éxito!",
                    text: response.message,
                    type: "success",
                    confirmButtonText: "OK"
                });
            } else {
                swal({
                    title: "Error",
                    text: response.message,
                    type: "error",
                    confirmButtonText: "OK"
                });
            }
        },
        error: function() {
            swal({
                title: "Error",
                text: "Error en la solicitud",
                type: "error",
                confirmButtonText: "OK"
            });
        }
    });
});
    </script>
</body>
</html>