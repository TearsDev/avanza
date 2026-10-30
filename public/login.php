<?php 
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

function irAlPanel($rol) {
    if ($rol === 'administrativo') {
        header('Location: admin/index.php');
    } else {
        header('Location: terapeuta/index.php');
    } exit;
}

if (isset($_SESSION['rol'])) {
    irAlPanel($_SESSION['rol']);
}

$paginaActual = 'login';
$usuario = '';
$tipoError = '';

$mensajes = [
    'credenciales' => 'Usuario o contraseña incorrectos.',
    'inactivo' => 'Tu usuario está inactivo. Consultá con el centro.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, nombre, password, rol, activo FROM usuarios WHERE usuario=?');
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    
    if(!$fila || !password_verify($password, $fila['password'])) {
        $tipoError = 'credenciales';
    } elseif (!$fila['activo']) {
        $tipoError = 'inactivo';
    } else {
        session_regenerate_id(true);
        $_SESSION['id'] = $fila['id'];
        $_SESSION['nombre'] = $fila['nombre'];
        $_SESSION['rol'] = $fila['rol'];
        irAlPanel($fila['rol']);
    }
}

$errorUsuario = $tipoError !== '';
$errorPassword = $tipoError === 'credenciales';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVANZA - Iniciar sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/nav-landing.php';?>
    <main class="login_seccion">
        <section class="login_panel">
            <div class="login_panel-encabezado">
                <h2 class="login_panel-titulo">Toda la evolución del niño ordenada en un solo lugar</h2>
                <p class="login_panel-texto">AVANZA es el puente que une a profesionales y centros de rehabilitación. Diseñado para simplificar la gestión sin perder la calidez humana y profesional que tu equipo necesita.</p>
            </div>
            <img src="assets/img/loginmockup.png" alt="Agenda y evoluciones clínicas" class="login_ilustracion">
            <div class="login_destacados">
                <div class="login_destacado">
                    <span class="login_punto login_punto-coral"></span>
                    Agenda de turnos
                </div>
                <div class="login_destacado">
                    <span class="login_punto"></span>
                    Evoluciones Clínicas
                </div>
            </div>
        </section>

        <section class="login_tarjeta">
            <div class="login_encabezado">
                <h1 class="login_titulo">¡Hola de nuevo!</h1>
                <p class="login_subtitulo">Ingresá a tu panel para gestionar tus turnos o pacientes.</p>
            </div>

            <form class="login_formulario" method="POST" action="login.php">
                <div class="login_campos">
                    <div class="login_campo">
                        <label class="login_label" for="usuario">Usuario</label>
                        <div class="input_caja <?= $errorUsuario ? 'input_caja-error' : '' ?>">
                            <input class="input_elemento" type="text" id="usuario" name="usuario" placeholder="centro.avanza" value="<?= e($usuario) ?>" required>
                        </div>
                    </div>
                    <div class="login_campo">
                        <label class="login_label" for="password">Contraseña</label>
                        <div class="input_caja <?= $errorPassword ? 'input_caja-error' : '' ?>">
                        <input class="input_elemento" type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                </div>

                <div class="login_acciones">
                    <?php if ($tipoError !== ''): ?>
                        <div class="login_error">
                            <svg class="login_error-icono" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                            <span class="login_error-texto"><?= e($mensajes[$tipoError]) ?></span>
                        </div>
                    <?php endif; ?>

                    <button class="login_boton" type="submit">Iniciar sesión</button>
                    <p class="login_ayuda">¿Olvidaste tu contraseña? Pedísela al administrativo del centro.</p>
                </div>
            </form>
        </section>
    </main>
    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>