<?php
include('basedatos.php');

function guardarRegistro($tabla, $datos)
{
    global $conexion;

    // Preparar las columnas y los valores
    $columnas = implode(", ", array_keys($datos));
    $valores = implode(", ", array_map(function ($item) {
        global $conexion;
        return "'" . $conexion->real_escape_string($item) . "'";
    }, array_values($datos)));

    // Crear la consulta SQL
    $sql = "INSERT INTO $tabla ($columnas) VALUES ($valores)";

    // Ejecutar
    if ($conexion->query($sql) === TRUE) {
        // Obtener el ID del registro insertado
        $save_id = $conexion->insert_id;

        return $save_id;
    } else {
        return "Error: " . $sql . "<br>" . $conexion->error;
    }
}


function guardarImagenEspecie($nameTabla, $dataImage, $numEspecie)
{
    global $conexion;

    // Preparar las columnas y los valores
    $columnas = implode(", ", array_keys($dataImage));
    $valores = implode(", ", array_map(function ($item) {
        global $conexion;
        return "'" . $conexion->real_escape_string($item) . "'";
    }, array_values($dataImage)));

    $idPredeterminado = 10000;

    $idLastImage = getLastIdTbl('imagen_especie', 'cod_imagen', $idPredeterminado);

    // Crear la consulta SQL
    $sql = "INSERT INTO $nameTabla (cod_imagen,num_especie,$columnas) VALUES ($idLastImage + 1,$numEspecie,$valores)";

    // Ejecutar
    if ($conexion->query($sql) === TRUE) {
        header('Location: ../../home_admin.php');
        echo 'Registro Guardado';
        exit;

    } else {
        return "Error: " . $sql . "<br>" . $conexion->error;
    }
}

function guardarImagenReporte($nameTabla, $dataImage, $numReporte)
{
    global $conexion;

    // Preparar las columnas y los valores
    $columnas = implode(", ", array_keys($dataImage));
    $valores = implode(", ", array_map(function ($item) {
        global $conexion;
        return "'" . $conexion->real_escape_string($item) . "'";
    }, array_values($dataImage)));

    $idPredeterminado = 100000;

    $idLastImage = getLastIdTbl($nameTabla, 'cod_imagen', $idPredeterminado);

    // Crear la consulta SQL
    $sql = "INSERT INTO $nameTabla (cod_imagen,num_reporte,$columnas) VALUES ($idLastImage + 1,$numReporte,$valores)";

    // Ejecutar
    if ($conexion->query($sql) === TRUE) {
        echo 'Registro Guardado';
        exit;

    } else {
        echo 'Algo salió mal' . $conexion->error;
    }
}

function guardarRelacionReporte($idReporte, $idEspecie)
{
    global $conexion;


    $sql = "INSERT INTO registro_reporte (num_especie,num_reporte) VALUES ($idEspecie,$idReporte)";


    // Ejecutar
    if ($conexion->query($sql) === TRUE) {
        $relacionEstadosql = "INSERT INTO rel_estado_reporte (num_reporte,id_estado,notas) VALUES ($idReporte,1,'Reporte Realizado')";
        if ($conexion->query($relacionEstadosql) === TRUE) {
            return 'Registro Guardado';
        }
    } else {
        return "Error: " . $sql . "<br>" . $conexion->error;
    }
}

//GETTERS 

function getAllData($conexion, $table)
{
    // Consulta para obtener los hoteles
    $sql = "SELECT * FROM $table";
    $result = $conexion->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        // save data to array
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    return $data;
}

function getGroupsDescNum($numGrupo)
{
    global $conexion;
    // Consulta para obtener los hoteles
    $sql = "SELECT * FROM grupo_especie WHERE cod_grupo=$numGrupo ORDER BY cod_grupo DESC";
    $result = $conexion->query($sql);

    return $result;
}

function getUserIp()
{
    // verificar si esta con proxy
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getLocationIp()
{
    $ipUser = getUserIp();
    $url = "http://ip-api.com/json/{$ipUser}";

    // Obtener datos de geolocalizacion
    $datosGeo = file_get_contents($url);
    $datosGeo = json_decode($datosGeo);

    // Verificar la solicitud
    if ($datosGeo->status === 'success') {
        $latitud = $datosGeo->lat;
        $longitud = $datosGeo->lon;

        // Devolver resultado
        return [
            'lat' => $latitud,
            'lon' => $longitud
        ];
    } else {
        // Si falla retornar null o manejar el error
        return null;
    }
}

function getLastIdTbl($tabla, $nameIdField, $idPredeterminado)
{
    global $conexion;

    // Obtener el ultimo ID
    $sql = "SELECT MAX($nameIdField) as max_id FROM $tabla";
    $result = $conexion->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['max_id'] ? $row['max_id'] : $idPredeterminado;
    }

    return $idPredeterminado;
}


?>