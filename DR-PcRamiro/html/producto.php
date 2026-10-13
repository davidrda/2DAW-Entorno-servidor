<?php

// Detalle de un producto. Se usa así: producto.php?id=2
//
// Pasos:
//   1. Leer el id de la URL
//   2. Validarlo: si no es un entero ≥ 1 → error 400 (petición incorrecta)
//   3. Buscarlo:  si no existe           → error 404 (no encontrado)
//   4. Si todo va bien, $producto tiene el producto y $error está vacío

// Cargamos lo que necesitamos (parecido a un import en Java).
// __DIR__ es la carpeta de este archivo, así la ruta no depende de dónde se ejecute.
require_once __DIR__ . "/../php/datos.php";      // $productos
require_once __DIR__ . "/../php/funciones.php";  // buscarProductoPorId()

// 1. El id llega por la URL siempre como texto, aunque sea "2".
//    El "?? ''" evita un aviso de PHP si la URL no trae ningún id.
$idBruto = $_GET["id"] ?? "";

// 2. Intentamos convertirlo a entero: "2" → 2 | "hola" → false
$id = filter_var($idBruto, FILTER_VALIDATE_INT);

$producto = null;  // el producto, si existe
$error = "";       // mensaje de error ("" = todo bien)

// Usamos === porque con == el 0 también se consideraría "false"
if ($id === false || $id < 1) {
    // El id no tiene sentido → 400
    http_response_code(400);
    $error = "El id de producto no es válido";

} else {
    // 3. El id es correcto: buscamos el producto (devuelve el producto o null)
    $producto = buscarProductoPorId($productos, $id);

    if ($producto === null) {
        // El id es válido pero no hay producto con ese id → 404
        http_response_code(404);
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
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<!-- Cabecera con el menú -->
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
    // PENDIENTE: estos datos están escritos a mano, no salen de $producto.
    //   - Si $error no está vacío → mostrar el error (con escapar()) y un enlace para volver.
    //   - Si no → pintar $producto["nombre"], ["categoria"], ["precio"] y ["stock"]:
    //     texto con escapar(), precio con formatearPrecio(), estado con
    //     obtenerClaseEstado() y obtenerEstadoStock().
    ?>
    <article class="producto">

        <h2>Teclado mecánico</h2>

        <p>Categoría: Periféricos</p>

        <p class="precio">79,90 €</p>

        <p>Stock: 7</p>

        <p>
            Estado:
            <span class="estado disponible">Disponible</span>
        </p>

        <div class="acciones">
            <a class="boton" href="compra.php">
                Comprar
            </a>
        </div>

    </article>

</main>

</body>
</html>
