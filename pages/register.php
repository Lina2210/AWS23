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
    <main>
        <form action="register.php" method="post" a>
            <input type="text" id="userName" name="userName" placeholder="Nombre" required>
            <button class="next-button">Siguiente</button>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>
            <button class="next-button">Siguiente</button>
            <input type="password" id="password" name="password" placeholder="Confirmar contraseña" required>
            <button class="next-button">Siguiente</button>
            <input type="email" id="email" name="email" placeholder="E-Mail" required>
            <button class="next-button">Siguiente</button>
            <input type="number" id="mobile" name="mobile" placeholder="Telefono" required>
            <button class="next-button">Siguiente</button>
            <input type="text" id="country" name="country" placeholder="Pais" required>
            <button class="next-button">Siguiente</button>
            <input type="text" id="city" name="city" placeholder="Ciudad" required>
            <button class="next-button">Siguiente</button>
            <input type="number" id="postalCode" name="postalCode" placeholder="Código Postal" required>
            <button class="next-button">Siguiente</button>

            <input type="submit" value="Registrarse">
        </form>
    </main>

    <script src="../assets/scripts/register.js"></script>
</body>

</html>