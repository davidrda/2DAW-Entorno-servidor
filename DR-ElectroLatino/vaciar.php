<?php

// TODO: dejar $_SESSION['carrito'] = [] y volver('carrito.php').
// No necesita producto_id porque afecta a todo el carrito. Ahora solo muestra un aviso.
require_once __DIR__ . '/includes/config.php';
exigirPostValido();

cabecera('Funcionalidad pendiente');
?>
<div class="panel"><h1>Vaciar carrito</h1><p>Esta operación se completará en la segunda clase.</p><a href="carrito.php">Volver</a></div>
<?php pie(); ?>
