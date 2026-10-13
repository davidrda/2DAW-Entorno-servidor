<?php

// Página principal: catálogo de productos que se puede ordenar.
// Se usa así: index.php?orden=id | nombre | precio
//
// Pasos:
//   1. Cargar datos y funciones
//   2. Leer el criterio de orden de la URL
//   3. Ordenar una copia de los productos
//   4. Pintar una tarjeta por producto (HTML de abajo)

// __DIR__ es la carpeta de este archivo, así la ruta no depende de dónde se ejecute.
require_once __DIR__ . "/../php/funciones.php";
require_once __DIR__ . "/../php/datos.php";

// 2. Dato del usuario → lo leemos con leerCadena(), nunca con $_GET directo
$orden = leerCadena($_GET, "orden");

if ($orden === "") {
    $orden = "id"; // orden por defecto
}

// 3. Ordenamos una copia para no tocar los datos originales
$productosOrdenados = $productos;

// usort() ordena con la función de comparación que le pasamos.
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

// Ojo: el HTML recorre $productosOrdenados, no $productos

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


<!-- Cabecera con el menú -->
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

    <!-- Enlaces para cambiar el orden (recargan la página con ?orden=...) -->
    <section class="panel">

        <h2>Catálogo</h2>
        <p>Orden actual:  </p>
        <nav class="navegacion">
            <a href="index.php?orden=id">Por id</a>
            <a href="index.php?orden=nombre">Por Nombre</a>
            <a href="index.php?orden=precio">Por Precio</a>
        </nav>

    </section>


    <!-- Catálogo: una tarjeta por cada producto -->
    <section class="grid-productos">

        <?php
        // foreach repite el bloque HTML de abajo una vez por producto
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

                <!-- PHP solo escribe el nombre de la clase (agotado/aviso/disponible);
                     el color lo pone el CSS -->
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


<!-- Pie de página -->
<footer class="pie">

    <div class="contenedor">

        Proyecto de Desarrollo Web en Entorno Servidor

    </div>

</footer>


</body>

</html>
