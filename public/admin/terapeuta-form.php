<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$paginaActual = 'terapeutas';

$dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'];

$campos = ['nombre', 'matricula', 'id_especialidad', 'email', 'telefono', 'usuario'];

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

$terapeuta = array_fill_keys($campos, '');
$activo = 1;
$diasMarcados = [];
$desde = [];
$hasta = [];
$asignados = [];
$error = '';

if ($editando) {
    $stmt = $conn->prepare("SELECT u.nombre, t.matricula, t.id_especialidad, u.email, t.telefono, u.usuario, u.activo
                            FROM usuarios u
                            JOIN terapeutas t ON t.id_usuario = u.id
                            WHERE u.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();

    if (!$fila) {
        header('Location: terapeutas.php');
        exit;
    }

    $activo = (int) $fila['activo'];
    unset($fila['activo']);
    $terapeuta = $fila;

    $stmt = $conn->prepare("SELECT dia, hora_desde, hora_hasta FROM horarios WHERE id_terapeuta = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $horarios = $stmt->get_result();

    while ($h = $horarios->fetch_assoc()) {
        $diasMarcados[] = (int) $h['dia'];
        $desde[$h['dia']] = substr($h['hora_desde'], 0, 5);
        $hasta[$h['dia']] = substr($h['hora_hasta'], 0, 5);
    }

    $stmt = $conn->prepare("SELECT id_paciente FROM asignaciones WHERE id_terapeuta = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $filas = $stmt->get_result();

    while ($a = $filas->fetch_assoc()) {
        $asignados[] = (int) $a['id_paciente'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($campos as $campo) {
        $terapeuta[$campo] = trim($_POST[$campo] ?? '');
    }

    $password = $_POST['password'] ?? '';
    $activo = (($_POST['activo'] ?? '1') === '0') ? 0 : 1;
    $diasMarcados = array_values(array_intersect(array_map('intval', $_POST['dias'] ?? []), array_keys($dias)));
    $desde = $_POST['desde'] ?? [];
    $hasta = $_POST['hasta'] ?? [];
    $asignados = array_map('intval', $_POST['pacientes'] ?? []);

    if (in_array('', $terapeuta, true) || (!$editando && $password === '')) {
        $error = 'Completá todos los campos.';
    } elseif (!filter_var($terapeuta['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido.';
    } elseif ($password !== '' && strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if ($error === '') {
        foreach ($diasMarcados as $d) {
            $inicio = $desde[$d] ?? '';
            $fin = $hasta[$d] ?? '';

            if ($inicio === '' || $fin === '') {
                $error = 'Completá el horario del ' . strtolower($dias[$d]) . '.';
                break;
            }

            if ($inicio >= $fin) {
                $error = 'El horario del ' . strtolower($dias[$d]) . ' es inválido: "Desde" debe ser anterior a "Hasta".';
                break;
            }
        }
    }

    if ($error === '') {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ? AND id <> ?");
        $stmt->bind_param("si", $terapeuta['usuario'], $id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Ya existe un usuario con ese nombre.';
        }
    }

    if ($error === '') {
        $conn->begin_transaction();

        try {
            $idEspecialidad = (int) $terapeuta['id_especialidad'];

            if ($editando) {
                $idUsuario = $id;

                $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, usuario = ?, email = ?, activo = ? WHERE id = ?");
                $stmt->bind_param("sssii", $terapeuta['nombre'], $terapeuta['usuario'], $terapeuta['email'], $activo, $idUsuario);
                $stmt->execute();

                if ($password !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $hash, $idUsuario);
                    $stmt->execute();
                }

                $stmt = $conn->prepare("UPDATE terapeutas SET matricula = ?, id_especialidad = ?, telefono = ? WHERE id_usuario = ?");
                $stmt->bind_param("sisi", $terapeuta['matricula'], $idEspecialidad, $terapeuta['telefono'], $idUsuario);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM horarios WHERE id_terapeuta = ?");
                $stmt->bind_param("i", $idUsuario);
                $stmt->execute();

                $stmt = $conn->prepare("DELETE FROM asignaciones WHERE id_terapeuta = ?");
                $stmt->bind_param("i", $idUsuario);
                $stmt->execute();
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, usuario, email, password, rol, activo)
                                        VALUES (?, ?, ?, ?, 'terapeuta', 1)");
                $stmt->bind_param("ssss", $terapeuta['nombre'], $terapeuta['usuario'], $terapeuta['email'], $hash);
                $stmt->execute();

                $idUsuario = $conn->insert_id;

                $stmt = $conn->prepare("INSERT INTO terapeutas (id_usuario, matricula, id_especialidad, telefono)
                                        VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isis", $idUsuario, $terapeuta['matricula'], $idEspecialidad, $terapeuta['telefono']);
                $stmt->execute();
            }

            $stmt = $conn->prepare("INSERT INTO horarios (id_terapeuta, dia, hora_desde, hora_hasta)
                                    VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $idUsuario, $dia, $inicio, $fin);

            foreach ($diasMarcados as $dia) {
                $inicio = $desde[$dia];
                $fin = $hasta[$dia];
                $stmt->execute();
            }

            $stmt = $conn->prepare("INSERT INTO asignaciones (id_paciente, id_terapeuta) VALUES (?, ?)");
            $stmt->bind_param("ii", $idPaciente, $idUsuario);

            foreach ($asignados as $idPaciente) {
                $stmt->execute();
            }

            $conn->commit();
            header('Location: terapeutas.php');
            exit;
        } catch (Throwable $ex) {
            $conn->rollback();
            $error = 'No se pudo guardar el terapeuta. Intentá de nuevo.';
        }
    }
}

$especialidades = $conn->query("SELECT id, nombre FROM especialidades ORDER BY nombre");

$stmt = $conn->prepare("SELECT id, nombre FROM pacientes
                        WHERE activo = 1 OR id IN (SELECT id_paciente FROM asignaciones WHERE id_terapeuta = ?)
                        ORDER BY nombre");
$stmt->bind_param("i", $id);
$stmt->execute();
$pacientes = $stmt->get_result();

$titulo     = $editando ? 'Editar terapeuta' : 'Nuevo terapeuta';
$subtitulo  = $editando ? 'Actualizá sus datos, horarios y pacientes asignados' : 'Cargá los datos, el acceso y los horarios del profesional';
$textoBoton = $editando ? 'Guardar cambios' : 'Guardar terapeuta';
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
    <link rel="stylesheet" href="../assets/css/terapeuta-form.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="admin_contenido">
    <div class="formulario_contenedor formulario_contenedor-ancho">

        <a href="terapeutas.php" class="volver">
            <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Volver a terapeutas
        </a>

        <div>
            <h1 class="admin_titulo"><?= $titulo ?></h1>
            <p class="admin_subtitulo"><?= $subtitulo ?></p>
        </div>

        <form method="post" class="formulario_tarjeta">

            <div class="formulario_secciones">

                <?php if ($error !== ''): ?>
                    <p class="formulario_error"><?= e($error) ?></p>
                <?php endif; ?>

                <section class="formulario_seccion">
                    <h2 class="seccion_titulo">Datos profesionales</h2>

                    <div class="campo">
                        <label for="nombre" class="campo_label">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" class="campo_control" placeholder="Nombre y apellido" value="<?= e($terapeuta['nombre']) ?>">
                    </div>

                    <div class="campo_fila">
                        <div class="campo">
                            <label for="matricula" class="campo_label">Matrícula</label>
                            <input type="text" id="matricula" name="matricula" class="campo_control" placeholder="Número de matrícula" value="<?= e($terapeuta['matricula']) ?>">
                        </div>
                        <div class="campo">
                            <label for="id_especialidad" class="campo_label">Especialidad</label>
                            <select id="id_especialidad" name="id_especialidad" class="campo_control">
                                <option value="">Seleccioná una especialidad</option>
                                <?php while ($esp = $especialidades->fetch_assoc()): ?>
                                    <option value="<?= (int) $esp['id'] ?>" <?= $esp['id'] == $terapeuta['id_especialidad'] ? 'selected' : '' ?>>
                                        <?= e($esp['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="campo_fila">
                        <div class="campo">
                            <label for="email" class="campo_label">Email</label>
                            <input type="text" id="email" name="email" class="campo_control" placeholder="nombre@correo.com" value="<?= e($terapeuta['email']) ?>">
                        </div>
                        <div class="campo">
                            <label for="telefono" class="campo_label">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="campo_control" placeholder="Código de área + número" value="<?= e($terapeuta['telefono']) ?>">
                        </div>
                    </div>
                </section>

                <section class="formulario_seccion formulario_seccion-borde">
                    <h2 class="seccion_titulo">Acceso al sistema</h2>

                    <div class="campo_fila">
                        <div class="campo">
                            <label for="usuario" class="campo_label">Usuario</label>
                            <input type="text" id="usuario" name="usuario" class="campo_control" placeholder="Usuario para ingresar" value="<?= e($terapeuta['usuario']) ?>">
                        </div>

                        <?php if ($editando): ?>
                            <div class="campo">
                                <label for="activo" class="campo_label">Estado</label>
                                <select id="activo" name="activo" class="campo_control">
                                    <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="campo">
                                <label for="password" class="campo_label">Contraseña</label>
                                <input type="password" id="password" name="password" class="campo_control" placeholder="Mínimo 8 caracteres">
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($editando): ?>
                        <div class="campo">
                            <label for="password" class="campo_label">Nueva contraseña</label>
                            <input type="password" id="password" name="password" class="campo_control" placeholder="Escribí una nueva para restablecerla">
                            <p class="campo_ayuda">Dejala vacía para mantener la actual.</p>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="formulario_seccion formulario_seccion-borde">
                    <div class="seccion_cabecera">
                        <h2 class="seccion_titulo">Horarios de atención</h2>
                        <p class="seccion_descripcion">Marcá los días que atiende y su franja horaria.</p>
                    </div>

                    <?php foreach ($dias as $n => $nombreDia): ?>
                        <div class="dia">
                            <input type="checkbox" id="dia<?= $n ?>" name="dias[]" value="<?= $n ?>" class="check" <?= in_array($n, $diasMarcados) ? 'checked' : '' ?>>
                            <label for="dia<?= $n ?>" class="dia_nombre"><?= $nombreDia ?></label>
                            <input type="time" name="desde[<?= $n ?>]" class="campo_control campo_control-hora" aria-label="Desde" value="<?= e($desde[$n] ?? '') ?>">
                            <input type="time" name="hasta[<?= $n ?>]" class="campo_control campo_control-hora" aria-label="Hasta" value="<?= e($hasta[$n] ?? '') ?>">
                        </div>
                    <?php endforeach; ?>
                </section>

                <section class="formulario_seccion formulario_seccion-borde">
                    <div class="seccion_cabecera">
                        <h2 class="seccion_titulo">Pacientes asignados</h2>
                        <p class="seccion_descripcion">Solo se listan pacientes activos. Podés dar turnos únicamente a los asignados.</p>
                    </div>

                    <div class="pacientes_lista">
                        <?php while ($p = $pacientes->fetch_assoc()): ?>
                            <label class="paciente_opcion">
                                <input type="checkbox" name="pacientes[]" value="<?= (int) $p['id'] ?>" class="check" <?= in_array((int) $p['id'], $asignados) ? 'checked' : '' ?>>
                                <?= e($p['nombre']) ?>
                            </label>
                        <?php endwhile; ?>
                    </div>
                </section>

            </div>

            <div class="formulario_acciones">
                <?php if ($editando): ?>
                    <a href="terapeuta-eliminar.php?id=<?= $id ?>" class="boton_borde-coral">Eliminar terapeuta</a>
                <?php endif; ?>

                <div class="formulario_botones">
                    <a href="terapeutas.php" class="boton_gris">Cancelar</a>
                    <button type="submit" class="boton_coral"><?= $textoBoton ?></button>
                </div>
            </div>

        </form>

    </div>
</main>

<script src="../assets/js/app.js"></script>
</body>
</html>