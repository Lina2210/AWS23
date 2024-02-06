<?php
    require_once("./data/dbAccess.php");
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        $queryCountry = $pdo->prepare("SELECT country_id, country_name, phone_prefix FROM Country");
        $queryCountry->execute();
        $countryOptions = $queryCountry->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../assets/styles/styles.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <title>Registro — encuesta2</title>
    </head>

    <body class="register-body">

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['userName']) && isset($_POST['password']) &&
                isset($_POST['confirmPassword']) && isset($_POST['email']) &&
                isset($_POST['country']) && isset($_POST['city']) &&
                isset($_POST['postalCode']) && isset($_POST['mobile']) && isset($_POST['mobilePrefix'])) {
                try {
                    $userName = trim($_POST['userName']);
                    $password = trim($_POST['password']);
                    $confirmPassword = trim($_POST['confirmPassword']);
                    $email = trim($_POST['email']);
                    $country = $_POST['country'];
                    $city = trim($_POST['city']);
                    $postalCode = trim($_POST['postalCode']);
                    $mobile = trim($_POST['mobile']);
                    $mobilePrefix = ltrim($_POST['mobilePrefix'], '+');

                    if (empty(trim($userName))) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: El nombre de usuario es requerido. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'El nombre de usuario es requerido.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }

                    if (trim($password) != trim($confirmPassword)) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No se puso contraseña. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'La contraseña es requerida.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }

                    if ($password != $confirmPassword) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: Las contraseñas no coinciden. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND); 
                        echo "  <script>
                                    localStorage.setItem('error', 'Las contraseñas no coinciden.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }

                    $hashedPassword = hash('sha512', $password);

                    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: El correo electrónico no es válido. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'El correo electrónico no es válido.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }

                    $query = $pdo->prepare("SELECT COUNT(*), invited_user FROM User WHERE mail = ? GROUP BY invited_user");
                    $query->execute([$email]);

                    $row = $query->fetch(PDO::FETCH_ASSOC); // Obtener la fila de resultados
                    $invited_user;

                    if ($row) {
                        $count = intval($row['COUNT(*)']); // Convertir a entero
                        $invited_user = intval($row['invited_user']);
                    
                                    
                        if ($count > 0 && !$invited_user) {
                            $date = date_create(null, timezone_open("Europe/Paris"));
                            $tz = date_timezone_get($date);
                            $dataSinCambiar = date("d-m-Y");
                            $dataReal = str_replace(" ", "-", $dataSinCambiar);
                            $carpetaArchivos = "logs/";
                            if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                            $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                            $informacionError = "Error: Ya hay un usuario con este correo electrónico. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                            file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                            echo "  <script>
                                        localStorage.setItem('error', 'Ya hay un usuario con este correo electrónico.');
                                        window.location.href = 'register.php';
                                    </script>";
                            exit;
                        }
                    }
                    $countryFound = false;
                    $countryPrefix = null;
                    $countryId = null;
                    foreach ($countryOptions as $option) {
                        if ($option['country_name'] == trim($country)) {
                            $countryFound = true;
                            $countryPrefix = $option['phone_prefix'];
                            $countryId = $option['country_id'];
                            break;
                        }}
                    if (!$countryFound) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No se seleccionó un país de las opciones. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'Selecciona un país de las opciones.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }

                    $mobilePrefix = intval(ltrim($mobilePrefix, '+'));

                    if ($countryPrefix != $mobilePrefix) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: El prefijo del móvil no corresponde al país seleccionado. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'El prefijo del móvil no corresponde al país seleccionado.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }

                    if (empty(trim($city))) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No se seleccionó ciudad. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'La ciudad es requerida.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }

                    if (!ctype_digit($postalCode) || strlen($postalCode) != 5) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: El código postal no es correcto. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'El código postal debe ser un número entero de 5 dígitos.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }

                    if (!ctype_digit($mobile) || strlen($mobile) < 7 || strlen($mobile) > 15) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        echo "<h1>".$dataReal."</h1>";
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: El número de teléfono es erróneo. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "  <script>
                                    localStorage.setItem('error', 'El número de teléfono debe ser un número entero de entre 7 y 15 dígitos.');
                                    window.location.href = 'register.php';
                                </script>";
                        exit;
                    }
                
                    $postalCode = intval($postalCode);
                    $countryId = intval($countryId);
                    $token = bin2hex(random_bytes(32 / 2));
                    file_put_contents('debug_log.txt', "valores: " . $invited_user, FILE_APPEND);
                    file_put_contents('debug_log.txt', "valores: " . $userName . "," . $hashedPassword . "," . $mobile . "," . $countryId ."," . $city . "," . $postalCode . "," . $token . "," . '0' . "," . '1' . "," . $email, FILE_APPEND);
                    if (!$invited_user) {
                        file_put_contents('debug_log.txt', "valores: " . $userName . $hashedPassword . $mobile . $countryId . $city . $postalCode . $token . '0' . '1' . $email, FILE_APPEND);
                        $query = $pdo->prepare("INSERT INTO User (user_name, mail, password, tlfn, country_id, city, postal_code, email_token, terms_of_use, invited_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $query->execute([$userName, $email, $hashedPassword, $mobile, $countryId, $city, $postalCode, $token, 0, 0]);
                    } else {
                        $query = $pdo->prepare("UPDATE User SET user_name = ?, password = ?, tlfn = ?, country_id = ?, city = ?, postal_code = ?, email_token = ?, terms_of_use = ?, invited_user = ? WHERE mail = ?");
                        $query->execute([$userName, $hashedPassword, $mobile, $countryId, $city, $postalCode, $token, 0, 0, $email]);

                        // select para obtener el user id
                        $querySelect = $pdo->prepare("SELECT user_id FROM User WHERE mail = ?");
                        $querySelect->bindParam(1, $email, PDO::PARAM_STR);
                        $querySelect->execute();

                        $selectRow = $querySelect->fetch();

                        // hago un update de uservote para poder desencriptar luego con la password del usuario
                        require_once("./data/conf.php");

                        $queryUpdate = $pdo->prepare("UPDATE UserVote SET user_id = AES_ENCRYPT(?, ?) WHERE user_id = AES_ENCRYPT(?, ?)");
                        $queryUpdate->bindParam(1, $selectRow["user_id"], PDO::PARAM_INT);
                        $queryUpdate->bindParam(2, $password, PDO::PARAM_STR);
                        $queryUpdate->bindParam(3, $selectRow["user_id"], PDO::PARAM_INT);
                        $queryUpdate->bindParam(4, $key, PDO::PARAM_STR);
                        $queryUpdate->execute();

                        $contenido = "User id = (".$selectRow["user_id"].") | default key = (".$key.") | password = (".$password.")";
                        file_put_contents("./logs/pruebasregister.txt", $contenido);
                        
                    }

                    // el usuario se ha creado correctamente

                    // formulario de auto envio para validar el correo
                    echo "<form id='auto-form' action='validate_email.php' method='post'>";
                    echo "      <input type='hidden' name='email' value='".$email."'>";
                    echo "      <input type='hidden' name='token' value='".$token."'>";
                    echo "</form>";

                    // Seleccionar el formulario y enviarlo automáticamente
                    echo "  <script>
                                document.getElementById('auto-form').submit();
                            </script>";
                    exit;

                } catch (PDOException $e) {
                    $date = date_create(null, timezone_open("Europe/Paris"));
                    $tz = date_timezone_get($date);
                    $dataSinCambiar = date("d-m-Y");
                    $dataReal = str_replace(" ", "-", $dataSinCambiar);
                    echo "<h1>".$dataReal."</h1>";
                    $carpetaArchivos = "logs/";
                    if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                    $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                    $informacionError = "Error: Error al registrar el usuario. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                    file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                    echo "  <script>
                                localStorage.setItem('error', 'Error al registrar el usuario: " . $e->getMessage() . "');
                                window.location.href = 'register.php';
                            </script>";
                    exit;
                }
            } else {
                $date = date_create(null, timezone_open("Europe/Paris"));
                $tz = date_timezone_get($date);
                $dataSinCambiar = date("d-m-Y");
                $dataReal = str_replace(" ", "-", $dataSinCambiar);
                echo "<h1>".$dataReal."</h1>";
                $carpetaArchivos = "logs/";
                if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                $informacionError = "Error: No se rellenó todo el formulario. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                echo "  <script>
                            localStorage.setItem('error', 'Rellena todo el formulario!');
                            window.location.href = 'register.php';
                        </script>";
                exit;
            }
        }
        
        ?>

        <h1>REGISTRARSE</h1>
        <?php include("./templates/header.php"); ?>
        
        <ul id="notification-container"></ul>
        
        <?php include("./templates/footer.php"); ?>
        
        <script>
            var countryOptions = <?php echo json_encode($countryOptions); ?>;
        </script>
        <script src="../assets/scripts/register.js"></script>
    </body>
</html>