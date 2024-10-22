<?php
include("../basedatos.php");
include("../dataBase_utils.php");

// Verificar que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $datos = [
        'fecha_avistamiento' => $_POST['fecha_avistamiento'],
        'cantidad' => $_POST['cantidad_especie'],
        'descripcion_entorno' => $_POST['entorno'],
        'coord_lat' => $_POST['ubiLat'],
        'coord_long' => $_POST['ubiLng']
    ];


    

    $num_especie = $_POST['num_especie'];

    // Guardar los datos en la tabla `reporte`
    $resultado = guardarRegistro('reporte', $datos);

    if ($resultado) {
        // Si el guardado fue exitoso, guardar las imágenes
        if (isset($_FILES['imagen_subida'])) {
            // Recorrer las imágenes seleccionadas
            foreach ($_FILES['imagen_subida']['tmp_name'] as $key => $tmp_name) {

                // Verificar si no hay errores en la carga del archivo
                if ($_FILES['imagen_subida']['error'][$key] == 0 && is_uploaded_file($tmp_name)) {
                    // Obtener la ruta temporal del archivo
                    $imagen = $_FILES['imagen_subida']['tmp_name'][$key];

                    // Leer el contenido de la imagen
                    $imagen_data = file_get_contents($imagen);

                    // Obtener el tipo MIME de la imagen
                    $tipo_mime = $_FILES['imagen_subida']['type'][$key];

                    // Crear un arreglo con la información de la imagen
                    $datoFotos = [
                        'foto' => $imagen_data,
                        'tipo_mime' => $tipo_mime
                    ];

                    // Llamar a la función para guardar la imagen en la tabla `imagen_reporte`
                    guardarImagenReporte('imagen_reporte', $datoFotos, $resultado);
                } else {
                    // Manejar el error de carga del archivo
                    echo "Error al cargar el archivo: " . $_FILES['imagen_subida']['name'][$key];
                }
            }
        }

        // Guardar la relación con la especie si existe un número de especie
        if (!empty($num_especie)) {
            guardarRelacionReporte($resultado, $num_especie);
        }
    }
    

    return "Reporte Realizado";
}
?>
