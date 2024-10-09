<?php
session_start();
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $$_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];

        // Verificar si ya se configuró el 2FA
        if ($user['2fa_secret'] && $user['2fa_enabled']) {
            // Redirigir a verificar el código de 2FA
            header('Location: verify_2fa.php');
        } else {
            // Redirigir a configurar 2FA si no está habilitado
            header('Location: setup_2fa.php');
        }
        exit();
    } else {
        echo 'Correo o contraseña incorrectos.';
    }
}
?>