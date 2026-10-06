<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$error = '';
$email = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pdo = conectarDB();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt = $pdo->execute([$_POST['email']]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($_POST['password'], $usuario['password'])) {
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];
        header('Location: ' . $usuario['rol'] . '/dashboard.php');
        exit;
    }
    $error = 'Correo o contraseña incorrectos.';
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
</head>
<body>
    <?php require_once __DIR__ . '/../includes/nav-landing.php'; ?>
    <main class="auth">
        <aside class="auth_panel">
            <div class="auth_panel-encabezado">
                <h2 class="auth_panel-titulo">Toda la evolución del niño ordenada en un solo lugar</h2>
                <p class="auth_panel-texto">AVANZA es el puente que une a familias, terapeutas y centros de rehabilitación. Diseñado para simplificar la gestion sin perder la calidez humana y profesional que tu equipo necesita.</p>
            </div>
            <div class="auth_ilustracion">
                <img src="../assets/img/login-ilustracion.png" alt="" class="auth_ilustracion-img">
            </div>
            <div class="auth_highlights">
                <div class="auth_highlight">
                    <span class="auth_punto auth_punto-coral"></span>
                    <span class="auth_highlight-texto">Seguimiento Familiar</span>
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
                    <label for="email" class="campo_label">Correo Electronico</label>
                    <div class="campo_input">
                        <input type="email" id="email" name="email" placeholder="nombre@centro.com" value="<?= htmlspecialchars($email)?>" required>
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

                <div class="auth_opciones">
                    <label class="auth_recordarme">
                        <input type="checkbox" name="recordarme">
                        <span>Recordarme</span>
                    </label>
                    <a href="#" class="auth_olvido">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="btn btn-primario auth_boton">Iniciar Sesión</button>
            </form>

            <div class="auth-divisor">
                <span class="auth_linea"></span>
                <span class="auth_divisor-texto">o también</span>
                <span class="auth_linea"></span>
            </div>

            <div class="auth_registro">
                <h3 class="auth_registro-titulo">¿Sos familia nueva en el centro?</h3>
                <p class="auth_registro-texto">Creá tu cuenta familiar de forma simple para registrar al paciente y solicitar los primeros turnos autorizados.</p>
                <a href="#" class="auth_registro-link">Crear cuenta familiar <span>→</span></a>
            </div>
        </section>

    </main>

    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
    <script src="../assets/js/main.js"></script>
</body>
</html>