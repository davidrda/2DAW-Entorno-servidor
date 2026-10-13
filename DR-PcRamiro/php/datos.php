<?php

// Datos de la tienda: constantes y lista de productos (no hay base de datos).
// Las páginas lo cargan con require_once.

// ---- Constantes ----
// Se declaran sin $ y en MAYÚSCULAS; no cambian durante la ejecución.
const IVA_GENERAL = 0.21;          // 21 %
const UNIDADES_DESCUENTO = 5;      // a partir de 5 unidades hay descuento
const DESCUENTO_CANTIDAD = 0.10;   // 10 % de descuento

// ---- Productos ----
// Array de arrays: cada producto es un array asociativo (clave => valor).
// El precio va en CÉNTIMOS enteros (7990 = 79,90 €) para evitar los errores
// de precisión que tienen los decimales (float).
// stock = unidades disponibles (0 = agotado).
$productos = [
    [
        "id" => 1,
        "nombre" => "Teclado",
        "categoria" => "Periféricos",
        "precio" => 7990,
        "stock" => 7
    ],
    [
        "id" => 2,
        "nombre" => "Ratón",
        "categoria" => "Periféricos",
        "precio" => 3990,
        "stock" => 3
    ],
    [
        "id" => 3,
        "nombre" => "Monitor",
        "categoria" => "Monitores",
        "precio" => 19090,
        "stock" => 0
    ]
];

// Este archivo no cierra la etiqueta de PHP a propósito: así no se envían
// espacios ni saltos de línea sobrantes al navegador.
