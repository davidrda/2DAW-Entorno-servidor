<?php

// TODO: quitar un producto del carrito según su producto_id.
// Validarlo igual que en agregar.php (filter_input) y usar unset($_SESSION['carrito'][$id]).
// Ahora solo muestra un aviso.
require_once __DIR__ . '/includes/config.php';
exigirPostValido();

cabecera('Funcionalidad pendiente');
?>
<div class="panel"><h1>Eliminar producto</h1><p>Esta operación se completará en la segunda clase.</p><a href="carrito.php">Volver</a></div>
<?php pie(); ?>
