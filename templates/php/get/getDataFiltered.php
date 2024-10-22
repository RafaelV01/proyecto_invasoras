<?php
include("../basedatos.php");
$grupo_filter = $_POST['grupo'] ?? null; // Maneja el caso si no existe
$text_busqueda = $_POST['busqueda'] ?? null; // Maneja el caso si no existe


if (!empty($grupo_filter) || !empty($text_busqueda)) {
    // Preparar el patrón de búsqueda
    $busqueda = !empty($text_busqueda) ? "%" . $conexion->real_escape_string($text_busqueda) . "%" : null;

    // Inicializar la consulta SQL
    $sql = "SELECT * FROM animal WHERE 1=1"; // 1=1 es una técnica para facilitar la adición de condiciones

    // Agregar condiciones dinámicamente
    $params = [];
    $types = "";

    if (!empty($grupo_filter)) {
        $sql .= " AND grupo = ?";
        $params[] = $grupo_filter;
        $types .= "i"; // Asegúrate de que 'grupo' es de tipo entero
    }

    if (!empty($busqueda)) {
        $sql .= " AND (nombre_comun LIKE ? OR familia_especie LIKE ?)";
        $params[] = $busqueda;
        $params[] = $busqueda;
        $types .= "ss";
    }

    $stmt = $conexion->prepare($sql);

    if ($types) {
        $stmt->bind_param($types, ...$params); // Usa el operador spread para pasar los parámetros
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Comprobar si hay resultados
    if ($result->num_rows > 0) {
        while ($especie = $result->fetch_assoc()) {
            $id = $especie['num_especie'];
            echo '
                <div class="container cardcontain">
                    <div class="card_specie">
                        <img src="php/viewImage.php?id=' . $id . '" alt="Imagen de la especie invasora" class="rounded imageEspecie">
                        <strong id="info_nombre_comun" class="center">' . $especie['nombre_comun'] . '</strong><br>
                        <strong>Especie: </strong> <span id="info_especie">' . $especie['familia_especie'] . '</span><br>
                        <strong>Origen: </strong> <span id="info_origen">' . $especie['origen'] . '</span><br>
                    </div>
                </div>';

                
        }
    } else {
        echo "No se encontraron resultados.";
    }
} else {
    echo "Por favor, ingrese un grupo o un término de búsqueda.";
}
?>
