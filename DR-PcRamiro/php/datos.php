<?php

/*
| En este archivo vamos a almacenar información que utilizará nuestra
| aplicación:
|
|   1. Constantes de configuración.
|   2. Un array con los productos de la tienda.
*/

const IVA_GENERAL = 0.21;
const UNIDADES_DESCUENTO = 5;
const DESCUENTO_CANTIDAD = 0.10;

$productos = [

    /*
    | Este array representa UN producto.
    |
    | Utilizamos un ARRAY ASOCIATIVO.
    |
    | En un array asociativo cada dato tiene:
    |
    |     clave => valor
    |
    | Por ejemplo:
    |
    |     "nombre" => "Teclado"
    |
    | La clave es:
    |
    |     "nombre"
    |
    | y el valor asociado es:
    |
    |     "Teclado"
    |
    | El operador:
    |
    |     =>
    |
    | relaciona una CLAVE con un VALOR dentro de un array.
    |
    | Podemos leerlo mentalmente como:
    |
    |     "nombre" apunta a "Teclado"
    |
    */

    [
        /*
         * Posteriormente podremos acceder a este dato mediante:
         *
         *     $producto["id"]
         */

        "id" => 1,

        /*
         * Para acceder posteriormente:
         *
         *     $producto["nombre"]
         *
         * obtendríamos:
         *
         *     "Teclado"
         */

        "nombre" => "Teclado",

        /*
         * La categoría también es un string.
         *
         * PHP trabaja perfectamente con caracteres UTF-8 como:
         *
         *     é
         *
         * siempre que nuestros archivos estén correctamente
         * guardados utilizando UTF-8.
         */

        "categoria" => "Periféricos",

        /*
         * PRECIO
         * ---------------------------------------------------------------
         *
         * El precio se almacena como:
         *
         *     7990
         *
         * y NO como:
         *
         *     79.90
         *
         * En este proyecto hemos decidido almacenar el dinero
         * utilizando CÉNTIMOS ENTEROS.
         *
         * Por tanto:
         *
         *     7990 céntimos
         *
         * equivalen a:
         *
         *     79,90 €
         *
         * Esto significa que el tipo de dato de "precio" es:
         *
         *     int
         *
         * Esta estrategia evita muchos de los problemas de precisión
         * que pueden aparecer al realizar cálculos monetarios utilizando
         * números de punto flotante.
         */

        "precio" => 7990,


        /*
         * STOCK
         * ---------------------------------------------------------------
         *
         * Representa el número de unidades disponibles.
         *
         * Como 7 es un número entero:
         *
         *     int
         *
         * podremos realizar operaciones y comparaciones:
         *
         *     $producto["stock"] > 0
         *
         *     $producto["stock"] <= 5
         *
         * etc.
         */

        "stock" => 7
    ],


    /*
    |--------------------------------------------------------------------------
    | LA COMA ENTRE ELEMENTOS
    |--------------------------------------------------------------------------
    |
    | Observa:
    |
    |     ],
    |
    | Cerramos el array del primer producto con:
    |
    |     ]
    |
    | y escribimos una coma:
    |
    |     ,
    |
    | porque después viene otro elemento del array $productos.
    |
    | Podemos imaginar:
    |
    |     $productos = [
    |
    |         producto1,
    |         producto2
    |
    |     ];
    |
    */

    // #region SEGUNDO PRODUCTO
    /*
    |--------------------------------------------------------------------------
    | SEGUNDO PRODUCTO
    |--------------------------------------------------------------------------
    |
    | Creamos otro array asociativo con exactamente la misma estructura.
    |
    | Es decir, todos nuestros productos tienen:
    |
    |     id
    |     nombre
    |     categoria
    |     precio
    |     stock
    |
    | Esto es muy útil porque posteriormente podremos recorrer
    | todos los productos utilizando foreach.
    |
    | NOTA:
    |
    | En el código original este segundo producto tiene exactamente
    | los mismos datos y también:
    |
    |     "id" => 1
    |
    | PHP permite hacerlo; no es un error de sintaxis.
    |
    | Sin embargo, en una aplicación real normalmente queremos que
    | cada producto tenga un identificador único.
    |
    | Por ejemplo:
    |
    |     producto 1 → id 1
    |     producto 2 → id 2
    |     producto 3 → id 3
    |
    */
    // #endregion
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


/*
|--------------------------------------------------------------------------
| IMPORTANTE SOBRE ?>
|--------------------------------------------------------------------------
|
| En un archivo que contiene solamente PHP, como datos.php,
| normalmente NO necesitamos escribir la etiqueta de cierre:
|
|     ?>
|
| De hecho, en proyectos PHP es habitual omitirla.
|
| El archivo podría terminar directamente después del código PHP.
|
| Esto ayuda a evitar que espacios o saltos de línea accidentales
| situados después de ?> sean enviados al navegador.
|
| Por tanto, para datos.php sería perfectamente correcto terminar
| el archivo aquí, sin escribir ?>.
|
*/