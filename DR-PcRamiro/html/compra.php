<?php

// Página de compra: recibe el formulario por POST, lo valida y calcula el importe.
// No guarda nada en base de datos ni hace ningún pago real.
//
// Flujo:
//   GET  → solo se muestra el formulario
//   POST → se leen y validan los datos → si no hay errores, se calcula la compra
//
// Variables que deja listas para el HTML de abajo:
//   $errores               lista de mensajes de error ([] = todo correcto)
//   $compraRealizada       true si la compra se ha calculado bien
//   $resultadoCompra       importes en céntimos: subtotal, descuento, base, iva, total
//   $productoSeleccionado  el producto elegido (o null)
//   $nombre, $email, $unidades, $productoId
//                          lo que escribió el usuario, para rellenar de nuevo el formulario


// ============================================================
// 1. CARGAR ARCHIVOS
// ============================================================

// __DIR__ es la carpeta de este archivo, así la ruta no depende de dónde se ejecute.
require_once __DIR__ . "/../php/datos.php";      // $productos y constantes
require_once __DIR__ . "/../php/funciones.php";  // leerCadena(), buscarProductoPorId()...


// ============================================================
// 2. VALORES INICIALES
// ============================================================
// Son los que se usan cuando la página se abre por primera vez (GET).

$errores = [];

$compraRealizada = false;

$resultadoCompra = null;

$productoSeleccionado = null;

$nombre = "";
$email = "";

$unidades = 1;

$productoId = null;


// ============================================================
// 3. PROCESAR EL FORMULARIO (solo si se ha enviado por POST)
// ============================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- Nombre: no puede estar vacío ---
    // leerCadena() devuelve "" si el campo no existe o no es texto.
    $nombre = leerCadena($_POST, "nombre");

    if ($nombre === "") {

        $errores[] = "Debes de introducir un nombre";

    }

    // --- Email: tiene que tener formato de correo ---
    $emailBruto = leerCadena($_POST, "email");

    // filter_var devuelve el email si es válido, o false si no lo es
    $emailValidado = filter_var($emailBruto, FILTER_VALIDATE_EMAIL);

    if ($emailValidado === false) {
        $errores[] = "Correo electrónico con formato incorrecto";
    } else {
        $email = $emailValidado;
    }

    // --- Producto: id entero válido y que exista en $productos ---
    // filter_var devuelve un int, o false si no es un entero
    $productoIdValidado = filter_var($_POST["producto"] ?? "", FILTER_VALIDATE_INT);

    if ($productoIdValidado === false || $productoIdValidado < 1) {
        $errores[] = "Debes de seleccionar un producto válido";
    } else {
        $productoId = $productoIdValidado;

        // Devuelve el producto o null si ese id no existe
        $productoSeleccionado = buscarProductoPorId($productos, $productoId);

        if ($productoSeleccionado === null) {
            $errores[] = "El producto seleccionado no existe.";
        }

    }

    // --- Unidades: entero mayor que cero ---
    $unidadesValidadas = filter_var($_POST["unidades"] ?? "", FILTER_VALIDATE_INT);

    if ($unidadesValidadas === false || $unidadesValidadas < 1) {
        $errores[] =
            "El número de unidades debe de ser un entero mayor que cero";
    } else {
        $unidades = $unidadesValidadas;
    }

    // --- Stock: solo se puede comprobar si el producto existe ---
    if ($productoSeleccionado !== null && $unidades > $productoSeleccionado["stock"]) {
        $errores[] = "No hay stock suficiente";
    }

    // --- Cálculo: solo si no ha fallado ninguna validación ---
    // Aquí $productoSeleccionado seguro que no es null (si lo fuera, habría error).
    if ($errores === []) {
        $resultadoCompra = calcularCompra($productoSeleccionado["precio"], $unidades);
        $compraRealizada = true;
    }

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar - DWES Store</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<!-- Cabecera con el menú -->
<header class="cabecera">

    <div class="contenedor">

        <h1>Realizar compra</h1>

        <nav class="navegacion">

            <a href="index.php">Inicio</a>

            <a href="buscar.php">Buscar</a>

        </nav>

    </div>

</header>

<main class="contenedor">

    <?php
    // PENDIENTE (parte de la práctica): ahora siempre se pinta solo el formulario.
    //   - Si $errores no está vacío → mostrar la lista de errores con un foreach
    //     (y el formulario debajo, para que el usuario pueda corregir).
    //   - Si $compraRealizada es true → mostrar el resumen con $resultadoCompra
    //     (cada importe con formatearPrecio()) en vez del formulario.
    ?>

    <!-- Formulario: se envía por POST a esta misma página -->
    <form class="formulario" action="compra.php" method="POST">

        <!-- Nombre y email: escapar() evita XSS y conserva lo escrito si hay errores -->
        <div class="campo">

            <label for="nombre">Nombre</label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                value="<?= escapar($nombre) ?>"
            >

        </div>

        <div class="campo">

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= escapar($email) ?>"
            >

        </div>

        <!-- Producto: las opciones salen del array $productos -->
        <div class="campo">

            <label for="producto">Producto</label>

            <select id="producto" name="producto">

                <option value="">Selecciona un producto</option>

                <?php foreach ($productos as $producto): ?>

                    <?php

                    // true si es el producto que el usuario eligió antes de enviar
                    $seleccionado = $productoId === $producto["id"];
                    ?>

                    <option
                        value="<?= $producto["id"] ?>"
                        <?= $seleccionado ? "selected" : "" ?>
                    >

                        <?= escapar($producto["nombre"]) ?>
                        -
                        <?= formatearPrecio($producto["precio"]) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <!-- Unidades: $unidades ya es un int validado, no hace falta escapar -->
        <div class="campo">

            <label for="unidades">Unidades</label>

            <input
                type="number"
                id="unidades"
                name="unidades"
                value="<?= $unidades ?>"
            >

        </div>

        <div>

            <button type="submit">
                Calcular compra
            </button>

        </div>

    </form>

</main>

</body>
</html>
