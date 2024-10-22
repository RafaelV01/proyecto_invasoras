<?php
session_start();

include_once("php/basedatos.php");
include_once("php/dataBase_utils.php");





if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login_adm.php");
    exit;
}

// Consulta a la base de datos para unir las tablas reporte, registro_reporte y imagen_especie
$sql = "
    SELECT 
    r.num_reporte, 
    r.fecha_avistamiento, 
    r.cantidad,  
    r.descripcion_entorno, 
    r.coord_lat, 
    r.coord_long, 
    rr.num_especie, 
    ie.foto 
FROM 
    reporte r
JOIN 
    registro_reporte rr ON r.num_reporte = rr.num_reporte
JOIN 
    imagen_especie ie ON rr.num_especie = ie.num_especie

";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Actualizar estados de reportes seleccionados
    // Actualizar estados de reportes seleccionados
    if (isset($_POST['update_selected'])) {
        if (!empty($_POST['nuevo_estado']) && !empty($_POST['num_reporte'])) {
            $nuevo_estado = $_POST['nuevo_estado']; // Array de nuevos estados
            $num_reportes = $_POST['num_reporte']; // Array de números de reporte

            // Asegúrate de que ambos arrays tengan la misma longitud
            foreach ($num_reportes as $num_reporte) {
                $estado = intval($nuevo_estado[$num_reporte]); // Obtener el nuevo estado
                $num_reporte = intval($num_reporte); // Asegúrate de que sea un entero

                // Construir la consulta de actualización
                $sql = "UPDATE rel_estado_reporte SET id_estado = $estado WHERE num_reporte = $num_reporte";

                // Ejecutar la consulta
                if ($conexion->query($sql) === TRUE) {
                    echo "<script>console.log('Estado del reporte $num_reporte actualizado correctamente.')</script>";
                } else {
                    echo "<p>Error al actualizar el reporte $num_reporte: " . $conexion->error . "</p>";
                }
            }
        } else {
            echo "<p>No se han seleccionado reportes o estados.</p>";
        }
    }

    // Eliminar reportes seleccionados
    if (isset($_POST['delete_selected'])) {
        if (!empty($_POST['num_reporte'])) {
            $num_reportes = $_POST['num_reporte']; // Array de números de reporte
            $num_reportes = array_map('intval', $num_reportes); // Convertir a enteros para mayor seguridad

            // Construir la consulta de eliminación
            $num_reporte_list = implode(',', $num_reportes);
            $sql = "DELETE FROM reporte WHERE num_reporte IN ($num_reporte_list)";

            // Ejecutar la consulta
            if ($conexion->query($sql) === TRUE) {
                echo "<p>Reportes eliminados correctamente.</p>";
            } else {
                echo "<p>Error al eliminar los reportes: " . $conexion->error . "</p>";
            }
        }
    }
}










$result = $conexion->query($sql);
$list_groups_specie = getAllData($conexion, 'grupo_especie');

$sql = "SELECT * FROM `reporte`";
$result = $conexion->query($sql);
?>
<html lang="es">



<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrador</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../Styles/styles_home_admin.css">
</head>


