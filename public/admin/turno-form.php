<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$paginaActual = 'turnos';

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

$turno = [
    'id_terapeuta' => '',
    'id_paciente'  => '',
    'fecha'        => '',
    'hora'         => '',
    'estado'       => 'pendiente',
];

if ($editando) {
    $stmt = $conn->prepare("SELECT id_terapeuta, id_paciente, fecha, hora, estado FROM turnos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();

    if (!$fila) {
        header('Location: turnos.php');
        exit;
    }

    $turno = $fila;
    $turno['hora'] = substr($turno['hora'], 0, 5);
}

$error = '';

if (($_GET['error'] ?? '') === 'sesion') {
    $error = 'No se puede eliminar: el turno ya tiene una sesión registrada.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turno['id_terapeuta'] = (int) ($_POST['id_terapeuta'] ?? 0);
    $turno['id_paciente']  = (int) ($_POST['id_paciente'] ?? 0);
    $turno['fecha']        = $_POST['fecha'] ?? '';
    $turno['hora']         = $_POST['hora'] ?? '';
    $turno['estado']       = $_POST['estado'] ?? '';

    $estadosValidos = ['pendiente', 'confirmado', 'realizado', 'cancelado'];

    if ($turno['id_terapeuta'] === 0 || $turno['id_paciente'] === 0 || $turno['fecha'] === '' || $turno['hora'] === '') {
        $error = 'Completá todos los campos.';
    } elseif (!in_array($turno['estado'], $estadosValidos)) {
        $error = 'El estado elegido no es válido.';
    } else {
        $stmt = $conn->prepare("SELECT 1 FROM asignaciones WHERE id_paciente = ? AND id_terapeuta = ?");
        $stmt->bind_param("ii", $turno['id_paciente'], $turno['id_terapeuta']);
        $stmt->execute();

        if ($stmt->get_result()->num_rows === 0) {
            $error = 'Ese paciente no está asignado a este profesional.';
        } else {
            $stmt = $conn->prepare("SELECT id FROM turnos
                                    WHERE id_terapeuta = ? AND fecha = ? AND hora = ?
                                      AND estado <> 'cancelado' AND id <> ?");
            $stmt->bind_param("issi", $turno['id_terapeuta'], $turno['fecha'], $turno['hora'], $id);
            $stmt->execute();

            if ($stmt->get_result()->num_rows > 0) {
                $error = 'El profesional ya tiene un turno en ese horario.';
            }
        }
    }

    if ($error === '') {
        if ($editando) {
            $stmt = $conn->prepare("UPDATE turnos
                                    SET id_terapeuta = ?, id_paciente = ?, fecha = ?, hora = ?, estado = ?
                                    WHERE id = ?");
            $stmt->bind_param("iisssi", $turno['id_terapeuta'], $turno['id_paciente'], $turno['fecha'], $turno['hora'], $turno['estado'], $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO turnos (id_terapeuta, id_paciente, fecha, hora, estado)
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $turno['id_terapeuta'], $turno['id_paciente'], $turno['fecha'], $turno['hora'], $turno['estado']);
        }

        $stmt->execute();
        header('Location: turnos.php');
        exit;
    }
}

$profesionales = $conn->query("SELECT u.id, u.nombre, e.nombre AS especialidad
                               FROM usuarios u
                               JOIN terapeutas t ON t.id_usuario = u.id
                               JOIN especialidades e ON e.id = t.id_especialidad
                               WHERE u.activo = 1
                               ORDER BY u.nombre");

$stmt = $conn->prepare("SELECT id, nombre FROM pacientes WHERE activo = 1 OR id = ? ORDER BY nombre");
$stmt->bind_param("i", $turno['id_paciente']);
$stmt->execute();
$pacientes = $stmt->get_result();

$titulo     = $editando ? 'Editar turno' : 'Nuevo turno';
$subtitulo  = $editando ? 'Cambiá la fecha u hora para reprogramarlo' : 'Completá los datos para agendar el turno';
$textoBoton = $editando ? 'Guardar cambios' : 'Guardar turno';
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
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="admin_contenido">
    <div class="formulario_contenedor">

        <a href="turnos.php" class="volver">
            <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Volver a turnos
        </a>

        <div>
            <h1 class="admin_titulo"><?= $titulo ?></h1>
            <p class="admin_subtitulo"><?= $subtitulo ?></p>
        </div>

        <form method="post" class="formulario_tarjeta">

            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <div class="formulario_campos">

                <?php if ($error !== ''): ?>
                    <p class="formulario_error"><?= e($error) ?></p>
                <?php endif; ?>

                <div class="campo">
                    <label for="id_terapeuta" class="campo_label">Profesional</label>
                    <select id="id_terapeuta" name="id_terapeuta" class="campo_control">
                        <option value="">Seleccioná un profesional</option>
                        <?php while ($p = $profesionales->fetch_assoc()): ?>
                            <option value="<?= (int) $p['id'] ?>" <?= $p['id'] == $turno['id_terapeuta'] ? 'selected' : '' ?>>
                                <?= e($p['nombre']) ?> · <?= e($p['especialidad']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="id_paciente" class="campo_label">Paciente</label>
                    <select id="id_paciente" name="id_paciente" class="campo_control">
                        <option value="">Seleccioná un paciente</option>
                        <?php while ($p = $pacientes->fetch_assoc()): ?>
                            <option value="<?= (int) $p['id'] ?>" <?= $p['id'] == $turno['id_paciente'] ? 'selected' : '' ?>>
                                <?= e($p['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <span class="campo_ayuda">Solo aparecen los pacientes activos asignados a este profesional.</span>
                </div>

                <div class="campo_fila">
                    <div class="campo">
                        <label for="fecha" class="campo_label">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="campo_control" value="<?= e($turno['fecha']) ?>">
                    </div>
                    <div class="campo">
                        <label for="hora" class="campo_label">Hora</label>
                        <input type="time" id="hora" name="hora" class="campo_control" value="<?= e($turno['hora']) ?>">
                    </div>
                </div>

                <div class="campo">
                    <label for="estado" class="campo_label">Estado</label>
                    <select id="estado" name="estado" class="campo_control">
                        <?php foreach (['pendiente', 'confirmado', 'realizado', 'cancelado'] as $estado): ?>
                            <option value="<?= $estado ?>" <?= $estado === $turno['estado'] ? 'selected' : '' ?>>
                                <?= ucfirst($estado) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="formulario_acciones">
                <?php if ($editando): ?>
                    <a href="turno-eliminar.php?id=<?= $id ?>" class="boton_borde-coral">Eliminar turno</a>
                <?php endif; ?>

                <div class="formulario_botones">
                    <a href="turnos.php" class="boton_gris">Cancelar</a>
                    <button type="submit" class="boton_coral"><?= $textoBoton ?></button>
                </div>
            </div>

        </form>

    </div>
</main>

<script src="../assets/js/app.js"></script>
</body>
</html>