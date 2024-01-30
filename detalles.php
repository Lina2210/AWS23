<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="./assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="./assets/styles/styles.css?no-cache=<?php echo time(); ?>">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <title>Detalles — encuesta2</title>
    </head>
    <body>
        <?php include("./templates/header.php"); ?>
        <main class="main-content">
            <?php
                if (isset($_POST["email"]) && isset($_POST["password"])) {
                    try {
                        require_once("./data/dbAccess.php");
                        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");

                        $userEmail = $_POST['email'];
                        $userpass = $_POST['password'];

                        // Obtener el ID del usuario
                        $queryUserId = $pdo->prepare("SELECT user_id FROM User WHERE mail = ? AND password = SHA2(?, 512)");
                        $queryUserId->bindParam(1, $userEmail, PDO::PARAM_STR);
                        $queryUserId->bindParam(2, $userpass, PDO::PARAM_STR);
                        $queryUserId->execute();
                        $userId = $queryUserId->fetchColumn();

                        // Obtener los votos del usuario
                        $queryUserVotes = $pdo->prepare("SELECT uv.*, sq.question FROM UserVote uv JOIN SurveyQuestion sq ON uv.question_id = sq.question_id WHERE uv.user_id = ?");
                        $queryUserVotes->bindParam(1, $userId, PDO::PARAM_INT);
                        $queryUserVotes->execute();
                        $userVotes = $queryUserVotes->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        // Manejo de errores
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No se pudo conectar a la base de datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $userEmail . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                        exit;
                    }
                }
            ?>
            <canvas id="barChart" width="400" height="400"></canvas>
            <canvas id="pieChart" width="400" height="400"></canvas>

        </main>
        <script>
            // Datos para la gráfica de barras
            var barChartData = {
                labels: ['Opción 1', 'Opción 2', 'Opción 3'], // Ejemplo de etiquetas
                datasets: [{
                    label: 'Votos',
                    data: [5, 10, 15], // Ejemplo de datos de votos
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            };

            // Datos para la gráfica de pastel
            var pieChartData = {
                labels: ['Opción 1', 'Opción 2', 'Opción 3'], // Ejemplo de etiquetas
                datasets: [{
                    data: [5, 10, 15], // Ejemplo de datos de votos
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            };

            // Configuración de la gráfica de barras
            var barChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            };

            // Configuración de la gráfica de pastel
            var pieChartOptions = {
                responsive: true
            };

            // Crear la gráfica de barras
            var ctxBar = document.getElementById('barChart').getContext('2d');
            var barChart = new Chart(ctxBar, {
                type: 'bar',
                data: barChartData,
                options: barChartOptions
            });

            // Crear la gráfica de pastel
            var ctxPie = document.getElementById('pieChart').getContext('2d');
            var pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: pieChartData,
                options: pieChartOptions
            });
        </script>
    </body>
</html>
