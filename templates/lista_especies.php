<?php
include_once("php/basedatos.php");
include_once("php/dataBase_utils.php");

// Manejo de la búsqueda
$searchQuery = '';
$selectedGroup = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $searchQuery = !empty($_POST['busqueda']) ? $_POST['busqueda'] : '';
    $selectedGroup = !empty($_POST['grupo']) ? $_POST['grupo'] : '';
}

// Cargar grupos de especies
$list_groups_specie = getAllData($conexion, 'grupo_especie');

// Número de registros por página
$registros_por_pagina = 12;

// Verificar página actual
$pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Calcular el inicio para la consulta
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

// Inicializar la consulta
$sql = "SELECT * FROM animal WHERE 1=1";

// Agregar condiciones de búsqueda y filtro
if (!empty($searchQuery)) {
    $sql .= " AND (nombre_comun LIKE '%$searchQuery%' OR familia_especie LIKE '%$searchQuery%')";
}

if (!empty($selectedGroup)) {
    $sql .= " AND grupo = '$selectedGroup'";
}

// Ejecutar la consulta para obtener el número total de registros
$resultado_total = $conexion->query($sql);
$total_registros = $resultado_total->num_rows;

// Calcular el número total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Limitar los resultados según la paginación
$sql .= " LIMIT $inicio, $registros_por_pagina";

// Ejecutar la consulta
$list_specie = $conexion->query($sql);






?>





