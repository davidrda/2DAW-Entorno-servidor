<?php

// Añade 1 unidad del producto al carrito y redirige a carrito.php.
// No muestra nada: solo procesa el POST y vuelve (patrón POST/Redirect/GET).
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/productos.php';
exigirPostValido();

// Los datos del formulario los controla el usuario (aunque el campo sea oculto,
// se puede editar desde DevTools), así que nunca se usan sin validar.
// filter_input devuelve: el entero si es válido, false si no lo es, null si no llegó.
$id = filter_input(
    INPUT_POST,
    'producto_id',
    FILTER_VALIDATE_INT);

// Tiene que ser un entero Y un producto que exista en el catálogo.
// 400 = petición incorrecta (el fallo es del cliente, no del servidor).
if ($id === false || $id === null || !isset($productos[$id])) {
    http_response_code(400);
    exit('Producto no válido.');
}

// ?? 0 : si el producto aún no está en el carrito, parte de 0 unidades
$actual = (int) ( $_SESSION['carrito'][$id] ?? 0 );

// min() pone un tope de 99 unidades por producto
$_SESSION['carrito'][$id] = min($actual + 1, 99);

volver('carrito.php');
