<?php
// Tipado estricto: pasar "5" a una función que pide int lanza TypeError
// en vez de convertirlo solo (se parece más a cómo se comporta Java).
// No valida $_POST/$_GET: esos datos se siguen comprobando a mano.
declare(strict_types=1);

// Configuración de la sesión. Debe cargarse ANTES de imprimir HTML:
// session_start() envía cabeceras HTTP y no se pueden enviar tras escribir salida.

// Si llega un PHPSESSID que el servidor no creó, lo rechaza y genera uno nuevo.
// Evita "session fixation": que un atacante te imponga un ID que ya conoce.
ini_set('session.use_strict_mode', '1');

// El ID solo se acepta por cookie, nunca por URL (tienda.php?PHPSESSID=abc).
// Una URL queda en el historial, en logs y en enlaces compartidos.
ini_set('session.use_only_cookies', '1');

// true si la petición actual va por HTTPS. Se reutiliza en preferencias.php.
// En local (http://localhost) vale false y por eso 'secure' no bloquea la cookie.
$https = !empty($_SERVER['HTTPS'])
    && $_SERVER['HTTPS'] !== 'off';

// Atributos de la cookie PHPSESSID (la cookie guarda solo el ID, no el carrito)
session_set_cookie_params([
    'lifetime' => 0,          // sin fecha: se borra al cerrar el navegador
    'path' => '/',            // válida para todas las páginas del sitio
    'secure' => $https,       // solo viaja por HTTPS cuando esté disponible
    'httponly' => true,       // JavaScript no puede leerla (frena robos por XSS)
    'samesite' => 'Lax',      // no se envía en POST desde otras webs (frena CSRF)
]);

// Crea la sesión o recupera la que corresponde al PHPSESSID de la cookie.
// $_SESSION es como un HashMap que vive en el servidor, uno por visitante.
session_start();

// Estructura del carrito: [id_producto => cantidad], p. ej. [1 => 2, 3 => 1]
// Se comprueba también el tipo por si la sesión tuviera un valor corrupto.
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Token CSRF: una cadena imposible de adivinar, distinta en cada sesión.
// Los formularios la envían y el servidor comprueba que coincide,
// así una web externa no puede mandar formularios en nombre del usuario.
// random_bytes() es seguro para criptografía (rand() o mt_rand() no lo son).
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/funciones.php';
