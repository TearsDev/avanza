<?php
$paginaActual = 'terminos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVANZA - Términos y Condiciones</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="assets/css/legal.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/nav-landing.php'; ?>

    <main class="legal_pagina">
        <article class="legal_articulo legal_articulo-terminos">
            <header class="legal_encabezado">
                <h1 class="legal_titulo">Términos y Condiciones</h1>
                <p class="legal_fecha">Última actualización: 24 de septiembre de 2026</p>
                <div class="legal_divisor"></div>
            </header>

            <div class="legal_cuerpo">
                <p class="legal_texto">Estos Términos y Condiciones regulan el acceso y uso de AVANZA, la plataforma de gestión de turnos y seguimiento terapéutico del centro. Al ingresar o utilizar el sistema, los profesionales y el personal administrativo aceptan las condiciones descriptas a continuación.</p>

                <h2 class="legal_subtitulo">1. Aceptación de los términos</h2>
                <p class="legal_texto">El uso de la plataforma implica la aceptación plena de estos Términos y Condiciones y de la Política de Privacidad. Si no estás de acuerdo con alguno de sus puntos, te pedimos que no utilices el servicio.</p>

                <h2 class="legal_subtitulo">2. Descripción del servicio</h2>
                <p class="legal_texto">AVANZA es una plataforma que permite gestionar turnos, registrar sesiones y hacer seguimiento de la evolución de los pacientes, y coordinar el trabajo entre profesionales y el equipo administrativo del centro. No reemplaza la atención médica presencial ni constituye asesoramiento clínico por sí sola.</p>

                <h2 class="legal_subtitulo">3. Cuentas de usuario y roles</h2>
                <p class="legal_texto">Cada cuenta pertenece a uno de dos roles, profesional o administrativo, con accesos y permisos distintos según lo definido por el centro. Las cuentas son creadas por el personal administrativo; no existe registro público. Sos responsable de mantener la confidencialidad de tu contraseña y de toda actividad realizada desde tu cuenta. Ante cualquier uso no autorizado, debés notificarlo de inmediato al centro.</p>

                <h2 class="legal_subtitulo">4. Uso aceptable</h2>
                <p class="legal_texto">Te comprometés a utilizar la plataforma de forma responsable, a cargar información veraz y actualizada, y a no utilizar el sistema para fines distintos a la gestión de turnos y el seguimiento terapéutico de los pacientes autorizados. Queda prohibido intentar acceder a información de pacientes que no correspondan a tu cuenta o rol.</p>

                <h2 class="legal_subtitulo">5. Responsabilidad sobre la información clínica</h2>
                <p class="legal_texto">Las notas de sesión y los diagnósticos son cargados por los profesionales del centro bajo su propio criterio clínico. AVANZA provee la herramienta para su registro y consulta, pero no participa en la elaboración del contenido clínico ni garantiza su exactitud médica.</p>

                <h2 class="legal_subtitulo">6. Turnos y cancelaciones</h2>
                <p class="legal_texto">Los turnos registrados en la plataforma quedan sujetos a la disponibilidad del profesional y a la confirmación del centro. Las cancelaciones o reprogramaciones deben realizarse con la anticipación que indique el centro; su incumplimiento puede estar sujeto a las políticas internas de ausentismo del establecimiento.</p>

                <h2 class="legal_subtitulo">7. Propiedad intelectual</h2>
                <p class="legal_texto">El diseño, la marca, el código y los contenidos propios de la plataforma Avanza son propiedad del centro y no pueden reproducirse, copiarse ni distribuirse sin autorización. Los datos cargados por cada usuario siguen siendo de su titularidad.</p>

                <h2 class="legal_subtitulo">8. Disponibilidad del servicio</h2>
                <p class="legal_texto">Trabajamos para que la plataforma esté disponible de forma continua, pero puede haber interrupciones por mantenimiento, actualizaciones o causas ajenas a nuestro control. No garantizamos disponibilidad ininterrumpida ni nos hacemos responsables por inconvenientes derivados de caídas del servicio.</p>

                <h2 class="legal_subtitulo">9. Limitación de responsabilidad</h2>
                <p class="legal_texto">Avanza no será responsable por daños indirectos derivados del uso o la imposibilidad de uso de la plataforma, ni por decisiones clínicas tomadas por los profesionales del centro. El servicio se ofrece "tal cual", sin garantías adicionales a las expresamente indicadas en estos términos.</p>

                <h2 class="legal_subtitulo">10. Modificaciones a estos términos</h2>
                <p class="legal_texto">Podemos actualizar estos Términos y Condiciones periódicamente para reflejar cambios en el servicio o en la normativa aplicable. Ante modificaciones relevantes, notificaremos a los usuarios a través de la plataforma o por correo electrónico.</p>

                <h2 class="legal_subtitulo">11. Ley aplicable y jurisdicción</h2>
                <p class="legal_texto">Estos términos se rigen por las leyes de la República Argentina. Cualquier controversia derivada de su interpretación o cumplimiento será sometida a los tribunales ordinarios competentes de la provincia de Mendoza.</p>

                <h2 class="legal_subtitulo">12. Contacto</h2>
                <p class="legal_texto">Si tenés preguntas sobre estos Términos y Condiciones, podés escribirnos a hola@avanza.app o a través del formulario de contacto disponible en el sitio.</p>
            </div>
        </article>
    </main>

    <?php require_once __DIR__ . '/../includes/footer-landing.php'; ?>
</body>
</html>