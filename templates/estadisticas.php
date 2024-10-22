<?php
// Cambia estos valores según lo que configuraste en phpMyAdmin
include_once("php/basedatos.php");
include_once("php/dataBase_utils.php");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Inicializar variables para las estadísticas
$totalAvistamientos = 0;
$totalCantidad = 0;
$reportesUnicos = 0;
$reportesUltimoMes = 0;

// Obtener la fecha actual y calcular la fecha hace un mes
$fechaActual = new DateTime();
$fechaUltimoMes = new DateTime();
$fechaUltimoMes->modify('-1 month');

// Consulta para obtener todos los reportes
$sql = "SELECT * FROM reporte";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    // Contar el total de avistamientos y calcular la cantidad total
    while ($row = $result->fetch_assoc()) {
        $totalAvistamientos++;
        $totalCantidad += $row["cantidad"];

        // Comparar la fecha de avistamiento con la fecha de hace un mes
        $fechaAvistamiento = new DateTime($row["fecha_avistamiento"]);
        if ($fechaAvistamiento >= $fechaUltimoMes) {
            $reportesUltimoMes++;
        }
    }
    // Obtener el número de reportes únicos
    $reportesUnicos = $result->num_rows; // En este caso, es igual al total de filas
} else {
    echo "<p>No hay reportes disponibles.</p>";
}

// Calcular el promedio de cantidad (si hay avistamientos)
$promedioCantidad = $totalAvistamientos > 0 ? $totalCantidad / $totalAvistamientos : 0;

// Cerrar conexión
$conexion->close();

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="../Styles/styles_rep.css">

    <title>Especies Invasoras Unitrópico</title>

    <style>
        .highlight {
            background-color: yellow;
            /* Color para resaltar coincidencias */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-sm navbar-dark topbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../Resources/Images/logo-unitropico.png" alt="Unitrópico" class="topbarlogo">
            </a>
        </div>
    </nav>

    <!-- Gráfico de barras -->
    <div class="row mb-5 d-none d-md-block">
        <div class="col-12">
            <canvas id="estadisticasChart" style="max-height: 500px; width: 100%;"></canvas>
        </div>
    </div>


    <script>
        // Datos para el gráfico
        // Datos originales para el gráfico
        let originalData = [
            <?php echo $totalAvistamientos; ?>,
            <?php echo $totalCantidad; ?>,
            <?php echo round($promedioCantidad, 2); ?>,
            <?php echo $reportesUnicos; ?>,
            <?php echo $reportesUltimoMes; ?>
        ];

        // Inicializa el gráfico una vez
        let data = {
            labels: [
                'Total de Avistamientos',
                'Total de Cantidad de Especies Avistadas',
                'Promedio de Cantidad por Reporte',
                'Número de Reportes Únicos',
                'Número de Reportes en el Último Mes'
            ],
            datasets: [{
                label: 'Estadísticas',
                data: originalData,  // Usa los datos originales aquí
                backgroundColor: [
                    'rgba(0, 123, 255)', // Total de Avistamientos
                    'rgba(40, 167, 69)', // Total de Cantidad de Especies Avistadas
                    'rgba(23, 162, 184)', // Promedio de Cantidad por Reporte
                    'rgba(255, 193, 7)', // Número de Reportes Únicos
                    'rgba(2255, 0, 0)'  // Número de Reportes en el Último Mes
                ],
                borderColor: [
                    'rgba(0, 0, 0)',
                    'rgba(0, 0, 0)',
                    'rgba(0, 0, 0)',
                    'rgba(0, 0, 0)',
                    'rgba(0, 0, 0)'
                ],
                borderWidth: 1
            }]
        };

        // Configuración del gráfico
        const config = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Estadísticas'
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Estadísticas de Avistamientos',
                        font: {
                            size: 20
                        }
                    }
                }
            }
        };

        // Renderizar el gráfico
        const estadisticasChart = new Chart(
            document.getElementById('estadisticasChart'),
            config
        );

        // Variable para mantener el estado del índice seleccionado
        let selectedIndex = null;

        // Función para actualizar el gráfico
        function updateChart(valueIndex) {
            // Crear un nuevo array con todos los valores en cero
            const newData = Array(originalData.length).fill(0);

            // Verificar si el card actual ya está seleccionado
            if (selectedIndex === valueIndex) {
                // Si es el mismo, mostrar todos los valores
                newData.forEach((_, index) => {
                    newData[index] = originalData[index];
                });
                // Reiniciar el índice seleccionado
                selectedIndex = null;
            } else {
                // Si no es el mismo, mostrar solo el valor correspondiente
                newData[valueIndex] = originalData[valueIndex];
                // Actualizar el índice seleccionado
                selectedIndex = valueIndex;
            }

            // Asignar los nuevos datos al gráfico
            estadisticasChart.data.datasets[0].data = newData;

            // Actualizar el gráfico
            estadisticasChart.update();
        }




    </script>

    <div class="container mt-5">


        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3 shadow" style="height: 150px;" data-value="0"
                    onclick="updateChart(0)">
                    <div class="card-header text-center font-weight-bold">Total de Avistamientos</div>
                    <div class="card-body text-center">
                        <h5 class="card-title" style="font-size: 1.5rem;"><?php echo $totalAvistamientos; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3 shadow" style="height: 150px;" data-value="1"
                    onclick="updateChart(1)">
                    <div class="card-header text-center font-weight-bold">Total de Cantidad de Especies Avistadas</div>
                    <div class="card-body text-center">
                        <h5 class="card-title" style="font-size: 1.5rem;"><?php echo $totalCantidad; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3 shadow" style="height: 150px;" data-value="2"
                    onclick="updateChart(2)">
                    <div class="card-header text-center font-weight-bold">Promedio de Cantidad por Reporte</div>
                    <div class="card-body text-center">
                        <h5 class="card-title" style="font-size: 1.5rem;"><?php echo round($promedioCantidad, 2); ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-white bg-warning mb-3 shadow" style="height: 150px;" data-value="3"
                    onclick="updateChart(3)">
                    <div class="card-header text-center font-weight-bold">Número de Reportes Únicos</div>
                    <div class="card-body text-center">
                        <h5 class="card-title" style="font-size: 1.5rem;"><?php echo $reportesUnicos; ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-danger mb-3 shadow" style="height: 150px;" data-value="4"
                    onclick="updateChart(4)">
                    <div class="card-header text-center font-weight-bold">Número de Reportes en el Último Mes</div>
                    <div class="card-body text-center">
                        <h5 class="card-title" style="font-size: 1.5rem;"><?php echo $reportesUltimoMes; ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>