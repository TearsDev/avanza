<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('administrativo');

$paginaActual = 'centro';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $centro = [
        'nombre'    => trim($_POST['nombre'] ?? ''),
        'direccion' => trim($_POST['direccion'] ?? ''),
        'telefono'  => trim($_POST['telefono'] ?? '')
    ];

    if (in_array('', $centro, true)) {
        $error = 'Completá todos los campos.';
    } else {
        $stmt = $conn->prepare("UPDATE centro SET nombre = ?, direccion = ?, telefono = ? LIMIT 1");
        $stmt->bind_param("sss", $centro['nombre'], $centro['direccion'], $centro['telefono']);
        $stmt->execute();

        header('Location: centro.php?guardado=1');
        exit;
    }
} else {
    $centro = $conn->query("SELECT nombre, direccion, telefono FROM centro LIMIT 1")->fetch_assoc();
}

$guardado = isset($_GET['guardado']);

$especialidades = $conn->query("SELECT id, nombre FROM especialidades ORDER BY id");
$obrasSociales = $conn->query("SELECT id, nombre FROM obras_sociales ORDER BY id");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/formulario.css">
    <link rel="stylesheet" href="../assets/css/centro.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-admin.php'; ?>

<main class="admin_contenido">

    <div>
        <h1 class="admin_titulo">Centro</h1>
        <p class="admin_subtitulo">Datos del centro, especialidades y obras sociales</p>
    </div>

    <form method="post" class="centro_tarjeta">
        <div>
            <h2 class="centro_titulo">Datos del centro</h2>
            <p class="centro_descripcion">Se muestran en la landing y en el pie de página.</p>
        </div>

        <?php if ($error !== ''): ?>
            <p class="formulario_error"><?= e($error) ?></p>
        <?php endif; ?>

        <?php if ($guardado): ?>
            <p class="centro_aviso">Los cambios se guardaron.</p>
        <?php endif; ?>

        <div class="campo">
            <label for="nombre" class="campo_label">Nombre del centro</label>
            <input type="text" id="nombre" name="nombre" class="campo_control" value="<?= e($centro['nombre']) ?>">
        </div>

        <div class="campo_fila">
            <div class="campo">
                <label for="direccion" class="campo_label">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="campo_control" value="<?= e($centro['direccion']) ?>">
            </div>
            <div class="campo">
                <label for="telefono" class="campo_label">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="campo_control" value="<?= e($centro['telefono']) ?>">
            </div>
        </div>

        <div class="centro_guardar">
            <button type="submit" class="boton_coral">Guardar cambios</button>
        </div>
    </form>

    <div class="centro_listas">

        <section class="centro_tarjeta">
            <div class="centro_cabecera">
                <div>
                    <h2 class="centro_titulo">Especialidades</h2>
                    <p class="centro_descripcion">Las que ofrece el centro.</p>
                </div>
                <a href="especialidad-form.php" class="boton_borde">+ Agregar</a>
            </div>

            <div>
                <?php while ($esp = $especialidades->fetch_assoc()): ?>
                    <div class="centro_fila">
                        <span class="centro_nombre"><?= e($esp['nombre']) ?></span>
                        <div class="acciones">
                            <a href="especialidad-form.php?id=<?= (int) $esp['id'] ?>" class="boton_icono" title="Editar">
                                <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </a>
                            <a href="especialidad-eliminar.php?id=<?= (int) $esp['id'] ?>" class="boton_icono boton_icono-borrar" title="Eliminar">
                                <svg class="icono icono-chico" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="centro_tarjeta">
            <div class="centro_cabecera">
                <div>
                    <h2 class="centro_titulo">Obras sociales</h2>
                    <p class="centro_descripcion">Las que atiende el centro.</p>
                </div>
                <a href="obra-social-form.php" class="boton_borde">+ Agregar</a>
            </div>

            <div>
                <?php while ($obra = $obrasSociales->fetch_assoc()): ?>
                    <div class="centro_fila">
                        <span class="centro_nombre"><?= e($obra['nombre']) ?></span>
                        <div class="acciones">
                            <a href="obra-social-form.php?id=<?= (int) $obra['id'] ?>" class="boton_icono" title="Editar">
                                <svg class="icono icono-chico" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </a>
                            <a href="obra-social-eliminar.php?id=<?= (int) $obra['id'] ?>" class="boton_icono boton_icono-borrar" title="Eliminar">
                                <svg class="icono icono-chico" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

    </div>

</main>

</body>
</html>