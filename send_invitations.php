<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
// mail($row["email"], "Has sido invitado a participar en una encuesta de encuesta2", "url")
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    mail("ifuriomartin.cf@iesesteveterradas.cat", "Has sido invitado a participar en una encuesta de encuesta2", json_encode(date("H:i:s") . $row));
}
?>