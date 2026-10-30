<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT p.nombre, p.dni, o.nombre AS obra_social
                        FROM pacientes p
                        LEFT JOIN obras_sociales o ON o.id = p.id_obra_social
                        WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$paciente = $stmt->get_result()->fetch_assoc();

if (!$paciente) {
    header('Location: pacientes.php');
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM turnos WHERE id_paciente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tieneTurnos = $stmt->get_result()->fetch_assoc()['total'] > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'eliminar' && !$tieneTurnos) {
        $stmt = $conn->prepare("DELETE FROM asignaciones WHERE id_paciente = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM pacientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    if ($accion === 'inactivo') {
        $stmt = $conn->prepare("UPDATE pacientes SET activo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    header('Location: pacientes.php');
    exit;
}

$dni = number_format((int) $paciente['dni'], 0, '', '.');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar paciente | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/alerta.css">
</head>
<body>

<main class="alerta_fondo">
    <div class="alerta_tarjeta">

        <?php if ($tieneTurnos): ?>

            <div class="alerta_icono">
                <svg class="icono" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>

            <div class="alerta_textos">
                <h1 class="alerta_titulo">No se puede eliminar</h1>
                <p class="alerta_mensaje"><?= e($paciente['nombre']) ?> tiene turnos registrados. Pasala a inactivo para que no aparezca al dar nuevos turnos.</p>
            </div>

            <form method="post" class="alerta_botones">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="accion" value="inactivo">
                <a href="pacientes.php" class="boton_gris">Entendido</a>
                <button type="submit" class="boton_verde">Pasar a inactivo</button>
            </form>

        <?php else: ?>

            <div class="alerta_icono">
                <svg class="icono" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </div>

            <div class="alerta_textos">
                <h1 class="alerta_titulo">¿Eliminar este paciente?</h1>
                <p class="alerta_mensaje">Esta acción no se puede deshacer.</p>
            </div>

            <div class="alerta_resumen">
                <div class="alerta_dato">
                    <span class="alerta_etiqueta">Paciente</span>
                    <span class="alerta_valor"><?= e($paciente['nombre']) ?></span>
                </div>
                <div class="alerta_dato">
                    <span class="alerta_etiqueta">DNI</span>
                    <span class="alerta_valor"><?= $dni ?></span>
                </div>
                <div class="alerta_dato">
                    <span class="alerta_etiqueta">Obra social</span>
                    <span class="alerta_valor"><?= e($paciente['obra_social'] ?? '') ?></span>
                </div>
            </div>

            <form method="post" class="alerta_botones">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="accion" value="eliminar">
                <a href="pacientes.php" class="boton_gris">Cancelar</a>
                <button type="submit" class="boton_coral">Sí, eliminar</button>
            </form>

        <?php endif; ?>

    </div>
</main>

<script src="../assets/js/app.js"></script>
</body>
</html>