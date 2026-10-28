<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$paginaActual = 'terapeutas';

$q = trim($_GET['q'] ?? '');
$estado = $_GET['estado'] ?? '';

$busqueda = '%' . $q . '%';
$activo = ($estado === '1' || $estado === '0') ? (int) $estado : null;

$stmt = $conn->prepare("SELECT u.id, u.nombre, u.activo, t.matricula, t.telefono,
                               e.nombre AS especialidad,
                               (SELECT COUNT(*) FROM asignaciones a WHERE a.id_terapeuta = t.id_usuario) AS pacientes
                        FROM terapeutas t
                        JOIN usuarios u ON u.id = t.id_usuario
                        JOIN especialidades e ON e.id = t.id_especialidad
                        WHERE (u.nombre LIKE ? OR t.matricula LIKE ?)
                          AND (? IS NULL OR u.activo = ?)
                        ORDER BY u.nombre");
$stmt->bind_param("ssii", $busqueda, $busqueda, $activo, $activo);
$stmt->execute();
$terapeutas = $stmt->get_result();

$total = $terapeutas->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terapeutas | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/terapeutas.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="admin_contenido">

    <div class="admin_encabezado">
        <div>
            <h1 class="admin_titulo">Terapeutas</h1>
            <p class="admin_subtitulo">Gestioná los profesionales, sus horarios y sus pacientes asignados</p>
        </div>
        <a href="terapeuta-form.php" class="boton_coral">+ Nuevo terapeuta</a>
    </div>

    <form method="get" class="filtros">
        <div class="filtro_campo filtro_campo-grande">
            <label for="q" class="filtro_label">Terapeuta</label>
            <div class="filtro_input">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="q" name="q" placeholder="Buscar por nombre o matrícula" value="<?= e($q) ?>">
            </div>
        </div>

        <div class="filtro_campo">
            <label for="estado" class="filtro_label">Estado</label>
            <div class="filtro_input filtro_input-estado">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                <select id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="1" <?= $estado === '1' ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= $estado === '0' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <button type="submit" class="boton_verde">Filtrar</button>
        <a href="terapeutas.php" class="boton_gris">Limpiar</a>
    </form>

    <div class="panel">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Profesional</th>
                    <th>Especialidad</th>
                    <th>Matrícula</th>
                    <th>Teléfono</th>
                    <th>Pacientes</th>
                    <th>Estado</th>
                    <th class="col_acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total === 0): ?>
                    <tr>
                        <td colspan="7" class="tabla_vacio">No se encontraron terapeutas.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($t = $terapeutas->fetch_assoc()): ?>
                    <tr>
                        <td class="tabla_profesional"><?= e($t['nombre']) ?></td>
                        <td><?= e($t['especialidad']) ?></td>
                        <td class="tabla_gris"><?= e($t['matricula']) ?></td>
                        <td class="tabla_gris"><?= e($t['telefono']) ?></td>
                        <td class="tabla_gris"><?= (int) $t['pacientes'] ?> <?= $t['pacientes'] == 1 ? 'asignado' : 'asignados' ?></td>
                        <td>
                            <?php if ($t['activo']): ?>
                                <span class="badge_activo">Activo</span>
                            <?php else: ?>
                                <span class="badge_inactivo">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="col_acciones">
                            <div class="acciones">
                                <a href="terapeuta-form.php?id=<?= (int) $t['id'] ?>" class="boton_icono" title="Editar">
                                    <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </a>
                                <a href="terapeuta-eliminar.php?id=<?= (int) $t['id'] ?>" class="boton_icono boton_icono-borrar" title="Eliminar">
                                    <svg class="icono icono-chico" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="panel_pie">Mostrando <?= $total ?> terapeutas</div>
    </div>

</main>

</body>
</html>