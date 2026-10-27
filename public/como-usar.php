<?php
$paginaActual = 'como-usar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVANZA - Cómo usar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="assets/css/como-usar.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/nav-landing.php'; ?>

    <section class="hero_como">
        <h1 class="hero_como-titulo">Todo lo que necesita tu centro</h1>
        <p class="hero_como-subtitulo">Una guía clara para profesionales y administración</p>
    </section>

    <section class="como_contenido">
        <div class="como_intro">
            <h2 class="como_intro-titulo">Cómo usar AVANZA</h2>
            <p class="como_intro-texto">El recorrido real del sistema, explicado de manera simple para cada perfil: acceso, turnos, evolución, agenda y configuración.</p>
        </div>

        <article class="paso paso-claro">
            <div class="paso_texto">
                <div class="paso_encabezado">
                    <span class="paso_numero">01</span>
                    <h3 class="paso_titulo">Acceso y creación de cuenta</h3>
                </div>
                <p class="paso_descripcion">Cada perfil ingresa al sistema con su usuario y contraseña. Si aún no tiene acceso, el administrador del centro puede crear la cuenta y asignar permisos desde la configuración.</p>
                <div class="paso_etiquetas">
                    <span class="etiqueta">Ingreso seguro</span>
                    <span class="etiqueta">Creación de cuenta</span>
                    <span class="etiqueta">Permisos por rol</span>
                </div>
            </div>
            <div class="paso_ilustracion">
                <img src="assets/img/como-acceso.png" alt="Ilustración de acceso" class="paso_imagen">
            </div>
        </article>

        <article class="paso">
            <div class="paso_ilustracion">
                <img src="assets/img/como-profesional.png" alt="Ilustración de profesionales" class="paso_imagen">
            </div>
            <div class="paso_texto">
                <div class="paso_encabezado">
                    <span class="paso_numero paso_numero-coral">02</span>
                    <h3 class="paso_titulo">Profesional: agenda, pacientes y sesiones</h3>
                </div>
                <p class="paso_descripcion">El profesional organiza su agenda, revisa pacientes asignados y registra sesiones de manera ordenada. Todo queda disponible para seguimiento y coordinación con el equipo.</p>
                <div class="paso_etiquetas">
                    <span class="etiqueta etiqueta-coral">Agenda</span>
                    <span class="etiqueta etiqueta-coral">Pacientes</span>
                    <span class="etiqueta etiqueta-coral">Registrar sesiones</span>
                </div>
            </div>
        </article>

        <article class="paso paso-claro">
            <div class="paso_texto">
                <div class="paso_encabezado">
                    <span class="paso_numero">03</span>
                    <h3 class="paso_titulo">Administración: turnos, personas y configuración</h3>
                </div>
                <p class="paso_descripcion">La administración centraliza turnos, gestiona personas, ajusta permisos y configura el sistema según las necesidades del centro. Todo queda disponible en un mismo lugar y con una lógica clara.</p>
                <div class="paso_etiquetas">
                    <span class="etiqueta">Turnos</span>
                    <span class="etiqueta">Personas</span>
                    <span class="etiqueta">Configuración</span>
                    <span class="etiqueta">Permisos</span>
                </div>
            </div>
            <div class="paso_ilustracion">
                <img src="assets/img/como-admin.png" alt="Ilustración de admin" class="paso_imagen">
            </div>
        </article>
    </section>

    <section class="cta_landing">
            <div class="cta_landing-contenido">
                <h2 class="cta_landing-titulo">Empezá a ordenar turnos, seguimiento y coordinación en un solo lugar</h2>
            </div>
            <div class="cta_landing-botones">
                <a href="login.php" class="cta_boton cta_boton-coral">Iniciar sesión</a>
                <a href="contacto.php" class="cta_boton cta_boton-blanco">Contactanos</a>
            </div>
    </section>

    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
</body>
</html>