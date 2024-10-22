<?php
$url_base = "http://localhost/proyecto_invasoras/";

include('./templates/php/basedatos.php');
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="Styles/styles_home.css">
  <title>Especies Invasoras Unitrópico</title>
</head>

<body>

<nav class="navbar navbar-expand-sm navbar-dark topbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="templates/home_admin.php">
      <img src="Resources/Images/logo-unitropico.png" alt="Avatar Logo" class="topbarlogo">
    </a>
  </div>
</nav>

<!-- options-->
<div class="options-container">
  <div class="option-box" data-value="opt_lista">
    <a href="templates/lista_especies.php">
      <img class="icon" src="Resources/Icons/ic_list_op.png" alt="Icono 1">
      <p class="option-name">LISTA DE INVASORAS</p>
    </a>
  </div>
  
  <div class="option-box" data-value="opt_reportar">
    <a href="templates/reportar.php">
      <img src="Resources/Icons/ic_report_op.png" alt="Icono 2" class="icon">
      <p class="option-name">REPORTAR AVISTAMIENTO</p>
    </a>
  </div>


  <div class="option-box" data-value="opt_mapa_reportes">
    <a href="templates/mapa.php">
      <img src="Resources/Icons/ic_map_rep_op.png" alt="Icono 4" class="icon">
      <p class="option-name">REPORTES EN MAPA</p>
    </a>

    

    
</div>

<div class="option-box" data-value="opt_estadistica">
    <a href="templates/estadisticas.php">
      <img src="Resources/Icons/ic_estadist_op.png" alt="Icono 5" class="icon">
      <p class="option-name">ESTADISTICAS</p>
    </a>
  </div>

</body>

</html>