<?php 
$paginaActual = 'contacto';
$enviado = $_SERVER['REQUEST_METHOD'] === 'POST';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVANZA - Contacto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="assets/css/como-usar.css">
    <link rel="stylesheet" href="assets/css/contacto.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/nav-landing.php'; ?>

    <section class="hero_como">
        <h1 class="hero_como-titulo">¿Necesitás ayuda o cotización?</h1>
        <p class="hero_como-subtitulo">Te ayudamos a resolver dudas, coordinar una demo o activar tu centro de manera rápida.</p>
    </section>

    <section class="como_contenido">
        <div class="como_intro">
            <h2 class="como_intro-titulo">Contacto AVANZA</h2>
            <p class="como_intro-texto">Completá el formulario, elegí el canal que prefieras o consultá nuestros datos de contacto. Te responderemos de manera clara y profesional para ayudarte a avanzar con tu centro.</p>
        </div>

        <section class="contacto_formulario">
            <div class="contacto_texto">
                <div class="paso_encabezado">
                    <span class="paso_numero">01</span>
                    <h3 class="paso_titulo">Elegí cómo querés contactarnos</h3>
                </div>
                <p class="paso_descripcion">Si necesitás una respuesta rápida, completá el formulario. Si preferís hablar con un especialista, elegí el canal que mejor se adapte a tu consulta.</p>
            </div>
            <?php if ($enviado): ?>
                <p class="formulario_aviso">¡Gracias! Recibimos tu consulta.</p>
            <?php endif; ?>

            <form class="formulario" method="POST" action="contacto.php">
                <div class="formulario_fila">
                    <div class="campo">
                        <label class="campo_label" for="nombre">Nombre y apellido</label>
                        <input class="campo_input" type="text" id="nombre" name="nombre" placeholder="Tu nombre completo">
                    </div>
                    <div class="campo">
                        <label class="campo_label" for="email">E-mail</label>
                        <input class="campo_input" type="email" id="email" name="email" placeholder="nombre@centro.com">
                    </div>
                </div>
                <div class="campo">
                    <label class="campo_label" for="asunto">Asunto</label>
                    <input class="campo_input" type="text" id="asunto" name="asunto" placeholder="Cotización, demo, soporte o consulta">
                </div>
                <div class="campo">
                    <label class="campo_label" for="mensaje">Mensaje</label>
                    <textarea class="campo_input campo_textarea" id="mensaje" name="mensaje" placeholder="Contanos qué necesitás y te responderemos con la información más útil para tu centro."></textarea>
                </div>
                <div class="formulario_pie">
                    <p class="formulario_nota">Te responderemos en menos de 24 horas hábiles. Si tu consulta es urgente, también podés escribirnos por WhatsApp.</p>
                    <button class="formulario_boton" type="submit">Enviar consulta</button>
                </div>
            </form>
        </section>

    <section class="contacto_canales">
        <div class="canales">
            <div class="paso_encabezado">
                <span class="paso_numero paso_numero-coral">02</span>
                <h3 class="paso_titulo">Canales de contacto</h3>
            </div>
            <p class="paso_descripcion">Elegí el canal que mejor se adapte a tu consulta. Si necesitás una respuesta rápida, el formulario o WhatsApp suelen ser los más efectivos.</p>

            <div class="lista_tarjetas">
                <div class="canal">
                    <span class="canal_icono">
                        <svg class="icono" viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </span>
                    <div class="canal_texto">
                        <span class="canal_titulo">E-mail</span>
                        <span class="canal_dato">hola@avanza.app</span>
                    </div>
                </div>
                <div class="canal">
                    <span class="canal_icono">
                        <svg class="icono" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </span>
                    <div class="canal_texto">
                        <span class="canal_titulo">Teléfono</span>
                        <span class="canal_dato">+54 12 345-678</span>
                    </div>
                </div>
                <div class="canal">
                    <span class="canal_icono">
                        <svg class="icono" viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    </span>
                    <div class="canal_texto">
                        <span class="canal_titulo">WhatsApp</span>
                        <span class="canal_dato">+54 12 345-678</span>
                    </div>
                </div>
                <div class="canal">
                    <span class="canal_icono">
                        <svg class="icono" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <div class="canal_texto">
                        <span class="canal_titulo">Sede</span>
                        <span class="canal_dato">Mendoza, Argentina</span>
                    </div>
                </div>
            </div>
        </div>

        <aside class="horarios">
            <div class="paso_encabezado">
                <span class="paso_numero">03</span>
                <h3 class="paso_titulo">Horarios</h3>
            </div>
            <p class="paso_descripcion">Nuestro equipo responde consultas comerciales y de soporte durante la semana laboral.</p>
            <div class="lista_tarjetas">
                <div class="horario">
                    <span class="canal_icono canal_icono-suave">
                        <svg class="icono" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span>
                <div class="canal_texto">
                    <span class="canal_titulo">Lunes a viernes</span>
                    <span class="canal_dato">09:00 a 18:00 hs</span>
                </div>
            </div>
            <div class="horario">
                <span class="canal_icono canal_icono-suave">
                    <svg class="icono" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                </span>
                <div class="canal_texto">
                    <span class="canal_titulo">Respuesta promedio</span>
                    <span class="canal_dato">Menos de 24 horas hábiles</span>
                </div>
            </div>
            <div class="horario">
                <span class="canal_icono canal_icono-suave">
                    <svg class="icono" viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                </span>
                <div class="canal_texto">
                    <span class="canal_titulo">Soporte prioritario</span>
                    <span class="canal_dato">Para centros activos y casos urgentes</span>
                </div>
            </div>
        </div>
    </aside>
</section>
</section>

<section class="cta_landing">
    <div class="cta_landing-contenido">
        <h2 class="cta_landing-titulo">¿Preferís hablar directamente con nosotros?</h2>
        <p class="cta_landing-texto">Escribinos y te ayudamos a encontrar la mejor forma de implementar AVANZA.</p>
    </div>
    <div class="cta_landing-botones">
        <a href="https://wa.me/5412345678" class="cta_boton cta_boton-blanco">Hablar por WhatsApp</a>
    </div>
</section>
    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
</body>
</html>