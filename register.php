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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../assets/styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Registrarse</title>
</head>

<body class="register-body">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (
            isset($_POST['userName']) && isset($_POST['password']) &&
            isset($_POST['confirmPassword']) && isset($_POST['email']) &&
            isset($_POST['country']) && isset($_POST['city']) &&
            isset($_POST['postalCode']) && isset($_POST['mobile']) && isset($_POST['mobilePrefix'])
        ) {
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
                    echo "El nombre de usuario es requerido.";
                    exit;
                }

                if (trim($password) != trim($confirmPassword)) {
                    echo "La contraseña es requerida.";
                    exit;
                }

                if ($password != $confirmPassword) {
                    echo "Las contraseñas no coinciden.";
                    exit;
                }

                $hashedPassword = hash('sha512', $password);

                if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                    echo "El correo electrónico no es válido.";
                    exit;
                }

                $query = $pdo->prepare("SELECT COUNT(*) FROM User WHERE mail = ?");
                $query->execute([$email]);

                if ($query->fetchColumn() > 0) {
                    echo "El correo electrónico ya está en uso.";
                    exit;
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
                    }
                }
                if (!$countryFound) {
                    echo "El país no es válido.";
                    exit;
                }

                $mobilePrefix = intval(ltrim($mobilePrefix, '+'));

                if ($countryPrefix != $mobilePrefix) {
                    echo "El prefijo del móvil no corresponde al país seleccionado.";
                    exit;
                }

                if (empty(trim($city))) {
                    echo "La ciudad es requerida.";
                    exit;
                }

                if (!ctype_digit($postalCode) || strlen($postalCode) != 5) {
                    echo "El código postal debe ser un número entero de 5 dígitos.";
                    exit;
                }

                if (!ctype_digit($mobile) || strlen($mobile) < 7 || strlen($mobile) > 15) {
                    echo "El número de teléfono debe ser un número entero de entre 7 y 15 dígitos.";
                    exit;
                }



                $mobile = intval($mobilePrefix . $mobile);
                $postalCode = intval($postalCode);
                $countryId = intval($countryId);

                $query = $pdo->prepare("INSERT INTO User (user_name, mail, password, tlfn, country_id, city, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $query->execute([$userName, $email, $hashedPassword, $mobile, $countryId, $city, $postalCode]);

                echo "Usuario registrado con éxito.";

            } catch (PDOException $e) {
                echo "Error al registrar el usuario: " . $e->getMessage();
                exit;
            }
        } else {
            echo "Todos los campos son requeridos.";
            exit;
        }
    }
    ?>

    <h1>REGISTRARSE</h1>
    <?php
    include("./templates/header.php");
    ?>
    <script>
        var countryOptions = <?php echo json_encode($countryOptions); ?>;
    </script>
    <script src="../assets/scripts/register.js"></script>
</body>

</html>