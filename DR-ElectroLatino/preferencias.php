<?php

// Preferencias en cookies: tema (claro/oscuro) y nombre del visitante.
// Diferencia con el carrito: la sesión guarda los datos en el servidor;
// una cookie guarda el dato en el navegador, que lo reenvía en cada petición.
// Por eso una cookie es modificable por el usuario y nunca se debe dar por buena.
require_once __DIR__ . '/includes/config.php';

// Esta página hace dos cosas: procesa el POST de los formularios y muestra el formulario (GET).
// El POST va primero porque setcookie() y la redirección son cabeceras HTTP,
// y deben enviarse antes de escribir cualquier HTML.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {

    exigirPostValido();

    // Cada formulario manda su acción en un campo oculto ('guardar' o 'borrar')
    $accion = $_POST['accion'] ?? 'guardar';

    // Mismos atributos de seguridad que la cookie de sesión (ver config.php).
    // expires es una fecha en segundos: ahora + 30 días (30 * 24 h * 60 min * 60 s)
    $opciones = [
        'expires' => time() + 30 * 24 * 60 * 60,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ];

    if ($accion === 'borrar') {

        // No existe "borrar cookie": se reenvía con la misma configuración (path, etc.)
        // y una fecha ya pasada, y el navegador la elimina. Si el path no coincide, no se borra.
        $opciones['expires'] = time() - 3600;

        setcookie('tema', '', $opciones);
        setcookie('visitante', '', $opciones);

    } else {

        // Lista blanca: solo se aceptan los valores que nosotros permitimos.
        // Es más seguro que intentar detectar valores "malos".
        // in_array con true compara también el tipo (como === en vez de ==).
        $tema = $_POST['tema'] ?? 'claro';

        $tema = is_string($tema)
            && in_array($tema, ['claro', 'oscuro'], true)
                ? $tema
                : 'claro';

        $nombre = $_POST['visitante'] ?? '';

        // is_string: un atacante podría enviar visitante[]=x, que llega como array
        $nombre = is_string($nombre)
            ? trim($nombre)
            : '';

        // Quita caracteres de control (saltos de línea, tabuladores, etc.): rango ASCII 0-31 y 127.
        // No tienen sentido en un nombre y no son válidos en una cookie.
        // preg_replace devuelve null si falla; ?? '' deja una cadena vacía en ese caso.
        $nombre = preg_replace(
            '/[\x00-\x1F\x7F]/u',
            '',
            $nombre
        ) ?? '';

        // maxlength del HTML no basta (se salta desde DevTools): el límite real se pone aquí.
        // Se repite al leer la cookie, porque también pudo editarse a mano.
        $nombre = substr($nombre, 0, 40);

        setcookie('tema', $tema, $opciones);

        if ($nombre !== '') {

            setcookie('visitante', $nombre, $opciones);

        } else {

            // Nombre vacío: se elimina la cookie en vez de guardar una vacía
            $opciones['expires'] = time() - 3600;

            setcookie('visitante', '', $opciones);
        }
    }

    // POST/Redirect/GET: setcookie() solo las envía al navegador; $_COOKIE no se actualiza
    // hasta la siguiente petición. Al redirigir, la página ya las ve y F5 no reenvía el formulario.
    volver('preferencias.php');
}

// Una cookie la controla el usuario: se valida el tipo y la longitud al leerla.
// Si no existe, ?? '' evita el aviso de "clave indefinida".
$visitante = $_COOKIE['visitante'] ?? '';

$visitante = is_string($visitante)
    ? substr($visitante, 0, 40)
    : '';

cabecera('Preferencias');

?>

<section class="intro">

    <p class="etiqueta">
        COOKIES EN EL CLIENTE
    </p>

    <h1>
        Preferencias del visitante
    </h1>

    <p>
        Estas cookies se crean únicamente cuando
        pulsas «Guardar preferencias» y duran
        hasta 30 días, salvo que las borres antes.
    </p>

</section>

<div class="panel estrecho">

    <form method="post">

        <?= campoCsrf() ?>

        <input
            type="hidden"
            name="accion"
            value="guardar"
        >

        <label for="visitante">
            Nombre (opcional)
        </label>

        <?php // e() es obligatorio: el valor viene de una cookie y se imprime dentro de un atributo HTML ?>
        <input
            id="visitante"
            name="visitante"
            maxlength="40"
            value="<?= e($visitante) ?>"
            placeholder="Ej.: Ana"
        >

        <label for="tema">
            Tema visual
        </label>

        <select
            id="tema"
            name="tema"
        >

            <option
                value="claro"
                <?= temaActual() === 'claro'
                    ? 'selected'
                    : '' ?>
            >
                Claro
            </option>

            <option
                value="oscuro"
                <?= temaActual() === 'oscuro'
                    ? 'selected'
                    : '' ?>
            >
                Oscuro
            </option>

        </select>

        <button type="submit">
            Guardar preferencias
        </button>

    </form>

    <form
        method="post"
        class="separado"
    >

        <?= campoCsrf() ?>

        <input
            type="hidden"
            name="accion"
            value="borrar"
        >

        <button
            type="submit"
            class="secundario"
        >
            Borrar cookies de preferencias
        </button>

    </form>

    <p class="nota">
        La cookie de sesión, necesaria para el carrito,
        es independiente de estas preferencias.

        Para la práctica, inspecciona ambas en
        DevTools → Application/Storage → Cookies.
    </p>

</div>

<?php

pie();
