<?php
if (isset($_POST["username"]) && isset($_POST["mail"])) {
    try {
        require_once("./data/dbAccess.php");
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
    } catch (PDOException $e) {

        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
        exit;
    }
    $userEmail = $_POST['mail'];

    $query = $pdo->prepare("UPDATE User SET terms_of_use = TRUE WHERE mail = ?");
    $query->bindParam(1, $userEmail, PDO::PARAM_STR);
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

    session_start();
    $_SESSION["user_name"] = $_POST["username"];
    $_SESSION["mail"] = $_POST["mail"];

    echo "  <script>
                localStorage.setItem('success', '¡Has iniciado sesión! Hola " . $_SESSION["user_name"] . "');
                window.location.href = 'dashboard.php';
            </script>";
} else {
    include("./error403.php");
    exit;
}
?>