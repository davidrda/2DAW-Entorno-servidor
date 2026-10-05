<?php

// Recibe el precio en CÉNTIMOS (2999 → "29,99 €")
function formatearPrecio(int $centimos): string
{
    $euros = $centimos / 100;

    // number_format(número, decimales, sep. decimal, sep. miles)
    return number_format($euros, 2, ",", ".") . " €";
}


// Texto de stock que ve el usuario: Agotado / Últimas unidades / Disponible
function obtenerEstadoStock(int $stock): string
{
    // === compara valor Y tipo
    if ($stock === 0) {
        return "Agotado";
    }

    // Aquí ya sabemos que stock > 0, así que este caso es de 1 a 5
    if ($stock <= 5) {
        return "Últimas unidades";
    }

    return "Disponible";
}


// Misma lógica que obtenerEstadoStock(), pero devuelve la clase CSS (ver estilos.css)
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


// Escapa el texto antes de pintarlo en HTML (evita inyección XSS)
function escapar(string $texto): string
{
    // ENT_QUOTES: convierte comillas simples y dobles
    // ENT_SUBSTITUTE: reemplaza UTF-8 inválido en vez de devolver ""
    // Aquí | combina flags (no es el OR lógico ||)
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}


// Devuelve el producto con ese id, o null si no existe (?array = array o null)
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

// Prepara un texto para comparar: sin espacios en los extremos y en minúsculas
function normalizarTexto(string $texto): string
{
    $texto = trim($texto);

    // strtolower no trata bien tildes/ñ; mb_strtolower($texto, "UTF-8") sí
    return strtolower($texto);
}

// Devuelve los productos cuyo nombre contiene el texto buscado
function buscarProductos(array $productos, string $busqueda): array
{
    $resultados = [];

    $busqueda = normalizarTexto($busqueda);

    // Búsqueda vacía → sin resultados (str_contains con "" siempre daría true)
    if ($busqueda === "") {
        return $resultados;
    }

    foreach ($productos as $producto) {
        // Normalizamos también el nombre para que no importen mayúsculas
        $nombre = normalizarTexto($producto["nombre"]);

        if (str_contains($nombre, $busqueda)) {
            $resultados[] = $producto; // [] añade al final del array
        }
    }

    return $resultados;
}


// Lee un string de un array ($_GET, $_POST...) de forma segura; "" si no existe o no es string
function leerCadena(array $origen, string $clave): string
{
    // ?? usa "" si la clave no existe o es null (evita warnings)
    $valor = $origen[$clave] ?? "";

    // Descartamos arrays u otros tipos que pueda mandar el usuario
    if (!is_string($valor)) {
        return "";
    }

    return $valor;
}
