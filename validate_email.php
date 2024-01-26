<?php
require_once("./data/dbAccess.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['token'])) {
    $link = "https://aws23.ieti.site/validate_email.php?token=".$_POST['token'];
    mail($_POST['email'], 'Verifica tu cuenta de encuesta2', 'Haz clic en el siguiente enlace para verificar tu cuenta de encuesta2: '.$link);
    // include de the hemos enviado un correo para verificar tu cuenta
    echo "te hemos enviado un correo para verificar tu cuenta";
}
elseif (isset($_GET["token"])) {
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        $query = $pdo->prepare("SELECT email_token FROM User WHERE email_token = ?");
        $query->bindParam(1, $_GET["token"], PDO::PARAM_STR);
        $query->execute();

        // Check query errors and print them
        $e = $query->errorInfo();
        if ($e[0] != '00000') {
            echo "\nPDO::errorInfo():\n";
            echo "  <script>
                        localStorage.setItem('error', 'PDO::errorInfo()');
                    </script>";
            die("Error accedint a dades: " . $e[2]);
        }

        $row = $query->fetch();
        if (!$row) {
            // token not found
            include("./error404.php");
            exit;
        } else {
            $query = $pdo->prepare("UPDATE User SET email_token = 'ok' WHERE email_token = ?");
            $query->bindParam(1, $_GET["token"], PDO::PARAM_STR);
            $query->execute();

            // Check query errors and print them
            $e = $query->errorInfo();
            if ($e[0] != '00000') {
                echo "\nPDO::errorInfo():\n";
                echo "  <script>
                            localStorage.setItem('error', 'PDO::errorInfo()');
                        </script>";
                die("Error accedint a dades: " . $e[2]);
            }

            $row = $query->fetch();
            if (!$row) {
                // error al validar cuenta.
                echo "<h1>error al validar la cuenta</h1>";
            } else {
                // include de has validado tu cuenta de encuesta2 con boton de ir al login
                echo "<h1>has validado tu cuenta de encuesta2</h1>";
            }
        }
    } catch (PDOException $e) {
        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }

} else {
    include("./error403.php");
    exit;
}
?>
