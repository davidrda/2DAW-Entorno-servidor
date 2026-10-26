<?php

// Muestra el carrito en una tabla.
// $_SESSION['carrito'] = [id => cantidad], p. ej. [1 => 2, 3 => 1]
// significa 2 unidades del producto 1 y 1 unidad del producto 3.
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/productos.php';

cabecera('Tu carrito');
?>

<section class="intro">
    <p class="etiqueta">ESTADO DEL CARRITO</p>
    <h1>Tu carrito</h1>
</section>

<?php if (empty($_SESSION['carrito'])): ?>

    <div class="panel">
        <p>Tu carrito está vacío.</p>

        <a class="boton" href="index.php">
            Ir al catálogo
        </a>
    </div>

<?php else: ?>

    <?php
    // ACTIVIDAD 1 (pendiente): <div class="panel"> con una <table>.
    // <thead> con 5 columnas (Producto, Precio, Cantidad, Subtotal, Acción);
    // <tbody> con las filas, que se generan con PHP (actividad 2).
    ?>

    <?php
    // ACTIVIDAD 2 (pendiente): foreach ($_SESSION['carrito'] as $id => $cantidad)
    // con sintaxis alternativa (foreach (...): ... endforeach;), que es la que se usa
    // para mezclar PHP con HTML. Genera una <tr> por producto.
    // En el foreach la clave es el ID del producto y el valor, la cantidad.
    ?>

    <?php
    // ACTIVIDAD 3 (pendiente): el ID del carrito podría no existir en el catálogo
    // (sesión antigua o dato manipulado). Si !isset($productos[$id]) -> continue;
    // que salta a la siguiente vuelta. Si existe, guardar $productos[$id] en $producto.
    ?>

    <?php
    // ACTIVIDAD 4 (pendiente): una <td> por dato: nombre, precio con euros() y cantidad.
    // Todo texto se imprime con e(), y los importes con euros().
    ?>

    <?php
    // ACTIVIDAD 5 (pendiente): subtotal = $producto['precio'] * (int) $cantidad,
    // mostrado con euros(). Ej.: 20 € x 3 unidades = 60 €.
    ?>

    <?php
    // ACTIVIDAD 6 (pendiente): en la columna Cantidad, un formulario POST a actualizar.php
    // con campoCsrf(), producto_id oculto, <input type="number" name="cantidad" min="0" max="99">
    // (valor inicial: la cantidad actual) y un botón "Actualizar".
    // Este formulario solo envía datos; la sesión se modifica en actualizar.php.
    ?>

    <?php
    // ACTIVIDAD 7 (pendiente): otro formulario POST a eliminar.php con campoCsrf(),
    // producto_id oculto y botón "Eliminar" (clase "peligro").
    // Hace falta enviar el ID porque el servidor no sabe en qué fila se pulsó.
    // No se borra aquí: carrito.php solo muestra, el cambio lo hace eliminar.php.
    ?>

    <?php
    // ACTIVIDAD 8 (pendiente): <p class="total"> con "Total:" y euros(totalCarrito($productos)).
    // Calcularlo en una función evita repetir el cálculo en cada página y los errores
    // de tener varias copias (es como un método reutilizable en Java).
    ?>

    <?php
    // ACTIVIDAD 9 (pendiente): formulario POST a vaciar.php con campoCsrf() y botón
    // "Vaciar carrito" (clase "secundario"). No lleva producto_id porque afecta a todo el carrito.
    ?>
<?php endif; ?>
<?php pie(); ?>
