<?php
    function actualizarEstadosPublicacion() {
        // Tu código para conectar a la base de datos y obtener los estados de publicación
        global $pSurvey, $pResult, $surveyId, $pdo;
        $surveyId=1;
        $hostname = "localhost";
        $dbname = "encuesta2";
        $username = "encuesta2";
        $pw = "naranjasV3rdes#";
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        
        // Aquí iría tu lógica para obtener y actualizar $pSurvey y $pResult
        $query = $pdo->prepare("SELECT publication_survey, publication_results from Survey where survey_id = ?");
        $query->bindParam(1, $surveyId, PDO::PARAM_STR);
        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($queryResult as $dato):
            $pSurvey = $dato['publication_survey'];
            $pResult = $dato['publication_results'];
        endforeach;
    }
    actualizarEstadosPublicacion();  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Document</title>
    <style>

        div{
            display: flex;
            flex-direction: row;
            
            align-items: center;
        }
        p, i{
            margin-left: 10px;
            margin-right: 10px;
        }

    </style>
</head>
<body>
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $hostname = "localhost";
                $dbname = "encuesta2";
                $username = "encuesta2";
                $pw = "naranjasV3rdes#";
                $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
            } catch (PDOException $e) {

                echo "  <script>
                                localStorage.setItem('error', 'Failed to get DB handle: " . $e->getMessage() . ");
                                window.location.href = 'login.php';
                            </script>";
                exit;
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['opcionesEncuesta'])) {
                    $estadoEncuesta = $_POST['opcionesEncuesta'];
                    $queryInsertpEncuesta = $pdo->prepare("UPDATE Survey SET publication_survey = ? WHERE survey_id = ?");
                    $queryInsertpEncuesta->bindParam(1, $estadoEncuesta, PDO::PARAM_STR);
                    $queryInsertpEncuesta->bindParam(2, $surveyId, PDO::PARAM_INT);
                    $queryInsertpEncuesta->execute();
                }
            
                if (isset($_POST['opcionesResultado'])) {
                    $estadoResultado = $_POST['opcionesResultado'];
                    $queryInsertpResultado = $pdo->prepare("UPDATE Survey SET publication_results = ? WHERE survey_id = ?");
                    $queryInsertpResultado->bindParam(1, $estadoResultado, PDO::PARAM_STR);
                    $queryInsertpResultado->bindParam(2, $surveyId, PDO::PARAM_INT);
                    $queryInsertpResultado->execute();
                }
                actualizarEstadosPublicacion();

            }            
        }
    ?>  
    

    <div class="icono encuesta">
        <h3>Estado de publicacion de la encuesta: </h3>
        <?php
            switch ($pSurvey) {
                case 'oculto':
                    echo '<p>Oculto</p>';
                    echo '<i class="fas fa-eye-slash" style="color: #63E6BE;"></i>';
                    break;
                case 'publico':
                    echo '<p>Público</p>';
                    echo '<i class="fas fa-users" style="color: #63E6BE;"></i>';
                    break;
                case 'privado':
                    echo '<p>Privado</p>';
                    echo '<i class="fas fa-user" style="color: #63E6BE;"></i>';
                    break;
                default:
                    echo '<p>Estado desconocido</p>';
                    break;
            }
        ?>
    </div>

    <div class="icono resultado">
        <h3>Estado de publicacion de los resultados: </h3>
        <?php
            switch ($pResult) {
                case 'oculto':
                    echo '<p>Oculto</p>';
                    echo '<i class="fas fa-eye-slash" style="color: #63E6BE;"></i>';
                    break;
                case 'publico':
                    echo '<p>Público</p>';
                    echo '<i class="fas fa-users" style="color: #63E6BE;"></i>';
                    break;
                case 'privado':
                    echo '<p>Privado</p>';
                    echo '<i class="fas fa-user" style="color: #63E6BE;"></i>';
                    break;
                default:
                    echo '<p>Estado desconocido</p>';
                    break;
            }
        ?>
    </div>

    <form method="post" id="formEncuesta">
        <label for="opcionesEncuesta">Modificar el estado de publicacion de la encuesta</label>
        <select name="opcionesEncuesta" id="opcionesEncuesta">
            <option value="opciones">Selecciona una opción</option>
            <option value="oculto">Oculto</option>
            <option value="publico">Público</option>
            <option value="privado">Privado</option>
        </select>   
        <label for="opcionesResultado">Modificar el estado de publicacion de los resultados</label>
        <select name="opcionesResultado" id="opcionesResultado" disabled>
            <option value="">Selecciona una opción</option>
            <option value="oculto">Oculto</option>
            <option value="publico">Público</option>
            <option value="privado">Privado</option>
        </select>
        <input type="submit" value="Guardar">
    </form>

        <script>
        $(document).ready(function(){
            
            $('#opcionesEncuesta').change(function(){
                var encuestaSeleccionada = $(this).val();
                $('#opcionesResultado').prop('disabled', false);
                                
                // Reiniciamos las opciones del select
                $('#opcionesResultado option').prop('disabled', false);
                
                if(encuestaSeleccionada === 'oculto') {
                    $('#opcionesResultado option[value="publico"]').prop('disabled', true);
                    $('#opcionesResultado option[value="privado"]').prop('disabled', true);
                    $('#opcionesResultado').val('oculto'); // seleccionamos 'oculto' automáticamente
                } else if(encuestaSeleccionada === 'privado') {
                    $('#opcionesResultado option[value="publico"]').prop('disabled', true);
                    $('#opcionesResultado').val('privado'); // seleccionamos 'privado' automáticamente
                }
            });
        });
    </script>
        
    
</body>
</html>