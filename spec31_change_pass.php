<?php
session_start();
if (!isset($_SESSION["mail"])) {
    include("./error403.php");
    exit;
}
if (isset($_POST["password"]) && isset($_POST["newPass"]) && isset($_POST["newPass2"])) {
    function validarContraseña($contraseña) {
        // Al menos 8 caracteres
        if (strlen($contraseña) < 8) {
            return false;
        }
    
        // Al menos una letra minúscula
        if (!preg_match('/[a-z]/', $contraseña)) {
            return false;
        }
    
        // Al menos una letra mayúscula
        if (!preg_match('/[A-Z]/', $contraseña)) {
            return false;
        }
    
        // Al menos un dígito
        if (!preg_match('/\d/', $contraseña)) {
            return false;
        }
    
        // Al menos un carácter especial
        if (!preg_match('/[!@#$%^&*()-_=+{};:,<.>]/', $contraseña)) {
            return false;
        }
    
        // La contraseña cumple con todos los criterios
        return true;
    }
    // comprobar campos del formulario
    if (!validarContraseña($_POST["newPass"])) {
        echo "  <script>
                    localStorage.setItem('error', 'La contraseña debe incluir al menos una minúscula, una mayúscula, un dígito y un carácter especial.');
                    window.location.href = 'spec31_change_pass.php';
                </script>";
        exit;
    }

    if ($_POST["newPass"] != $_POST["newPass2"]) {
        echo "  <script>
                    localStorage.setItem('error', 'Las nuevas contraseñas no coinciden');
                    window.location.href = 'spec31_change_pass.php';
                </script>";
        exit;
    } 
    // acceder bd
    require_once("./data/dbAccess.php");
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
    } catch (PDOException $e) {

        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }
    $hashedPassword = hash('sha512', $_POST["password"]);
    $query = $pdo->prepare("SELECT * FROM User WHERE password = ? AND user_id = ?");
    $query->bindParam(1, $hashedPassword, PDO::PARAM_STR);
    $query->bindParam(2, $_SESSION["user_id"], PDO::PARAM_INT);
    $query->execute();
    
    // compruebo errores
    $e = $query->errorInfo();
    if ($e[0] != '00000') {
        echo "\nPDO::errorInfo():\n";
        die("Error accedint a dades: " . $e[2]);
    }

    $row = $query->fetch();

    if (!$row) {
        echo "  <script>
                    localStorage.setItem('error', 'La contraseña actual no coincide');
                    window.location.href = 'spec31_change_pass.php';
                </script>";
        exit;
    }

    $hashedPassword = hash('sha512', $_POST["newPass"]);
    // cambio la contraseña del usuario
    $queryUpdate = $pdo->prepare("UPDATE User SET password = ? WHERE user_id = ?");
    $queryUpdate->bindParam(1, $hashedPassword, PDO::PARAM_STR);
    $queryUpdate->bindParam(2, $_SESSION["user_id"], PDO::PARAM_INT);
    $queryUpdate->execute();

    // cambio los votos encriptados segun la contraseña antigua y la nueva
    $queryUpdateVote = $pdo->prepare("UPDATE UserVote SET user_id = AES_ENCRYPT(?, ?) WHERE user_id = AES_ENCRYPT(?, ?)");
    $queryUpdateVote->bindParam(1, $_SESSION["user_id"], PDO::PARAM_INT);
    $queryUpdateVote->bindParam(2, $_POST["newPass"], PDO::PARAM_STR);
    $queryUpdateVote->bindParam(3, $_SESSION["user_id"], PDO::PARAM_INT);
    $queryUpdateVote->bindParam(4, $_POST["password"], PDO::PARAM_STR);
    $queryUpdateVote->execute();

    echo "  <script>
                    localStorage.setItem('success', 'Has cambiado tu contraseña!');
                    window.location.href = 'dashboard.php';
            </script>";

}
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="./assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="./assets/styles/styles.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/spec31_change_pass.js"></script>
        <title>Cambiar Contraseña</title>
    </head>
    <body class="spec31">
        <?php include("./templates/header.php"); ?>
        <main>
            <h2 class="spec31h1">Cambiar contraseña</h2>
            <form action="spec31_change_pass.php" method="POST">
                <label for="password">Contraseña actual</label><br>
                <input name="password" type="password" required><br>
                <label for="newPass">Nueva contraseña</label><br>
                <input type="password" name="newPass" required><br>
                <label for="newPass2">Repite la nueva contraseña</label><br>
                <input type="password" name="newPass2" required><br>
                <input type="submit" class="finala" value="Cambiar Contraseña">
            </form>
            <ul id="notification-container"></ul>
            <?php include("./templates/footer.php"); ?>
        </main>
    </body>
</html>