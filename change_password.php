<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="./assets/scripts/notifications.js"></script>
    <link rel="stylesheet" href="./assets/styles/styles.css?no-cache=<?php echo time(); ?>">    
    <title>¿Olvidaste la contraseña?</title>
</head>

<body class="change-password">
    <?php include("./templates/header.php"); ?>
    <?php
    session_start();
    if (isset($_POST["email"])) {

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
                    $_SESSION['email'] = $userEmail;
                    $query = $pdo->prepare("SELECT * FROM User WHERE mail = ?");
                    $query->bindParam(1, $userEmail, PDO::PARAM_STR);
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
                        $informacionError = "Error: El email introducido no está registrado. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $user_name . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'El email introducido no está registrado.');
                                    window.location.href = 'change_password.php';
                                </script>";
                    } else {
                        if ($row) {
                            $token = bin2hex(random_bytes(32 / 2));
                            $link = "https://aws23.ieti.site/forgot_password.php?token=".$token;//corregir
                            file_put_contents('token_result.txt', print_r($link, true));
                            $updateQuery = $pdo->prepare("UPDATE User SET change_pass = ? WHERE mail = ?;");
                            $updateQuery->bindParam(1, $token, PDO::PARAM_STR);
                            $updateQuery->bindParam(2, $userEmail, PDO::PARAM_STR);
                            $updateQuery->execute();
                            $e = $updateQuery->errorInfo();
                            if ($e[0] != '00000') {
                                echo "\nPDO::errorInfo():\n";
                                die("Error accedint a dades: " . $e[2]);
                            }
                            
                            echo "<script>
                                    localStorage.setItem('success', 'Se ha enviado un correo electrónico para recuperar tu contraseña.');
                                    window.location.href = 'change_password.php';
                                </script>";
                        }
                        mail($userEmail, "Has sido invitado a participar en una encuesta de encuesta2", "No respondas a este mensaje. Link de la encuesta: ".$link);
                    }

                }
    ?>
    <main>
        <form method="post" action="/change_password.php" id="formEmail">
            <h2>Por favor, ingresa el correo electrónico que utilizaste durante el registro</h2>
            <input type="email" id="email" name="email" placeholder="Correo Electronico" required><br>
            <input type="submit"  id="submitBtn" value="Enviar" >
        </form>
    </main>
    
    <ul id="notification-container"></ul>
    <script src="./assets/scripts/change_password.js"></script>
    <?php
        include("./templates/footer.php");
    ?>
</body>
</html>