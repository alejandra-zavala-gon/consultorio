<?php
$message = "";
require 'conect.php';

$pacientes_query = "SELECT id_paciente, nombre, ap_pat FROM pacientes";
$pacientes_result = $conn->query($pacientes_query);
$pacientes = [];
if ($pacientes_result) {
    while ($row = $pacientes_result->fetch_assoc()) {
        $pacientes[] = $row;
    }
}

$doctores_query = "SELECT id_doctor, nombre, ap_pat, id_especialidad FROM doctores";
$doctores_result = $conn->query($doctores_query);
$doctores = [];
if ($doctores_result) {
    while ($row = $doctores_result->fetch_assoc()) {
        $doctores[] = $row;
    }
}

$horas = [];
for ($h = 8; $h <= 19; $h++) {
    $am_pm = ($h < 12) ? 'AM' : 'PM';
    for ($m = 0; $m < 60; $m += 30) {
        if ($h == 14 && $m == 0) continue;
        if ($h == 19 && $m > 30) continue;
        $horas[] = sprintf("%02d:%02d", $h, $m);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = $_POST['id_paciente'];
    $motivo_cita = $_POST['motivo_cita'];
    $id_doctor = $_POST['id_doctor'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $am_pm = $_POST['am_pm'];

    if (empty($id_paciente) || empty($motivo_cita) || empty($id_doctor) || empty($fecha) || empty($hora)) {
        $message = "Todos los campos son obligatorios.";
    } else {
        $dia_semana = date('N', strtotime($fecha));
        if ($dia_semana > 5) {
            $message = "Las citas solo se pueden agendar de lunes a viernes.";
        } else {
            $hora_inicio = strtotime("08:00");
            $hora_fin = strtotime("19:30");
            $hora_cita = strtotime($hora);

            if ($hora_cita < $hora_inicio || $hora_cita > $hora_fin || ($hora_cita >= strtotime("14:00") && $hora_cita <= strtotime("14:30"))) {
                $message = "La cita debe estar programada entre 8:00 y 14:00 o entre 14:30 y 19:30.";
            } else {
                $cita_query = "SELECT id_doctor FROM citas WHERE fecha = ? AND hora = ? AND id_doctor = ?";
                $cita_stmt = $conn->prepare($cita_query);
                $cita_stmt->bind_param("ssi", $fecha, $hora, $id_doctor);
                $cita_stmt->execute();
                $cita_stmt->store_result();

                if ($cita_stmt->num_rows > 0) {
                    $message = "Ya existe una cita agendada a esta hora con el mismo doctor.";
                } else {
                    $stmt = $conn->prepare("INSERT INTO citas (id_paciente, motivo_cita, id_doctor, fecha, hora, am_pm) 
                                            VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssss", $id_paciente, $motivo_cita, $id_doctor, $fecha, $hora, $am_pm);

                    if ($stmt->execute()) {
                        $message = "El registro fue guardado exitosamente.";
                    } else {
                        $message = "Error: " . $stmt->error;
                    }

                    $stmt->close();
                }
                $cita_stmt->close();
            }
        }
    }

    $conn->close();
    echo json_encode(['message' => $message]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Un Rayito de Luz</title>
    <link rel="stylesheet" href="css/estilo3.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="actualizar.html">Actualizar Datos</a></li>
                <li><a href="horarios.php">Horarios de especialidades</a></li>
                <li><a href="paciente.php">Pacientes</a></li>
                <li><a href="citas.php">Citas</a></li>
                <li><a href="doctores.php">Doctores</a></li>
            </ul>
        </nav>

        <main>
            <h2 id="citas">Agregar Cita</h2>
            <form id="citaForm" method="post" action="">
                <label for="id_paciente">Paciente:</label>
                <select id="id_paciente" name="id_paciente" required>
                    <option value="">Seleccione un paciente...</option>
                    <?php foreach ($pacientes as $paciente): ?>
                        <option value="<?php echo $paciente['id_paciente']; ?>">
                            <?php echo $paciente['nombre'] . ' ' . $paciente['ap_pat']; ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="motivo_cita">Motivo de la Cita:</label>
                <input type="text" id="motivo_cita" name="motivo_cita" required><br><br>

                <label for="id_doctor">Doctor:</label>
                <select id="id_doctor" name="id_doctor" required>
                    <option value="">Seleccione un doctor...</option>
                    <?php foreach ($doctores as $doctor): ?>
                        <option value="<?php echo $doctor['id_doctor']; ?>">
                            <?php echo $doctor['nombre'] . ' ' . $doctor['ap_pat']; ?>
                        </option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required><br><br>

                <label for="hora">Hora:</label>
                <select id="hora" name="hora" required>
                    <option value="">Seleccione una hora...</option>
                    <?php foreach ($horas as $h): ?>
                        <option value="<?php echo $h; ?>"><?php echo $h; ?></option>
                    <?php endforeach; ?>
                </select><br><br>

                <input type="hidden" id="am_pm" name="am_pm" value="">
                <input type="submit" value="Agregar Cita">
            </form>
        </main>

        <footer>
            <p>&copy; 2024 Un Rayito de Luz. Todos los derechos reservados.</p>
            <p>Alejandra Zavala Gonzalez</p>
        </footer>
    </div>

    <script>
        $(document).ready(function() {
            $('#hora').change(function() {
                var horaSeleccionada = $(this).val();
                if (horaSeleccionada) {
                    var horaArray = horaSeleccionada.split(':');
                    var horaNumerica = parseInt(horaArray[0]);
                    if (horaNumerica < 12) {
                        $('#am_pm').val('AM');
                    } else {
                        $('#am_pm').val('PM');
                    }
                }
            });

            $('#citaForm').on('submit', function(e) {
                e.preventDefault(); 

                $.ajax({
                    type: 'POST',
                    url: '', 
                    data: $(this).serialize(),
                    success: function(response) {
                        const result = JSON.parse(response);
                        let icon = 'success';
                        let title = 'Éxito';

                        if (result.message.includes("Ya existe una cita agendada a esta hora con el mismo doctor.")) {
                            icon = 'error';
                            title = 'Error';
                        }

                        Swal.fire({
                            icon: icon,
                            title: title,
                            text: result.message,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            if (result.message === "El registro fue guardado exitosamente.") {
                                location.reload();
                            }
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al procesar la solicitud.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
