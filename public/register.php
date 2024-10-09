<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!--CSS de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>
<body>
    <?php include '../templates/nav.php'?>
    <div class="container mt-5">
        <div class="row justify-content-md-center">
            <div class="col col-md-6">
                <h2>CREAR CUENTA</h2>
                <form action="register_process.php" method="POST">
                    <label for="email">Correo Electrónico:</label><br>
                    <input type="email" name="email" required>
                    <br>
                    <label for="password">Contraseña:</label><br>
                    <input type="password" name="password" required>
                    <br><br>
                    <button type="submit">Registrar</button>
                </form>
        </div>
    </div> 

</body>
</html>