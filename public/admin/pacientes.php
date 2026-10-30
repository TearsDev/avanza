<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$paginaActual = 'pacientes';

$buscar = trim($_GET['buscar'] ?? '');
$estado = $_GET['estado'] ?? '';

$like = '%' . $buscar . '%';

if ($estado === 'activo') {
    $activoFiltro = 1;
} elseif ($estado === 'inactivo') {
    $activoFiltro = 0;
} else {
    $activoFiltro = null;
}

$stmt = $conn->prepare("SELECT p.id, p.nombre, p.dni, p.diagnostico, p.activo,
                               TIMESTAMPDIFF(YEAR, p.nacimiento, CURDATE()) AS edad,
                               o.nombre AS obra_social
                        FROM pacientes p
                        LEFT JOIN obras_sociales o ON o.id = p.id_obra_social
                        WHERE (p.nombre LIKE ? OR p.dni LIKE ?)
                          AND (? IS NULL OR p.activo = ?)
                        ORDER BY p.id");
$stmt->bind_param("ssii", $like, $like, $activoFiltro, $activoFiltro);
$stmt->execute();
$pacientes = $stmt->get_result();

$cantidad = $pacientes->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/pacientes.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="admin_contenido">

    <div class="admin_encabezado">
        <div>
            <h1 class="admin_titulo">Pacientes</h1>
            <p class="admin_subtitulo">Registrá y administrá los pacientes del centro</p>
        </div>
        <a href="paciente-form.php" class="boton_coral">+ Nuevo paciente</a>
    </div>

    <form method="get" action="pacientes.php" class="filtros">

        <div class="filtro_campo filtro_campo-grande">
            <label for="buscar" class="filtro_label">Paciente</label>
            <div class="filtro_input">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" id="buscar" name="buscar" placeholder="Buscar por nombre o DNI" value="<?= e($buscar) ?>">
            </div>
        </div>

        <div class="filtro_campo">
            <label for="estado" class="filtro_label">Estado</label>
            <div class="filtro_input filtro_input-estado">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                <select id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <button type="submit" class="boton_verde">Filtrar</button>
        <a href="pacientes.php" class="boton_gris">Limpiar</a>

    </form>

    <div class="panel">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>DNI</th>
                    <th>Edad</th>
                    <th>Diagnóstico</th>
                    <th>Obra social</th>
                    <th class="col_estado">Estado</th>
                    <th class="tabla_acciones col_acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($cantidad === 0): ?>
                    <tr>
                        <td colspan="7" class="tabla_vacio">No se encontraron pacientes.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($p = $pacientes->fetch_assoc()): ?>
                    <tr>
                        <td class="tabla_paciente"><?= e($p['nombre']) ?></td>
                        <td class="tabla_gris"><?= e($p['dni']) ?></td>
                        <td class="tabla_gris"><?= (int) $p['edad'] ?> <?= (int) $p['edad'] === 1 ? 'año' : 'años' ?></td>
                        <td><?= e($p['diagnostico']) ?></td>
                        <td class="tabla_gris"><?= e($p['obra_social'] ?? '—') ?></td>
                        <td>
                            <?php if ($p['activo']): ?>
                                <span class="badge badge_activo">Activo</span>
                            <?php else: ?>
                                <span class="badge badge_inactivo">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="tabla_acciones">
                            <div class="acciones">
                                <a href="paciente-form.php?id=<?= (int) $p['id'] ?>" class="boton_icono" title="Editar">
                                    <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                </a>
                                <a href="paciente-eliminar.php?id=<?= (int) $p['id'] ?>" class="boton_icono boton_icono-borrar" title="Eliminar">
                                    <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="panel_pie">
            Mostrando <?= $cantidad ?> <?= $cantidad === 1 ? 'paciente' : 'pacientes' ?>
        </div>
    </div>

</main>

<script src="../assets/js/app.js"></script>
</body>
</html>