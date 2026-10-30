<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

$nombre = '';
$usuario = '';
$activo = 1;
$error = '';

if ($editando) {
    $stmt = $conn->prepare("SELECT nombre, usuario, activo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();

    if (!$fila) {
        header('Location: usuarios.php');
        exit;
    }

    $nombre = $fila['nombre'];
    $usuario = $fila['usuario'];
    $activo = (int) $fila['activo'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($editando) {
        $activo = (int) ($_POST['activo'] ?? 1) === 1 ? 1 : 0;
    }

    if ($nombre === '' || $usuario === '') {
        $error = 'Completá el nombre y el usuario.';
    } elseif (!$editando && $password === '') {
        $error = 'Escribí una contraseña.';
    } elseif ($password !== '' && strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ? AND id <> ?");
        $stmt->bind_param("si", $usuario, $id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Ese usuario ya existe.';
        }
    }

    if ($error === '') {
        if ($editando) {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, usuario = ?, activo = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssisi", $nombre, $usuario, $activo, $hash, $id);
            } else {
                $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, usuario = ?, activo = ? WHERE id = ?");
                $stmt->bind_param("ssii", $nombre, $usuario, $activo, $id);
            }
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, usuario, password, rol, activo) VALUES (?, ?, ?, 'administrativo', 1)");
            $stmt->bind_param("sss", $nombre, $usuario, $hash);
        }

        $stmt->execute();
        header('Location: usuarios.php');
        exit;
    }
}

$titulo      = $editando ? 'Editar usuario' : 'Nuevo administrativo';
$descripcion = $editando ? 'Un usuario inactivo no puede ingresar al sistema.' : 'Se crea con rol administrativo. Los terapeutas se cargan desde la sección Terapeutas.';
$textoBoton  = $editando ? 'Guardar cambios' : 'Crear usuario';
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
    <link rel="stylesheet" href="../assets/css/alerta.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
</head>
<body>

<main class="alerta_fondo">
    <form method="post" class="modal_tarjeta modal_tarjeta-ancha" autocomplete="off">

        <div class="modal_titulos">
            <h1 class="modal_titulo"><?= $titulo ?></h1>
            <p class="modal_descripcion"><?= $descripcion ?></p>
        </div>

        <?php if ($error !== ''): ?>
            <p class="formulario_error"><?= e($error) ?></p>
        <?php endif; ?>

        <div class="modal_campos">

            <div class="campo">
                <label for="nombre" class="campo_label">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" class="campo_control" placeholder="Nombre y apellido" value="<?= e($nombre) ?>">
            </div>

            <div class="campo">
                <label for="usuario" class="campo_label">Usuario</label>
                <input type="text" id="usuario" name="usuario" class="campo_control" placeholder="Usuario para ingresar" value="<?= e($usuario) ?>">
            </div>

            <?php if ($editando): ?>
                <div class="campo">
                    <label for="activo" class="campo_label">Estado</label>
                    <div class="campo_select">
                        <select id="activo" name="activo" class="campo_control">
                            <option value="1" <?= $activo === 1 ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= $activo === 0 ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                        <svg class="icono icono-chico" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            <?php endif; ?>

            <div class="campo">
                <label for="password" class="campo_label"><?= $editando ? 'Nueva contraseña' : 'Contraseña' ?></label>
                <input type="password" id="password" name="password" class="campo_control" placeholder="<?= $editando ? 'Escribí una nueva para restablecerla' : 'Mínimo 8 caracteres' ?>" autocomplete="new-password">
                <?php if ($editando): ?>
                    <span class="campo_ayuda">Dejala vacía para mantener la actual.</span>
                <?php endif; ?>
            </div>

        </div>

        <div class="modal_botones">
            <a href="usuarios.php" class="boton_gris">Cancelar</a>
            <button type="submit" class="boton_coral"><?= $textoBoton ?></button>
        </div>

    </form>
</main>

<script src="../assets/js/app.js"></script>
</body>
</html>