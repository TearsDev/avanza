<?php
function cargarEnv(string $rutaArchivo): void
{
    $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        [$clave, $valor] = explode('=', $linea, 2);
        putenv("{$clave}={$valor}");
    }
}

cargarEnv(__DIR__ . '/../.env');