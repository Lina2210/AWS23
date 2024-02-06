<?php
    session_start();
    if (isset($_SESSION["user_name"])) { header("Location: dashboard.php"); }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="./assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="./assets/styles/styles.css?no-cache=<?php echo time(); ?>">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/login.js"></script>
        <title>Iniciar sesión — encuesta2</title>
    </head>
    <body class="login-body">
        <h1>INICIAR SESIÓN</h1>    
        <?php include("./templates/header.php"); ?>
        <main class="main-content">
            <?php
                if (isset($_POST["email"]) && isset($_POST["password"])) {
                    try {
                        require_once("./data/dbAccess.php");
                        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
                        
                    } catch (PDOException $e) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No se pudo conectar a la base de datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                        exit;
                    }
                    $userEmail = $_POST['email'];
                    $userpass = $_POST['password'];
                    $query = $pdo->prepare("SELECT user_id, user_name, mail, email_token, terms_of_use FROM User WHERE mail = ? AND password=SHA2(?, 512)");
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
                                    localStorage.setItem('error', 'Credenciales Invalidas.');
                                    window.location.href = 'login.php';
                                </script>";
                    } else {
                        if ($row) {
                            if ($row["email_token"] != "ok") {
                                // cuenta no validada por email
                                echo "  <script>
                                            localStorage.setItem('error', 'Debes validar tu cuenta. Comprueba tu email.');
                                            window.location.href = 'login.php';
                                        </script>";
                            } elseif (!$row["terms_of_use"]) {
                                // mostrar bocadillo de aceptacion de terminos de uso
                                include("./templates/terms_of_use.php");
                                $user_id = htmlspecialchars($row['user_id']);
                                $user_name = htmlspecialchars($row['user_name']);
                                $mail = htmlspecialchars($row['mail']);
                                echo "  <script> checkTerms('$user_name', '$mail', '$user_id'); </script>";
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
                                $_SESSION["user_id"] = $row["user_id"];
                                echo "  <script>
                                            localStorage.setItem('success', '¡Has iniciado sesión! Hola, " . $_SESSION["user_name"] . "');
                                            window.location.href = 'dashboard.php';
                                        </script>";
                            }
                        }
                }}                    
            ?>
            <form action="login.php" method="post">
                <label for="email">E-Mail</label>
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