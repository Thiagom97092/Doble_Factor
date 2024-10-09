<?php
session_start();
require_once '../config/db.php';
require_once '../vendor/autoload.php';

use PragmaRX\Google2FA\Google2FA;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$google2fa = new Google2FA();

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['2fa_code'];

    if ($google2fa->verifyKey($user['2fa_secret'], $code)) {
        $_SESSION['authenticated'] = true;
        header('Location: index.php');
        exit();
    } else {
        echo 'Código incorrecto.';
    }
}
?>
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
                <!-- Formulario para ingresar el código 2FA -->
                <h2>Verificar Código de Autenticación de Google</h2>
                <form method="POST">
                    <label for="2fa_code">Código:</label>
                    <input type="text" id="2fa_code" name="2fa_code" required>
                    <button type="submit">Verificar</button>
                </form>
            </div>
        </div>
    </div> 
</body>
</html>





