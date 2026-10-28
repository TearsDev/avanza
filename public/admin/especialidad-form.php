<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

$nombre = '';
$error = '';

if ($editando) {
    $stmt = $conn->prepare("SELECT nombre FROM especialidades WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();

    if (!$fila) {
        header('Location: centro.php');
        exit;
    }

    $nombre = $fila['nombre'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');

    if ($nombre === '') {
        $error = 'Escribí el nombre de la especialidad.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM especialidades WHERE nombre = ? AND id <> ?");
        $stmt->bind_param("si", $nombre, $id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Ya existe una especialidad con ese nombre.';
        }
    }

    if ($error === '') {
        if ($editando) {
            $stmt = $conn->prepare("UPDATE especialidades SET nombre = ? WHERE id = ?");
            $stmt->bind_param("si", $nombre, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO especialidades (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombre);
        }

        $stmt->execute();
        header('Location: centro.php');
        exit;
    }
}

$titulo      = $editando ? 'Editar especialidad' : 'Nueva especialidad';
$descripcion = $editando ? 'Cambiar el nombre se actualiza en todo el sistema.' : 'Escribí el nombre para agregarla a la lista.';
$textoBoton  = $editando ? 'Guardar cambios' : 'Guardar';
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
    <form method="post" class="modal_tarjeta">

        <div class="modal_titulos">
            <h1 class="modal_titulo"><?= $titulo ?></h1>
            <p class="modal_descripcion"><?= $descripcion ?></p>
        </div>

        <?php if ($error !== ''): ?>
            <p class="formulario_error"><?= e($error) ?></p>
        <?php endif; ?>

        <div class="campo">
            <label for="nombre" class="campo_label">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="campo_control" placeholder="Ej: Terapia ocupacional" value="<?= e($nombre) ?>" autofocus>
        </div>

        <div class="modal_botones">
            <a href="centro.php" class="boton_gris">Cancelar</a>
            <button type="submit" class="boton_coral"><?= $textoBoton ?></button>
        </div>

    </form>
</main>

</body>
</html>