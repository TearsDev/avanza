<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "avanza";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn -> connect_errno) {
    die("Error de conexion: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");