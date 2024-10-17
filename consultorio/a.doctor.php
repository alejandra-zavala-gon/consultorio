<?php
require 'conect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
        $id_doctor = $_POST['id_doctor'];
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

        $sql = "UPDATE doctores 
                SET nombre=?, seg_nom=?, ap_pat=?, ap_mat=?, id_especialidad=?, telefono=?, cel_prof=?, curp=?, rfc=?, sexo=? 
                WHERE id_doctor=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisssssi", $nombre, $seg_nom, $ap_pat, $ap_mat, $id_especialidad, $telefono, $cel_prof, $curp, $rfc, $sexo, $id_doctor);

        if ($stmt->execute()) {
            echo "Actualización exitosa";
        } else {
            echo "Error al actualizar: " . $conn->error;
        }

        $stmt->close();
        exit; 
    }

    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $id_doctor = $_POST['id_doctor'];

        $sql = "SELECT COUNT(*) as total FROM citas WHERE id_doctor = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_doctor);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['total'] > 0) {
            echo "Por favor, modifique las citas para poder eliminarlo.";
        } else {
            $sql = "DELETE FROM doctores WHERE id_doctor = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_doctor);

            if ($stmt->execute()) {
                echo "Doctor eliminado exitosamente.";
            } else {
                echo "Error al eliminar: " . $conn->error;
            }
        }

        $stmt->close();
        exit; 
    }
}

// Consulta para obtener las especialidades
$sql_especialidades = "SELECT id_especialidad, nombre FROM especialidades";
$result_especialidades = $conn->query($sql_especialidades);

$especialidades = [];
if ($result_especialidades->num_rows > 0) {
    while ($row = $result_especialidades->fetch_assoc()) {
        $especialidades[] = $row;
    }
}

// Consulta para obtener la lista de doctores
$sql = "SELECT id_doctor, nombre, seg_nom, ap_pat, ap_mat, id_especialidad, telefono, cel_prof, curp, rfc, sexo FROM doctores";
$result = $conn->query($sql);

$doctores = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctores[] = $row;
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
    <title>Un Rayito de Luz - Doctores</title>
    <link rel="stylesheet" href="css/estilo3.css">
    <link rel="stylesheet" href="css/estilo4.css">
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
                <li><a href="agregar.html">Agregar Datos</a></li>
                <li><a href="horarios.php">Horarios de especialidades</a></li>
                <li><a href="a.paciente.php">Pacientes</a></li>
                <li><a href="a.citas.php">Citas</a></li>
                <li><a href="a.doctor.php">Doctores</a></li>
            </ul>
        </nav>
        <main>
            <h2>Lista de Doctores</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Doctor</th>
                            <th>Nombre</th>
                            <th>Segundo Nombre</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Especialidad</th>
                            <th>Teléfono</th>
                            <th>Cédula Profesional</th>
                            <th>CURP</th>
                            <th>RFC</th>
                            <th>Sexo</th>
                            <th>Actualizar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($doctores as $doctor) {
                            echo "<tr>";
                            echo "<td>{$doctor['id_doctor']}</td>";
                            echo "<td><input type='text' name='nombre_{$doctor['id_doctor']}' value='{$doctor['nombre']}' /></td>";
                            echo "<td><input type='text' name='seg_nom_{$doctor['id_doctor']}' value='{$doctor['seg_nom']}' /></td>";
                            echo "<td><input type='text' name='ap_pat_{$doctor['id_doctor']}' value='{$doctor['ap_pat']}' /></td>";
                            echo "<td><input type='text' name='ap_mat_{$doctor['id_doctor']}' value='{$doctor['ap_mat']}' /></td>";
                            
                            // Menú desplegable para especialidades
                            echo "<td>";
                            echo "<select name='id_especialidad_{$doctor['id_doctor']}'>";
                            foreach ($especialidades as $especialidad) {
                                $selected = ($especialidad['id_especialidad'] == $doctor['id_especialidad']) ? "selected" : "";
                                echo "<option value='{$especialidad['id_especialidad']}' {$selected}>{$especialidad['nombre']}</option>";
                            }
                            echo "</select>";
                            echo "</td>";
                            
                            echo "<td><input type='text' name='telefono_{$doctor['id_doctor']}' value='{$doctor['telefono']}' /></td>";
                            echo "<td><input type='text' name='cel_prof_{$doctor['id_doctor']}' value='{$doctor['cel_prof']}' /></td>";
                            echo "<td><input type='text' name='curp_{$doctor['id_doctor']}' value='{$doctor['curp']}' /></td>";
                            echo "<td><input type='text' name='rfc_{$doctor['id_doctor']}' value='{$doctor['rfc']}' /></td>";
                            echo "<td><input type='text' name='sexo_{$doctor['id_doctor']}' value='{$doctor['sexo']}' /></td>";
                            echo "<td><button type='button' onclick='actualizar({$doctor['id_doctor']})'>Actualizar</button></td>";
                            echo "<td><button type='button' onclick='eliminar({$doctor['id_doctor']})'>Eliminar</button></td>";
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
        function actualizar(id) {
            var nombre = $("input[name='nombre_" + id + "']").val();
            var seg_nom = $("input[name='seg_nom_" + id + "']").val();
            var ap_pat = $("input[name='ap_pat_" + id + "']").val();
            var ap_mat = $("input[name='ap_mat_" + id + "']").val();
            var id_especialidad = $("select[name='id_especialidad_" + id + "']").val();
            var telefono = $("input[name='telefono_" + id + "']").val();
            var cel_prof = $("input[name='cel_prof_" + id + "']").val();
            var curp = $("input[name='curp_" + id + "']").val();
            var rfc = $("input[name='rfc_" + id + "']").val();
            var sexo = $("input[name='sexo_" + id + "']").val();

            $.ajax({
                url: '', 
                type: 'POST',
                data: {
                    accion: 'actualizar',
                    id_doctor: id,
                    nombre: nombre,
                    seg_nom: seg_nom,
                    ap_pat: ap_pat,
                    ap_mat: ap_mat,
                    id_especialidad: id_especialidad,
                    telefono: telefono,
                    cel_prof: cel_prof,
                    curp: curp,
                    rfc: rfc,
                    sexo: sexo
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Doctor actualizado correctamente',
                        confirmButtonText: 'OK'
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al actualizar el doctor',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function eliminar(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '', 
                        type: 'POST',
                        data: {
                            accion: 'eliminar',
                            id_doctor: id
                        },
                        success: function(response) {
                            Swal.fire(
                                'Eliminado!',
                                'El doctor ha sido eliminado.',
                                'success'
                            ).then(() => {
                                location.reload(); 
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al eliminar el doctor',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
