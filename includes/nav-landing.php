<?php
require_once __DIR__ . '/auth.php';
$paginaActual = $paginaActual ?? '';
?>

<header class="nav_landing">
    <a href="index.php" class="nav_landing-logo">
        <img src="../assets/img/Avanza-logo.png" alt="AVANZA">
    </a>
    <nav class="nav_landing-central">
        <a href="index.php" class="nav_landing-link <?= $paginaActual === 'inicio' ? 'nav_landing-link-activo' : '' ?>">Inicio</a>
        <a href="como-usar.php" class="nav_landing-link <?= $paginaActual === 'como-usar' ? 'nav_landing-link-activo' : '' ?>">Cómo usar</a>
        <a href="contacto.php" class="nav_landing-link <?= $paginaActual === 'contacto' ? 'nav_landing-link-activo' : '' ?>">Contacto</a>
    </nav>
    <div class="nav_landing-derecha">
        <a href="contacto.php" class="btn btn_outline">Contacto</a>
        <a href="login.php" class="btn btn_primario">Iniciar Sesion</a>
    </div>
</header>