<?php
include_once("php/basedatos.php");
include_once("php/dataBase_utils.php");

$reportes = getAllData($conexion, 'reporte');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>Especies Invasoras Unitrópico</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            /* Altura del mapa */
            width: 100%;
            /* Ancho del mapa */
        }

        /* Estilos para la tarjeta */
        .custom-popup {
            font-family: Arial, sans-serif;
            width: 200px;
        }

        .custom-popup h3 {
            margin-top: 0;
            font-size: 16px;
        }

        .custom-popup p {
            margin: 8px 0;
            font-size: 14px;
        }

        img{
            width: 100px;
            height: auto;
        }

        .container-s{
            min-width: 80%;
            min-height: 80%;
        }

        @media(min-width:768px){
            .container-s{
                min-width:80%;
            }
        }
    </style>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <link rel="stylesheet" href="../Styles/styles_rep.css">
</head>

<body>
    <nav class="navbar navbar-expand-sm navbar-dark topbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../Resources/Images/logo-unitropico.png" alt="Unitrópico" class="topbarlogo">
            </a>
            
        </div>
    </nav>

    <!-- options -->
    <div class="options-main">
        <div class="container-s">
            <div class="text-block mb-3">
                <label for="locationInput" class="form-label">Ubicación</label>
                <div class="input-group">
                    
                    
                </div>
            </div>

            <div id="map"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar el mapa de Leaflet con la vista centrada en Yopal, Casanare
            var map = L.map('map').setView([5.33775, -72.39586], 5); // Coordenadas y nivel de zoom

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);



            <?php
            // Iterar sobre las coordenadas y generar marcadores para cada una
            foreach ($reportes as $reporte) {
                // Crear un popup HTML personalizado con el nombre y descripción
                $popupContent = "<div class='custom-popup'>"
                    . "<h3>{$reporte['fecha_avistamiento']}</h3>"
                    . "<p>Cantidad: {$reporte['cantidad']}</p>"
                    . "<p>Descripción del entorno: {$reporte['descripcion_entorno']}</p>"
                    . "<img src='php/getImageReport.php?id=" . $reporte['num_reporte'] . "' alt='Imagen del reporte'>"
                    . "</div>";

                echo "L.marker([{$reporte['coord_lat']}, {$reporte['coord_long']}]).addTo(map)"
                    . ".bindPopup(\"$popupContent\");\n";
            }
            ?>

            // Manejar la posición GPS
            /*document.getElementById('useGPSBtn').addEventListener('click', function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var lat = position.coords.latitude;
                        var lon = position.coords.longitude;
                        map.setView([lat, lon], 13);
                        L.marker([lat, lon]).addTo(map)
                            .bindPopup('Tu ubicación actual')
                            .openPopup();
                        document.getElementById('locationInput').value = `Lat: ${lat}, Lon: ${lon}`;
                    }, function () {
                        alert('No se pudo obtener la ubicación.');
                    });
                } else {
                    alert('Geolocalización no soportada por este navegador.');
                }
            });*/
        });
    </script>


</body>

</html>