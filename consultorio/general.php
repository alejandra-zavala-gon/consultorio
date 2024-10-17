<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Un Rayito de Luz</title>
    <link rel="stylesheet" href="css/estilo3.css"> 
    <link rel="stylesheet" href="css/estilo4.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
</head>
<body>
    <div class="page-container">
        <header>
            <h1>Un Rayito de Luz</h1>
        </header>

        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="agregar.html">Agregar Datos</a></li>
                <li><a href="actualizar.html">Actualizar</a></li>
                <li><a href="consultas.html">Consultar Datos</a></li>
                <li><a href="horarios.php">Horarios de especialidades</a></li>
                <li><a href="especificas.php">Especifica</a></li>
            </ul>
        </nav>

        <main>
            <h2>Selecciona una vista para consultar:</h2>

            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label for="name">Selecciona una vista:</label>
                <select name="name" id="name">
                    <option value="citas" <?php if (isset($_POST['name']) && $_POST['name'] == 'citas') echo 'selected'; ?>>Citas</option>
                    <option value="doctores" <?php if (isset($_POST['name']) && $_POST['name'] == 'doctores') echo 'selected'; ?>>Doctores</option>
                    <option value="especialidades" <?php if (isset($_POST['name']) && $_POST['name'] == 'especialidades') echo 'selected'; ?>>Especialidades</option>
                    <option value="pacientes" <?php if (isset($_POST['name']) && $_POST['name'] == 'pacientes') echo 'selected'; ?>>Pacientes</option>
                </select>
                <input type="submit" value="Consultar">
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = $_POST['name'];

                require 'conect.php'; 

                switch ($name) {
                    case 'citas':
                        $sql = "SELECT id_cita, (SELECT CONCAT(nombre, ' ', seg_nom, ' ', ap_pat, ' ', ap_mat) FROM pacientes WHERE id_paciente = citas.id_paciente) AS paciente, motivo_cita, (SELECT CONCAT(nombre, ' ', seg_nom, ' ', ap_pat, ' ', ap_mat) FROM doctores WHERE id_doctor = citas.id_doctor) AS doctor, fecha, hora, am_pm FROM citas";
                        break;
                    case 'doctores':
                        $sql = "SELECT id_doctor, CONCAT(nombre, ' ', seg_nom, ' ', ap_pat, ' ', ap_mat) AS nombre_completo, id_especialidad, telefono FROM doctores";
                        break;
                    case 'especialidades':
                        $sql = "SELECT id_especialidad, nombre FROM especialidades";
                        break;
                    case 'pacientes':
                        $sql = "SELECT id_paciente, CONCAT(nombre, ' ', seg_nom, ' ', ap_pat, ' ', ap_mat) AS nombre_completo, fecha_nac, domicilio, telefono FROM pacientes";
                        break;
                    default:
                        echo "Opción no válida.";
                        break;
                }

                if (isset($sql)) {
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        echo "<h2>Resultados de la consulta: $name</h2>";
                        echo "<div class='table-container'>";
                        echo "<table border='1'>";

                        $columns = $result->fetch_fields();
                        echo "<tr>";
                        foreach ($columns as $column) {
                            echo "<th>" . htmlspecialchars($column->name) . "</th>";
                        }
                        echo "</tr>";

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            foreach ($row as $field) {
                                echo "<td>" . htmlspecialchars($field) . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div>";
                    } else {
                        echo "No se encontraron resultados en la consulta $name.";
                    }
                }

                $conn->close();
            }
            ?>
        </main>
        <footer>
            <p>&copy; 2024 Un Rayito de Luz. Todos los derechos reservados.</p>
            <p>Alejandra Zavala Gonzalez</p>
        </footer>
    </div>
</body>
</html>
