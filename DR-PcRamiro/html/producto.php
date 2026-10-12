<?php

/*
 * DETALLE DE UN PRODUCTO
 * Recibe el id por GET (producto.php?id=2), lo valida y muestra la ficha.
 *
 * Flujo:
 *   id inválido (hola, -5)        -> HTTP 400 Bad Request
 *   id válido pero no existe (999) -> HTTP 404 Not Found
 *   id válido y existe             -> se muestra el producto
 */

// datos.php: array $productos | funciones.php: buscarProductoPorId(), escapar()...
// require_once: son imprescindibles y solo se cargan una vez.
require_once "datos.php";
require_once "funciones.php";

// Dato externo sin validar (por eso "bruto"). ?? evita error si no existe "id".
$idBruto = $_GET["id"] ?? "";

// Devuelve un int si es un entero válido, o false si no lo es.
$id = filter_var($idBruto, FILTER_VALIDATE_INT);

$producto = null; // null = todavía no hay producto
$error = "";      // vacío = todavía no hay error

// Inválido: no es un entero o es menor que 1.
if ($id === false || $id < 1) {

    http_response_code(400); // Bad Request
    $error = "El id de producto no es válido";

} else {

    // Devuelve un array si lo encuentra, o null si no existe.
    $producto = buscarProductoPorId($productos, $id);

    // ID con formato válido, pero sin producto asociado.
    if ($producto === null) {

        http_response_code(404); // Not Found
        $error = "El producto no existe";
    }
}

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto - DWES Store</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<header class="cabecera">
    <div class="contenedor">
        <h1>DWES Store</h1>

        <nav class="navegacion">
            <a href="index.php">Inicio</a>
            <a href="buscar.php">Buscar</a>
            <a href="compra.php">Comprar</a>
        </nav>
    </div>
</header>
<main class="contenedor">

    <?php
    /*
     * Solo se muestra uno de los dos bloques: error o producto.
     * Usamos la sintaxis alternativa (if: / else: / endif;), más cómoda
     * al mezclar PHP con HTML.
     */
    ?>

    <?php if ($error !== ""): ?>

        <!-- CASO 1: hay un error -->
        <section class="panel">

            <h2>Error</h2>

            <?php
            // <?= es la forma corta de echo. Escapamos el texto al sacarlo al HTML
            // (validar al entrar, escapar al salir).
            ?>
            <p><?= escapar($error) ?></p>

            <a class="boton" href="index.php">Volver</a>

        </section>


    <?php else: ?>

        <!-- CASO 2: sin error, $producto existe -->
        <article class="producto">

            <?php
            // Nombre y categoría son texto dinámico: se escapan para que
            // algo como "Teclado <Pro>" no se interprete como etiqueta HTML.
            ?>
            <h2><?= escapar($producto["nombre"]) ?></h2>

            <p>
                Categoría: <?= escapar($producto["categoria"]) ?>
            </p>

            <?php
            // El precio se guarda en céntimos (7990); formatearPrecio() lo muestra como "79,90 €".
            ?>
            <p class="precio">
                <?= formatearPrecio($producto["precio"]) ?>
            </p>

            <?php
            // El stock es un número controlado por la app, no hace falta escaparlo.
            ?>
            <p>
                Stock: <?= $producto["stock"] ?>
            </p>

            <?php
            /*
             * PHP no cambia el CSS: solo genera el nombre de la clase
             * (disponible, aviso...) y el navegador aplica sus estilos.
             * Texto según stock: 0 "Agotado", 3 "Últimas unidades", 7 "Disponible".
             */
            ?>
            <p class="estado <?= obtenerClaseEstado($producto["stock"]); ?>">

                Estado:

                <?= obtenerEstadoStock($producto["stock"]); ?>

            </p>

        </article>

    <?php endif; ?>

</main>

</body>
</html>
