<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="./assets/images/dos.png" type="image/png">
        <link rel="stylesheet" href="./assets/styles/styles.css?no-cache=<?php echo time(); ?>">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="./assets/scripts/notifications.js"></script>
        <script src="./assets/scripts/login.js"></script>
        <title>Detalles — encuesta2</title>
    </head>
    <body class="graphics">
        <?php include("./templates/header.php"); ?>
        
        <main>
            <?php
                session_start();
                $idUsuari = 1;

                if (isset($_GET['id'])) {
                    $id_encuesta = intval($_GET['id']);
                    try {
                        require_once("./data/dbAccess.php");
                        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
                        
                    } catch (PDOException $e) {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No se pudo conectar a la base de datos. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                        exit;
                    }

                    $query = 'SELECT * FROM Survey WHERE survey_id = :id_encuesta';
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
                    $stmt->execute();
                    $encuesta = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($encuesta && $encuesta['user_id'] == $idUsuari) {
                        echo "<h1 id='pollName'>Detalles de {$encuesta['title']}</h1>";

                        // Consultar respuestas a las preguntas de la encuesta
                        $queryRespuestas = 'SELECT COUNT(*) as cantidad_respuestas, Answer.answer_text 
                                            FROM UserVote 
                                            JOIN Answer ON UserVote.answer_id = Answer.answer_id 
                                            JOIN Question ON Answer.question_id = Question.question_id 
                                            WHERE Question.survey_id = :id_encuesta 
                                            GROUP BY Answer.answer_id';
                        $stmtRespuestas = $pdo->prepare($queryRespuestas);
                        $stmtRespuestas->bindParam(':id_encuesta', $id_encuesta, PDO::PARAM_INT);
                        $stmtRespuestas->execute();
                        $respuestas = $stmtRespuestas->fetchAll(PDO::FETCH_ASSOC);

                        // Preparar datos para los gráficos
                        $labelsBarra = [];
                        $datosBarra = [];
                        $labelsPastel = [];
                        $datosPastel = [];
                        foreach ($respuestas as $respuesta) {
                            $labelsBarra[] = $respuesta['answer_text'];
                            $datosBarra[] = $respuesta['cantidad_respuestas'];
                            $labelsPastel[] = $respuesta['answer_text'];
                            $datosPastel[] = $respuesta['cantidad_respuestas'];
                        }

                        // Generar gráfico de barras
                        echo '<div class="divGraficos"><canvas id="graficoBarras"></canvas></div>';
                        echo "
                        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var graficoBarras = document.getElementById('graficoBarras').getContext('2d');
                                var barrasGrafical = new Chart(graficoBarras, {
                                    type: 'bar',
                                    data: {
                                        labels: " . json_encode($labelsBarra) . ",
                                        datasets: [{
                                            label: 'Cantidad de Votos',
                                            data: " . json_encode($datosBarra) . ",
                                            backgroundColor: ['rgba(255, 99, 132)', 'rgba(54, 162, 235)', 'rgba(255, 206, 86)'],
                                            borderColor: ['rgba(255, 99, 132)', 'rgba(54, 162, 235)', 'rgba(255, 206, 86)'],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                            });
                        </script>";

                        // Generar gráfico de anillo
                        echo '<div class="divGraficos" id="pieChart"><canvas id="graficoPastel"></canvas></div>';
                        echo "
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var grfPastel = document.getElementById('graficoPastel').getContext('2d');
                                var pastelitoPastel = new Chart(grfPastel, {
                                    type: 'doughnut',
                                    data: {
                                        labels: " . json_encode($labelsPastel) . ",
                                        datasets: [{
                                            label: 'Cantidad de Votos',
                                            data: " . json_encode($datosPastel) . ",
                                            backgroundColor: ['rgba(255, 99, 132)', 'rgba(54, 162, 235)', 'rgba(255, 206, 86)'],
                                            borderColor: ['rgba(255, 99, 132)', 'rgba(54, 162, 235)', 'rgba(255, 206, 86)'],
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        cutout: '80%',
                                        plugins: {
                                            legend: {
                                                position: 'bottom',
                                            }
                                        }
                                    }
                                });
                            });
                        </script>";

                    } else {
                        $date = date_create(null, timezone_open("Europe/Paris"));
                        $tz = date_timezone_get($date);
                        $dataSinCambiar = date("d-m-Y");
                        $dataReal = str_replace(" ", "-", $dataSinCambiar);
                        $carpetaArchivos = "logs/";
                        if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                        $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                        $informacionError = "Error: No tienes permisos para acceder a la encuesta. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                        file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                        echo "<p>Error: No tienes permisos para acceder a esta encuesta.</p>";
                    }

                    unset($pdo);
                } else {
                    $date = date_create(null, timezone_open("Europe/Paris"));
                    $tz = date_timezone_get($date);
                    $dataSinCambiar = date("d-m-Y");
                    $dataReal = str_replace(" ", "-", $dataSinCambiar);
                    $carpetaArchivos = "logs/";
                    if (!file_exists($carpetaArchivos)) {  mkdir($carpetaArchivos, 0777, true); }
                    $nombreArchivo = $carpetaArchivos . "errorLog-" . $dataReal . ".txt";
                    $informacionError = "Error: No se proporcionó el ID. Hora del error: " . $dataSinCambiar . ". Error realizado por: " . $email . ".\n";
                    file_put_contents($nombreArchivo, $informacionError, FILE_APPEND);
                    echo "Error: No se proporcionó el ID.";
                }
            ?>
        </main>
        <?php include("./templates/footer.php"); ?>
    </body>
</html>