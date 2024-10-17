<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Un rayito de luz</title>
    <link rel="stylesheet" href="css/estilo4.css">
    <link rel="stylesheet" href="css/estilo3.css">
    <link rel="stylesheet" href="css/estilo6.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="page-container">
        <header>
            <h1>Un rayito de luz</h1>
        </header>

        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="actualizar.html">Actualizar Datos</a></li>
                <li><a href="agregar.html">Agregar Datos</a></li>
                <li><a href="consultas.html">Consultar Datos</a></li>
                <li><a href="horarios.php">Horarios de especialidades</a></li>
                <li><a href="general.php">General</a></li>
            </ul>
        </nav>

        <main>
            <h2>Selecciona una vista para consultar:</h2>

            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label for="name">Selecciona una vista:</label>
                <select name="name" id="name">
                    <option value="citas_detalladas">Citas Detalladas</option>
                    <option value="citas_futuras">Citas Futuras</option>
                    <option value="citas_por_doctor">Citas por Doctor</option>
                    <option value="citas_por_paciente">Citas por Paciente</option>
                    <option value="especialidades_doctores">Especialidades por Doctores</option>
                    <option value="pacientes_con_alergias">Pacientes con Alergias</option>
                </select>
                <input type="submit" value="Consultar">
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = $_POST['name'];

                require 'conect.php'; 

                $result = $conn->query("SHOW TABLES LIKE '$name'");
                if ($result && $result->num_rows > 0) {
                    $sql = "SELECT * FROM $name";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        echo "<h2>Resultados de la consulta: $name</h2>";
                        echo "<div class='table-container'>";
                        echo "<table border='1'>";

                        $columns = $result->fetch_fields();
                        echo "<tr>";
                        foreach ($columns as $column) {
                            echo "<th>" . $column->name . "</th>";
                        }
                        echo "</tr>";

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            foreach ($row as $field) {
                                if ($column->name == 'am_pm') {
                                    echo "<td>" . ($field == 'AM' ? 'Mañana' : 'Tarde') . "</td>";
                                } else {
                                    echo "<td>" . $field . "</td>";
                                }
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div>";
                    } else {
                        echo "No se encontraron resultados en la consulta $name.";
                    }
                } else {
                    echo "La vista $name no existe.";
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
