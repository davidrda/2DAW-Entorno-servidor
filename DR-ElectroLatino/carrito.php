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

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php // Una fila <tr> por cada producto: clave = ID, valor = cantidad ?>
                <?php foreach ($_SESSION['carrito'] as $id => $cantidad): ?>

                    <?php
                    // Si el ID ya no está en el catálogo (sesión antigua o dato manipulado),
                    // continue salta a la siguiente vuelta y no se pinta fila.
                    if (!isset($productos[$id])) continue;

                    // Nombre y precio salen del catálogo del servidor, nunca del navegador
                    $producto = $productos[$id];
                    ?>

                    <tr>
                        <td><?= e($producto['nombre']) ?></td>
                        <td><?= euros($producto['precio']) ?></td>

                        <td>
                            <?php // Un formulario por fila: así el POST lleva el producto_id correcto ?>
                            <form action="actualizar.php" method="post" class="en-linea">
                                <?= campoCsrf() ?>
                                <input type="hidden" name="producto_id" value="<?= (int)$id ?>">
                                <?php // min/max solo ayudan al usuario: actualizar.php debe validarlos otra vez ?>
                                <input
                                    type="number"
                                    name="cantidad"
                                    value="<?= (int)$cantidad ?>"
                                    min="0"
                                    max="99"
                                    aria-label="Cantidad de <?= e($producto['nombre']) ?>"
                                >
                                <button type="submit">Actualizar</button>
                            </form>
                        </td>

                        <?php // Subtotal = precio * unidades; (int) asegura que la cantidad es un número ?>
                        <td><?= euros($producto['precio'] * (int)$cantidad) ?></td>

                        <td>
                            <?php // Solo envía el ID: quien borra es eliminar.php, no esta página ?>
                            <form action="eliminar.php" method="post">
                                <?= campoCsrf() ?>
                                <input type="hidden" name="producto_id" value="<?= (int)$id ?>">
                                <button type="submit" class="peligro">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

        <?php // Ahora muestra 0,00 € porque totalCarrito() aún es un TODO (funciones.php) ?>
        <p class="total">
            Total:
            <?= euros(totalCarrito($productos)) ?>
        </p>

        <?php // Sin producto_id: vaciar afecta a todo el carrito ?>
        <form action="vaciar.php" method="post">
            <?= campoCsrf() ?>
            <button type="submit" class="secundario">Vaciar carrito</button>
        </form>
    </div>

<?php endif; ?>
<?php pie(); ?>
