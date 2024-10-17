<?php
$message = "";
require 'conect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $seg_nom = $_POST['seg_nom'];
    $ap_pat = $_POST['ap_pat'];
    $ap_mat = $_POST['ap_mat'];
    $fecha_nac = $_POST['fecha_nac'];
    $domicilio = $_POST['domicilio'];
    $telefono = $_POST['telefono'];
    $peso = $_POST['peso'];
    $altura = $_POST['altura'];
    $alergias = $_POST['alergias'];
    $curp = $_POST['curp'];
    $nss = $_POST['nss'];
    $rfc = $_POST['rfc'];
    $sexo = $_POST['sexo'];
    $padecimientos = $_POST['padecimientos'];

    $fecha_nacimiento = new DateTime($fecha_nac);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nacimiento)->y;

    if (empty($nombre) || empty($ap_pat) || empty($ap_mat) || empty($fecha_nac) || empty($domicilio) || empty($telefono) || empty($peso) || empty($altura) || empty($curp) || empty($nss) || empty($sexo) || empty($padecimientos)) {
        $message = "Todos los campos son obligatorios, excepto el segundo nombre y alergias.";
        $error = true;
    } else {
        if ($edad >= 18 && empty($rfc)) {
            $message = "El RFC es obligatorio para pacientes mayores de edad.";
            $error = true;
        } else {
            $stmt = $conn->prepare("INSERT INTO pacientes (nombre, seg_nom, ap_pat, ap_mat, fecha_nac, domicilio, telefono, peso, altura, alergias, curp, nss, rfc, sexo, padecimientos) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssdsssssss", $nombre, $seg_nom, $ap_pat, $ap_mat, $fecha_nac, $domicilio, $telefono, $peso, $altura, $alergias, $curp, $nss, $rfc, $sexo, $padecimientos);

            if ($stmt->execute()) {
                $message = "El registro fue guardado exitosamente.";
                $error = false;
            } else {
                $message = "Error: " . $stmt->error;
                $error = true;
            }

            $stmt->close();
        }
    }

    $conn->close();
    echo json_encode(['message' => $message, 'error' => $error]);
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
            <h2 id="pacientes">Agregar Paciente</h2>
            <form id="pacienteForm" method="post" action="">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required><br><br>
                <label for="seg_nom">Segundo Nombre:</label>
                <input type="text" id="seg_nom" name="seg_nom"><br><br>
                <label for="ap_pat">Apellido Paterno:</label>
                <input type="text" id="ap_pat" name="ap_pat" required><br><br>
                <label for="ap_mat">Apellido Materno:</label>
                <input type="text" id="ap_mat" name="ap_mat" required><br><br>
                <label for="fecha_nac">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nac" name="fecha_nac" required><br><br>
                <label for="domicilio">Domicilio:</label>
                <input type="text" id="domicilio" name="domicilio" required><br><br>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required><br><br>
                <label for="peso">Peso (kg):</label>
                <input type="number" id="peso" name="peso" step="0.1" required><br><br>
                <label for="altura">Altura (cm):</label>
                <input type="number" id="altura" name="altura" step="0.1" required><br><br>
                <label for="alergias">Alergias:</label>
                <input type="text" id="alergias" name="alergias"><br><br>
                <label for="curp">CURP:</label>
                <input type="text" id="curp" name="curp" required><br><br>
                <label for="nss">NSS:</label>
                <input type="text" id="nss" name="nss" required><br><br>
                <label for="rfc">RFC:</label>
                <input type="text" id="rfc" name="rfc"><br><br>
                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Seleccione...</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select><br><br>
                <label for="padecimientos">Padecimientos:</label>
                <input type="text" id="padecimientos" name="padecimientos" required><br><br>
                <input type="submit" value="Agregar Paciente">
            </form>
        </main>
        <footer>
            <p>&copy; 2024 Un Rayito de Luz. Todos los derechos reservados.</p>
            <p>Alejandra Zavala Gonzalez</p>
        </footer>
    </div>
    <script>
        $(document).ready(function() {
            $('#pacienteForm').on('submit', function(e) {
                e.preventDefault(); 
                let errorMessage = '';
                $('input[required], select[required]').each(function() {
                    if (!$(this).val()) {
                        errorMessage += 'El campo ' + $(this).prev('label').text() + ' es obligatorio.<br>';
                    }
                });
                if (errorMessage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage,
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: '', 
                    data: $(this).serialize(),
                    success: function(response) {
                        const result = JSON.parse(response);
                        const icon = result.error ? 'error' : 'success';
                        const title = result.error ? 'Error' : 'Éxito';
                        Swal.fire({
                            icon: icon,
                            title: title,
                            text: result.message,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            if (!result.error) {
                                $('#pacienteForm')[0].reset(); 
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
