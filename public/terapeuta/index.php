<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('terapeuta');

date_default_timezone_set('America/Argentina/Mendoza');

$paginaActual = 'inicio';

$primerNombre = explode(' ', trim($_SESSION['nombre']))[0];
$hoy = date('Y-m-d');

$stmt = $conn->prepare("SELECT t.id, t.hora, t.estado, p.nombre AS paciente
                        FROM turnos t
                        JOIN pacientes p ON p.id = t.id_paciente
                        WHERE t.id_terapeuta = ? AND t.fecha = ?
                        ORDER BY t.hora");
$stmt->bind_param("is", $_SESSION['id'], $hoy);
$stmt->execute();
$turnos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total = count($turnos);
$sinRegistrar = 0;

foreach ($turnos as $t) {
    if ($t['estado'] === 'pendiente' || $t['estado'] === 'confirmado') {
        $sinRegistrar++;
    }
}

$dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$fechaTexto = ucfirst($dias[(int) date('w')]) . ' ' . date('j') . ' de ' . $meses[(int) date('n') - 1];

if ($total === 0) {
    $resumen = 'No tenés turnos hoy';
} else {
    $resumen = 'Tenés ' . $total . ($total === 1 ? ' turno' : ' turnos') . ' hoy';
    if ($sinRegistrar > 0) {
        $resumen .= ', ' . $sinRegistrar . ' sin registrar';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/turnos.css">
    <link rel="stylesheet" href="../assets/css/terapeuta.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-terapeuta.php'; ?>

<main class="admin_contenido">

    <div class="admin_encabezado">
        <div>
            <h1 class="admin_titulo">Hola, <?= e($primerNombre) ?></h1>
            <p class="admin_subtitulo"><?= $fechaTexto ?> · <?= $resumen ?></p>
        </div>
    </div>

    <div class="panel">
        <div class="hoy_cabecera">
            <h2 class="hoy_titulo">Mis turnos de hoy</h2>
        </div>

        <table class="tabla">
            <thead>
                <tr>
                    <th class="col_hora">Hora</th>
                    <th>Paciente</th>
                    <th class="col_estado">Estado</th>
                    <th class="tabla_acciones col_acciones">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total === 0): ?>
                    <tr>
                        <td colspan="4" class="tabla_vacio">No tenés turnos para hoy.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($turnos as $t): ?>
                    <tr>
                        <td class="tabla_hora"><?= e(substr($t['hora'], 0, 5)) ?></td>
                        <td class="tabla_profesional"><?= e($t['paciente']) ?></td>
                        <td>
                            <span class="badge badge_<?= e($t['estado']) ?>"><?= e(ucfirst($t['estado'])) ?></span>
                        </td>
                        <td class="tabla_acciones">
                            <div class="acciones">
                                <?php if ($t['estado'] === 'realizado'): ?>
                                    <a href="sesion-ver.php?id=<?= (int) $t['id'] ?>" class="boton_borde">Ver sesión</a>
                                <?php elseif ($t['estado'] === 'cancelado'): ?>
                                    <span class="tabla_gris">—</span>
                                <?php else: ?>
                                    <a href="sesion-form.php?id=<?= (int) $t['id'] ?>" class="boton_verde">Registrar sesión</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</main>

<script src="../assets/js/app.js"></script>
</body>
</html>