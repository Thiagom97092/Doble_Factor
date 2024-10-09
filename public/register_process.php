<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
    if ($stmt->execute([$email, $password])) {
        echo 'Cuenta creada exitosamente. <a href="login.php">Iniciar sesión</a>';
    } else {
        echo 'Error al crear la cuenta.';
    }
}
?>

