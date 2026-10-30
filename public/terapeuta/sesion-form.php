<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('terapeuta');

$paginaActual = 'inicio';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT t.id, t.fecha, t.hora, t.estado, p.nombre AS paciente,
                               s.id AS id_sesion, s.notas
                        FROM turnos t
                        JOIN pacientes p ON p.id = t.id_paciente
                        LEFT JOIN sesiones s ON s.id_turno = t.id
                        WHERE t.id = ? AND t.id_terapeuta = ?");
$stmt->bind_param("ii", $id, $_SESSION['id']);
$stmt->execute();
$turno = $stmt->get_result()->fetch_assoc();

if (!$turno || $turno['estado'] === 'cancelado') {
    header('Location: index.php');
    exit;
}

$editando = $turno['estado'] === 'realizado';

if ($editando && $turno['id_sesion'] === null) {
    header('Location: index.php');
    exit;
}

$notas = $editando ? $turno['notas'] : '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notas = trim($_POST['notas'] ?? '');

    if ($notas === '') {
        $error = 'Escribí las notas de la sesión.';
    } elseif ($editando) {
        $stmt = $conn->prepare("UPDATE sesiones SET notas = ? WHERE id_turno = ?");
        $stmt->bind_param("si", $notas, $id);
        $stmt->execute();

        header('Location: sesion-ver.php?id=' . $id);
        exit;
    } else {
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO sesiones (id_turno, notas) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $notas);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE turnos SET estado = 'realizado' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $conn->commit();

        header('Location: index.php');
        exit;
    }
}

$titulo     = $editando ? 'Editar sesión' : 'Registrar sesión';
$subtitulo  = $editando ? 'Modificá la nota de esta sesión' : 'Anotá cómo fue la sesión y marcala como realizada';
$textoBoton = $editando ? 'Guardar cambios' : 'Guardar y marcar como realizada';
$urlCancelar = $editando ? 'sesion-ver.php?id=' . $id : 'index.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/formulario.css">
    <link rel="stylesheet" href="../assets/css/terapeuta.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-terapeuta.php'; ?>

<main class="admin_contenido">

    <div class="sesion_contenedor">

        <a href="index.php" class="volver">
            <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            Volver al inicio
        </a>

        <div>
            <h1 class="admin_titulo"><?= $titulo ?></h1>
            <p class="admin_subtitulo"><?= $subtitulo ?></p>
        </div>

        <div class="sesion_resumen">
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Paciente</span>
                <span class="ficha_valor"><?= e($turno['paciente']) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Fecha</span>
                <span class="ficha_valor"><?= date('d/m/Y', strtotime($turno['fecha'])) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Hora</span>
                <span class="ficha_valor"><?= substr($turno['hora'], 0, 5) ?></span>
            </div>
        </div>

        <form method="post" class="sesion_form">

            <?php if ($error !== ''): ?>
                <p class="formulario_error"><?= e($error) ?></p>
            <?php endif; ?>

            <div class="sesion_tarjeta">
                <div class="campo">
                    <label for="notas" class="campo_label">Notas de la sesión</label>
                    <textarea id="notas" name="notas" class="sesion_notas" placeholder="Escribí acá la evolución, los ejercicios realizados y las indicaciones para la próxima sesión..."><?= e($notas) ?></textarea>
                </div>
            </div>

            <div class="sesion_acciones">
                <a href="<?= $urlCancelar ?>" class="boton_gris">Cancelar</a>
                <button type="submit" class="boton_coral"><?= $textoBoton ?></button>
            </div>

        </form>

    </div>

</main>

<script src="../assets/js/app.js"></script>
</body>
</html>