<?php
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function tokenCSRF(): string 
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarCSRF(): void
{
    $recibido = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $recibido)) {
        http_response_code(403);
        exit('Token de seguridad invalido. Recargá la pagina e intentá de nuevo');
    }
}