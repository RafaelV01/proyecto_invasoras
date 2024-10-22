<?php
include_once("php/basedatos.php");
include_once("php/dataBase_utils.php");

// Obtener todos los datos de las especies y grupos
$list_specie = getAllData($conexion, 'animal');
$list_groups_specie = getAllData($conexion, 'grupo_especie');



// Manejo de la búsqueda de especies
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Manejo de la búsqueda
    $searchQuery = !empty($_POST['busqueda']) ? $_POST['busqueda'] : '';

    // Manejo del filtro
    $selectedGroup = !empty($_POST['grupo']) ? $_POST['grupo'] : '';

    // Si el campo de búsqueda está vacío, reiniciar la lista de especies
    if (empty($searchQuery) && empty($selectedGroup)) {
        $list_specie = getAllData($conexion, 'animal'); // Volver a cargar todos los registros
    } else {
        // Filtrar las especies si hay término de búsqueda o grupo seleccionado
        $list_specie = array_filter($list_specie, function ($especie) use ($searchQuery, $selectedGroup) {
            $matchSearch = stripos($especie['nombre_comun'], $searchQuery) !== false ||
                stripos($especie['familia_especie'], $searchQuery) !== false;
            $matchGroup = empty($selectedGroup) || $especie['grupo'] == $selectedGroup; // Asumiendo que 'grupo' es una columna en 'animal'
            return $matchSearch && $matchGroup;
        });
    }
}
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../Styles/styles_rep.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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

    <div class="options-main">
        <div class="container-s">
            <div class="option-box">
                <div class="text-block text-center mb-3">
                    <label for="">Reportar avistamiento de <br><strong>especie</strong></label>
                </div>

                <div class="content_options">

                    <form id="reportForm">
                        <label for="select_especie" class="labelTitle">Selecciona especie a reportar:</label>

                        <div id="contain_select_especie" class="d-flex mb-3">

                            <button id="showSearchAndListBtn" class="btn btn-secondary flex-fill me-2"
                                data-bs-toggle="modal" data-bs-target="#speciesModal">Seleccionar de lista</button>
                            <button id="select_especie_2" class="btn btn-secondary flex-fill" data-bs-toggle="modal"
                                data-bs-target="#imageModal" style=";">Subir Imagen</button>

                        </div>
                        <h6 id="errorEspecieMsj"></h6>

                        <input type="hidden" id="field_num_especie_select" name="num_especie" required>


                        <div class="text-block mb-3">
                            <label for="fecha_avistamiento">Fecha de avistamiento: </label>
                            <input id="fecha_avistamiento" name="fecha_avistamiento" type="date"
                                placeholder="XX/XX/XXXX" required>
                        </div>

                        <div class="text-block mb-3">
                            <label for="cantidad_especie">Cantidad: (Hasta 20)</label>
                            <input id="cantidad_especie" name="cantidad_especie" type="number"
                                placeholder="Número de individuos" min="0" max="20" required>
                        </div>

                        <div id="containUbiSel" class="text-block mb-3">
                            <label for="useGPSBtn" class="form-label">Ubicación: </label>
                            <div class="input-group w-100">
                                <button
                                    class="btn btn-outline-secondary btn-custom bg-light w-50 text-secondary  btn-gps"
                                    type="button" id="selectLocationBtn">Seleccionar Ubicación</button>
                                <button class="btn btn-outline-secondary btn-custom bg-light w-50 text-secondary"
                                    type="button" id="useGPSBtn">Usar mi posición GPS</button>
                            </div>
                            <h5 id="msj_ubi_result"></h5>
                        </div>

                        <input type="hidden" id="latitudSel" name="ubiLat" required>
                        <input type="hidden" id="longitudSel" name="ubiLng" required>

                        <div class="text-block mb-3">
                            <label for="comentarios">Descripción de entorno/sitio: (Max 100 Caracteres)</label><br>
                            <textarea id="comentarios" name="entorno" class="w-100"
                                placeholder="Escribe los detalles del sitio por ejemplo: lote baldío, potrero, área urbana, área rural, etc."
                                required maxlength="100"></textarea>
                        </div>


                        <h6 id="error"></h6>


                        <!-- Modal para subir imágenes -->
                        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="imageModalLabel">Selecciona imágenes</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="file" id="image_input" accept="image/*" multiple>
                                        <div class="text-center mt-3" id="image_warning"
                                            style="display: none; color: red;">Solo puedes
                                            seleccionar hasta 4 imágenes.</div>
                                        <div class="mt-3" id="image_preview_modal" style="display: none;"></div>
                                        <!-- Contenedor de vista previa en el modal -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cerrar</button>
                                        <button type="button" class="btn btn-primary"
                                            id="confirm_images">Confirmar</button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Contenedor de las imágenes -->
                        <div class="img-container" id="image_preview_container"></div>

                        <div id="selectedSpeciesText" class="mb-3" style="display: none;">
                            <strong>Especie seleccionada:</strong> <span id="selectedSpeciesName"></span> <span
                                id="selectedSpeciesId"></span><br>

                                

                
                    



                                
                 <div class="cardcontain">
                 <div id="selectedSpeciesContainer" style="display: none;">
    <img id="selectedSpeciesImage" src="" alt="Imagen seleccionada" />
    <strong id="selectedSpeciesName"></strong>
    <span id="selectedSpeciesId"></span>
