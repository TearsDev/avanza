<?php

$links = [
    'inicio'     => ['index.php', 'Inicio'],
    'turnos'     => ['turnos.php', 'Turnos'],
    'pacientes'  => ['pacientes.php', 'Pacientes'],
    'terapeutas' => ['terapeutas.php', 'Terapeutas'],
    'usuarios'   => ['usuarios.php', 'Usuarios'],
    'centro'     => ['centro.php', 'Centro'],
];

$iniciales = strtoupper(mb_substr($_SESSION['nombre'], 0, 2));
?>
<header class="nav_admin">
    <div class="nav_admin-izquierdo">
        <a href="index.php">
            <img src="../assets/img/Avanza-Logo.png" alt="Avanza" class="nav_admin-logo">
        </a>
    </div>
    <nav class="nav_admin-links">
        <?php foreach ($links as $clave => $link): ?>
            <a href="<?= $link[0] ?>"
               class="nav_admin-link<?= $paginaActual === $clave ? ' nav_admin-link-activo' : '' ?>">
                <?= $link[1] ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="nav_admin-derecho">
        <div class="nav_admin-usuario">
            <span class="nav_admin-avatar"><?= e($iniciales) ?></span>
            <span class="nav_admin-nombre"><?= e($_SESSION['nombre']) ?></span>
        </div>
        <a href="../logout.php" class="boton_borde">Cerrar sesión</a>
    </div>
</header>