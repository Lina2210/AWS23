<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link rel="stylesheet" href="./assets/styles/styles.css">
    <script src="./assets/scripts/notifications.js"></script>
    <title>Document</title>
</head>
<body class="createPoll-body">
    <h1>Crear encuesta</h1>
    <script>
        $(document).ready(function() {
        var main = $('<main class="main-content"></main>');
        var form = $('<form action="create_poll2.php" method="post"></form>');
        main.append(form);
        $('body').append(main);

        var inputNamePoll = createInput("text", "namePoll", "Nombre de la encuesta");
        form.append(inputNamePoll);

        form.on('keydown', '.field', function(event) {
            var $this = $(this);
            if (event.which === 13 || event.which === 9) {
                event.preventDefault();
                $this.trigger('blur');
            }
        
        });        
        form.on('blur', '.field', function() {
        var $this = $(this);
        if ($this.val().trim() !== '') {
            console.log('El campo está diligenciado.');
            lDateStart = createLabel("dateStart", "Fecha de apertura")
            iDateStart = createInput("date","dateStart")
            form.append(lDateStart);
            form.append(iDateStart);
        } else {
            console.log('El campo está vacío.');
            // Aquí puedes realizar acciones adicionales si el campo está vacío
        }
    });
        }); 

    

    function createInput(iType, iName, iPlaceholder){ 
        return $('<input type="'+ iType + '" class="field" "name="' + iName + '" placeholder="' + iPlaceholder +'" required></select>');
    }

    function createLabel(lFor, lText){
        return $('<label for="' + lFor +'">' + lText + '</label>')
    }


    </script>
</body>
</html>