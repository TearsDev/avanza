<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('terapeuta');

$paginaActual = 'inicio';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT t.id, t.fecha, t.hora, p.nombre AS paciente, s.notas
                        FROM turnos t
                        JOIN pacientes p ON p.id = t.id_paciente
                        JOIN sesiones s ON s.id_turno = t.id
                        WHERE t.id = ? AND t.id_terapeuta = ? AND t.estado = 'realizado'");
$stmt->bind_param("ii", $id, $_SESSION['id']);
$stmt->execute();
$sesion = $stmt->get_result()->fetch_assoc();

if (!$sesion) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión realizada | Avanza</title>
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
            <h1 class="admin_titulo">Sesión realizada</h1>
            <p class="admin_subtitulo">Nota registrada para este turno</p>
        </div>

        <div class="sesion_resumen">
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Paciente</span>
                <span class="ficha_valor"><?= e($sesion['paciente']) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Fecha</span>
                <span class="ficha_valor"><?= date('d/m/Y', strtotime($sesion['fecha'])) ?></span>
            </div>
            <div class="ficha_dato">
                <span class="ficha_etiqueta">Hora</span>
                <span class="ficha_valor"><?= substr($sesion['hora'], 0, 5) ?></span>
            </div>
        </div>

        <div class="sesion_form">

            <div class="sesion_tarjeta">
                <div class="campo">
                    <span class="campo_label">Notas de la sesión</span>
                    <div class="sesion_nota"><?= e($sesion['notas']) ?></div>
                </div>
            </div>

            <div class="sesion_acciones">
                <a href="index.php" class="boton_gris">Volver</a>
                <a href="sesion-form.php?id=<?= (int) $sesion['id'] ?>" class="boton_verde">Editar nota</a>
            </div>

        </div>

    </div>

</main>

</body>
</html>