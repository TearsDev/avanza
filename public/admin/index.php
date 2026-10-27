<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

date_default_timezone_set('America/Argentina/Buenos_Aires');

$paginaActual = 'inicio';

$dias  = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio',
          'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

$hoy = date('Y-m-d');
$fechaTexto = $dias[date('w')] . ' ' . date('j') . ' de ' . $meses[date('n') - 1] . ' de ' . date('Y');

/* ---------- KPI 1: turnos de hoy ---------- */
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM turnos WHERE fecha = ?");
$stmt->bind_param("s", $hoy);
$stmt->execute();
$turnosHoy = $stmt->get_result()->fetch_assoc()['total'];

/* ---------- KPI 2: pacientes activos ---------- */
$res = $conn->query("SELECT COUNT(*) AS total FROM pacientes WHERE activo = 1");
$pacientesActivos = $res->fetch_assoc()['total'];

/* ---------- KPI 3: terapeutas activos ---------- */
$res = $conn->query("SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'terapeuta' AND activo = 1");
$terapeutasActivos = $res->fetch_assoc()['total'];

/* ---------- KPI 4: turnos sin registrar ---------- */
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM turnos
                        WHERE fecha < ? AND estado IN ('pendiente', 'confirmado')");
$stmt->bind_param("s", $hoy);
$stmt->execute();
$sinRegistrar = $stmt->get_result()->fetch_assoc()['total'];

/* ---------- Gráfico: turnos de la semana (lunes a viernes) ---------- */
$lunes   = date('Y-m-d', strtotime('monday this week'));
$viernes = date('Y-m-d', strtotime('friday this week'));

$stmt = $conn->prepare("SELECT fecha, COUNT(*) AS total FROM turnos
                        WHERE fecha BETWEEN ? AND ?
                        GROUP BY fecha");
$stmt->bind_param("ss", $lunes, $viernes);
$stmt->execute();
$resultado = $stmt->get_result();

$porFecha = [];
while ($fila = $resultado->fetch_assoc()) {
    $porFecha[$fila['fecha']] = $fila['total'];
}

$nombresDias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie'];
$semana = [];
$maximo = 1;

for ($i = 0; $i < 5; $i++) {
    $fecha = date('Y-m-d', strtotime($lunes . ' +' . $i . ' days'));
    $total = $porFecha[$fecha] ?? 0;

    $semana[] = [
        'dia'   => $nombresDias[$i],
        'fecha' => $fecha,
        'total' => $total,
    ];

    if ($total > $maximo) {
        $maximo = $total;
    }
}

/* ---------- Tabla: turnos de hoy ---------- */
$stmt = $conn->prepare("SELECT t.id, t.hora, t.estado,
                               p.nombre AS paciente,
                               u.nombre AS terapeuta
                        FROM turnos t
                        JOIN pacientes p ON p.id = t.id_paciente
                        JOIN usuarios u ON u.id = t.id_terapeuta
                        WHERE t.fecha = ?
                        ORDER BY t.hora
                        LIMIT 6");
$stmt->bind_param("s", $hoy);
$stmt->execute();
$turnos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de administración | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="admin_contenido">

    <!-- Encabezado -->
    <div class="admin_encabezado">
        <div>
            <h1 class="admin_titulo">Panel de administración</h1>
            <p class="admin_subtitulo"><?= $fechaTexto ?> · Resumen del día</p>
        </div>
        <div class="admin_acciones">
            <a href="paciente-form.php" class="boton_borde">+ Nuevo paciente</a>
            <a href="turno-form.php" class="boton_coral">+ Nuevo turno</a>
        </div>
    </div>

    <!-- KPIs -->
    <section class="kpis">

        <div class="kpi">
            <div class="kpi_encabezado">
                <span class="kpi_icono">
                    <svg class="icono" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </span>
                <span class="kpi_label">Turnos hoy</span>
            </div>
            <div class="kpi_pie">
                <span class="kpi_valor"><?= (int) $turnosHoy ?></span>
                <a href="turnos.php" class="kpi_link">Ver turnos →</a>
            </div>
        </div>

        <div class="kpi">
            <div class="kpi_encabezado">
                <span class="kpi_icono">
                    <svg class="icono" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <span class="kpi_label">Pacientes activos</span>
            </div>
            <div class="kpi_pie">
                <span class="kpi_valor"><?= (int) $pacientesActivos ?></span>
                <a href="pacientes.php" class="kpi_link">Ver pacientes →</a>
            </div>
        </div>

        <div class="kpi">
            <div class="kpi_encabezado">
                <span class="kpi_icono">
                    <svg class="icono" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                </span>
                <span class="kpi_label">Profesionales activos</span>
            </div>
            <div class="kpi_pie">
                <span class="kpi_valor"><?= (int) $terapeutasActivos ?></span>
                <a href="terapeutas.php" class="kpi_link">Ver terapeutas →</a>
            </div>
        </div>

        <div class="kpi kpi-alerta">
            <div class="kpi_encabezado">
                <span class="kpi_icono kpi_icono-coral">
                    <svg class="icono" viewBox="0 0 24 24"><path d="M12 7v6M12 17h.01"/></svg>
                </span>
                <span class="kpi_label">Turnos sin registrar</span>
            </div>
            <div class="kpi_pie">
                <span class="kpi_valor kpi_valor-coral"><?= (int) $sinRegistrar ?></span>
                <a href="turnos.php" class="kpi_link kpi_link-coral">Revisar →</a>
            </div>
        </div>

    </section>

    <!-- Gráfico + tabla -->
    <section class="fila_principal">

        <!-- Turnos de la semana -->
        <div class="panel panel-grafico">
            <div class="panel_cabecera">
                <h2 class="panel_titulo">Turnos de la semana</h2>
                <span class="panel_rango">Lun–Vie</span>
            </div>
            <div class="grafico">
                <?php foreach ($semana as $d): ?>
                    <?php
                    $esHoy = ($d['fecha'] === $hoy);
                    $alto  = round($d['total'] / $maximo * 270);
                    ?>
                    <div class="barra_col">
                        <span class="barra_valor"><?= (int) $d['total'] ?></span>
                        <div class="barra<?= $esHoy ? ' barra-hoy' : '' ?>" style="height: <?= $alto ?>px"></div>
                        <span class="barra_dia<?= $esHoy ? ' barra_dia-hoy' : '' ?>"><?= $d['dia'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Turnos de hoy -->
        <div class="panel panel-tabla">
            <div class="panel_cabecera">
                <h2 class="panel_titulo">Turnos de hoy</h2>
                <a href="turnos.php" class="panel_link">Ver todos →</a>
            </div>

            <table class="tabla">
                <thead>
                    <tr>
                        <th class="col_hora">Hora</th>
                        <th>Paciente</th>
                        <th>Profesional</th>
                        <th class="col_estado">Estado</th>
                        <th class="tabla_acciones col_acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($turnos->num_rows === 0): ?>
                        <tr>
                            <td colspan="5" class="tabla_vacio">No hay turnos para hoy.</td>
                        </tr>
                    <?php endif; ?>

                    <?php while ($t = $turnos->fetch_assoc()): ?>
                        <tr>
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
        </div>
    </section>
</main>
</body>
</html>