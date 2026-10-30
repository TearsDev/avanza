<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

date_default_timezone_set('America/Argentina/Buenos_Aires');

$paginaActual = 'pacientes';

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

$campos = ['nombre', 'dni', 'nacimiento', 'diagnostico', 'id_obra_social',
           'resp_nombre', 'resp_vinculo', 'resp_email', 'resp_telefono'];

$paciente = array_fill_keys($campos, '');
$activo = 1;

if ($editando) {
    $stmt = $conn->prepare("SELECT nombre, dni, nacimiento, diagnostico, id_obra_social,
                                   resp_nombre, resp_vinculo, resp_email, resp_telefono, activo
                            FROM pacientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();

    if (!$fila) {
        header('Location: pacientes.php');
        exit;
    }

    $activo = (int) $fila['activo'];
    unset($fila['activo']);
    $paciente = $fila;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($campos as $campo) {
        $paciente[$campo] = trim($_POST[$campo] ?? '');
    }

    $paciente['dni'] = str_replace('.', '', $paciente['dni']);
    $activo = (($_POST['activo'] ?? '1') === '0') ? 0 : 1;

    if (in_array('', $paciente, true)) {
        $error = 'Completá todos los campos.';
    } elseif (!preg_match('/^\d{7,8}$/', $paciente['dni'])) {
        $error = 'El DNI debe tener 7 u 8 números.';
    } elseif ($paciente['nacimiento'] > date('Y-m-d')) {
        $error = 'La fecha de nacimiento no puede ser futura.';
    } elseif (!filter_var($paciente['resp_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'El email del responsable no es válido.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM pacientes WHERE dni = ? AND id <> ?");
        $stmt->bind_param("si", $paciente['dni'], $id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Ya existe un paciente con ese DNI.';
        }
    }

    if ($error === '') {
        $idObraSocial = (int) $paciente['id_obra_social'];

        if ($editando) {
            $stmt = $conn->prepare("UPDATE pacientes
                                    SET nombre = ?, dni = ?, nacimiento = ?, diagnostico = ?, id_obra_social = ?,
                                        resp_nombre = ?, resp_vinculo = ?, resp_email = ?, resp_telefono = ?, activo = ?
                                    WHERE id = ?");
            $stmt->bind_param("ssssissssii",
                $paciente['nombre'],
                $paciente['dni'],
                $paciente['nacimiento'],
                $paciente['diagnostico'],
                $idObraSocial,
                $paciente['resp_nombre'],
                $paciente['resp_vinculo'],
                $paciente['resp_email'],
                $paciente['resp_telefono'],
                $activo,
                $id
            );
        } else {
            $stmt = $conn->prepare("INSERT INTO pacientes
                                    (nombre, dni, nacimiento, diagnostico, id_obra_social,
                                     resp_nombre, resp_vinculo, resp_email, resp_telefono, activo)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssissssi",
                $paciente['nombre'],
                $paciente['dni'],
                $paciente['nacimiento'],
                $paciente['diagnostico'],
                $idObraSocial,
                $paciente['resp_nombre'],
                $paciente['resp_vinculo'],
                $paciente['resp_email'],
                $paciente['resp_telefono'],
                $activo
            );
        }

        $stmt->execute();
        header('Location: pacientes.php');
        exit;
    }
}

$obrasSociales = $conn->query("SELECT id, nombre FROM obras_sociales ORDER BY nombre");

$vinculos = ['Madre', 'Padre', 'Tutor/a', 'Abuelo/a', 'Otro'];

if ($paciente['resp_vinculo'] !== '' && !in_array($paciente['resp_vinculo'], $vinculos)) {
    $vinculos[] = $paciente['resp_vinculo'];
}

$titulo     = $editando ? 'Editar paciente' : 'Nuevo paciente';
$subtitulo  = $editando ? 'Actualizá los datos o cambiá el estado del paciente' : 'Completá los datos del paciente y de su responsable';
$textoBoton = $editando ? 'Guardar cambios' : 'Guardar paciente';
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

        <a href="pacientes.php" class="volver">
            <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Volver a pacientes
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
                    <h2 class="seccion_titulo">Datos del paciente</h2>

                    <div class="campo">
                        <label for="nombre" class="campo_label">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" class="campo_control" placeholder="Nombre y apellido" value="<?= e($paciente['nombre']) ?>">
                    </div>

                    <div class="campo_fila">
                        <div class="campo">
                            <label for="dni" class="campo_label">DNI</label>
                            <input type="text" id="dni" name="dni" class="campo_control" placeholder="Sin puntos" value="<?= e($paciente['dni']) ?>">
                        </div>
                        <div class="campo">
                            <label for="nacimiento" class="campo_label">Fecha de nacimiento</label>
                            <input type="date" id="nacimiento" name="nacimiento" class="campo_control" value="<?= e($paciente['nacimiento']) ?>">
                        </div>
                    </div>

                    <div class="campo">
                        <label for="diagnostico" class="campo_label">Diagnóstico</label>
                        <input type="text" id="diagnostico" name="diagnostico" class="campo_control" placeholder="Diagnóstico principal" value="<?= e($paciente['diagnostico']) ?>">
                    </div>

                    <div class="campo_fila">
                        <div class="campo">
                            <label for="id_obra_social" class="campo_label">Obra social</label>
                            <select id="id_obra_social" name="id_obra_social" class="campo_control">
                                <option value="">Seleccioná una obra social</option>
                                <?php while ($o = $obrasSociales->fetch_assoc()): ?>
                                    <option value="<?= (int) $o['id'] ?>" <?= $o['id'] == $paciente['id_obra_social'] ? 'selected' : '' ?>>
                                        <?= e($o['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <?php if ($editando): ?>
                            <div class="campo">
                                <label for="activo" class="campo_label">Estado</label>
                                <select id="activo" name="activo" class="campo_control">
                                    <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="formulario_seccion formulario_seccion-borde">
                    <h2 class="seccion_titulo">Responsable</h2>

                    <div class="campo_fila">
                        <div class="campo">
                            <label for="resp_nombre" class="campo_label">Nombre del responsable</label>
                            <input type="text" id="resp_nombre" name="resp_nombre" class="campo_control" placeholder="Nombre y apellido" value="<?= e($paciente['resp_nombre']) ?>">
                        </div>
                        <div class="campo">
                            <label for="resp_vinculo" class="campo_label">Vínculo</label>
                            <select id="resp_vinculo" name="resp_vinculo" class="campo_control">
                                <option value="">Madre, padre, tutor...</option>
                                <?php foreach ($vinculos as $v): ?>
                                    <option value="<?= e($v) ?>" <?= $v === $paciente['resp_vinculo'] ? 'selected' : '' ?>><?= e($v) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="campo_fila">
                        <div class="campo">
                            <label for="resp_email" class="campo_label">Email</label>
                            <input type="text" id="resp_email" name="resp_email" class="campo_control" placeholder="nombre@correo.com" value="<?= e($paciente['resp_email']) ?>">
                        </div>
                        <div class="campo">
                            <label for="resp_telefono" class="campo_label">Teléfono</label>
                            <input type="text" id="resp_telefono" name="resp_telefono" class="campo_control" placeholder="Código de área + número" value="<?= e($paciente['resp_telefono']) ?>">
                        </div>
                    </div>
                </section>

            </div>

            <div class="formulario_acciones">
                <?php if ($editando): ?>
                    <a href="paciente-eliminar.php?id=<?= $id ?>" class="boton_borde-coral">Eliminar paciente</a>
                <?php endif; ?>

                <div class="formulario_botones">
                    <a href="pacientes.php" class="boton_gris">Cancelar</a>
                    <button type="submit" class="boton_coral"><?= $textoBoton ?></button>
                </div>
            </div>

        </form>

    </div>
</main>

<script src="../assets/js/app.js"></script>
</body>
</html>