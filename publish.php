<!DOCTYPE html>
<html lang="es">
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
    <body onload="submitForm()">
        <?php
            $pSurvey = ""; 
            $pResult = "";
            $surveyId = 1;
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

                if ($_SERVER["REQUEST_METHOD"] == "POST"){ 
                    $query = $pdo->prepare("SELECT publication_survey, publication_results from Survey where survey_id = ?");
                    $query->bindParam(1, $surveyId, PDO::PARAM_STR);
                    $query->execute();
                    $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);

                    
                    foreach ($queryResult as $dato):
                        $pSurvey = $dato['publication_survey'];
                        $pResult = $dato['publication_results'];
                        
                    endforeach;
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
                }
            }
        ?> 
            <div>
                <h3>Estado de publicacion de la encuesta:</h3>
                
                <?php if ($pSurvey === 'oculto'): ?>
                    <p>Oculto</p>
                    <i class="fas fa-eye-slash" style="color: #63E6BE;"></i>
                
                <?php elseif ($pSurvey === 'publico'): ?>
                    <p>Público</p>
                    <i class="fas fa-users" style="color: #63E6BE;"></i>
                
                <?php elseif ($pSurvey === 'privado'): ?>
                    <p>Privado</p>
                    <i class="fas fa-user" style="color: #63E6BE;"></i>
                
                <?php endif; ?>
            </div>
        
            <form method="post" id="formEncuesta" >
                <label for="opcionesEncuesta">Modificar el estado de publicación de la encuesta</label>
                <select name="opcionesEncuesta" id="opcionesEncuesta">
                    <option value="">Selecciona una opción</option>
                    <option value="oculto">Oculto</option>
                    <option value="publico">Público</option>
                    <option value="privado">Privado</option>
                </select>
                <input type="submit" value="Guardar">
            </form>
    
            <div>
                <h3>Estado de publicacion de los resultados: </h3>
                <?php if ($pResult === 'oculto'): ?>
                    <p>Oculto</p>
                    <i class="fas fa-eye-slash" style="color: #63E6BE;"></i>
                <?php elseif ($pResult === 'publico'): ?>
                    <p>Público</p>
                    <i class="fas fa-users" style="color: #63E6BE;"></i>
                <?php elseif ($pResult === 'privado'): ?>
                    <p>Privado</p>
                    <i class="fas fa-user" style="color: #63E6BE;"></i>
                <?php endif; ?>
            </div>
        
            <form method="post" id="formResultado">
                <label for="opcionesResultado">Modificar el estado de publicacion de los resultados</label>
                <select name="opcionesResultado" id="opcionesResultado">
                    <option value="">Selecciona una opción</option>
                    <option value="oculto">Oculto</option>
                    <option value="publico">Público</option>
                    <option value="privado">Privado</option>
                </select>
                <input type="submit" value="Guardar">
            </form> 
    </body>
</html>