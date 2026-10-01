<?php
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}

function verificarRol(array $rolesPermitidos) : void
{
    if (empty($_SESSION['usuario_id']) || empty($_SESSION['rol'])) {
        header('Location: /login.php');
        exit;
    }

    if(!in_array($_SESSION['rol'], $rolesPermitidos, true)) {
        http_response_code(403);
        exit('No tenés permisos para ver esta pagina');
    }
}

function usuarioLogueado() : bool
{
    return !empty($_SESSION['usuario_id']);
}

function iniciarSesionUsuario(array $usuario): void
{
    session_generate_id(true);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];
}

function cerrarSesion(): void
{
    $_SESSION = [];
    session_destroy();
}