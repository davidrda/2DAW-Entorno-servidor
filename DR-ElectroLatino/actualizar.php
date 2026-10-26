<?php

// TODO: cambiar la cantidad de un producto. Se reciben producto_id y cantidad:
// validar ambos en el servidor (el min/max del HTML se salta desde DevTools)
// y limitar la cantidad al rango 0-99. Ahora solo muestra un aviso.
require_once __DIR__ . '/includes/config.php';
exigirPostValido();

cabecera('Funcionalidad pendiente');
?>
<div class="panel"><h1>Actualizar cantidad</h1><p>Esta operación se completará en la segunda clase.</p><a href="carrito.php">Volver</a></div>
<?php pie(); ?>
