<?php
session_start();
if (isset($_SESSION["user_name"])) {
    header("Location: dashboard.php");
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
    <script src="./assets/scripts/login.js"></script>
    <title>Iniciar Sesión</title>
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

                echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                exit;
            }
            $userEmail = $_POST['email'];
            $userpass = $_POST['password'];

            $query = $pdo->prepare("SELECT user_name, mail, email_token, terms_of_use FROM User WHERE mail = ? AND password=SHA2(?, 512)");
            $query->bindParam(1, $userEmail, PDO::PARAM_STR);
            $query->bindParam(2, $userpass, PDO::PARAM_STR);
            $query->execute();

            //comprovo errors:
            $e = $query->errorInfo();
            if ($e[0] != '00000') {
                echo "\nPDO::errorInfo():\n";
                echo "  <script>
                            localStorage.setItem('error', 'PDO::errorInfo()');
                            window.location.href = 'login.php';
                        </script>";
                die("Error accedint a dades: " . $e[2]);
            }

            $row = $query->fetch();
            if (!$row) {
                echo "  <script>
                            localStorage.setItem('error', 'Credenciales Invalidas.');
                            window.location.href = 'login.php';
                        </script>";
            } else {
                if ($row["email_token"] != "ok") {
                    // cuenta no validada por email
                    echo "  <script>
                                localStorage.setItem('error', 'Debes validar tu cuenta. Comprueba tu email.');
                                window.location.href = 'login.php';
                            </script>";
                }
                elseif (!$row["terms_of_use"]) {
                    // mostrar bocadillo de aceptacion de terminos de uso
                    include("./templates/terms_of_use.php");
                    $user_name = htmlspecialchars($row['user_name']);
                    $mail = htmlspecialchars($row['mail']);
                    echo "  <script> checkTerms('$user_name', '$mail'); </script>";

                } else {
                    // inicio de sesion normal
                    $_SESSION["user_name"] = $row["user_name"];
                    $_SESSION["mail"] = $row["mail"];
                    echo "  <script>
                                localStorage.setItem('success', '¡Has iniciado sesión! Hola " . $_SESSION["user_name"] . "');
                                window.location.href = 'dashboard.php';
                            </script>";
                }
            }
        }
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
    <?php
    include("./templates/footer.php");
    ?>

</body>

</html>