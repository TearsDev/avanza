<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT nombre FROM especialidades WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$especialidad = $stmt->get_result()->fetch_assoc();

if (!$especialidad) {
    header('Location: centro.php');
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM terapeutas WHERE id_especialidad = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$enUso = $stmt->get_result()->fetch_assoc()['total'] > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$enUso) {
    $stmt = $conn->prepare("DELETE FROM especialidades WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header('Location: centro.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar especialidad | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/alerta.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
</head>
<body>

<main class="alerta_fondo">
    <div class="alerta_tarjeta">

        <?php if ($enUso): ?>

            <div class="alerta_icono">
                <svg class="icono" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>

            <div class="alerta_textos">
                <h1 class="alerta_titulo">No se puede eliminar</h1>
                <p class="alerta_mensaje">Hay terapeutas que usan <?= e($especialidad['nombre']) ?>. Cambiales la especialidad y después volvé a intentarlo.</p>
            </div>

            <div class="alerta_botones">
                <a href="centro.php" class="boton_verde">Entendido</a>
            </div>

        <?php else: ?>

            <div class="alerta_icono">
                <svg class="icono" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </div>

            <div class="alerta_textos">
                <h1 class="alerta_titulo">¿Eliminar la especialidad?</h1>
                <p class="alerta_mensaje">Esta acción no se puede deshacer.</p>
            </div>

            <div class="alerta_resumen">
                <div class="alerta_dato">
                    <span class="alerta_etiqueta">Especialidad</span>
                    <span class="alerta_valor"><?= e($especialidad['nombre']) ?></span>
                </div>
            </div>

            <form method="post" class="alerta_botones">
                <input type="hidden" name="id" value="<?= $id ?>">
                <a href="centro.php" class="boton_gris">Cancelar</a>
                <button type="submit" class="boton_coral">Sí, eliminar</button>
            </form>

        <?php endif; ?>

    </div>
</main>

</body>
</html>