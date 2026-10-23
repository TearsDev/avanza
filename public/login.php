<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$error = '';
$usuarioIngresado = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioIngresado = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = conectarDB();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = ?');
    $stmt->execute([$usuarioIngresado]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
        if(!$usuario['activo']) {
            $error = 'Tu usuario está inactivo. Consultá con el centro.';
        } else {
            session_regenerate_id(true);
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            $destino = $usuario['rol'] === 'administrativo' ? 'admin/index.php' : 'terapeuta/index.php';
            header('Location: ' . $destino);
            exit;
        }
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}

$paginaActual = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVANZA - Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/landing.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/nav-landing.php'; ?>
    <main class="auth">
        <aside class="auth_panel">
            <div class="auth_panel-encabezado">
                <h2 class="auth_panel-titulo">Toda la evolución del niño ordenada en un solo lugar</h2>
                <p class="auth_panel-texto">AVANZA es el puente que une a profesionales y centros de rehabilitación. Diseñado para simplificar la gestión sin perder la calidez humana y profesional que tu equipo necesita.</p>
            </div>
            <div class="auth_ilustracion">
                <img src="../assets/img/login-ilustracion.png" alt="" class="auth_ilustracion-img">
            </div>
            <div class="auth_highlights">
                <div class="auth_highlight">
                    <span class="auth_punto auth_punto-coral"></span>
                    <span class="auth_highlight-texto">Agenda de turnos</span>
                </div>
                <div class="auth_highlight">
                    <span class="auth_punto"></span>
                    <span class="auth_highlight-texto">Evoluciones Clínicas</span>
                </div>
            </div>
        </aside>

        <section class="auth_card">
            <div class="auth_bienvenida">
                <h1 class="auth_titulo">¡Hola de nuevo!</h1>
                <p class="auth_subtitulo">Ingresá a tu panel para gestionar tus turnos o pacientes</p>
            </div>
            <form method="post" class="auth_form">
                <?php if($error): ?>
                    <p class="auth_error"><?= $error ?></p>
                <?php endif; ?>

                <div class="campo">
                    <label for="usuario" class="campo_label">Correo Electrónico</label>
                    <div class="campo_input">
                        <input type="text" id="usuario" name="usuario" placeholder="centro.avanza" value="<?= e($usuarioIngresado) ?>" autocomplete="username" required>
                    </div>
                </div>

                <div class="campo">
                    <label for="password" class="campo_label">Contraseña</label>
                    <div class="campo_input">
                        <input type="password" id="password" name="password" placeholder="********" required>
                        <button type="button" class="campo_ojo" data-toggle-password="password" aria-label="Mostrar Contraseña">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7A8A8B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn_primario auth_boton">Iniciar Sesión</button>
                <p class="auth_ayuda">¿Olvidaste tu contraseña? Pedísela al administrativo del centro.</p>
            </form>
        </section>
    </main>
    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>