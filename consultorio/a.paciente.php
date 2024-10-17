<?php
require 'conect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'update') {
        $id_paciente = $_POST['id_paciente'];
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

        $sql = "UPDATE pacientes 
                SET nombre=?, seg_nom=?, ap_pat=?, ap_mat=?, fecha_nac=?, domicilio=?, telefono=?, 
                peso=?, altura=?, alergias=?, curp=?, nss=?, rfc=?, sexo=?, padecimientos=? 
                WHERE id_paciente=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssssssi", $nombre, $seg_nom, $ap_pat, $ap_mat, $fecha_nac, $domicilio, $telefono, $peso, 
                          $altura, $alergias, $curp, $nss, $rfc, $sexo, $padecimientos, $id_paciente);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Paciente actualizado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . $conn->error]);
        }

        $stmt->close();
    } elseif ($action === 'delete') {
        $id_paciente = $_POST['id_paciente'];

        $sql = "DELETE FROM pacientes WHERE id_paciente=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_paciente);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Paciente eliminado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar: ' . $conn->error]);
        }

        $stmt->close();
    }
    
    $conn->close();
    exit; 
}

$sql = "SELECT id_paciente, nombre, seg_nom, ap_pat, ap_mat, fecha_nac, domicilio, telefono, peso, altura, alergias, curp, nss, rfc, sexo, padecimientos FROM pacientes"; 
$result = $conn->query($sql);

$pacientes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = $row;
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
    <title>Un Rayito de Luz</title>
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
            <h2>Lista de Pacientes</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Paciente</th>
                            <th>Nombre</th>
                            <th>Segundo Nombre</th>
                            <th>Apellido Paterno</th>
                            <th>Apellido Materno</th>
                            <th>Fecha Nac.</th>
                            <th>Domicilio</th>
                            <th>Teléfono</th>
                            <th>Peso</th>
                            <th>Altura</th>
                            <th>Alergias</th>
                            <th>CURP</th>
                            <th>NSS</th>
                            <th>RFC</th>
                            <th>Sexo</th>
                            <th>Padecimientos</th>
                            <th>Actualizar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($pacientes as $paciente) {
                            echo "<tr>";
                            echo "<td>{$paciente['id_paciente']}</td>";
                            echo "<td><input type='text' name='nombre_{$paciente['id_paciente']}' value='{$paciente['nombre']}' /></td>";
                            echo "<td><input type='text' name='seg_nom_{$paciente['id_paciente']}' value='{$paciente['seg_nom']}' /></td>";
                            echo "<td><input type='text' name='ap_pat_{$paciente['id_paciente']}' value='{$paciente['ap_pat']}' /></td>";
                            echo "<td><input type='text' name='ap_mat_{$paciente['id_paciente']}' value='{$paciente['ap_mat']}' /></td>";
                            echo "<td><input type='date' name='fecha_nac_{$paciente['id_paciente']}' value='{$paciente['fecha_nac']}' /></td>";
                            echo "<td><input type='text' name='domicilio_{$paciente['id_paciente']}' value='{$paciente['domicilio']}' /></td>";
                            echo "<td><input type='text' name='telefono_{$paciente['id_paciente']}' value='{$paciente['telefono']}' /></td>";
                            echo "<td><input type='text' name='peso_{$paciente['id_paciente']}' value='{$paciente['peso']}' /></td>";
                            echo "<td><input type='text' name='altura_{$paciente['id_paciente']}' value='{$paciente['altura']}' /></td>";
                            echo "<td><input type='text' name='alergias_{$paciente['id_paciente']}' value='{$paciente['alergias']}' /></td>";
                            echo "<td><input type='text' name='curp_{$paciente['id_paciente']}' value='{$paciente['curp']}' /></td>";
                            echo "<td><input type='text' name='nss_{$paciente['id_paciente']}' value='{$paciente['nss']}' /></td>";
                            echo "<td><input type='text' name='rfc_{$paciente['id_paciente']}' value='{$paciente['rfc']}' /></td>";
                            echo "<td><input type='text' name='sexo_{$paciente['id_paciente']}' value='{$paciente['sexo']}' /></td>";
                            echo "<td><input type='text' name='padecimientos_{$paciente['id_paciente']}' value='{$paciente['padecimientos']}' /></td>";
                            echo "<td><button type='button' onclick='actualizar({$paciente['id_paciente']})'>Actualizar</button></td>";
                            echo "<td><button type='button' onclick='eliminar({$paciente['id_paciente']})'>Eliminar</button></td>";
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
            var fecha_nac = $("input[name='fecha_nac_" + id + "']").val();
            var domicilio = $("input[name='domicilio_" + id + "']").val();
            var telefono = $("input[name='telefono_" + id + "']").val();
            var peso = $("input[name='peso_" + id + "']").val();
            var altura = $("input[name='altura_" + id + "']").val();
            var alergias = $("input[name='alergias_" + id + "']").val();
            var curp = $("input[name='curp_" + id + "']").val();
            var nss = $("input[name='nss_" + id + "']").val();
            var rfc = $("input[name='rfc_" + id + "']").val();
            var sexo = $("input[name='sexo_" + id + "']").val();
            var padecimientos = $("input[name='padecimientos_" + id + "']").val();

            $.post("a.paciente.php", {
                action: 'update',
                id_paciente: id,
                nombre: nombre,
                seg_nom: seg_nom,
                ap_pat: ap_pat,
                ap_mat: ap_mat,
                fecha_nac: fecha_nac,
                domicilio: domicilio,
                telefono: telefono,
                peso: peso,
                altura: altura,
                alergias: alergias,
                curp: curp,
                nss: nss,
                rfc: rfc,
                sexo: sexo,
                padecimientos: padecimientos
            }, function(response) {
                const result = JSON.parse(response);
                Swal.fire({
                    icon: result.status === 'success' ? 'success' : 'error',
                    title: result.status === 'success' ? 'Éxito' : 'Error',
                    text: result.message,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    if (result.status === 'success') {
                        location.reload();
                    }
                });
            });
        }

        function eliminar(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este paciente?")) {
                $.post("a.paciente.php", {
                    action: 'delete',
                    id_paciente: id
                }, function(response) {
                    const result = JSON.parse(response);
                    Swal.fire({
                        icon: result.status === 'success' ? 'success' : 'error',
                        title: result.status === 'success' ? 'Éxito' : 'Error',
                        text: result.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        if (result.status === 'success') {
                            location.reload();
                        }
                    });
                });
            }
        }
    </script>
</body>
</html>
