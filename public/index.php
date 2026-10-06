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

    <section class="hero_landing">
        <div class="hero_landing-contenido">
            <h1 class="hero_landing-titulo">Centralizá turnos, seguimiento clínico y coordinación en un solo lugar</h1>
            <p class="hero_landing-texto">AVANZA reemplaza WhatsApp, planillas sueltas y comunicaciones dispersas por una plataforma pensada para familias, terapeutas y administrativos. Todo el recorrido del niño queda ordenado, visible y facil de seguir.</p>
            <div class="hero_landing-botones">
                <a href="login.php" class="btn btn_primario">Iniciar Sesión</a>
                <a href="contacto.php" class="btn btn_outline">Contacto</a>
            </div>
        </div>
        <div class="hero_landing-mock">
            <img src="../assets/img/hero-mockup.png" alt="Mockup Hero" class="hero_landing_mock-img">
        </div>
    </section>

    <section class="solucion">
        <h2 class="seccion_titulo">Una plataforma para cada actor del centro</h2>
        <div class="solucion_tarjetas">
            <article class="tarjeta">
                <div class="tarjeta_numero">01</div>
                <h3 class="tarjeta_titulo">Familias</h3>
                <p class="tarjeta_texto">Solicitan turnos, ven el calendario, reciben confirmaciones y acceden al recorrido del niño de forma simple y visual.</p>
                <ul class="lista">
                    <li class="lista_item"><span class="lista_punto"></span>Pedir turnos y ver disponibilidad</li>
                    <li class="lista_item"><span class="lista_punto"></span>Confirmar asistencia y cambios</li>
                    <li class="lista_item"><span class="lista_punto"></span>Ver evoluciones y recordatorios</li>
                </ul>
            </article>
            <article class="tarjeta">
                <div class="tarjeta_numero">02</div>
                <h3 class="tarjeta_titulo">Terapeutas</h3>
                <p class="tarjeta_texto">Registra evoluciones, notas clinicas y objetivos con una estructura clara, sin perder tiempo en coordinacion administrativa.</p>
                <ul class="lista">
                    <li class="lista_item"><span class="lista_punto"></span>Registra evoluciones y notas clinicas</li>
                    <li class="lista_item"><span class="lista_punto"></span>Ver historial clínico completo</li>
                    <li class="lista_item"><span class="lista_punto"></span>Seguir objetivos y avances</li>
                </ul>
            </article>
            <article class="tarjeta">
                <div class="tarjeta_numero">03</div>
                <h3 class="tarjeta_titulo">Administrativos</h3>
                <p class="tarjeta_texto">Gestionan agenda, confirmaciones, recordatorio y permisos con una vista operativa que reduce el doble de trabajo y errores.</p>
                <ul class="lista">
                    <li class="lista_item"><span class="lista_punto"></span>Gestionar agenda y confirmaciones</li>
                    <li class="lista_item"><span class="lista_punto"></span>Asignar roles y permisos</li>
                    <li class="lista_item"><span class="lista_punto"></span>Ver indicadores y recordatorios</li>
                </ul>
            </article>
        </div>
    </section>

    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
</body>
</html>