<?php

// Constantes (sin $, en MAYÚSCULAS)
const IVA_GENERAL = 0.21;          // 21 %
const UNIDADES_DESCUENTO = 5;      // a partir de 5 unidades hay descuento
const DESCUENTO_CANTIDAD = 0.10;   // 10 % de descuento

// Array de arrays: cada producto es un array asociativo (clave => valor)
// IMPORTANTE: el precio va en CÉNTIMOS enteros (7990 = 79,90 €)
// para evitar errores de precisión con decimales (float)
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

// Sin ?> de cierre a propósito: evita enviar espacios o saltos de línea sobrantes
