<?php
session_start();
require_once '../config/db.php'; // Conexión a la base de datos
require_once '../vendor/autoload.php'; // Carga de las librerías de Composer

use PragmaRX\Google2FA\Google2FA;
use Endroid\QrCode\Builder\Builder; // Para generar el código QR
use Endroid\QrCode\Writer\PngWriter; // Especifica el formato de imagen del QR

// Crear instancia de Google2FA
$google2fa = new Google2FA();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener los datos del usuario desde la base de datos
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si el 2FA no está habilitado
if (!$user['2fa_enabled']) {
    if (!$user['2fa_secret']) {
        // Generar un nuevo secreto si no existe uno
        $secret = $google2fa->generateSecretKey();
        // Guardar el secreto en la base de datos
        $stmt = $pdo->prepare('UPDATE users SET 2fa_secret = ? WHERE id = ?');
        $stmt->execute([$secret, $user['id']]);
    } else {
        // Usar el secreto existente si ya fue generado previamente
        $secret = $user['2fa_secret'];
    }

    // Generar la URL del código QR con la información del usuario
    $qrCodeUrl = $google2fa->getQRCodeUrl(
        'TuApp', // Nombre de la app que aparecerá en Google Authenticator
        $user['email'], // Identificador del usuario
        $secret // Secreto generado
    );

    // Construir el código QR con la librería Endroid
    $result = Builder::create()
        ->writer(new PngWriter()) // Especifica el formato de imagen
        ->data($qrCodeUrl) // Datos del QR (la URL generada)
        ->build();

    // Mostrar la página con el código QR
    echo "<h2>Configurar Autenticación de Dos Factores</h2>";
    echo "<p>Escanea este código QR con la aplicación Google Authenticator:</p>";
    echo '<img src="data:image/png;base64,' . base64_encode($result->getString()) . '" />';

    // Opción de volver al inicio en caso de no querer configurar
    echo '<br><a href="logout.php">Regresar a la página de inicio</a>';

    // Mostrar formulario para introducir el código de Google Authenticator
    echo '<form method="POST">';
    echo '<label for="2fa_code">Ingresa el código de Google Authenticator:</label>';
    echo '<input type="text" id="2fa_code" name="2fa_code" required>';
    echo '<button type="submit">Verificar</button>';
    echo '</form>';
} else {
    // Si ya está activado, redirigir al inicio
    header('Location: index.php');
    exit();
}

// Procesar el código de verificación cuando se envíe el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['2fa_code']; // Código introducido por el usuario

    // Verificar si el código es correcto
    if ($google2fa->verifyKey($user['2fa_secret'], $code)) {
        // Si es correcto, habilitar 2FA en la base de datos
        $stmt = $pdo->prepare('UPDATE users SET 2fa_enabled = 1 WHERE id = ?');
        $stmt->execute([$user['id']]);

        // Redirigir a la página principal después de activar el 2FA
        echo 'Autenticación de dos factores activada correctamente.';
        header('Location: index.php');
        exit();
    } else {
        // Si el código es incorrecto, mostrar un mensaje de error
        echo 'Código incorrecto. Intenta nuevamente.';
    }
}
?>
