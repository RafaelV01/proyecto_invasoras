<?php
include_once("../basedatos.php");
include_once("../dataBase_utils.php");

$grupos = getAllData($conexion, 'grupo_especie');

$registros_por_pagina = 15;

// Verificar página actual
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;

// Calcular el inicio para la consulta
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta para contar los registros totales (puedes agregar filtros aquí si es necesario)
$sql_total = "SELECT COUNT(*) AS total FROM animal";
$resultado_total = $conexion->query($sql_total);
$fila_total = $resultado_total->fetch_assoc();
$total_registros = $fila_total['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Obtener los registros de la página actual
$query = "SELECT * FROM animal LIMIT $inicio, $registros_por_pagina";
$list_specie = $conexion->query($query);


$data = [];

if ($list_specie) {
    while ($especie = $list_specie->fetch_assoc()) {
        $data[] = $especie;
    }
}

// Enviar respuesta JSON
echo json_encode([
    'total_paginas' => $total_paginas,
    'especies' => $data,
    'grupos' => $grupos
]);
?>
