<?php
require_once __DIR__ . '/../includes/env.php';

function conectarDB(): PDO 
{
    $host = getenv('DB_HOST');
    $db = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');

    $dsn = "mysql:host={$host};dbname{$db};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass);

    return $pdo;
}