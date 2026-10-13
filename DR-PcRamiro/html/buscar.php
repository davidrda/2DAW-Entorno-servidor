<?php

// Buscador de productos. El formulario envía el texto por GET: buscar.php?q=teclado
//
// PENDIENTE (falta conectar la lógica):
//   1. Leer el texto:  $q = leerCadena($_GET, "q");
//   2. Buscar:         $resultados = buscarProductos($productos, $q);
//   3. Pintar $resultados con un foreach (como en index.php)

// datos.php → $productos | funciones.php → leerCadena(), buscarProductos()
// __DIR__ es la carpeta de este archivo, así la ruta no depende de dónde se ejecute.
       require_once __DIR__ . "/../php/datos.php";
       require_once __DIR__ . "/../php/funciones.php";

       // Aquí irán los productos encontrados (de momento vacío)
       $resultados = [];

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar - DWES Store</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<!-- Cabecera con el menú -->
<header class="cabecera">
    <div class="contenedor">
        <h1>Buscar productos</h1>

        <nav class="navegacion">
            <a href="index.php">Inicio</a>
            <a href="compra.php">Comprar</a>
        </nav>
    </div>
</header>

<main class="contenedor">

    <section class="panel">

        <!--
            Formulario por GET: al enviarlo, el texto viaja en la URL (?q=...),
            así el resultado de la búsqueda se puede guardar o compartir como enlace.
            El name="q" es la clave que se leerá en $_GET["q"].
        -->
        <form class="formulario" action="buscar.php" method="GET">

            <div class="campo">
                <label for="q">Nombre del producto</label>

                <input
                    type="text"
                    id="q"
                    name="q"
                    placeholder="Ej.: teclado"
                >
            </div>

            <div>
                <button type="submit">
                    Buscar
                </button>
            </div>

        </form>

    </section>

    <section class="panel">
        <h2>Ejemplo de resultados</h2>
        <p>
            Como esta versión no tiene PHP, estos resultados son estáticos.
        </p>
    </section>

    <!-- Resultados: cuando haya PHP, una tarjeta por cada producto de $resultados -->
    <section class="grid-productos">

        <article class="producto">
            <h3>Teclado mecánico</h3>
            <p>Periféricos</p>
            <p class="precio">79,90 €</p>
            <a class="boton" href="producto.php">
                Ver producto
            </a>
        </article>

    </section>

</main>

</body>
</html>
