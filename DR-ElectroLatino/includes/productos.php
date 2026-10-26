<?php

declare(strict_types=1);

// Catálogo fijo (sustituye a una tabla de base de datos). La clave es el ID del producto.
// El carrito solo guarda ID y cantidad; nombre y precio se consultan siempre aquí,
// así el precio no puede manipularse desde el navegador.
$productos = [
    1 => ['nombre' => 'Ratón inalámbrico', 'precio' => 15.00, 'icono' => '🖱️'],
    2 => ['nombre' => 'Teclado mecánico', 'precio' => 45.00, 'icono' => '⌨️'],
    3 => ['nombre' => 'Alfombrilla', 'precio' => 8.00, 'icono' => '🟦'],
    4 => ['nombre' => 'Auriculares', 'precio' => 32.00, 'icono' => '🎧'],
];
