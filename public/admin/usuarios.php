<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$paginaActual = 'usuarios';

$q = trim($_GET['q'] ?? '');
$rol = $_GET['rol'] ?? '';

if (!in_array($rol, ['administrativo', 'terapeuta'], true)) {
    $rol = '';
}

$busqueda = '%' . $q . '%';

$stmt = $conn->prepare("
    SELECT id, usuario, nombre, rol, activo
    FROM usuarios
    WHERE (usuario LIKE ? OR nombre LIKE ?)
      AND (? = '' OR rol = ?)
    ORDER BY (rol = 'administrativo') DESC, id ASC
");
$stmt->bind_param("ssss", $busqueda, $busqueda, $rol, $rol);
$stmt->execute();
$usuarios = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total = count($usuarios);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/usuarios.css">
</head>
<body>

<?php require __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="usuarios_contenido">

    <div class="usuarios_encabezado">
        <div class="usuarios_titulos">
            <h1 class="usuarios_titulo">Usuarios</h1>
            <p class="usuarios_subtitulo">Quién puede ingresar al sistema, con qué rol y si está activo</p>
        </div>
        <a href="usuario-form.php" class="boton_coral">+ Nuevo administrativo</a>
    </div>

    <form method="get" class="usuarios_filtros">

        <div class="usuarios_campo usuarios_campo-ancho">
            <label for="q" class="usuarios_label">Usuario</label>
            <div class="usuarios_control">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="q" name="q" placeholder="Buscar por usuario o nombre" value="<?= e($q) ?>">
            </div>
        </div>

        <div class="usuarios_campo usuarios_campo-rol">
            <label for="rol" class="usuarios_label">Rol</label>
            <div class="usuarios_control">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                <select id="rol" name="rol">
                    <option value="">Todos</option>
                    <option value="administrativo" <?= $rol === 'administrativo' ? 'selected' : '' ?>>Administrativo</option>
                    <option value="terapeuta" <?= $rol === 'terapeuta' ? 'selected' : '' ?>>Terapeuta</option>
                </select>
            </div>
        </div>

        <button type="submit" class="boton_verde">Filtrar</button>
        <a href="usuarios.php" class="boton_gris">Limpiar</a>

    </form>

    <div class="usuarios_tabla">

        <div class="usuarios_fila usuarios_fila-encabezado">
            <span>Usuario</span>
            <span>Nombre</span>
            <span>Rol</span>
            <span>Estado</span>
            <span class="usuarios_derecha">Acciones</span>
        </div>

        <?php foreach ($usuarios as $u): ?>
            <div class="usuarios_fila">
                <span class="usuarios_usuario"><?= e($u['usuario']) ?></span>
                <span><?= e($u['nombre']) ?></span>
                <span>
                    <?php if ($u['rol'] === 'administrativo'): ?>
                        <span class="usuarios_badge usuarios_badge-coral">Administrativo</span>
                    <?php else: ?>
                        <span class="usuarios_badge usuarios_badge-verde">Terapeuta</span>
                    <?php endif; ?>
                </span>
                <span>
                    <?php if ($u['activo']): ?>
                        <span class="usuarios_badge usuarios_badge-verde">Activo</span>
                    <?php else: ?>
                        <span class="usuarios_badge usuarios_badge-gris">Inactivo</span>
                    <?php endif; ?>
                </span>
                <span class="usuarios_derecha">
                    <a href="usuario-form.php?id=<?= (int) $u['id'] ?>" class="usuarios_editar" aria-label="Editar usuario">
                        <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                    </a>
                </span>
            </div>
        <?php endforeach; ?>

        <div class="usuarios_pie">
            Mostrando <?= $total ?> <?= $total === 1 ? 'usuario' : 'usuarios' ?>
        </div>

    </div>

</main>

</body>
</html>