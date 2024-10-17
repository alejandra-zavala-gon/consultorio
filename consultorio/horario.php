<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Un Rayito de Luz</title>
    <link rel="stylesheet" href="css/estilo4.css">
    <link rel="stylesheet" href="css/estilo3.css">
    <link rel="stylesheet" href="css/estilo6.css">
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
                <li><a href="actualizar.html">Actualizar Datos</a></li>
                <li><a href="agregar.html">Agregar Datos</a></li>
                <li><a href="consultas.html">Consultar Datos</a></li>
                <li><a href="general.php">General</a></li>
            </ul>
        </nav>
        <main>
            <h2>Horario de Especialidades</h2>

            <form method="GET" action="">
                <label for="especialidad">Elige una especialidad:</label>
                <select name="especialidad" id="especialidad">
                    <option value="Alergología">Alergología</option>
                    <option value="Anestesiología">Anestesiología</option>
                    <option value="Cardiología">Cardiología</option>
                    <option value="Dermatología">Dermatología</option>
                </select>
                <button type="submit">Consultar Horario</button>
            </form>

            <?php
            require 'conect.php';

            if (isset($_GET['especialidad'])) {
                $especialidad = $_GET['especialidad'];

                echo "<h3>Horario de $especialidad</h3>";

                // Mapeo de especialidades a tablas correspondientes
                $tabla_map = [
                    'Alergología' => 'horario_alergologia',
                    'Anestesiología' => 'horario_anestesiologia',
                    'Cardiología' => 'horario_cardiologia',
                    'Dermatología' => 'horario_dermatologia'
                ];

                // Verificamos que exista la tabla para la especialidad seleccionada
                if (isset($tabla_map[$especialidad])) {
                    $tabla_especialidad = $tabla_map[$especialidad];

                    // Consulta para obtener las citas de la especialidad seleccionada
                    $sql = "SELECT Dia, Periodo, Hora FROM $tabla_especialidad WHERE Especialidad = '$especialidad'";
                   

                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                      

                        // Inicializamos el array con las horas y los días
                        $horas = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30',
                                  '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', 
                                  '18:00', '18:30', '19:00', '19:30'];
                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

                        // Creamos un array para guardar las citas
                        $horario = [];
                        foreach ($dias as $dia) {
                            foreach ($horas as $hora) {
                                $horario[$dia][$hora] = 'libre'; // Inicializamos como libre
                            }
                        }

                        // Mapeo de nombres de días en inglés a español
                        $day_translation = [
                            'Monday' => 'Lunes',
                            'Tuesday' => 'Martes',
                            'Wednesday' => 'Miércoles',
                            'Thursday' => 'Jueves',
                            'Friday' => 'Viernes'
                        ];

                        // Procesamos los resultados de la consulta
                        while ($row = $result->fetch_assoc()) {
                            $dia_ingles = date('l', strtotime($row['Dia'])); // Obtenemos el nombre del día en inglés
                            $dia_espanol = $day_translation[$dia_ingles]; // Lo traducimos al español
                            $hora = date('H:i', strtotime($row['Hora'])); // Convertimos la hora al formato correcto

                            // Si el horario coincide con un día y hora, lo marcamos como ocupado
                            if (isset($horario[$dia_espanol][$hora])) {
                                $horario[$dia_espanol][$hora] = 'ocupada'; // Marcamos la cita como ocupada
                            }
                        }

                        // Mostramos el horario en formato de tabla
                        echo "<table border='1'>";
                        echo "<tr><th>Hora</th><th>Lunes</th><th>Martes</th><th>Miércoles</th><th>Jueves</th><th>Viernes</th></tr>";

                        // Imprimir la tabla del horario
                        foreach ($horas as $hora) {
                            echo "<tr>";
                            echo "<td>$hora</td>"; // Columna de la hora
                            foreach ($dias as $dia) {
                                echo "<td>" . $horario[$dia][$hora] . "</td>"; // Columna de cada día (ocupada o libre)
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No se encontraron citas para la especialidad seleccionada.</p>";
                    }
                } else {
                    echo "<p>Especialidad no válida.</p>";
                }
            } else {
                echo "<p>Por favor, selecciona una especialidad para ver el horario.</p>";
            }

            $conn->close();
            ?>
        </main>

        <footer>
            <p>&copy; 2024 Un Rayito de Luz. Todos los derechos reservados.</p>
            <p>Alejandra Zavala Gonzalez</p>
        </footer>
    </div>
</body>
</html>
