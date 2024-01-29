<?php

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

}
?>