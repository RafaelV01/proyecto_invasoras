<?php
include_once("../basedatos.php");



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Comprobar si el formulario ha sido enviado
    if (isset($_POST['num_especie'])) {
        // Obtener los valores del formulario enviados por POST
        $num_especie = $_POST['num_especie'];
        $nombre_comun = $_POST['nombre_comun'];
        $nombre_cientifico = $_POST['nombre_cientifico'];
        $familia_especie = $_POST['familia_especie'];
        // $grupo se omite
        $region_pais = $_POST['region_pais'];
        $afectacion_consecuencia = $_POST['afectacion_consecuencia'];
        $estado_regional = $_POST['estado_regional'];
        $origen = $_POST['origen'];
        $fuente_bibliografica = $_POST['fuente_bibliografica'];

        // Debug: Imprimir los datos recibidos
        var_dump($_POST);

        // Consulta SQL para actualizar el registro
        $sql = "UPDATE animal SET 
            nombre_comun = ?, 
            nombre_cientifico = ?, 
            familia_especie = ?, 
            region_pais = ?, 
            afectacion_consecuencia = ?, 
            estado_regional = ?, 
            origen = ?, 
            fuente_bibliografica = ? 
            WHERE num_especie = ?";

        // Preparar la declaración
        if ($stmt = $conexion->prepare($sql)) {
            // Enlazar parámetros
            $stmt->bind_param("ssssssssi", 
                $nombre_comun, 
                $nombre_cientifico, 
                $familia_especie, 
                $region_pais, 
                $afectacion_consecuencia, 
                $estado_regional, 
                $origen, 
                $fuente_bibliografica,
                $num_especie
            );

            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo "Registro actualizado exitosamente";
            } else {
                echo "Error al actualizar el registro: " . $stmt->error;
            }

            // Cerrar la declaración
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conexion->error;
        }
    } else {
        echo "Formulario vacío o no se recibió 'num_especie'.";
    }
} else {
    echo "Método no permitido.";
}

// Cerrar conexión
$conexion->close();


/**
 * Función para validar si el grupo existe en la base de datos
 */
function validarGrupo($grupo, $conexion) {
    // Inicializar la variable $count
    $count = 0; // Asignar un valor predeterminado

    // Preparar la consulta
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM grupo_especie WHERE cod_grupo = ?");
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    // Cambia 's' a 'i' si 'cod_grupo' es un entero
    $stmt->bind_param("s", $grupo);
    
    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Obtener el resultado
        $stmt->bind_result($count);
        $stmt->fetch();
    } else {
        echo "Error al ejecutar la consulta: " . $stmt->error;
    }
    
    // Cerrar la declaración
    $stmt->close();
    
    return $count > 0; // Devuelve true si el grupo existe
}

