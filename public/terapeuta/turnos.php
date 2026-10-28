<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('terapeuta');

date_default_timezone_set('America/Argentina/Mendoza');

$paginaActual = 'turnos';

$fecha = $_GET['fecha'] ?? '';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $fecha = date('Y-m-d');
}

$stmt = $conn->prepare("SELECT t.hora, t.estado, p.id AS id_paciente, p.nombre AS paciente
                        FROM turnos t
                        JOIN pacientes p ON p.id = t.id_paciente
                        WHERE t.fecha = ? AND t.id_terapeuta = ?
                        ORDER BY t.hora");
$stmt->bind_param("si", $fecha, $_SESSION['id']);
$stmt->execute();
$turnos = $stmt->get_result();

$cantidad = $turnos->num_rows;

$dias  = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

$marca  = strtotime($fecha);
$titulo = $dias[date('w', $marca)] . ' ' . date('j', $marca) . ' de ' . $meses[date('n', $marca) - 1];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis turnos | Avanza</title>
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
            <h1 class="admin_titulo">Mis turnos</h1>
            <p class="admin_subtitulo">Tu agenda día por día</p>
        </div>
    </div>

    <form method="get" action="turnos.php" class="filtros">

        <div class="filtro_campo filtro_campo-grande">
            <label for="fecha" class="filtro_label">Fecha</label>
            <div class="filtro_input">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <input type="date" id="fecha" name="fecha" value="<?= e($fecha) ?>">
            </div>
        </div>

        <button type="submit" class="boton_verde">Filtrar</button>
        <a href="turnos.php" class="boton_gris">Hoy</a>

    </form>

    <div class="panel">

        <div class="hoy_cabecera">
            <h2 class="hoy_titulo"><?= $titulo ?></h2>
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
                <?php if ($cantidad === 0): ?>
                    <tr>
                        <td colspan="4" class="tabla_vacio">No tenés turnos para este día.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($t = $turnos->fetch_assoc()): ?>
                    <tr>
                        <td class="tabla_hora"><?= substr($t['hora'], 0, 5) ?></td>
                        <td class="tabla_profesional"><?= e($t['paciente']) ?></td>
                        <td>
                            <span class="badge badge_<?= e($t['estado']) ?>"><?= e(ucfirst($t['estado'])) ?></span>
                        </td>
                        <td class="tabla_acciones">
                            <div class="acciones">
                                <a href="paciente-ficha.php?id=<?= (int) $t['id_paciente'] ?>" class="boton_borde">Ver ficha</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</main>

</body>
</html>