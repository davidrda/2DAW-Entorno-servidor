<?php

// Laboratorio: muestra la cookie, el ID y el contenido de la sesión.
// Sirve para ver que el navegador solo guarda el ID y los datos están en el servidor.
require_once __DIR__ . '/includes/config.php';

// Esta misma página recibe el POST del botón "Regenerar" y también se muestra con GET
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {

    exigirPostValido();

    // Cambia el ID de sesión pero conserva los datos (el carrito sigue igual).
    // true = elimina el archivo de la sesión antigua, para que el ID viejo ya no sirva.
    // Es lo que se hace al iniciar sesión (login) para evitar session fixation.
    session_regenerate_id(true);

    volver('sesion.php');

}

cabecera('Inspección de sesión');
?>
<section class="intro">
    <p class="etiqueta">LABORATORIO</p><h1>¿Cómo funciona una sesión?</h1></section>
<div class="panel">

  <p> <strong>Nombre de la cookie de sesión: </strong>
     <code class="romper"><?= e(session_name()) ?>  </code>
  </p>

  <p> <strong>Id de sesión: </strong>
     <code class="romper"><?= e(session_id()) ?>  </code>
  </p>

  <?php // print_r(..., true) devuelve el array como texto en vez de imprimirlo; e() lo escapa ?>
  <p><?= e(print_r($_SESSION['carrito'], true)) ?> </p>

  <p> Unidades: <?= unidadesCarrito() ?> </p>

  <form method="post">

    <?= campoCsrf() ?>

     <button type="submit">
        Regenerar identificador de sesión
     </button>

  </form>

  <form action="cerrar.php" method="post" class="separado">
      <?= campoCsrf() ?>

     <button type="submit">
        Cerrar sesión y borrar carrito
     </button>

  </form>

</div>
<?php pie(); ?>
