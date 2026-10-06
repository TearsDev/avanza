<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function exigirRol($rol) {
    if (!isset($_SESSIOn['rol']) || $_SESSION['rol'] !== $rol) {
        header('Location: ../login.php');
        exit;
    }
}