</div>
</div>


                        </div>
                    </form>
                </div>

            </div>

            <button type="button" class="report-btn w-100" id="reportBtn">Reportar avistamiento de esta
                especie</button>
        </div>

    </div>
    </div>
    </div>
    </form>
    </div>
    </div>
    </div>
    </div>






    </form>
    </div>
    </div>
    </div>
    </div>

    <script>
        document.getElementById('reportBtn').onclick = function () {
            // Obtiene los valores de los campos del formulario
            const especieSeleccionada = document.getElementById('field_num_especie_select');

            const imagenSubida = document.getElementById('image_input')
            
            const fechaAvistamiento = document.getElementById('fecha_avistamiento');
            const cantidadEspecie = document.getElementById('cantidad_especie');
            const selLatitudUser = document.getElementById('latitudSel');
            const selLongitudUser = document.getElementById('longitudSel');
            const afectacion = document.getElementById('comentarios');

            const errorEspecieSel = document.getElementById('errorEspecieMsj');
            const errorUbicacion = document.getElementById('msj_ubi_result');
            const errorGeneral = document.getElementById('error');
            const imgSrc = this.getAttribute('data-img');


            // Resetea mensajes de error
            errorEspecieSel.innerText = '';
            errorGeneral.innerText = '';
            errorUbicacion.innerText = '';

            // Verifica si una especie ha sido seleccionada o una imagen ha sido subida
            if (especieSeleccionada.value === '' && (!imagenSubida || imagenSubida.files.length === 0)) {
                document.getElementById('showSearchAndListBtn').focus();
                errorEspecieSel.innerText = 'Debe seleccionar una especie o subir una imagen!';
                return;
            }

            // Verifica que todos los campos requeridos están llenos
            if (!fechaAvistamiento.value || !cantidadEspecie.value || !afectacion.value) {
                cantidadEspecie.focus();
                errorGeneral.innerText = 'Por favor, completa todos los campos requeridos.';
                return;
            }

            // Verifica si la ubicación ha sido seleccionada
            if (!selLatitudUser.value || !selLongitudUser.value) {
                document.getElementById('containUbiSel').focus();
                errorUbicacion.innerText = 'Debe seleccionar una ubicación!';
                return;
            }

            // Crea un objeto FormData para enviar los datos
            const formData = new FormData(document.getElementById('reportForm'));

            // Recorrer los archivos y agregarlos al FormData
            for (let i = 0; i < imagenSubida.files.length; i++) {
                formData.append('imagen_subida[]', imagenSubida.files[i]);
            }

            // Envía los datos a PHP usando fetch
            fetch('php/regist/save_report.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta de la red');
                    }
                    return response.text();
                })
                .then(data => {
                    // Limpia el formulario
                    document.getElementById('reportForm').reset();

                    // Muestra un mensaje de éxito al usuario
                    alert('Reporte guardado correctamente.');

                    // Redirecciona al usuario a la página principal
                    window.location.href = "../";
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al reportar el avistamiento.');
                });
        };




    </script>









    <div class="text-block mb-3">
        <label for="selectedSpeciesDetails"></label>
        <div id="selectedSpeciesDetails" style="display: none;">
            <strong></strong> <span id="selectedSpeciesOrigin"></span><br>
            <strong></strong> <span id="selectedSpeciesFamily"></span>
            <strong></strong> <span id="selectedSpeciesImage"></span>
        </div>
    </div>

    <!-- Modal para la barra de búsqueda y lista de especies -->
    <div class="modal fade" id="speciesModal" tabindex="-1" aria-labelledby="speciesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 90%;">

            <div class="modal-content" style="max-width: 100%;">

                <div class="modal-header">
                    <h5 class="modal-title" id="speciesModalLabel">Buscar Especie Invasora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="search-box" id="searchBox">
                        <form id="searcherInfo" method="POST" class="searcher">
                            <fieldset>

                                <input type="text" id="searchInput" name="busqueda" placeholder="Buscar"
                                    class="form-control" />

                                <select name="grupo" id="groupSelect" class="form-select mt-2">
                                    <option value="">Selecciona un grupo</option>
                                    <?php foreach ($list_groups_specie as $group): ?>
                                        <option value="<?php echo $group['cod_grupo']; ?>">
                                            <?php echo $group['nombre']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit" class="btn btn-primary mt-2">Buscar</button>
                            </fieldset>
                        </form>
                    </div>

                    <div id="list" class="center-objects">
                        <?php

                        if (!empty($list_specie)) {
                            foreach ($list_specie as $especie) {
                                $num = $especie['num_especie'];
                                $nombreComun = $especie['nombre_comun'];
                                $familiaEspecie = $especie['familia_especie'];
                                $origenEspecie = $especie['origen'];


                                ?>
                                <div class="double-column">

                                    <div class="cardcontain">
                                        <img src="php/getImgListEspecie.php?id=<?php echo $num; ?>"
                                            alt="Imagen de la especie invasora" class="rounded imageEspecie">
                                    </div>

                                    <div class="cardcontain alin-start">
                                        <strong id="info_nombre_comun" class="center"><?php echo $nombreComun; ?></strong>
                                        <strong>Especie: </strong> <span id="info_especie"><?php echo $familiaEspecie; ?></span>
                                        <strong>Origen: </strong> <span id="info_origen"><?php echo $origenEspecie; ?></span>
                                    </div>

                                    <div class="cardcontain button_center">
                                        <button type="button" class="btn btn-sm select-species" id="boton_select_especie"
                                            data-species-name="<?php echo $nombreComun; ?>"
                                            data-species-num="<?php echo $num; ?>"
                                            data-img="php/getImgListEspecie.php?id=<?php echo $num; ?>"
                                            >Seleccionar</button>
                                            
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="container cardcontain">
                                <div class="img-container img-container mb-3">
                                    <p>No se encontraron especies.</p>
                                  </div>
                              </div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- busqueda especie -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const speciesList = document.getElementById('list');
            const speciesCards = document.querySelectorAll('.cardcontain');

            // Búsqueda en tiempo real
            document.getElementById('searchInput').addEventListener('input', function () {
                const searchValue = this.value.toLowerCase();

                speciesCards.forEach(card => {
                    const commonName = card.querySelector('#info_nombre_comun').textContent.toLowerCase();
                    const speciesName = card.querySelector('#info_especie').textContent.toLowerCase();

                    if (commonName.includes(searchValue) || speciesName.includes(searchValue)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            // Enviar formulario al presionar Enter
            document.getElementById('searchInput').addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    const searchValue = this.value.trim(); // Obtener el valor del campo y eliminar espacios
                    if (searchValue === '') {
                        // Si el campo está vacío, reiniciar la lista de especies sin enviar el formulario
                        card.style.display = ''; // Recargar la página para mostrar todos los registros
                    } else {
                        document.getElementById('btnHideFormBusqueda').click();
                    }
                }
            });









            // Selección de especie
            speciesList.addEventListener('click', function (e) {
                if (e.target.classList.contains('select-species')) {
                    const specieId = e.target.getAttribute('data-species-num');
                    const speciesName = e.target.getAttribute('data-species-name');
                    const imgSrc = e.target.getAttribute('data-img');

                    // Mostrar el nombre de la especie seleccionada
                    document.getElementById('selectedSpeciesText').style.display = 'block';
                    document.getElementById('selectedSpeciesName').textContent = speciesName;
                    

                    document.getElementById('selectedSpeciesId').textContent = '[' + specieId + ']';

                    document.getElementById('field_num_especie_select').value = specieId;

                        // Mostrar la imagen de la especie seleccionada
            const selectedSpeciesImage = document.getElementById('selectedSpeciesImage');
            const selectedSpeciesContainer = document.getElementById('selectedSpeciesContainer');

            // Verificar que el contenedor y la imagen existen
            if (selectedSpeciesImage && selectedSpeciesContainer) {
                selectedSpeciesImage.src = imgSrc; // Establecer la URL de la imagen
                selectedSpeciesContainer.style.display = 'block'; // Mostrar el contenedor
            } else {
                console.error('No se encontró el contenedor o la imagen.'); // Mensaje de error
            }
                    // Cerrar el modal de especies
                    const speciesModal = bootstrap.Modal.getInstance(document.getElementById('speciesModal'));
                    speciesModal.hide();
                }
            });
        });
    </script>



    <!-- seleccionar imagen -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const maxImages = 4; // Número máximo de imágenes
            const container = document.getElementById('image_preview_container');
            const modalPreviewContainer = document.getElementById('image_preview_modal');

            // Funcionalidad para seleccionar imagen y mostrarla
            document.getElementById('select_especie_2').addEventListener('click', function () {
                // Resetear el input al abrir el modal
                document.getElementById('image_input').value = '';
                document.getElementById('image_warning').style.display = 'none';
                modalPreviewContainer.innerHTML = ''; // Limpiar contenedor de vista previa del modal
                modalPreviewContainer.style.display = 'none'; // Ocultar contenedor de vista previa del modal
            });

            // Funcionalidad para mostrar la vista previa de las imágenes en el modal
            document.getElementById('image_input').addEventListener('change', function (event) {
                const files = event.target.files; // Obtener los archivos seleccionados
                const currentImages = modalPreviewContainer.querySelectorAll('.full-width-image');
                const currentImageCount = currentImages.length; // Contar imágenes actuales en el modal

                if (currentImageCount + files.length > maxImages) {
                    document.getElementById('image_warning').style.display = 'block'; // Mostrar advertencia
                    return; // Salir si se excede el límite
                } else {
                    document.getElementById('image_warning').style.display = 'none'; // Ocultar advertencia
                    modalPreviewContainer.style.display = 'flex'; // Mostrar contenedor de vista previa
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const imageWrapper = document.createElement('div');
                            imageWrapper.classList.add('image-wrapper');

                            const imgElement = document.createElement('img');
                            imgElement.src = e.target.result; // Asignar la imagen seleccionada
                            imgElement.className = 'full-width-image img-fluid rounded';

                            const closeButton = document.createElement('button');
                            closeButton.className = 'close-btn';
                            closeButton.innerText = 'x';
                            closeButton.onclick = function () {
                                imageWrapper.remove(); // Eliminar la imagen del modal
                                updateWarning(); // Actualizar advertencia después de eliminar
                            };

                            // Agregar elementos al contenedor de vista previa del modal
                            imageWrapper.appendChild(imgElement);
                            imageWrapper.appendChild(closeButton);
                            modalPreviewContainer.appendChild(imageWrapper);
                        }
                        reader.readAsDataURL(file); // Leer el archivo como una URL de datos
                    }
                }
            });

            // Funcionalidad para confirmar las imágenes seleccionadas
            document.getElementById('confirm_images').addEventListener('click', function () {
                container.innerHTML = ''; // Limpiar contenido previo
                const modalImages = modalPreviewContainer.querySelectorAll('.image-wrapper'); // Selecciona los contenedores completos

                modalImages.forEach(imageWrapper => {
                    // Clonar el contenedor completo con la imagen y el botón de cerrar
                    const clonedWrapper = imageWrapper.cloneNode(true);

                    // Actualizar el botón de cerrar para que funcione en la vista previa
                    const closeButton = clonedWrapper.querySelector('.close-btn');
                    closeButton.onclick = function () {
                        clonedWrapper.remove(); // Eliminar la imagen de la vista previa principal
                        updateWarning(); // Actualizar advertencia después de eliminar
                    };

                    container.appendChild(clonedWrapper);
                });

                // Mostrar el contenedor de imágenes en la página principal
                container.style.display = modalImages.length > 0 ? 'flex' : 'none';
                container.style.flexWrap = 'wrap'; // Asegurar que las imágenes puedan ir a la siguiente fila si es necesario
                container.style.gap = '15px'; // Espacio entre las imágenes
                container.style.justifyContent = 'flex-start'; // Alinear las imágenes hacia el inicio

                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('imageModal'));
                modal.hide();
            });

            // Función para actualizar la advertencia si se eliminan imágenes en el modal
            function updateWarning() {
                const currentImages = modalPreviewContainer.querySelectorAll('.full-width-image');
                const currentImageCount = currentImages.length;
                // Actualiza el mensaje de advertencia correctamente
                document.getElementById('image_warning').style.display = currentImageCount >= maxImages ? 'block' : 'none';
            }
        });

    </script>







    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('image_preview_container');
            const modalPreviewContainer = document.getElementById('image_preview_modal');


            // Funcionalidad para seleccionar ubicación en el mapa

            document.getElementById('selectLocationBtn').addEventListener('click', function () {
                // Crear un modal solo si no existe

                let modalElement = document.getElementById('mapModalElement');

                if (!modalElement) {
                    modalElement = document.createElement('div');

                    modalElement.id = 'mapModalElement';

                    modalElement.innerHTML = `
            <div class="modal" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="mapModalLabel">Selecciona Ubicación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="map"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" id="confirmLocationBtn">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
                    document.body.appendChild(modalElement);
                }

                const modal = new bootstrap.Modal(document.getElementById('mapModal'));
                modal.show();

                // Inicializar el mapa
                const map = L.map('map').setView([4.570868, -74.297333], 5); // Coordenadas iniciales (Colombia)

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);


                // Variable para guardar la ubicación seleccionada
                let selectedLocation;

                // Agregar un marcador en el mapa
                map.on('click', function (e) {
                    // Limpiar cualquier marcador previo
                    if (selectedLocation) {
                        map.removeLayer(selectedLocation);
                    }

                    // Crear un nuevo marcador en la ubicación seleccionada
                    selectedLocation = L.marker(e.latlng).addTo(map);

                    // Guardar las coordenadas seleccionadas en el botón de confirmar
                    document.getElementById('confirmLocationBtn').onclick = function () {

                        if (selectedLocation) {

                            document.getElementById('latitudSel').value = e.latlng.lat;
                            document.getElementById('longitudSel').value = e.latlng.lng;

                            console.log(`Ubicación seleccionada: ${e.latlng.lat}, ${e.latlng.lng}`);

                            document.getElementById('msj_ubi_result').innerText = 'Ubicación seleccionada correctamente';



                        } else {
                            // Si no hay coordenadas, mostrar un mensaje de error
                            document.getElementById('msj_ubi_result').innerText = 'No se pudo obtener la ubicación. Inténtalo de nuevo.';
                            console.error('Error: No se obtuvo una ubicación válida.');
                        }

                        modal.hide(); // Cerrar el modal
                    };

                });

            });

            // Funcionalidad para usar la posición GPS del usuario
            document.getElementById('useGPSBtn').addEventListener('click', function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;

                        console.log('Ubicación actual gps: Latitud: ' + lat + ', Longitud: ' + lon);

                        document.getElementById('latitudSel').value = lat;
                        document.getElementById('longitudSel').value = lon;

                        document.getElementById('msj_ubi_result').innerText = 'Ubicación seleccionada correctamente';

                    }, function () {
                        document.getElementById('msj_ubi_result').innerText = 'No se pudo obtener ubicación';

                    });
                } else {
                    document.getElementById('msj_ubi_result').innerText = 'Geolocalización no es soportada por este navegador.';
                }
            });
        });
    </script>


    <!-- Obtener fecha y inicializar el input fecha reporte -->
    <script>
        // Obtener la fecha actual
        const today = new Date();

        // Formatear
        const formattedDate = today.toISOString().split('T')[0];

        // Asignar 
        document.getElementById('fecha_avistamiento').value = formattedDate;
    </script>
</body>

</html>