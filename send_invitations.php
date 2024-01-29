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
    die("Error accedint a dades: " . $e[2]);
}

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    file_put_contents("PRUEBA.txt", json_encode(date("H:i:s") . $row) . PHP_EOL, FILE_APPEND);
}
?>