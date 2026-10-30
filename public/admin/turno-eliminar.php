<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("SELECT id FROM sesiones WHERE id_turno = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        header('Location: turno-form.php?id=' . $id . '&error=sesion');
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM turnos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header('Location: turnos.php');
    exit;
}

$stmt = $conn->prepare("SELECT t.fecha, t.hora,
                               p.nombre AS paciente,
                               u.nombre AS terapeuta
                        FROM turnos t
                        JOIN pacientes p ON p.id = t.id_paciente
                        JOIN usuarios u ON u.id = t.id_terapeuta
                        WHERE t.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$turno = $stmt->get_result()->fetch_assoc();

if (!$turno) {
    header('Location: turnos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar turno | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/alerta.css">
</head>
<body>

<main class="alerta_fondo">
    <form method="post" class="alerta_tarjeta">

        <input type="hidden" name="id" value="<?= $id ?>">

        <span class="alerta_icono">
            <svg class="icono" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </span>

        <div class="alerta_textos">
            <h1 class="alerta_titulo">¿Eliminar este turno?</h1>
            <p class="alerta_mensaje">Esta acción no se puede deshacer.</p>
        </div>

        <div class="alerta_resumen">
            <div class="alerta_dato">
                <span class="alerta_etiqueta">Paciente</span>
                <span class="alerta_valor"><?= e($turno['paciente']) ?></span>
            </div>
            <div class="alerta_dato">
                <span class="alerta_etiqueta">Profesional</span>
                <span class="alerta_valor"><?= e($turno['terapeuta']) ?></span>
            </div>
            <div class="alerta_dato">
                <span class="alerta_etiqueta">Fecha y hora</span>
                <span class="alerta_valor"><?= date('d/m/Y', strtotime($turno['fecha'])) ?> · <?= substr($turno['hora'], 0, 5) ?></span>
            </div>
        </div>

        <div class="alerta_botones">
            <a href="turnos.php" class="boton_gris">Cancelar</a>
            <button type="submit" class="boton_coral">Sí, eliminar</button>
        </div>

    </form>
</main>

<script src="../assets/js/app.js"></script>
</body>
</html>