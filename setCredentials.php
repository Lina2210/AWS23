<?php
if (isset($_POST["username"]) && isset($_POST["mail"])) {
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