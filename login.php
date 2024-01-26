<?php
    session_start();
    if (isset($_SESSION["user_name"])) { header("Location: dashboard.php"); }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="index, follow">
        <meta name="keywords" content="encuesta2, votación en línea, votación, encuestas, elecciones, privacidad, seguridad">
        <meta name="description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
        <meta property="og:title" content="encuesta2">
        <meta property="og:description" content="Plataforma de votación en línea comprometida con la privacidad y seguridad de los usuarios. Regístrate ahora y participa en encuestas y elecciones de manera segura.">
        <meta property="og:image" content="assets/images/logo2.png">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="author" content="Isaac Furió, Lina Ramírez, Eric Escrich i Claudia Moyano">
        <link rel="icon" href="./assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="./assets/styles/styles.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/login.js"></script>
        <title>Inicia sesión  —  ENCUESTA2</title>
    </head>

    <body class="login-body">
        INICIAR SESIÓN</h1>    
        
        <?php include("./templates/header.php"); ?>

        <main class="main-content">
            <?php
                if (isset($_POST["email"]) && isset($_POST["password"])) {
                    try {
                        $hostname = "localhost";
                        $dbname = "encuesta2";
                        $username = "encuesta2";
                        $pw = "naranjasV3rdes#";
                        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No se pudo conectar a la base de datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);} catch (PDOException $e) {
                        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                        exit;
                    }
                    $userEmail = $_POST['email'];
                    $userpass = $_POST['password'];
                    $query = $pdo->prepare("SELECT user_name, mail FROM User WHERE mail = ? AND password=SHA2(?, 512)");
                    $query->bindParam(1, $userEmail, PDO::PARAM_STR);
                    $query->bindParam(2, $userpass, PDO::PARAM_STR);
                    $query->execute();

                    // Comprovem errors:
                    $e = $query->errorInfo();
                    if ($e[0] != '00000') {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No se pudo acceder a los datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $user_name . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "\nPDO::errorInfo():\n";
                        echo "  <script>
                                    localStorage.setItem('error', 'PDO::errorInfo()');
                                    window.location.href = 'login.php';
                                </script>";
                        die("Error accediendo a los datos: " . $e[2]);
                    }

                    $row = $query->fetch();
                    if (!$row) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: Credenciales inválidas. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $user_name . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'Credenciales inválidas.');
                                    window.location.href = 'login.php';
                                </script>";
                    } else {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Acción: " . $user_name . " ha iniciado sesión. Hora de la acción: " . $dataSinCambiar . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        $_SESSION["user_name"] = $row["user_name"];
                        $_SESSION["mail"] = $row["mail"];
                        echo "  <script>
                                    localStorage.setItem('success', '¡Has iniciado sesión! Hola, " . $_SESSION["user_name"] . ".');
                                    window.location.href = 'dashboard.php';
                                </script>";
                    }}
            ?>
            
            <form action="login.php" method="post">
                <label for="email">Correo electrónico</label>
                <input class="field" type="email" name="email" autocomplete="off" required>
                <label for="password">Contraseña</label>
                <input class="field" type="password" name="password" required>
                <input type="submit" value="ENTRAR">
            </form>
        </main>
        <ul id="notification-container"></ul>
        <?php include("./templates/footer.php"); ?>
    </body>
</html>