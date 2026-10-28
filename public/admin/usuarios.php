<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$paginaActual = 'usuarios';

$q = trim($_GET['q'] ?? '');
$rol = $_GET['rol'] ?? '';

$busqueda = '%' . $q . '%';
$rolFiltro = ($rol === 'administrativo' || $rol === 'terapeuta') ? $rol : null;

$stmt = $conn->prepare("SELECT id, usuario, nombre, rol, activo
                        FROM usuarios
                        WHERE (usuario LIKE ? OR nombre LIKE ?)
                          AND (? IS NULL OR rol = ?)
                        ORDER BY (rol = 'administrativo') DESC, id");
$stmt->bind_param("ssss", $busqueda, $busqueda, $rolFiltro, $rolFiltro);
$stmt->execute();
$usuarios = $stmt->get_result();

$total = $usuarios->num_rows;
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

<?php require_once __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="admin_contenido">

    <div class="admin_encabezado">
        <div>
            <h1 class="admin_titulo">Usuarios</h1>
            <p class="admin_subtitulo">Quién puede ingresar al sistema, con qué rol y si está activo</p>
        </div>
        <a href="usuario-form.php" class="boton_coral">+ Nuevo administrativo</a>
    </div>

    <form method="get" class="filtros">
        <div class="filtro_campo filtro_campo-grande">
            <label for="q" class="filtro_label">Usuario</label>
            <div class="filtro_input">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="q" name="q" placeholder="Buscar por usuario o nombre" value="<?= e($q) ?>">
            </div>
        </div>

        <div class="filtro_campo">
            <label for="rol" class="filtro_label">Rol</label>
            <div class="filtro_input filtro_input-estado">
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

    <div class="panel">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="col_acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total === 0): ?>
                    <tr>
                        <td colspan="5" class="tabla_vacio">No se encontraron usuarios.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($u = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td class="tabla_profesional"><?= e($u['usuario']) ?></td>
                        <td><?= e($u['nombre']) ?></td>
                        <td>
                            <?php if ($u['rol'] === 'administrativo'): ?>
                                <span class="badge_rol-administrativo">Administrativo</span>
                            <?php else: ?>
                                <span class="badge_rol-terapeuta">Terapeuta</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['activo']): ?>
                                <span class="badge_activo">Activo</span>
                            <?php else: ?>
                                <span class="badge_inactivo">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="col_acciones">
                            <div class="acciones">
                                <a href="usuario-form.php?id=<?= (int) $u['id'] ?>" class="boton_icono" title="Editar">
                                    <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="panel_pie">
            Mostrando <?= $total ?> <?= $total === 1 ? 'usuario' : 'usuarios' ?>
        </div>
    </div>

</main>

</body>
</html>