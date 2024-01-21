<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="float.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Registrarse</title>
</head>

<body class="register">
    <h1>REGISTRARSE</h1>
    <main>
        <form action="register.php" method="post" a>
            <input type="text" id="userName" name="userName" required>
            <button class="next-button">Siguiente</button>
            <input type="email" id="email" name="email" required>
            <button class="next-button">Siguiente</button>
            <input type="password" id="password" name="password" required>
            <button class="next-button">Siguiente</button>

            <input type="submit" value="Registrarse">
        </form>
    </main>

    <script src="../assets/scripts/register.js"></script>
</body>

</html>