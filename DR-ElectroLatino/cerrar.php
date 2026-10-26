<?php

// TODO: cerrar sesión de verdad, en 3 pasos:
// 1) $_SESSION = []                  vacía los datos en memoria
// 2) caducar la cookie PHPSESSID     (setcookie con fecha pasada y los mismos atributos)
// 3) session_destroy()               borra el archivo de sesión del servidor
// Hacer solo uno deja la sesión a medias (p. ej. los datos siguen en el servidor).
require_once __DIR__ . '/includes/config.php';
exigirPostValido();

volver('index.php');