<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Services/Filter_List/getDataFilter.js"></script>
    <link rel="stylesheet" href="../Styles/styles_list.css">
    <title>Especies Invasoras Unitrópico</title>
    <style>
        .highlight {
            background-color: yellow;
            /* Resaltado en amarillo */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-sm navbar-dark topbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../Resources/Images/logo-unitropico.png" alt="Logo Unitrópico" class="topbarlogo">
            </a>
        </div>
    </nav>





    <div class="search-box">
        <form id="searcherInfo" method="POST" class="searcher">
            <input type="text" class="input-buscador" id="searchInput" name="busqueda" placeholder="Buscar" />
            <button id="filtroBtn" type="button" class="filter-button">
                <img src="../Resources/Icons/icon_filtro.png" alt="Filtrar" class="filter-icon" />
            </button>
            <button style="display:none;" id="btnHideFormBusqueda" class="btn bg-light" type="submit">Enviar</button>
        </form>
    </div>

    <div id="filtroModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Clasificar Especies</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="filtroForm" method="POST">
                        <label for="categoria">Grupo:</label>
                        <select id="categoria" name="grupo">
                            <option value="">Todos</option>
                            <?php
                            if (!empty($list_groups_specie)) {
                                foreach ($list_groups_specie as $group) {
                                    ?>
                                    <option value="<?php echo $group['cod_grupo']; ?>" <?php echo ($selectedGroup == $group['cod_grupo']) ? 'selected' : ''; ?>>
                                        <?php echo $group['nombre']; ?>
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <br><br>

                        <div class="modal-footer">
                            <button class="btn bg-light" type="submit" data-bs-dismiss="modal">Aplicar Filtro</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php
        // mostrar enlaces de paginacion
        echo "<div>";
        if ($pagina_actual > 1) {
            echo "<a class='pag_button' href='?pagina=" . ($pagina_actual - 1) . "'><strong>Anterior</strong></a> ";
        }
        for ($i = 1; $i <= $total_paginas; $i++) {
            if ($i == $pagina_actual) {
                echo "<strong>$i</strong> ";
            } else {
                echo "<a href='?pagina=$i'>$i</a> ";
            }
        }
        if ($pagina_actual < $total_paginas) {
            echo "<a class='pag_button' href='?pagina=" . ($pagina_actual + 1) . "'><strong>Siguiente</strong></a>";
        }
        echo "</div>";
        ?>
    </div>



    <div id="list" class="container">
        <div class="row justify-content-center">
            <?php
            if (!empty($list_specie)) {
                foreach ($list_specie as $especie) {
                    $id = $especie['num_especie'];
                    $nombreComun = $especie['nombre_comun'];
                    $familiaEspecie = $especie['familia_especie'];
                    $origen = $especie['origen'];
                    $fuente = $especie['fuente_bibliografica'];
                    $afectacion = $especie['afectacion_consecuencia'];
                    $region = $especie['region_pais'];
                    $estado = $especie['estado_regional'];





                    ?>
                    <div class="col-md-4 mb-4 d-flex justify-content-center">
                        <div class="card" style="width: 90%;">
                            <img src="php/getImgListEspecie.php?id=<?php echo $id; ?>" alt="Imagen de la especie invasora"
                                class="card-img-top imageEspecie">
                            <div class="card-body text-center">
                                <strong><?php echo $nombreComun; ?></strong><br>
                                <button class="btn btn-primary open-modal btn-inf"
                                    data-nombre="<?php echo htmlspecialchars($nombreComun); ?>"
                                    data-especie="<?php echo htmlspecialchars($familiaEspecie); ?>"
                                    data-origen="<?php echo htmlspecialchars($origen); ?>"
                                    data-fuente="<?php echo htmlspecialchars($fuente); ?>"
                                    data-afectacion="<?php echo htmlspecialchars($afectacion); ?>"
                                    data-region="<?php echo htmlspecialchars($region); ?>"
                                    data-estado="<?php echo htmlspecialchars($estado); ?>"
                                    data-img="php/getImgListEspecie.php?id=<?php echo $id; ?>"
                                    aria-label="Ver más información sobre <?php echo htmlspecialchars($nombreComun); ?>">
                                    Ver más información
                                </button>


                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No se encontraron resultados.</p>"; // Mensaje si no hay especies
            }


            ?>


        </div>
    </div>
</body>

</html>








<div class="modal fade" id="specieModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Información de la especie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="php/getImgListEspecie.php?id=<?php echo $id; ?>" alt="Imagen de la especie invasora"
                    class="img-fluid rounded mb-3" style="max-height: 300px; object-fit: cover;">
                <p><strong>Nombre común:</strong> <span id="modalNombre"></span></p>
                <p><strong>Especie:</strong> <span id="modalEspecie"></span></p>
                <p><strong>Origen:</strong> <span id="modalOrigen"></span></p>
                <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                <p><strong>Región:</strong> <span id="modalRegion"></span></p>
                <p><strong>Afectación:</strong> <span id="modalAfectacion"></span></p>
                <p><strong>Fuente:</strong> <span id="modalFuente"></span></p>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Selecciona todos los botones que abren el modal
        const buttons = document.querySelectorAll('.open-modal');
        buttons.forEach(button => {


            button.addEventListener('click', function () {
                // Obtén los datos del botón seleccionado
                const img = this.getAttribute('data-img')
                const nombre = this.getAttribute('data-nombre');
                const especie = this.getAttribute('data-especie');
                const origen = this.getAttribute('data-origen');
                const fuente = this.getAttribute('data-fuente');
                const afectacion = this.getAttribute('data-afectacion');
                const region = this.getAttribute('data-region');
                const estado = this.getAttribute('data-estado');

                // Llena el modal con la información
                document.querySelector('.modal-body img').src = img;
                document.getElementById('modalNombre').textContent = nombre;
                document.getElementById('modalEspecie').textContent = especie;
                document.getElementById('modalOrigen').textContent = origen;
                document.getElementById('modalFuente').textContent = fuente;
                document.getElementById('modalAfectacion').textContent = afectacion;
                document.getElementById('modalRegion').textContent = region;
                document.getElementById('modalEstado').textContent = estado;

                // Muestra el modal
                var modal = new bootstrap.Modal(document.getElementById('specieModal'));
                modal.show();
            });
        });




    });
</script>





















</div>

<div class="container">
    <?php
    // mostrar enlaces de paginacion
    echo "<div>";
    if ($pagina_actual > 1) {
        echo "<a class='pag_button' href='?pagina=" . ($pagina_actual - 1) . "'><strong>Anterior</strong></a> ";
    }
    for ($i = 1; $i <= $total_paginas; $i++) {
        if ($i == $pagina_actual) {
            echo "<strong>$i</strong> ";
        } else {
            echo "<a href='?pagina=$i'>$i</a> ";
        }
    }
    if ($pagina_actual < $total_paginas) {
        echo "<a class='pag_button' href='?pagina=" . ($pagina_actual + 1) . "'><strong>Siguiente</strong></a>";
    }
    echo "</div>";
    ?>
</div>

<script src="../Services/Filter_List/getDataFilter.js"></script>

<script>
    // Enviar formulario al presionar Enter
    document.getElementById('searchInput').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const searchValue = this.value.trim(); // Obtener el valor del campo y eliminar espacios
            if (searchValue === '') {
                // Si el campo está vacío, reiniciar la lista de especies sin enviar el formulario
                window.location.reload(); // Recargar la página para mostrar todos los registros
            } else {
                document.getElementById('btnHideFormBusqueda').click();
            }
        }
    });

    // Ejecutar la consulta
    $list_specie = $conexion -> query($sql);

    // Mostrar especies o cargar la página de manera normal si no se encuentran resultados
    if ($list_specie -> num_rows > 0) {
        foreach($list_specie as $especie) {
            // (Código para mostrar las especies)
        }
    } else {
        // Si no hay resultados, redirigir a la misma página sin búsqueda
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }


    // Ejecutar la consulta
    $list_specie = $conexion -> query($sql);

    // Mostrar especies o cargar la página de manera normal si no se encuentran resultados
    if ($list_specie -> num_rows > 0) {
        foreach($list_specie as $especie) {
            // (Código para mostrar las especies)
        }
    } else {
        // Si no hay resultados, redirigir a la misma página sin búsqueda
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }


    // Búsqueda en tiempo real
    document.getElementById('searchInput').addEventListener('input', function () {
        const searchValue = this.value.toLowerCase();
        const speciesCards = document.querySelectorAll('.cardcontain');

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
</script>

<script>
    // Mostrar el modal al hacer clic en el botón de filtro
    document.getElementById('filtroBtn').addEventListener('click', function () {
        document.getElementById('filtroModal').style.display = 'block';
    });

    // Cerrar el modal al hacer clic en la "x"
    document.querySelector('.close').addEventListener('click', function () {
        document.getElementById('filtroModal').style.display = 'none';
    });

    // Cerrar el modal al hacer clic fuera de él
    window.addEventListener('click', function (event) {
        if (event.target == document.getElementById('filtroModal')) {
            document.getElementById('filtroModal').style.display = 'none';
        }
    });
</script>

<script>
    // Mostrar el modal al hacer clic en el botón de filtro
    document.getElementById('filtroBtn').addEventListener('click', function () {
        document.getElementById('filtroModal').style.display = 'block';
    });

    // Cerrar el modal al hacer clic en la "x"
    document.querySelector('.close').addEventListener('click', function () {
        document.getElementById('filtroModal').style.display = 'none';
    });

    // Cerrar el modal al hacer clic fuera de él
    window.addEventListener('click', function (event) {
        if (event.target == document.getElementById('filtroModal')) {
            document.getElementById('filtroModal').style.display = 'none';
        }
    });
</script>

<!-- Botón para subir al inicio -->
<button id="scrollToTop" class="scroll-to-top" style="display: none;">↑</button>

<script>
    // Mostrar/ocultar el botón al hacer scroll
    window.onscroll = function () {
        const button = document.getElementById("scrollToTop");
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            button.style.display = "block"; // Mostrar el botón
        } else {
            button.style.display = "none"; // Ocultar el botón
        }
    };

    // Funcionalidad para scroll hacia arriba
    document.getElementById("scrollToTop").onclick = function () {
        window.scrollTo({
            top: 0,
            behavior: "smooth" // Desplazamiento suave
        });
    };

</script>

</body>

</html>