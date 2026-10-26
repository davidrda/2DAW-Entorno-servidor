<?php

declare(strict_types=1);

/** Funciones reutilizables: HTML, formato, validación y navegación. */

// Convierte <, >, & y comillas en entidades HTML (<script> -> &lt;script&gt;).
// Imprescindible con datos del usuario o de cookies: evita XSS (que se ejecute su código).
// ENT_QUOTES escapa también ' y " para poder usarla dentro de atributos HTML.
function e(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Formato español: coma decimal, punto de miles y símbolo al final.
// 25.5 -> "25,50 €"   |   1234.5 -> "1.234,50 €"
function euros(float $cantidad): string {
    return number_format($cantidad, 2, ',', '.') . ' €';
}

// Cuenta unidades, no productos distintos: [1 => 2, 3 => 1] devuelve 3.
function unidadesCarrito(): int {
      return array_sum($_SESSION['carrito']);
}

// TODO: recorrer el carrito y sumar precio * cantidad de cada producto.
// Se recibe $productos por parámetro (en vez de usar una global) para que
// la función dependa solo de lo que recibe y se pueda reutilizar en varias páginas.
function totalCarrito(array $productos): float {
    return 0.0;
}

// Barrera para las páginas que cambian datos: si no llegan por POST, error 405.
// Un GET nunca debería modificar el carrito (cualquier enlace o buscador lo lanzaría).
// La cabecera Allow indica al cliente qué método sí se acepta.
function exigirPostValido(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Método no permitido.');
    }
}

// TODO: usar el valor de $_SESSION['csrf_token'] (hoy es el texto fijo "prueba")
// y comprobarlo en exigirPostValido() con hash_equals() al recibir el POST.
function campoCsrf(): string
{
    $token = htmlspecialchars(
        "prueba",
        ENT_QUOTES,
        'UTF-8'
    );

    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Patrón POST/Redirect/GET: tras procesar un POST se redirige a una página que se carga con GET.
// Así, si el usuario recarga (F5), el navegador no vuelve a enviar el formulario
// y no se añade el producto dos veces. 303 = "ve a esta otra URL con GET".
// Tipo never: la función nunca devuelve el control porque llama a exit.
function volver(string $pagina): never {
    header('Location: ' . $pagina, true, 303);
    exit;
}

// TODO: leer $_COOKIE['tema'] y devolver solo 'claro' u 'oscuro' (lista blanca),
// con 'claro' por defecto. Nunca se debe fiar de una cookie sin validar.
function temaActual(): string {
    return 'claro';
}

// Parte superior común de todas las páginas. Cada página la llama primero
// y cierra con pie(): así el menú y el <head> están escritos una sola vez.
function cabecera(string $titulo): void {
    // TODO: con cookies, recuperar aquí el nombre del visitante
    $tema = temaActual();
    ?>
    <!doctype html>
    <html lang="es" data-tema="<?= e($tema) ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($titulo) ?> | MiniTienda PHP</title>
        <link rel="stylesheet" href="css/estilos.css">
    </head>
    <body>
    <header class="cabecera">
        <div class="contenedor barra">
            <a class="marca" href="index.php">MiniTienda PHP</a>
            <nav aria-label="Navegación principal">
                <a href="index.php">Catálogo</a>
                <a href="carrito.php">Carrito (<?= unidadesCarrito() ?>)</a>
                <a href="preferencias.php">Preferencias</a>
                <a href="sesion.php">Sesión</a>
            </nav>
        </div>
    </header>
    <main class="contenedor">
    <?php
}

// Parte inferior común: cierra lo que abrió cabecera() (<main>, <body> y <html>)
function pie(): void {
    ?>
    </main>
    <footer class="pie">Proyecto didáctico de DWES · Unidad 4 · Docente: José Manuel Villar Ferradal</footer>
    </body></html>
    <?php
}
