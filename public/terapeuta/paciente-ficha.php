<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('terapeuta');

$paginaActual = 'pacientes';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT p.id, p.nombre, p.dni, p.nacimiento, p.diagnostico,
                               p.resp_nombre, p.resp_vinculo, p.resp_telefono, p.resp_email,
                               o.nombre AS obra_social,
                               TIMESTAMPDIFF(YEAR, p.nacimiento, CURDATE()) AS edad
                        FROM asignaciones a
                        JOIN pacientes p ON p.id = a.id_paciente
                        JOIN obras_sociales o ON o.id = p.id_obra_social
                        WHERE a.id_terapeuta = ? AND p.id = ?");
$stmt->bind_param("ii", $_SESSION['id'], $id);
$stmt->execute();
$paciente = $stmt->get_result()->fetch_assoc();

if (!$paciente) {
    header('Location: pacientes.php');
    exit;
}

$stmt = $conn->prepare("SELECT t.id, t.fecha, t.hora, t.estado, s.notas
                        FROM turnos t
                        LEFT JOIN sesiones s ON s.id_turno = t.id
                        WHERE t.id_paciente = ? AND t.id_terapeuta = ?
                        ORDER BY t.fecha DESC, t.hora DESC");
$stmt->bind_param("ii", $id, $_SESSION['id']);
$stmt->execute();
$sesiones = $stmt->get_result();

$total = $sesiones->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($paciente['nombre']) ?> | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/formulario.css">
    <link rel="stylesheet" href="../assets/css/turnos.css">
    <link rel="stylesheet" href="../assets/css/terapeuta.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-terapeuta.php'; ?>

<main class="admin_contenido">

    <a href="pacientes.php" class="volver">
        <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        Volver a mis pacientes
    </a>

    <div class="admin_encabezado">
        <div>
            <h1 class="admin_titulo"><?= e($paciente['nombre']) ?></h1>
            <p class="admin_subtitulo"><?= (int) $paciente['edad'] ?> <?= $paciente['edad'] == 1 ? 'año' : 'años' ?> · <?= e($paciente['diagnostico']) ?></p>
        </div>
    </div>

    <div class="ficha_tarjetas">

        <section class="ficha_tarjeta">
            <h2 class="ficha_titulo">Datos del paciente</h2>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">DNI</span>
                <span class="ficha_valor"><?= number_format((int) $paciente['dni'], 0, '', '.') ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Fecha de nacimiento</span>
                <span class="ficha_valor"><?= date('d/m/Y', strtotime($paciente['nacimiento'])) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Diagnóstico</span>
                <span class="ficha_valor"><?= e($paciente['diagnostico']) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Obra social</span>
                <span class="ficha_valor"><?= e($paciente['obra_social']) ?></span>
            </div>
        </section>

        <section class="ficha_tarjeta">
            <h2 class="ficha_titulo">Responsable</h2>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Nombre</span>
                <span class="ficha_valor"><?= e($paciente['resp_nombre']) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Parentesco</span>
                <span class="ficha_valor"><?= e($paciente['resp_vinculo']) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Teléfono</span>
                <span class="ficha_valor"><?= e($paciente['resp_telefono']) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Email</span>
                <span class="ficha_valor"><?= e($paciente['resp_email']) ?></span>
            </div>
        </section>

    </div>

    <div class="panel">
        <div class="hoy_cabecera">
            <h2 class="hoy_titulo">Sesiones</h2>
        </div>

        <table class="tabla">
            <thead>
                <tr>
                    <th class="col_fecha">Fecha</th>
                    <th class="col_hora">Hora</th>
                    <th class="col_estado">Estado</th>
                    <th>Nota</th>
                    <th class="tabla_acciones col_acciones">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total === 0): ?>
                    <tr>
                        <td colspan="5" class="tabla_vacio">Todavía no hay sesiones.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($s = $sesiones->fetch_assoc()): ?>
                    <tr>
                        <td class="tabla_fecha"><?= date('d/m/Y', strtotime($s['fecha'])) ?></td>
                        <td class="tabla_hora"><?= substr($s['hora'], 0, 5) ?></td>
                        <td>
                            <span class="badge badge_<?= e($s['estado']) ?>"><?= e(ucfirst($s['estado'])) ?></span>
                        </td>
                        <td class="tabla_gris">
                            <?= $s['notas'] ? e(mb_strimwidth($s['notas'], 0, 53, '...')) : '—' ?>
                        </td>
                        <td class="tabla_acciones">
                            <div class="acciones">
                                <?php if ($s['estado'] === 'realizado'): ?>
                                    <a href="sesion-ver.php?id=<?= (int) $s['id'] ?>" class="boton_borde">Ver sesión</a>
                                <?php elseif ($s['estado'] === 'cancelado'): ?>
                                    <span class="tabla_gris">—</span>
                                <?php else: ?>
                                    <a href="sesion-form.php?id=<?= (int) $s['id'] ?>" class="boton_verde">Registrar sesión</a>
                                <?php endif; ?>
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