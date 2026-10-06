<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = conectarDB();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt = $pdo->execute([$_POST['email']]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($_POST['password'], $usuario['password'])) {
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];
        header('Location: ' . $usuario['rol'] . '/dashboard.php');
        exit;
    }
    $error = 'Correo o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVANZA - Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/landing.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/nav-landing.php'; ?>
    <main class="login">
        <form method="post" class="login_form">
            <h1 class="login_titulo">Iniciar Sesión</h1>
            <?php if($error): ?> <p class="login_error"><?= $error ?></p><?php endif; ?>
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="btn btn-primario">Ingresar</button>
        </form>
    </main>
    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
</body>
</html>