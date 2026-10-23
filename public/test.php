<?php
require_once __DIR__ . '/../config/database.php';

echo "Conexion exitosa<br><br>";

$resultado = $conn->query("SELECT usuario, rol, activo FROM usuarios");

while ($fila = $resultado->fetch_assoc()) {
    echo $fila['usuario'] . " - " . $fila['rol'] . " - " . $fila['activo'] . "<br>";
}