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

<!-- Formulario para ingresar el código 2FA -->
<h2>Verificar Código de Autenticación de Google</h2>
<form method="POST">
    <label for="2fa_code">Código:</label>
    <input type="text" id="2fa_code" name="2fa_code" required>
    <button type="submit">Verificar</button>
</form>
