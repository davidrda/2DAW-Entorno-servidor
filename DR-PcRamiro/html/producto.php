<?php

// Detalle de un producto: producto.php?id=2
// Flujo: $_GET["id"] → validar entero → buscar producto
//   id inválido      → 400 Bad Request
//   id no existente  → 404 Not Found

require_once __DIR__ . "/../php/datos.php";      // array $productos
require_once __DIR__ . "/../php/funciones.php";  // buscarProductoPorId(), etc.

// Dato externo sin validar (?? evita warning si no viene "id")
$idBruto = $_GET["id"] ?? "";

// Devuelve el int si es válido, o false si no lo es ("hola")
$id = filter_var($idBruto, FILTER_VALIDATE_INT);

$producto = null;
$error = "";

// === false: comparación estricta, porque 0 también se evaluaría como "falso"
if ($id === false || $id < 1) {
    http_response_code(400);
    $error = "El id de producto no es válido";

} else {
    // Formato correcto: ahora comprobamos si existe (array o null)
    $producto = buscarProductoPorId($productos, $id);

    if ($producto === null) {
        http_response_code(404);
        $error = "El producto no existe";
    }
}

?>


<!-- HTML -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto - DWES Store</title>
    <link rel="stylesheet" href="css/estilos.css">
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
