<?php
// Este archivo prepara la sesión. Todas las páginas lo incluyen lo primero.
//
// Idea clave: HTTP no recuerda nada entre peticiones. Para "recordar" el carrito:
//   - el SERVIDOR guarda los datos (carrito, token) en un archivo de sesión
//   - el NAVEGADOR guarda solo un número de ticket en la cookie PHPSESSID
// Es como un guardarropa: te dan un resguardo (cookie) y tu abrigo (carrito)
// se queda dentro. En cada petición enseñas el resguardo y PHP busca tus datos.

// 1. TIPADO ESTRICTO
// Sin esto, PHP convierte tipos solo: una función con (int $n) acepta "5" y lo pasa a 5.
// Con strict_types=1 eso lanza un TypeError, más parecido al tipado de Java:
// los fallos salen pronto y no se esconden. Ojo: solo afecta a este archivo,
// y NO valida lo que llega de $_POST o $_COOKIE (eso se comprueba a mano).
declare(strict_types=1);

// 2. SESSION FIXATION (ataque que se evita con la línea de abajo)
// El atacante entra en la web, recibe un ID (ej. abc123) y consigue que la víctima
// use ese mismo ID. Cuando la víctima inicia sesión, el atacante ya conoce su ticket
// y entra como ella. Con use_strict_mode, si llega un ID que el servidor no creó,
// PHP lo ignora y genera uno nuevo, así que el ID "impuesto" no sirve.
// (La defensa se completa regenerando el ID al hacer login: ver sesion.php.)
ini_set('session.use_strict_mode', '1');

// 3. ID SOLO POR COOKIE
// Sin esto PHP aceptaría el ID también en la URL: tienda.php?PHPSESSID=abc123.
// Una URL se copia, se comparte y queda en el historial y en los logs del servidor,
// y quien la tenga entraría con tu sesión. Una cookie no viaja en la URL.
ini_set('session.use_only_cookies', '1');

// 4. ¿ESTAMOS EN HTTPS?
// Guarda true/false para usarlo en el atributo 'secure' de las cookies.
// Hay dos comprobaciones porque algunos servidores rellenan $_SERVER['HTTPS']
// con 'off' cuando la conexión NO es segura, y !empty() daría true por error.
// preferencias.php reutiliza esta variable para sus cookies.
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

// 5. ATRIBUTOS DE LA COOKIE DE SESIÓN
// Hay que configurarlos ANTES de session_start(), que es quien crea la cookie.
session_set_cookie_params([
    // 0 = cookie "de navegador": desaparece al cerrarlo. Si fuera 3600, duraría 1 hora.
    'lifetime' => 0,

    // '/' = el navegador la envía en TODAS las páginas del sitio.
    // Con '/carrito/' no llegaría a index.php y se crearía otra sesión distinta.
    'path' => '/',

    // true = el navegador solo envía la cookie por HTTPS (nadie puede verla en una wifi pública).
    // Usamos $https y no un true fijo porque en http://localhost algunos navegadores
    // no guardarían la cookie y el carrito no funcionaría durante las pruebas.
    'secure' => $https,

    // true = JavaScript no puede leerla (document.cookie no la muestra).
    // Si alguien consigue inyectar un script en la página (XSS), no puede robar el ID.
    'httponly' => true,

    // Lax = el navegador no la envía cuando otra web lanza una petición POST hacia
    // la nuestra, pero sí cuando el usuario hace clic en un enlace normal.
    // Es una primera barrera contra CSRF (ver más abajo). 'Strict' sería más
    // restrictivo: ni siquiera la enviaría al llegar desde un enlace externo.
    'samesite' => 'Lax',
]);

// 6. ARRANCAR LA SESIÓN
// Mira si el navegador envía PHPSESSID:
//   - si existe y es válido: carga sus datos en $_SESSION
//   - si no: crea un ID nuevo y lo manda al navegador con una cabecera Set-Cookie
// Como envía una cabecera HTTP, tiene que ejecutarse antes de imprimir cualquier HTML.
// $_SESSION funciona como un HashMap de Java: claves y valores, uno por visitante.
session_start();

// 7. VALOR INICIAL DEL CARRITO
// Formato: [id_producto => cantidad]. Ej.: [1 => 2, 3 => 1] son 2 unidades del
// producto 1 y 1 unidad del producto 3.
// Se crea vacío para que el resto del código pueda usar $_SESSION['carrito'] sin
// comprobar si existe (si no, funciones como array_sum() fallarían con null).
// is_array() cubre el caso raro de que la clave exista pero con otro tipo de dato.
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// 8. TOKEN CSRF
// El ataque: estás con la tienda abierta y visitas otra web con un formulario oculto
// que apunta a vaciar.php. Tu navegador adjunta tu cookie automáticamente y la
// tienda cree que la petición es tuya. El token lo evita: se genera una vez por sesión,
// se mete en cada formulario nuestro y el servidor comprueba que vuelve igual.
// La otra web no puede conocerlo, así que su formulario falla.
//
// random_bytes(32) = 32 bytes aleatorios de calidad criptográfica (rand() es predecible).
// bin2hex() los pasa a texto legible (64 caracteres) para poder ponerlos en un <input>.
// Todavía NO se usa de verdad: campoCsrf() manda un texto fijo (ver funciones.php).
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Al final, para que las funciones ya tengan la sesión disponible.
// Así cada página solo necesita incluir config.php.
require_once __DIR__ . '/funciones.php';
