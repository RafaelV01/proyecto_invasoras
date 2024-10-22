<html>
<header>
    <title>Iniciar Sesión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../Styles/styles_login.css">

</header>

<body>

    <div class="container-fluid cont">
        <form class="cardLogin" method="POST" action="../Services/auth.php">

            <div class="head-text">
                <h3>Usuario Privilegiado</h3>
                <p>Ingrese sus credenciales de acceso para <strong>Especies Invasoras Colombia</strong></p>
            </div>

            <div class="cont_credentials">
                <label for="userInput">Usuario: </label><br>
                <input id="userInput" name="user" type="text" placeholder="usuario"><br>


                <label for="passwordInput">Contraseña: </label><br>
                <input id="passwordInput" name="password" type="password" placeholder="usuario"><br><br>

            </div>

            <button class="botonLogear">Aceptar</button>
        </form>
    </div>
</body>

</html>