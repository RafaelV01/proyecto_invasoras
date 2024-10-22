<?php
include("../basedatos.php");
include("../dataBase_utils.php");

// Verificar que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario

    if (isset($_FILES['imagen_especie']) && $_FILES['imagen_especie']['error'] == 0) {
        $imagen = $_FILES['imagen_especie']['tmp_name'];
        $imagen_data = file_get_contents($imagen);
        $tipo_mime = $_FILES['imagen_especie']['type'];
    } else {
        echo "Error subiendo la imagen.";
        exit;
    }

    $datos = [
        'nombre_comun' => $_POST['nombre_comun'],
        'nombre_cientifico' => $_POST['nombre_cientifico'],
        'grupo' => $_POST['grupo'],
        'familia_especie' => $_POST['familia'],
        'afectacion_consecuencia' => $_POST['afectacion'],
        'region_pais' => $_POST['region'],
        'estado_regional' => $_POST['estado'],
        'origen' => $_POST['origen'],
        'fuente_bibliografica' => $_POST['referencia']
    ];

    $datoFotos = [
        'foto' => $imagen_data,
        'tipo_mime' => $tipo_mime
    ];

    // Guardar los datos en la tabla `animal`
    $resultado = guardarRegistro('animal', $datos);

    // Con el id de la especie agregarle las imagenes
    if($resultado){
        guardarImagenEspecie('imagen_especie', $datoFotos, $resultado);
    }

    // Mostrar el resultado
    echo $resultado;
}
?>