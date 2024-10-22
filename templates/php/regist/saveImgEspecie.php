<?php

include("../basedatos.php");
include("../dataBase_utils.php");

header('Content-Type: application/json');
echo "<script>console.log('data id ='+" . $_POST['id'] . ");</script>";
if (isset($_POST['id']) && isset($_FILES['file'])) {
    try {
        // Verificar si se subió el archivo sin errores
        if ($_FILES['file']['error'] === 0) {
            $imagen = $_FILES['file']['tmp_name'];
            $imagen_data = file_get_contents($imagen);
            $tipo_mime = $_FILES['file']['type'];
        } else {
            throw new Exception("Error subiendo la imagen: " . $_FILES['file']['error']);
        }

        // Datos de la imagen
        $datoFoto = [
            'foto' => $imagen_data,
            'tipo_mime' => $tipo_mime
        ];

        $id_especie = $_POST['id'];

        // Guardar la imagen en la base de datos
        guardarImagenEspecie('imagen_especie', $datoFoto, $id_especie);

        // Respuesta de éxito
        echo json_encode(['success' => true, 'message' => 'Imagen guardada correctamente']);
    } catch (Exception $e) {
        // Manejar errores y devolver mensaje en JSON
        echo json_encode(['success' => false, 'message' => 'Se ha producido un error: ' . $e->getMessage()]);
    }
} else {
    // Respuesta si no se recibieron los datos necesarios
    echo json_encode(['success' => false, 'message' => 'No se recibió el ID de la especie o el archivo']);
}

?>