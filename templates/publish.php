<?php
    function actualizarEstadosPublicacion() {
        // Tu código para conectar a la base de datos y obtener los estados de publicación
        global $pSurvey, $pResult, $id_encuesta, $pdo;
        $id_encuesta = $_POST['survey_id'];
        $hostname = "localhost";
        $dbname = "encuesta2";
        $username = "encuesta2";
        $pw = "naranjasV3rdes#";
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        
        // Aquí iría tu lógica para obtener y actualizar $pSurvey y $pResult
        $query = $pdo->prepare("SELECT publication_survey, publication_results from Survey where survey_id = ?");
        $query->bindParam(1, $id_encuesta, PDO::PARAM_STR);
        $query->execute();
        $queryResult = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($queryResult as $dato):
            $pSurvey = $dato['publication_survey'];
            $pResult = $dato['publication_results'];
        endforeach;
    }
    actualizarEstadosPublicacion();  
?>
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
                $queryInsertpEncuesta->bindParam(2, $id_encuesta, PDO::PARAM_INT);
                $queryInsertpEncuesta->execute();
            }
        
            if (isset($_POST['opcionesResultado'])) {
                $estadoResultado = $_POST['opcionesResultado'];
                $queryInsertpResultado = $pdo->prepare("UPDATE Survey SET publication_results = ? WHERE survey_id = ?");
                $queryInsertpResultado->bindParam(1, $estadoResultado, PDO::PARAM_STR);
                $queryInsertpResultado->bindParam(2, $id_encuesta, PDO::PARAM_INT);
                $queryInsertpResultado->execute();
            }
            actualizarEstadosPublicacion();

        }            
    }
?>  


<div class="iconoEncuesta">
    <h3 class="tituloiconoEncuesta">Estado de publicacion de la encuesta: </h3>
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

<div class="iconoResultado">
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
    <br><label for="opcionesEncuesta"><em>Modificar el estado de publicacion de la encuesta</em></label>
    <br><select name="opcionesEncuesta" id="opcionesEncuesta">
        <option value="opciones">Selecciona una opción</option>
        <option value="oculto">Oculto</option>
        <option value="publico">Público</option>
        <option value="privado">Privado</option>
    </select>   
    <br><label for="opcionesResultado"><em>Modificar el estado de publicacion de los resultados</em></label><br>
    <select name="opcionesResultado" id="opcionesResultado" disabled>
        <option value="">Selecciona una opción</option>
        <option value="oculto">Oculto</option>
        <option value="publico">Público</option>
        <option value="privado">Privado</option>
    </select>
    <input type="hidden" name="survey_id" value="<?php echo $idEncuesta; ?>">
    <br><input type="submit" value="Guardar" id="guardarButton">
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