<body>
    <!-- Barra superior -->
    <nav class="navbar navbarcolor fixed-top">
        <div class="container">
            <a class="navbar-brand text-white" href="home_admin.php">
                <img src="../Resources/Images/logo-unitropico.png" alt="Logo Unitrópico"
                    class="rounded-pill topbarlogo">
            </a>
            <a class="navbar-brand text-white" href="" onclick="reload()">
                <p><strong>Especies Invasoras Colombia</strong></p>
            </a>
        </div>
    </nav>


    <div class="content-wrapper">
        <!-- Barra lateral -->
        <div class="sidebar">
            <div class="list-group">
                <a href="#" class="colorSideBar list-group-item list-group-item-action"
                    onclick="showContent('content1')">Agregar Especie</a>

                <a href="#" class="colorSideBar list-group-item list-group-item-action"
                    onclick="showContent('content2')">Editar Especies</a>

                <a href="#" class="colorSideBar list-group-item list-group-item-action"
                    onclick="showContent('content3')">Reportes</a>

                <a href="../Services/log_out.php" class="colorSideBar list-group-item list-group-item-action">
                    Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="main-content text-light">
            <form action="php/regist/guardar_especie.php" method="POST" enctype="multipart/form-data">
                <div id="content1" class="content">
                    <h2>Formulario de nueva especie</h2>
                    <br>
                    <label for="edtnombrecomun_reg">Nombre común (Límite 100 caracteres)</label><br>
                    <input type="text" name="nombre_comun" id="edtnombrecomun_reg" maxlength="100"
                        placeholder="Por ejemplo: Águila Calva" required><br>

                    <label for="edtnombrecientifico_reg">Nombre científico (Límite 100 caracteres)</label><br>
                    <input type="text" name="nombre_cientifico" id="edtnombrecientifico_reg" maxlength="100"
                        placeholder="Por ejemplo: Haliaeetus leucocephalus" required><br>

                    <label for="edtgrupo">Grupo al que pertenece la especie</label><br>
                    <select id="edtgrupo" name="grupo" required>
                        <option value="">Seleccionar</option>
                        <?php
                        if (!empty($list_groups_specie)) {
                            foreach ($list_groups_specie as $group) {
                                ?>
                                <option value="<?php echo $group['cod_grupo']; ?>"><?php echo $group['nombre']; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select><br>

                    <label for="edtfamilia_reg">Familia de la especie (Límite 100 caracteres)</label><br>
                    <input type="text" name="familia" id="edtfamilia_reg" placeholder="Por ejemplo: Accipitridae"
                        maxlength="100" required><br>

                    <label for="edtafectacion_reg">Afectación (Límite 500 caracteres)</label><br>
                    <textarea name="afectacion" id="edtafectacion_reg" maxlength="500"
                        placeholder="Consecuencias de su presencia" rows="4" required></textarea><br>


                    <label for="edtregion_pais">Región del País</label><br>
                    <input type="text" name="region" id="edtregion_pais" maxlength="150"
                        placeholder="Región donde se encuentra" required><br>

                    <label for="edtestado_reg">Estado Regional</label><br>
                    <input type="text" name="estado" id="edtestado_reg" maxlength="200"
                        placeholder="Por ejemplo: Introducida" required><br>

                    <label for="edtorigen_reg">Origen</label><br>
                    <input type="text" name="origen" id="edtorigen_reg" placeholder="Sitio de procedencia"
                        maxlength="100" required><br>

                    <label for="edtreferencia_reg">Referencia Bibliográfica (Límite 1000 caracteres)</label><br>
                    <textarea name="referencia" id="edtreferencia_reg" maxlength="1000" rows="4"
                        placeholder="Fuentes de la información (Links separados por comas)" required></textarea><br>

                    <label for="imagen_especie">Imagen especie</label><br>
                    <input type="file" name="imagen_especie" id="imagen_especie" accept="image/*" required><br><br>

                    <button type="submit" class="btn btn-success btn-outline-light" id="btnreg">Registrar
                        Especie</button>
                    <br>
                </div>
            </form>


            <div id="content2" class="content" style="display: none;">
                <h2>Especies</h2>

                <input type="text" id="edtnit_search" placeholder="Buscar por nombre común o científico" required
                    style="display:none;">
                <button class="btnSearch" style="display:none;">Buscar</button>
                <br>

                <div class="container">
                    <!-- mostrar enlaces de paginacion -->
                    <div class='paginacion'>

                    </div>

                </div>

                <table id="list_edt_especies">
                    <!-- Resultados de la consulta -->
                </table>
            </div>


            <div id="content3" class="content" style="display: none;">
                <h3>Reportes</h3>


                <form action="" method="post">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Seleccionar</th>
                                <th>Número de Reporte</th>
                                <th>Fecha de Avistamiento</th>
                                <th>Cantidad</th>
                                <th>Descripción del Entorno</th>
                                <th>Foto</th>
                                <th>Coordenadas</th>
                                <th>Estado</th> <!-- Nueva columna para el estado -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // funciones.php
                            include_once("php/basedatos.php");

                            function getEstadoReporte($num_reporte)
                            {
                                global $conexion; // Asegúrate de que la conexión esté disponible
                            
                                // Usar una consulta preparada
                                $query = "SELECT e.nombre 
                                    FROM rel_estado_reporte r 
                                    JOIN estado_reporte e ON r.id_estado = e.id_estado 
                                    WHERE r.num_reporte = ? 
                                    ORDER BY r.fecha_actualizado DESC LIMIT 1";

                                // Preparar la consulta
                                $stmt = $conexion->prepare($query);
                                $stmt->bind_param("i", $num_reporte);

                                $stmt->execute();
                                $resultado = $stmt->get_result();

                                if ($resultado && $row = $resultado->fetch_assoc()) {
                                    return $row['nombre'];
                                }

                                return 'pendiente'; // Valor por defecto si no se encuentra el estado
                            }

                            function obtenerEstados()
                            {
                                global $conexion;
                                $estados = [];

                                $query = "SELECT id_estado, nombre FROM estado_reporte";
                                $resultado = $conexion->query($query);

                                if ($resultado) {
                                    while ($row = $resultado->fetch_assoc()) {
                                        $estados[$row['id_estado']] = $row['nombre'];
                                    }
                                }

                                return $estados;
                            }


                            $estados = obtenerEstados();
                            if ($result && $result->num_rows > 0) {
                                // Salida de datos de cada fila
                                while ($row = $result->fetch_assoc()) {
                                    // Obtener el estado actual del reporte
                                    $num_reporte = $row["num_reporte"];
                                    $estado_actual = getEstadoReporte($num_reporte); // Función para obtener el estado actual
                            
                                    echo "<tr>";
                                    echo "<td><input type='checkbox' name='num_reporte[]' value='" . $row["num_reporte"] . "'></td>";
                                    echo "<td>" . $row["num_reporte"] . "</td>";
                                    echo "<td>" . $row["fecha_avistamiento"] . "</td>";
                                    echo "<td>" . $row["cantidad"] . "</td>";
                                    echo "<td>" . $row["descripcion_entorno"] . "</td>";
                                    echo "<td><img src='php/getImageReport.php?id=" . $row["num_reporte"] . "' alt='Foto del reporte' width='100'></td>";
                                    echo "<td>" . $row["coord_lat"] . ", " . $row["coord_long"] . "</td>";
                                    echo "<td>
                                            <select name='nuevo_estado[$num_reporte]'>";

                                    // Generar opciones dinámicamente desde la base de datos
                                    foreach ($estados as $id_estado => $nombre_estado) {
                                        // Si el estado_actual es null, no seleccionamos ninguna opción
                                        $selected = ($estado_actual == $nombre_estado) ? ' selected' : '';
                                        echo "<option value='$id_estado'$selected>$nombre_estado</option>";
                                    }

                                    // Si estado_actual es null, agregar una opción por defecto
                                    if (is_null($estado_actual)) {
                                        echo "<option value='-1' selected>No asignado</option>"; // Valor especial para reportes sin estado
                                    }

                                    echo "</select>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>No hay reportes disponibles.</td></tr>";
                            }

                            // Cerrar conexión
                            $conexion->close();
                            ?>
                        </tbody>
                    </table>
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" name="delete_selected"
                            onclick="return confirm('¿Estás seguro de que deseas eliminar los reportes seleccionados?')">Eliminar
                            Seleccionados</button>
                        <button type="submit" name="update_selected"
                            onclick="return confirm('¿Estás seguro de que deseas actualizar los estados de los reportes seleccionados?')">Actualizar
                            Estados</button>
                    </div>
                </form>



            </div>
        </div>






    </div>




    <div id="content4" class="content" style="display: none;">

    </div>


    </div>








    </script>


    <script>
        function showContent(contentId) {
            const contents = document.querySelectorAll('.content');
            contents.forEach(content => {
                content.style.display = 'none';
            });
            document.getElementById(contentId).style.display = 'block';
        }

        function reload() {
            window.location.reload();
        }
    </script>

    <script>
        function opcionVerAddImgCarousel() {
            const cajaNuevaImagen = document.getElementById('boxNuevaImagenC');

            if (cajaNuevaImagen.style.display === 'none') {
                cajaNuevaImagen.style.display = 'block';
            } else {
                cajaNuevaImagen.style.display = 'none';
            }
        }
    </script>


    <!-- Update Estado -->
    <script>
        function updateEstado(num_reporte, estado) {
            // Realiza una petición AJAX para actualizar el estado en la base de datos
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "updateEstado.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log("Estado actualizado correctamente");
                } else {
                    console.error("Error al actualizar el estado");
                }
            };

            xhr.send("num_reporte=" + num_reporte + "&estado=" + estado);
        }

    </script>


    <script>
        // Función para cargar las especies
        function cargarEspecies(pagina) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'php/get/getPaginEspecie.php?pagina=' + pagina, true);
            xhr.onload = function () {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    const especies = response.especies;
                    const totalPaginas = response.total_paginas;
                    const grupos = response.grupos;

                    // Actualizar la lista de especies
                    const listContainer = document.getElementById('list_edt_especies');
                    listContainer.innerHTML = ''; // Limpiar la lista actual

                    if (listContainer.innerHTML === '') {
                        listContainer.innerHTML += `
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Nombre Común</th>
                                        <th>Especie</th>
                                        <th>Acciones</th>
                                    </tr>
                                `;
                    }

                    especies.forEach(especie => {
                        // Crear la fila de la tabla
                        const row = document.createElement('tr');
                        // Crear la celda para el ícono
                        const iconoCelda = document.createElement('td');
                        iconoCelda.style.position = "relative";

                        // Crear un div contenedor 
                        const imgContainer1 = document.createElement('div');
                        imgContainer1.style.position = 'relative';
                        imgContainer1.style.display = 'inline-block';

                        const iconoImg = document.createElement('img');
                        iconoImg.src = `php/getImgListEspecie.php?id=${especie.num_especie}`;
                        iconoImg.alt = "Vista previa de la especie invasora";
                        iconoImg.className = "rounded imageEspecie";
                        iconoImg.style.width = "100px";
                        iconoImg.style.height = "auto";

                        imgContainer1.appendChild(iconoImg);

                        // Crear el botón que activará el modal de edición
                        const btnEdtImage = document.createElement('button');
                        btnEdtImage.className = 'btn btnEditImgs';
                        btnEdtImage.style.position = 'absolute';
                        btnEdtImage.style.top = '-3px';
                        btnEdtImage.style.right = '-25px';

                        btnEdtImage.setAttribute("data-bs-toggle", "modal");
                        btnEdtImage.setAttribute("data-bs-target", `#ventanaModal${especie.num_especie}`);

                        const icon = document.createElement('img');
                        icon.className = 'img-fluid';
                        icon.src = "../Resources/Icons/ic_edit_img.png";

                        // Agregar el ícono al botón
                        btnEdtImage.appendChild(icon);

                        imgContainer1.appendChild(btnEdtImage);

                        iconoCelda.appendChild(imgContainer1);

                        // Crear la ventana modal para mostrar el ícono
                        const ventanaModal = document.createElement('div');
                        ventanaModal.className = "modal fade";
                        ventanaModal.id = `ventanaModal${especie.num_especie}`;
                        ventanaModal.tabIndex = -1;
                        ventanaModal.setAttribute("aria-labelledby", `etiquetaModal${especie.num_especie}`);
                        ventanaModal.setAttribute("aria-hidden", "true");

                        const dialogoModal = document.createElement('div');
                        dialogoModal.className = "modal-dialog modal-lg";
                        const contenidoModal = document.createElement('div');
                        contenidoModal.className = "modal-content";

                        const cabeceraModal = document.createElement('div');
                        cabeceraModal.className = "modal-header";
                        const tituloModal = document.createElement('h5');
                        tituloModal.className = "modal-title";
                        tituloModal.id = `etiquetaModal${especie.num_especie}`;
                        tituloModal.textContent = especie.nombre_comun;
                        const botonCerrar = document.createElement('button');
                        botonCerrar.type = "button";
                        botonCerrar.className = "btn-close";
                        botonCerrar.setAttribute("data-bs-dismiss", "modal");
                        botonCerrar.setAttribute("aria-label", "Close");

                        cabeceraModal.appendChild(tituloModal);
                        cabeceraModal.appendChild(botonCerrar);

                        const cuerpoModal = document.createElement('div');
                        cuerpoModal.className = "modal-body";
                        const imagenModal = document.createElement('img');
                        imagenModal.src = `php/getImgListEspecie.php?id=${especie.num_especie}`;
                        imagenModal.alt = "Vista ampliada de la especie exótica";
                        imagenModal.className = "img-fluid mb-3";
                        cuerpoModal.appendChild(imagenModal);

                        contenidoModal.appendChild(cabeceraModal);
                        contenidoModal.appendChild(cuerpoModal);
                        dialogoModal.appendChild(contenidoModal);
                        ventanaModal.appendChild(dialogoModal);

                        // Formulario para subir nueva imagen
                        const inputGroup = document.createElement('div');
                        inputGroup.className = "mb-3";
                        const inputFile = document.createElement('input');
                        inputFile.type = "file";
                        inputFile.accept = "image/*";
                        inputFile.className = "form-control";
                        inputFile.id = `newImageUpload${especie.num_especie}`;
                        inputGroup.appendChild(inputFile);

                        cuerpoModal.appendChild(inputGroup);

                        // Botón para eliminar la imagen
                        const deleteImgButton = document.createElement('button');
                        deleteImgButton.className = "btn btn-danger me-2";
                        deleteImgButton.textContent = "Eliminar imagen";
                        deleteImgButton.addEventListener('click', async (event) => {

                            const formDatas = new FormData();
                            formDatas.append('id', especie.num_especie);

                            try {
                                const response = await fetch('php/delete/del_img_especie.php', {
                                    method: 'POST',
                                    body: formDatas
                                });

                                if (response.ok) {
                                    alert('Imagen eliminada correctamente');

                                    let modalElement = document.getElementById(`ventanaModal${especie.num_especie}`);
                                    let modalInstance = bootstrap.Modal.getInstance(modalElement);  // Obtener la instancia del modal
                                    modalInstance.hide();  // Cerrar el modal

                                    cargarEspecies(1);

                                } else {
                                    alert('Error al guardar los cambios');
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                alert('Ocurrió un error al guardar los cambios');
                            }
                        });

                        // Botón para guardar la nueva imagen
                        const saveBtn = document.createElement('button');
                        saveBtn.className = "btn btn-primary";
                        saveBtn.textContent = "Guardar cambios";
                        saveBtn.addEventListener('click', async (event) => {

                            const fileInput = document.getElementById(`newImageUpload${especie.num_especie}`);

                            const formDatas = new FormData();
                            formDatas.append('id', especie.num_especie);
                            formDatas.append('file', fileInput.files[0]);

                            try {
                                const response = await fetch('php/regist/saveImgEspecie.php', {
                                    method: 'POST',
                                    body: formDatas
                                });

                                if (response.ok) {
                                    alert('Cambios guardados correctamente');

                                    let modalElement = document.getElementById(`ventanaModal${especie.num_especie}`);
                                    let modalInstance = bootstrap.Modal.getInstance(modalElement);  // Obtener la instancia del modal
                                    modalInstance.hide();  // Cerrar el modal

                                    cargarEspecies(1);

                                } else {
                                    alert('Error al guardar los cambios');
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                alert('Ocurrió un error al guardar los cambios');
                            }
                        });

                        /* cuerpoModal.appendChild(deleteImgButton); */
                        cuerpoModal.appendChild(saveBtn);


                        // Añadir la ventana modal a la celda del ícono
                        iconoCelda.appendChild(ventanaModal);
                        row.appendChild(iconoCelda);


                        // Crear la celda para el nombre común
                        const nombreComunCell = document.createElement('td');
                        const nombreComunStrong = document.createElement('strong');
                        nombreComunStrong.id = "info_nombre_comun";
                        nombreComunStrong.textContent = especie.nombre_comun;
                        nombreComunCell.appendChild(nombreComunStrong);
                        row.appendChild(nombreComunCell);

                        // Crear la celda para la familia
                        const familiaCell = document.createElement('td');
                        const familiaSpan = document.createElement('span');
                        familiaSpan.id = "info_especie";
                        familiaSpan.textContent = especie.familia_especie;
                        familiaCell.appendChild(familiaSpan);
                        row.appendChild(familiaCell);

                        // Crear la celda para los botones de acción
                        const actionCell = document.createElement('td');

                        const editButton = document.createElement('button');
                        editButton.className = "btn btn-warning";
                        editButton.setAttribute("data-bs-toggle", "modal");
                        editButton.setAttribute("data-bs-target", `#editModal${especie.num_especie}`);
                        editButton.textContent = "Editar";
                        actionCell.appendChild(editButton);

                        const deleteButton = document.createElement('button');
                        deleteButton.className = "btn btn-danger";
                        deleteButton.onclick = () => eliminarEspecie(especie.num_especie);
                        deleteButton.textContent = "Eliminar";
                        actionCell.appendChild(deleteButton);

                        row.appendChild(actionCell);
                        listContainer.appendChild(row);

                        // Crear el modal para editar especie
                        const editModalDiv = document.createElement('div');
                        editModalDiv.className = "modal fade";
                        editModalDiv.id = `editModal${especie.num_especie}`;
                        editModalDiv.tabIndex = -1;
                        editModalDiv.setAttribute("aria-labelledby", `editModalLabel${especie.num_especie}`);
                        editModalDiv.setAttribute("aria-hidden", "true");

                        const editModalDialog = document.createElement('div');
                        editModalDialog.className = "modal-dialog";
                        const editModalContent = document.createElement('div');
                        editModalContent.className = "modal-content";

                        const editModalHeader = document.createElement('div');
                        editModalHeader.className = "modal-header";
                        const editModalTitle = document.createElement('h5');
                        editModalTitle.className = "modal-title";
                        editModalTitle.id = `editModalLabel${especie.num_especie}`;
                        editModalTitle.textContent = `Editar Especie ${especie.nombre_cientifico}`;
                        const editCloseButton = document.createElement('button');
                        editCloseButton.type = "button";
                        editCloseButton.className = "btn-close";
                        editCloseButton.setAttribute("data-bs-dismiss", "modal");
                        editCloseButton.setAttribute("aria-label", "Close");

                        editModalHeader.appendChild(editModalTitle);
                        editModalHeader.appendChild(editCloseButton);

                        const editModalBody = document.createElement('div');
                        editModalBody.className = "modal-body";
                        const form = document.createElement('form');
                        form.id = `updateForm${especie.num_especie}`;
                        form.name = "dataForm";

                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "num_especie";
                        hiddenInput.value = especie.num_especie;
                        form.appendChild(hiddenInput);

                        const fields = [
                            /* { label: 'Nombre Común', name: 'nombre_comun', value: especie.nombre_comun, type: 'text' },
                            { label: 'Nombre Científico', name: 'nombre_cientifico', value: especie.nombre_cientifico, type: 'text' },
                            { label: 'Familia', name: 'familia_especie', value: especie.familia_especie, type: 'text' }, // Cambiado a 'familia_especie'
                            { label: 'Grupo', name: 'grupo', value: especie.familia_especie, type: 'select', options: grupos.map(grupo => ({ id: grupo.id, nombre: grupo.nombre, selected: especie.familia_especie === grupo.nombre })) },
                            { label: 'Región País', name: 'region_pais', value: especie.region_pais, type: 'text' },
                            { label: 'Afectación', name: 'afectacion_consecuencia', value: especie.afectacion_consecuencia, type: 'textarea' },
                            { label: 'Estado Regional', name: 'estado_regional', value: especie.estado_regional, type: 'text' },
                            { label: 'Origen', name: 'origen', value: especie.origen, type: 'text' },
                            { label: 'Fuente Bibliográfica', name: 'fuente_bibliografica', value: especie.fuente_bibliografica, type: 'textarea' } */

                            { label: 'Nombre Común', name: 'nombre_comun', value: especie.nombre_comun, type: 'text' },
                            { label: 'Nombre Científico', name: 'nombre_cientifico', value: especie.nombre_cientifico, type: 'text' },
                            // Elimina el campo de familia especie
                            { label: 'Familia', name: 'familia_especie', value: especie.familia_especie, type: 'text' },
                            { label: 'Grupo', name: 'grupo', value: especie.grupo, type: 'select', options: grupos.map(grupo => ({ id: grupo.id, nombre: grupo.nombre, selected: especie.grupo === grupo.id })) },
                            { label: 'Región País', name: 'region_pais', value: especie.region_pais, type: 'text' },
                            { label: 'Afectación', name: 'afectacion_consecuencia', value: especie.afectacion_consecuencia, type: 'textarea' },
                            { label: 'Estado Regional', name: 'estado_regional', value: especie.estado_regional, type: 'text' },
                            { label: 'Origen', name: 'origen', value: especie.origen, type: 'text' },
                            { label: 'Fuente Bibliográfica', name: 'fuente_bibliografica', value: especie.fuente_bibliografica, type: 'textarea' }
                        ];

                        fields.forEach(field => {
                            const div = document.createElement('div');
                            div.className = "mb-3";
                            const label = document.createElement('label');
                            label.className = "form-label";
                            label.setAttribute("for", `${field.name}_${especie.num_especie}`);
                            label.textContent = field.label;
                            div.appendChild(label);

                            let input;
                            if (field.type === 'select') {
                                input = document.createElement('select');
                                input.className = "form-select";
                                input.id = `${field.name}_${especie.num_especie}`;
                                input.name = field.name;
                                input.required = true;

                                field.options.forEach(option => {
                                    const opt = document.createElement('option');
                                    opt.value = option.id;
                                    opt.textContent = option.nombre;
                                    if (option.selected) {
                                        opt.selected = true;
                                    }
                                    input.appendChild(opt);
                                });
                            } else if (field.type === 'textarea') {
                                input = document.createElement('textarea');
                                input.className = "form-control";
                                input.id = `${field.name}_${especie.num_especie}`;
                                input.name = field.name;
                                input.rows = 5;
                                input.required = true;
                                input.textContent = field.value;
                            } else {
                                input = document.createElement('input');
                                input.type = field.type;
                                input.className = "form-control";
                                input.id = `${field.name}_${especie.num_especie}`;
                                input.name = field.name;
                                input.value = field.value;
                                input.required = true; // Asegurando que todos los campos son obligatorios
                            }

                            div.appendChild(input);
                            form.appendChild(div);
                        });

                        // Después de crear el formulario y los campos...
                        const saveButton = document.createElement('button');
                        saveButton.type = "submit";
                        saveButton.className = "btn btn-primary";
                        saveButton.textContent = "Guardar Cambios";
                        form.appendChild(saveButton);

                        editModalBody.appendChild(form);
                        editModalContent.appendChild(editModalHeader);
                        editModalContent.appendChild(editModalBody);
                        editModalDialog.appendChild(editModalContent);
                        editModalDiv.appendChild(editModalDialog);

                        // Añadir event listener al formulario
                        form.addEventListener('submit', async (event) => {
                            event.preventDefault(); // Evitar el comportamiento por defecto del formulario

                            const formData = new FormData(form); // Recoger los datos del formulario

                            // Validar que todos los campos necesarios están completos
                            const requiredFields = ['num_especie', 'nombre_comun', 'nombre_cientifico', 'familia_especie', 'grupo', 'region_pais', 'afectacion_consecuencia', 'estado_regional', 'origen', 'fuente_bibliografica'];
                            let isValid = true;

                            requiredFields.forEach(field => {
                                if (!formData.get(field)) {
                                    isValid = false;
                                    alert(`El campo ${field.replace('_', ' ')} es obligatorio.`);
                                }
                            });

                            if (!isValid) return; // Si no es válido, detener el envío

                            try {
                                const response = await fetch('php/update/updateEspecie.php', { // Cambia 'ruta/a/tu/endpoint.php' por la ruta correcta
                                    method: 'POST',
                                    body: formData
                                });

                                const responseText = await response.text(); // Obtener el texto de la respuesta
                                console.log('Respuesta del servidor:', responseText); // Mostrar la respuesta en la consola

                                if (response.ok) {
                                    alert('Cambios guardados correctamente');
                                    const modal = new bootstrap.Modal(editModalDiv);
                                    modal.hide();
                                    // Aquí podrías actualizar la lista de especies o hacer cualquier otra acción necesaria
                                } else {
                                    alert('Error al guardar los cambios');
                                }
                            } catch (error) {
                                console.error('Error:', error);
                                alert('Ocurrió un error al guardar los cambios');
                            }
                        });


                        // Añadir el modal de edición a la fila
                        row.appendChild(editModalDiv);
                    });




                    // Actualizar enlaces de paginación
                    actualizarPaginacion(pagina, totalPaginas);
                };
            }

            xhr.send();
        }


        // Función para actualizar los enlaces de paginación


        function actualizarPaginacion(paginaActual, totalPaginas) {
            const paginacionContainer = document.querySelector('.paginacion');
            paginacionContainer.innerHTML = '';

            if (paginaActual > 1) {
                paginacionContainer.innerHTML += `<a class='pag_button' href='#'
        onclick='cargarEspecies(${paginaActual - 1})'><strong>Anterior</strong></a> `;
            }

            for (let i = 1; i <= totalPaginas; i++) {
                if (i === paginaActual) {
                    paginacionContainer.innerHTML += `<strong>
        ${i}</strong> `;
                } else {
                    paginacionContainer.innerHTML += `<a href='#' onclick='cargarEspecies(${i})'>${i}</a> `;
                }
            }

            if (paginaActual < totalPaginas) {
                paginacionContainer.innerHTML += `<a class='pag_button' href='#'
            onclick='cargarEspecies(${paginaActual + 1})'><strong>Siguiente</strong></a>`;
            }
        }

        function eliminarEspecie(idEspecie) {
            if (confirm('¿Estás seguro de que deseas eliminar esta especie?')) {
                fetch('php/delete/del_especie.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${idEspecie}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Especie eliminada correctamente');
                            cargarEspecies(1);
                        } else {
                            alert('Hubo un error al intentar eliminar la especie');
                        }
                    })
                    .catch(() => alert('Error al eliminar la especie'));
            }
        }


        // Cargar la primera página al cargar la página
        document.addEventListener('DOMContentLoaded', function () {
            cargarEspecies(1);
        });
    </script>


</body>

</html>