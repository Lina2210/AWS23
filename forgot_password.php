<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../assets/styles/styles.css">
    <script src="./assets/scripts/notifications.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Nueva contraseña</title>
</head>

<body class="forgot-password">
    <?php include("./templates/header.php"); ?>
    <?php 

        if (isset($_GET["token"]) && $_GET["token"] != 'ko') {
            session_start();
            if(isset($_SESSION['email'])) {
                // Acceder al correo electrónico almacenado en la variable de sesión
                $email = $_SESSION['email'];
                require_once("./data/dbAccess.php");
                try {
                    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
                } catch (PDOException $e) {
            
                    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                    exit;
                }
                echo $_GET["token"];
                $query = $pdo->prepare("SELECT * FROM User WHERE change_pass = ?");
                $query->bindParam(1, $_GET["token"], PDO::PARAM_STR);
                $query->execute();
                
                // compruebo errores
                $e = $query->errorInfo();
                if ($e[0] != '00000') {
                    echo "\nPDO::errorInfo():\n";
                    die("Error accedint a dades: " . $e[2]);
                }
            
                $row = $query->fetch();
                if (!$row) {
                    include("./error404.php");
                    exit;
                } else {                
                    if (isset($_POST['password']) && isset($_POST['confirmPassword']) ) {
                        file_put_contents('debug_log.txt', "entro al post\n", FILE_APPEND);
                        $password = trim($_POST['password']);
                        file_put_contents('post_result.txt', print_r($password, true));
                        $confirmPassword = trim($_POST['confirmPassword']);

                        if ($password != $confirmPassword) {
                            $date = date_create(null, timezone_open("Europe/Paris"));
                            $tz = date_timezone_get($date);
                            $dataSinCambiar = date("d-m-Y");
                            $dataReal = str_replace(" ", "-", $dataSinCambiar);
                            $carpetaArchivos = "logs/";
                            if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                            $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                            $informacionError = "Error: Las contraseñas no coinciden. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                            file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                            echo "  <script>
                                        localStorage.setItem('error', 'Las contraseñas no coinciden.');
                                        window.location.href = 'forgot_password.php';
                                    </script>";
                            exit;
                        }else{
                            $hashedPassword = hash('sha512', $password);
                            file_put_contents("prueba.txt", $hashedPassword . " hashedPassword", FILE_APPEND);
                            $updateQuery = $pdo->prepare("UPDATE User SET password = ? WHERE mail = ?;");
                            $updateQuery->bindParam(1, $hashedPassword, PDO::PARAM_STR);
                            $updateQuery->bindParam(2, $email, PDO::PARAM_STR);
                            $updateQuery->execute();
                            $e = $updateQuery->errorInfo();
                            if ($e[0] != '00000') {
                                echo "\nPDO::errorInfo():\n";
                                die("Error accedint a dades: " . $e[2]);
                            }
                    
                            $updateQuery = $pdo->prepare("UPDATE User SET change_pass = ? WHERE mail = ?;");
                            $updateQuery->bindValue(1, "ko", PDO::PARAM_STR);
                            $updateQuery->bindParam(2, $email, PDO::PARAM_STR);
                            $updateQuery->execute();
                            $e = $updateQuery->errorInfo();
                            if ($e[0] != '00000') {
                                echo "\nPDO::errorInfo():\n";
                                die("Error accedint a dades: " . $e[2]);
                            }
                        }

                        header("Location: /login.php");
                    
            
                    }else{
                        file_put_contents('debug_log.txt', "no funciona el post\n", FILE_APPEND);
                    }
                        
                }
            }else{
                $date = date_create(null, timezone_open("Europe/Paris"));
                $tz = date_timezone_get($date);
                $dataSinCambiar = date("d-m-Y");
                $dataReal = str_replace(" ", "-", $dataSinCambiar);
                $carpetaArchivos = "logs/";
                if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                $informacionError = "Error: No hay datos en la session. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";

            }

        }


    ?>
    <main>
        <form id="passwordForm" method="post">
            <label for="password">Contraseña Nueva</label>
            <input id="password" type="password" name="password" required>
            <label for="confirmPassword">Confirmar contraseña nueva</label>
            <input id="confirmPassword" type="password" name="confirmPassword" required>
            <input type="submit" id="submitBtn" value="Guardar">
        </form>
    </main>
    <ul id="notification-container"></ul>
    <script src="./assets/scripts/forgot_password.js"></script>
    <?php
        include("./templates/footer.php");
    ?>
</body>
</html>