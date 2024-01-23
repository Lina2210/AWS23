<select name="" id=""></select>
<?php
require_once("../data/dbAccess.php");
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
    $query = $pdo->prepare("select Country.country_name from Country");
    $query->execute();
    $countryOptions = $query->fetchAll(PDO::FETCH_COLUMN, 0);
    echo json_encode($countryOptions);
} catch (PDOException $e) {
    echo "Failed to get DB handle: " . $e->getMessage() . "\n";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../assets/styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Registrarse</title>
</head>

<body class="register-body">
    <h1>REGISTRARSE</h1>

    <script src="../assets/scripts/register.js"></script>
</body>

</html>