<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$paginaActual = 'turnos';

$fecha  = $_GET['fecha'] ?? '';
$buscar = trim($_GET['buscar'] ?? '');

$fechaFiltro = ($fecha !== '') ? $fecha : null;
$like = '%' . $buscar . '%';

$stmt = $conn->prepare("SELECT t.id, t.fecha, t.hora, t.estado,
                               p.nombre AS paciente,
                               u.nombre AS terapeuta
                        FROM turnos t
                        JOIN pacientes p ON p.id = t.id_paciente
                        JOIN usuarios u ON u.id = t.id_terapeuta
                        WHERE (? IS NULL OR t.fecha = ?)
                          AND (p.nombre LIKE ? OR p.dni LIKE ?)
                        ORDER BY t.fecha, t.hora");
$stmt->bind_param("ssss", $fechaFiltro, $fechaFiltro, $like, $like);
$stmt->execute();
$turnos = $stmt->get_result();

$cantidad = $turnos->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/turnos.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="admin_contenido">

    <div class="admin_encabezado">
        <div>
            <h1 class="admin_titulo">Turnos</h1>
            <p class="admin_subtitulo">Creá, editá y reprogramá los turnos del centro</p>
        </div>
        <a href="turno-form.php" class="boton_coral">+ Nuevo turno</a>
    </div>

    <form method="get" action="turnos.php" class="filtros">

        <div class="filtro_campo">
            <label for="fecha" class="filtro_label">Fecha</label>
            <div class="filtro_input filtro_input-fecha">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <input type="date" id="fecha" name="fecha" value="<?= e($fecha) ?>">
            </div>
        </div>

        <div class="filtro_campo filtro_campo-grande">
            <label for="buscar" class="filtro_label">Paciente</label>
            <div class="filtro_input">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" id="buscar" name="buscar" placeholder="Buscar por nombre o DNI" value="<?= e($buscar) ?>">
            </div>
        </div>

        <button type="submit" class="boton_verde">Filtrar</button>
        <a href="turnos.php" class="boton_gris">Limpiar</a>

    </form>

    <div class="panel">
        <table class="tabla">
            <thead>
                <tr>
                    <th class="col_fecha">Fecha</th>
                    <th class="col_hora">Hora</th>
                    <th>Paciente</th>
                    <th>Profesional</th>
                    <th class="col_estado">Estado</th>
                    <th class="tabla_acciones col_acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($cantidad === 0): ?>
                    <tr>
                        <td colspan="6" class="tabla_vacio">No se encontraron turnos.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($t = $turnos->fetch_assoc()): ?>
                    <tr>
                        <td class="tabla_fecha"><?= date('d/m/Y', strtotime($t['fecha'])) ?></td>
                        <td class="tabla_hora"><?= substr($t['hora'], 0, 5) ?></td>
                        <td><?= e($t['paciente']) ?></td>
                        <td class="tabla_profesional"><?= e($t['terapeuta']) ?></td>
                        <td>
                            <span class="badge badge_<?= e($t['estado']) ?>"><?= e(ucfirst($t['estado'])) ?></span>
                        </td>
                        <td class="tabla_acciones">
                            <div class="acciones">
                                <a href="turno-form.php?id=<?= (int) $t['id'] ?>" class="boton_icono" title="Editar">
                                    <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                </a>
                                <a href="turno-eliminar.php?id=<?= (int) $t['id'] ?>" class="boton_icono boton_icono-borrar" title="Eliminar">
                                    <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="panel_pie">
            Mostrando <?= $cantidad ?> <?= $cantidad === 1 ? 'turno' : 'turnos' ?>
        </div>
    </div>

</main>

<script src="../assets/js/app.js"></script>
</body>
</html>