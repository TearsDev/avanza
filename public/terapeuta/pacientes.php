<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

exigirRol('terapeuta');

$paginaActual = 'pacientes';

$q = trim($_GET['q'] ?? '');
$busqueda = '%' . $q . '%';

$stmt = $conn->prepare("SELECT p.id, p.nombre, p.diagnostico,
                               o.nombre AS obra_social,
                               TIMESTAMPDIFF(YEAR, p.nacimiento, CURDATE()) AS edad
                        FROM asignaciones a
                        JOIN pacientes p ON p.id = a.id_paciente
                        JOIN obras_sociales o ON o.id = p.id_obra_social
                        WHERE a.id_terapeuta = ?
                          AND (p.nombre LIKE ? OR p.dni LIKE ?)
                        ORDER BY p.nombre");
$stmt->bind_param("iss", $_SESSION['id'], $busqueda, $busqueda);
$stmt->execute();
$pacientes = $stmt->get_result();

$total = $pacientes->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis pacientes | Avanza</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/terapeuta.css">
</head>
<body>

<?php require_once __DIR__ . '/../../includes/nav-terapeuta.php'; ?>

<main class="admin_contenido">

    <div class="admin_encabezado">
        <div>
            <h1 class="admin_titulo">Mis pacientes</h1>
            <p class="admin_subtitulo">Los pacientes que tenés asignados</p>
        </div>
    </div>

    <form method="get" class="filtros">
        <div class="filtro_campo filtro_campo-grande">
            <label for="q" class="filtro_label">Paciente</label>
            <div class="filtro_input">
                <svg class="icono icono-chico" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="q" name="q" placeholder="Buscar por nombre o DNI" value="<?= e($q) ?>">
            </div>
        </div>

        <button type="submit" class="boton_verde">Buscar</button>
        <a href="pacientes.php" class="boton_gris">Limpiar</a>
    </form>

    <div class="panel">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Edad</th>
                    <th>Diagnóstico</th>
                    <th>Obra social</th>
                    <th class="col_acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total === 0): ?>
                    <tr>
                        <td colspan="5" class="tabla_vacio">No se encontraron pacientes.</td>
                    </tr>
                <?php endif; ?>

                <?php while ($p = $pacientes->fetch_assoc()): ?>
                    <tr>
                        <td class="tabla_profesional"><?= e($p['nombre']) ?></td>
                        <td class="tabla_gris"><?= (int) $p['edad'] ?> <?= $p['edad'] == 1 ? 'año' : 'años' ?></td>
                        <td><?= e($p['diagnostico']) ?></td>
                        <td class="tabla_gris"><?= e($p['obra_social']) ?></td>
                        <td class="col_acciones">
                            <div class="acciones">
                                <a href="paciente-ficha.php?id=<?= (int) $p['id'] ?>" class="boton_borde">Ver ficha</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="panel_pie">
            Mostrando <?= $total ?> <?= $total === 1 ? 'paciente' : 'pacientes' ?>
        </div>
    </div>

</main>

<script src="../assets/js/app.js"></script>
</body>
</html>