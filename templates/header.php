<header>
    <?php 
    session_start();
    if (isset($_SESSION["user_name"])) {
        echo "\t\t<h1>Bienvenido, ".$_SESSION['user_name']."</h1>\n"; 
    }
    ?>
    <a href="index.php"><img href="" class="logo" src="/assets/images/logo2.png" alt="encuesta2"></a>
    <nav>
        <?php
            if (isset($_SESSION["user_name"])) {
                echo "\t\t<a class='buttonLeft' href='dashboard.php'>PANEL DE CONTROL</a>\n";
                echo "\t\t<a class='buttonRight' href='logout.php'>CERRAR SESIÓN</a>\n";
            }
            elseif ($_SERVER['PHP_SELF'] == "/pages/login.php") {
                echo "\t\t<a class='buttonLeft' href='index.php'>INICIO</a>\n";
                echo "\t\t<a class='buttonRight' href='register.php'>REGISTRARSE</a>\n";
            } else {
                echo "\t\t<a class='buttonLeft' href='login.php'>INICIAR SESIÓN</a>\n";
                echo "\t\t<a class='buttonRight' href='register.php'>REGISTRARSE</a>\n";
            }
        ?>
    </nav> 
</header>