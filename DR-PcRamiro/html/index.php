<?php

require_once __DIR__ . "/../php/funciones.php";
require_once __DIR__ . "/../php/datos.php";

// Criterio de orden por GET: index.php?orden=nombre|precio|id
// Dato del usuario → lo leemos con leerCadena(), nunca con $_GET directo
$orden = leerCadena($_GET, "orden");

if ($orden === "") {
    $orden = "id"; // orden por defecto
}

// Ordenamos una copia para no tocar los datos originales
$productosOrdenados = $productos;

// usort() ordena con nuestra función de comparación.
// <=> devuelve negativo / 0 / positivo según $a sea menor / igual / mayor que $b
if ($orden === "nombre") {

    usort($productosOrdenados, function (array $a, array $b): int {
        return $a["nombre"] <=> $b["nombre"];
    });

} elseif ($orden === "precio") {

    usort($productosOrdenados, function (array $a, array $b): int {
        return $a["precio"] <=> $b["precio"];
    });

} else {

    // Cualquier valor desconocido (?orden=patata) acaba ordenando por id
    $orden = "id";

    usort($productosOrdenados, function (array $a, array $b): int {
        return $a["id"] <=> $b["id"];
    });
}

// Ojo: abajo se recorre $productosOrdenados, no $productos

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>DWES Store</title>

    <link
        rel="stylesheet"
        href="css/estilos.css"
    >

</head>


<body>


<header class="cabecera">

    <div class="contenedor">

        <h1>DWES Store</h1>

        <p>Versión estática en HTML y CSS</p>

        <nav class="navegacion">

            <a href="index.php">
                Inicio
            </a>

            <a href="buscar.php">
                Buscar
            </a>

            <a href="compra.php">
                Comprar
            </a>

        </nav>

    </div>

</header>


<main class="contenedor">

    <section class="panel">

        <h2>Catálogo</h2>
        <p>Orden actual:  </p>
        <nav class="navegacion">
            <a href="index.php?orden=id">Por id</a>
            <a href="index.php?orden=nombre">Por Nombre</a>
            <a href="index.php?orden=precio">Por Precio</a>
        </nav>

    </section>


    <section class="grid-productos">

        <?php
        // Un <article> por cada producto
        foreach ($productosOrdenados as $producto) {
        ?>

            <article class="producto">

                <h2>
                    <?= $producto["nombre"] ?>
                </h2>

                <p>
                    Categoria:
                    <?= $producto["categoria"] ?>
                </p>

                <p class="precio">
                    <?= formatearPrecio($producto["precio"]) ?>
                </p>

                <p>
                    Stock:
                    <?= $producto["stock"] ?>
                </p>

                <!-- La clase CSS (agotado/aviso/disponible) depende del stock -->
                <p class="estado <?= obtenerClaseEstado($producto["stock"]); ?>">

                    Estado:

                    <?= obtenerEstadoStock($producto["stock"]); ?>

                </p>

                <a class="boton" href="producto.php?id= <?= $producto["id"] ?> " >Ver Producto</a>

            </article>

        <?php
        }
        ?>

    </section>

</main>


<footer class="pie">

    <div class="contenedor">

        Proyecto de Desarrollo Web en Entorno Servidor

    </div>

</footer>


</body>

</html>
