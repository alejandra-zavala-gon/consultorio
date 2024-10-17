<?php
$message = "";
require 'conect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $seg_nom = $_POST['seg_nom'];
    $ap_pat = $_POST['ap_pat'];
    $ap_mat = $_POST['ap_mat'];
    $id_especialidad = $_POST['id_especialidad'];
    $telefono = $_POST['telefono'];
    $cel_prof = $_POST['cel_prof'];
    $curp = $_POST['curp'];
    $rfc = $_POST['rfc'];
    $sexo = $_POST['sexo'];

    if (empty($nombre) || empty($ap_pat) || empty($ap_mat) || empty($id_especialidad) || empty($telefono) || empty($cel_prof) || empty($curp) || empty($rfc) || empty($sexo)) {
        $message = "Todos los campos son obligatorios, excepto el segundo nombre.";
    } else {
        $stmt = $conn->prepare("INSERT INTO doctores (nombre, seg_nom, ap_pat, ap_mat, id_especialidad, telefono, cel_prof, curp, rfc, sexo) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssissss", $nombre, $seg_nom, $ap_pat, $ap_mat, $id_especialidad, $telefono, $cel_prof, $curp, $rfc, $sexo);

        if ($stmt->execute()) {
            $message = "El registro fue guardado exitosamente.";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
    echo json_encode(['message' => $message]);
    exit();
}

$especialidades = $conn->query("SELECT id_especialidad, nombre FROM especialidades");
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
            <h2 id="doctores">Agregar Doctor</h2>
            <form id="doctorForm" method="post" action="">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required><br><br>

                <label for="seg_nom">Segundo Nombre:</label>
                <input type="text" id="seg_nom" name="seg_nom"><br><br>

                <label for="ap_pat">Apellido Paterno:</label>
                <input type="text" id="ap_pat" name="ap_pat" required><br><br>

                <label for="ap_mat">Apellido Materno:</label>
                <input type="text" id="ap_mat" name="ap_mat" required><br><br>

                <label for="id_especialidad">Especialidad:</label>
                <select id="id_especialidad" name="id_especialidad" required>
                    <option value="">Seleccione...</option>
                    <?php while ($row = $especialidades->fetch_assoc()): ?>
                        <option value="<?= $row['id_especialidad'] ?>"><?= $row['id_especialidad'] . ' - ' . $row['nombre'] ?></option>
                    <?php endwhile; ?>
                </select><br><br>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required><br><br>

                <label for="cel_prof">Celular Profesional:</label>
                <input type="text" id="cel_prof" name="cel_prof" required><br><br>

                <label for="curp">CURP:</label>
                <input type="text" id="curp" name="curp" required><br><br>

                <label for="rfc">RFC:</label>
                <input type="text" id="rfc" name="rfc" required><br><br>

                <label for="sexo">Sexo:</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Seleccione...</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select><br><br>

                <input type="submit" value="Agregar Doctor">
            </form>
        </main>

        <footer>
            <p>&copy; 2024 Un Rayito de Luz. Todos los derechos reservados.</p>
            <p>Alejandra Zavala Gonzalez</p>
        </footer>
    </div>

    <script>
        $(document).ready(function() {
            $('#doctorForm').on('submit', function(e) {
                e.preventDefault(); 

                $.ajax({
                    type: 'POST',
                    url: '', 
                    data: $(this).serialize(),
                    success: function(response) {
                        const result = JSON.parse(response);
                        Swal.fire({
                            icon: result.message.includes("Error") ? 'error' : 'success',
                            title: result.message.includes("Error") ? 'Error' : 'Éxito',
                            text: result.message,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            if (!result.message.includes("Error")) {
                                $('#doctorForm')[0].reset(); 
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
