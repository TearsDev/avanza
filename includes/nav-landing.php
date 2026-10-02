<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$paginaActual = $paginaActual ?? '';
?>

<header class="nav-landing">
    <a href="index.php" class="nav-landing_logo">
        <img src="../assets/img/Avanza-logo.png" alt="AVANZA">
    </a>
    <nav class="nav-landing_central">
        <a href="index.php" class="nav-landing_link <?= $paginaActual === 'inicio' ? 'nav-landing_link-activo' : '' ?>">Inicio</a>
        <a href="como-usar.php" class="nav-landing_link <?= $paginaActual === 'como-usar' ? 'nav-landing_link-activo' : '' ?>">Cómo usar</a>
        <a href="contacto.php" class="nav-landing_link <?= $paginaActual === 'contacto' ? 'nav-landing_link-activo' : '' ?>">Contacto</a>
    </nav>
    <div class="nav-landing_derecha">
        <a href="contacto.php" class="btn btn-outline">Contacto</a>
        <a href="login.php" class="btn btn-primario">Iniciar Sesion</a>
    </div>
</header>