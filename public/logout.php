<?php 
require_once __DIR__ . '/../includes.php';
session_destroy();
header('Location: index.php');
exit;