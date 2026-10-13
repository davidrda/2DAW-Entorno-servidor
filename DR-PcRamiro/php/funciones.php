<?php

// Funciones reutilizables de la tienda (las usan index, producto, buscar y compra).
// Este archivo solo define funciones: no pinta nada ni lee datos por sí mismo.
//
// Contenido:
//   1. Formato y estado del stock
//   2. Seguridad y lectura de datos del usuario
//   3. Búsqueda de productos
//   4. Cálculo de la compra


// ============================================================
// 1. FORMATO Y ESTADO DEL STOCK
// ============================================================

// Convierte un precio en céntimos a texto con formato: 2999 → "29,99 €".
function formatearPrecio(int $centimos): string
{
    $euros = $centimos / 100;

    // number_format(número, decimales, separador decimal, separador de miles)
    return number_format($euros, 2, ",", ".") . " €";
}


// Texto del estado de stock que ve el usuario.
// 0 → "Agotado" | de 1 a 5 → "Últimas unidades" | más de 5 → "Disponible"
function obtenerEstadoStock(int $stock): string
{
    // === compara valor y tipo, sin conversiones automáticas
    if ($stock === 0) {
        return "Agotado";
    }

    // Aquí ya sabemos que stock > 0, así que este caso es de 1 a 5
    if ($stock <= 5) {
        return "Últimas unidades";
    }

    return "Disponible";
}


// Misma lógica que obtenerEstadoStock(), pero devuelve la clase CSS (ver estilos.css).
// Así PHP elige el nombre de la clase y el navegador aplica el color.
function obtenerClaseEstado(int $stock): string
{
    if ($stock === 0) {
        return "agotado";
    }

    if ($stock <= 5) {
        return "aviso";
    }

    return "disponible";
}


// ============================================================
// 2. SEGURIDAD Y LECTURA DE DATOS DEL USUARIO
// ============================================================

// Escapa el texto antes de pintarlo en HTML (evita inyección XSS).
// Ejemplo: "<script>" se muestra como texto, no se ejecuta.
function escapar(string $texto): string
{
    // ENT_QUOTES: convierte comillas simples y dobles
    // ENT_SUBSTITUTE: reemplaza UTF-8 inválido en vez de devolver ""
    // Aquí | combina flags (no es el OR lógico ||)
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}


// Lee un texto de un array ($_GET, $_POST...) de forma segura.
// Devuelve "" si la clave no existe o si el valor no es un string.
function leerCadena(array $origen, string $clave): string
{
    // $a ?? $b → usa $a; si no existe o es null, usa $b (evita warnings)
    $valor = $origen[$clave] ?? "";

    // El usuario podría mandar un array (?q[]=x): lo descartamos
    if (!is_string($valor)) {
        return "";
    }

    return $valor;
}


// ============================================================
// 3. BÚSQUEDA DE PRODUCTOS
// ============================================================

// Devuelve el producto con ese id, o null si no existe (?array = array o null).
function buscarProductoPorId(array $productos, int $id): ?array
{
    foreach ($productos as $producto) {
        // return corta la función: no seguimos recorriendo
        if ($producto["id"] === $id) {
            return $producto;
        }
    }

    return null;
}

// Prepara un texto para compararlo: sin espacios en los extremos y en minúsculas.
function normalizarTexto(string $texto): string
{
    $textoNormalizado = trim($texto);

    // strtolower no trata bien tildes/ñ; mb_strtolower($texto, "UTF-8") sí
    return strtolower($textoNormalizado);
}

// Devuelve los productos cuyo nombre contiene el texto buscado (sin importar mayúsculas).
function buscarProductos(array $productos, string $busqueda): array
{
    $resultados = [];

    $busqueda = normalizarTexto($busqueda);

    // Búsqueda vacía → sin resultados (str_contains con "" siempre daría true)
    if ($busqueda === "") {
        return $resultados;
    }

    foreach ($productos as $producto) {
        // Normalizamos también el nombre para comparar en igualdad de condiciones
        $nombre = normalizarTexto($producto["nombre"]);

        if (str_contains($nombre, $busqueda)) {
            $resultados[] = $producto; // [] añade al final del array
        }
    }

    return $resultados;
}


// ============================================================
// 4. CÁLCULO DE LA COMPRA
// ============================================================

// Calcula los importes de una compra. Todo va en céntimos (enteros).
// Recibe: precio de 1 unidad y número de unidades.
// Devuelve: ["subtotal", "descuento", "base", "iva", "total"]
// Necesita las constantes de datos.php (UNIDADES_DESCUENTO, DESCUENTO_CANTIDAD, IVA_GENERAL).
function calcularCompra(int $precio, int $unidades) : array {

        // 1. Precio sin descuentos ni impuestos
        $subtotal = $precio *  $unidades;

        // 2. Descuento: solo si se llega al mínimo de unidades
        $descuento = 0;

        if ($unidades >= UNIDADES_DESCUENTO){

            // round() devuelve float; (int) lo convierte otra vez a céntimos enteros
            $descuento = (int) round($subtotal * DESCUENTO_CANTIDAD);
        }

        // 3. Base imponible = subtotal con el descuento ya aplicado
        $base = $subtotal - $descuento;

        // 4. El IVA se calcula sobre la base, no sobre el subtotal
        $iva = (int) round ($base * IVA_GENERAL);

        // 5. Lo que paga el cliente
        $total = $base + $iva;

        return [
            "subtotal" => $subtotal,
            "descuento" => $descuento,
            "base" => $base,
            "iva" => $iva,
            "total" => $total
        ];

}
