<?php
include_once("php/basedatos.php");
include_once("php/dataBase_utils.php");


// Obtener todos los datos de las especies y grupos
$list_specie = getAllData($conexion, 'animal');
$list_groups_specie = getAllData($conexion, 'grupo_especie');

$sql = "SELECT * FROM `reporte`";
$result = $conexion->query($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Manejo de la búsqueda
    $searchQuery = !empty($_POST['busqueda']) ? $_POST['busqueda'] : '';

    // Manejo del filtro
    $selectedGroup = !empty($_POST['grupo']) ? $_POST['grupo'] : '';

    // Si el campo de búsqueda está vacío, reiniciar la lista de especies
    if (empty($searchQuery) && empty($selectedGroup)) {
        $list_specie = getAllData($conexion, 'animal'); // Volver a cargar todos los registros
    } else {
        // Filtrar las especies si hay término de búsqueda o grupo seleccionado
        $list_specie = array_filter($list_specie, function ($especie) use ($searchQuery, $selectedGroup) {
            $matchSearch = stripos($especie['nombre_comun'], $searchQuery) !== false ||
                stripos($especie['familia_especie'], $searchQuery) !== false;
            $matchGroup = empty($selectedGroup) || $especie['grupo'] == $selectedGroup; // Asumiendo que 'grupo' es una columna en 'animal'
            return $matchSearch && $matchGroup;
        });
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../Styles/styles_rep.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <title>Especies Invasoras Unitrópico</title>
</head>

<body>
    <nav class="navbar navbar-expand-sm navbar-dark topbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../Resources/Images/logo-unitropico.png" alt="Unitrópico" class="topbarlogo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

        </div>
    </nav>






    <h1>Reportes de Especies Invasoras</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Número de Reporte</th>
                <th>Fecha de Avistamiento</th>
                <th>Cantidad</th>
                <th>Descripción del Entorno</th>
                <th>Foto</th>
                <th>Coordenadas</th>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($result->num_rows > 0) {
                // Salida de datos de cada fila
                while ($row = $result->fetch_assoc()) {
                    
                    echo "<tr>";
                    echo "<td>" . $row["num_reporte"] . "</td>";
                    echo "<td>" . $row["fecha_avistamiento"] . "</td>";
                    echo "<td>" . $row["cantidad"] . "</td>";

                    echo "<td>" . $row["descripcion_entorno"] . "</td>";
                    echo "<td><img src='php/getImageReport.php?id=" . $row["num_reporte"] . "' alt='Foto del reporte' width='100'></td>"; // Ajusta el ancho según sea necesario
                    echo "<td>" . $row["coord_lat"] . ", " . $row["coord_long"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>0 resultados</td></tr>";
            }

            // Cerrar conexión
            $conexion->close();
            ?>
        </tbody>
    </table>










</body>
</html>