<?php
$paginaActual = 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVANZA - Inicio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/landing.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/nav-landing.php'; ?>

    <section class="hero-landing">
        <div class="hero-landing_contenido">
            <h1 class="hero-landing_titulo">Centralizá turnos, seguimiento clínico y coordinación en un solo lugar</h1>
            <p class="hero-landing_texto">AVANZA reemplaza WhatsApp, planillas sueltas y comunicaciones dispersas por una plataforma pensada para familias, terapeutas y administrativos. Todo el recorrido del niño queda ordenado, visible y facil de seguir.</p>
            <div class="hero-landing_botones">
                <a href="login.php" class="btn btn-primario">Iniciar Sesión</a>
                <a href="contacto.php" class="btn btn-outline">Contacto</a>
            </div>
        </div>
        <div class="hero-landing_mock">
            <img src="../assets/img/hero-mockup.png" alt="Mockup Hero" class="hero-landing_mock-img">
        </div>
    </section>
    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
</body>
</html>