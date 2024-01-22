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
    <title>Iniciar Sesión</title>
</head>
<body class="login-body">
    <h1>INICIAR SESIÓN</h1>
    <?php
        include("./templates/header.php");
    ?>
    <main class="main-content">
    <?php
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        try {
            $hostname = "localhost";
            $dbname = "encuesta2";
            $username = "encuesta2User";
            $pw = "encuesta2User";
            $pdo = new PDO ("mysql:host=$hostname;dbname=$dbname","$username","$pw");
          } catch (PDOException $e) {

            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit;
        }
        $userEmail = $_POST['email'];
        $userpass = $_POST['password'];

        $query = $pdo -> prepare("SELECT user_name FROM user WHERE mail = ? AND password=SHA2(?, 512)");
        $query->bindParam(1, $userEmail, PDO::PARAM_STR);
        $query->bindParam(2, $userpass, PDO::PARAM_STR);
        $query->execute();

        //comprovo errors:
        $e = $query -> errorInfo();
        if ($e[0]!='00000') {
            echo "\nPDO::errorInfo():\n";
            die("Error accedint a dades: " . $e[2]);
        }

        $row = $query -> fetch();
        if (!$row) {
            echo "<h2 class='feedback'>Credenciales Invalidas</h2>";
        } else {
            $_SESSION["user_name"] = $row["user_name"];
            header("Location: dashboard.php");
        }
    }
    ?>
        <form action="login.php" method="post">
            <label for="email">E-Mail</label>
            <input class="field" type="email" name="email" required>
            <label for="password">Contraseña</label>
            <input class="field" type="password" name="password" required>
            <input type="submit" value="ENTRAR">
        </form>
    </main>
    <?php
        include("./templates/footer.php");
    ?>

</body>
</html>