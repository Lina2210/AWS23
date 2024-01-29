<?php
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    include("./error403.php");
    exit;
}
try {
    require_once("./data/dbAccess.php");
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
} catch (PDOException $e) {

    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}

$query = $pdo->prepare("SELECT * FROM SendEmailTo LIMIT 5");
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
if ($row) {
    file_put_contents("PRUEBA.txt");
}
?>