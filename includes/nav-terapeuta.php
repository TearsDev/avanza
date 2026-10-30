<?php

$links = [
    'inicio'    => ['index.php', 'Inicio'],
    'turnos'    => ['turnos.php', 'Mis turnos'],
    'pacientes' => ['pacientes.php', 'Mis pacientes'],
];

$stmtNav = $conn->prepare("
    SELECT e.nombre AS especialidad
    FROM terapeutas t
    JOIN especialidades e ON e.id = t.id_especialidad
    WHERE t.id_usuario = ?
");
$stmtNav->bind_param("i", $_SESSION['id']);
$stmtNav->execute();
$especialidadNav = $stmtNav->get_result()->fetch_assoc()['especialidad'] ?? '';

$partesNombre = preg_split('/\s+/', trim($_SESSION['nombre']));
$iniciales = mb_strtoupper(mb_substr($partesNombre[0], 0, 1) . mb_substr($partesNombre[1] ?? '', 0, 1));
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
            <div class="nav_admin-datos">
                <span class="nav_admin-nombre"><?= e($_SESSION['nombre']) ?></span>
                <span class="nav_admin-especialidad"><?= e($especialidadNav) ?></span>
            </div>
        </div>
        <a href="../logout.php" class="boton_borde">Cerrar sesión</a>
    </div>
</header>