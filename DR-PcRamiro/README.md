# DWES Store

Pequeña tienda online de periféricos hecha con **PHP, HTML y CSS** para la asignatura de *Desarrollo Web en Entorno Servidor* (2º DAW).

No usa base de datos: los productos están guardados en un array de PHP. Sirve para aprender lo básico de PHP: variables, arrays, funciones, `foreach`, formularios, validación y peticiones `GET`/`POST`.

---

## Estructura del proyecto

```
DR-PcRamiro/
├── html/
│   ├── css/
│   │   └── estilos.css    → estilos de todas las páginas
│   ├── index.php          → página principal (catálogo)
│   ├── producto.php       → ficha de un producto
│   ├── buscar.php         → buscador de productos
│   └── compra.php         → formulario de compra
└── php/
    ├── datos.php          → productos y constantes
    └── funciones.php      → funciones reutilizables
```

> **Nota:** las páginas de `html/` cargan los archivos de `php/` con rutas basadas en `__DIR__` (`require_once __DIR__ . "/../php/datos.php"`). `__DIR__` es la carpeta del propio archivo, así las rutas funcionan se ejecute desde donde se ejecute.

---

## Cómo ejecutarlo

1. Tener PHP instalado.
2. Desde la carpeta del proyecto (`DR-PcRamiro`):
   ```bash
   php -S localhost:8000
   ```
3. Abrir `http://localhost:8000/html/index.php` en el navegador.

---

## Estado del proyecto

| Página | Estado |
|---|---|
| `index.php` | ✅ Funciona: catálogo con orden por id, nombre o precio. |
| `producto.php` | ⚠️ La lógica (validar id, 400/404) está hecha, pero el HTML aún muestra datos fijos. |
| `buscar.php` | ⚠️ Solo el formulario; falta leer `q` y llamar a `buscarProductos()`. |
| `compra.php` | ⚠️ Valida y calcula la compra; falta mostrar los errores y el resumen. |

---

## Explicación de cada archivo

### `php/datos.php` — los datos

Guarda la información de la tienda. No tiene funciones, solo datos.

**Constantes** (valores fijos que no cambian):

| Constante | Valor | Para qué sirve |
|---|---|---|
| `IVA_GENERAL` | `0.21` | IVA del 21 % para calcular precios con impuestos. |
| `UNIDADES_DESCUENTO` | `5` | Unidades a partir de las cuales se aplica descuento. |
| `DESCUENTO_CANTIDAD` | `0.10` | Descuento del 10 % por comprar muchas unidades. |

**Variable `$productos`**: un array de arrays. Cada producto tiene:

| Clave | Tipo | Significado |
|---|---|---|
| `id` | int | Identificador único. |
| `nombre` | string | Nombre del producto. |
| `categoria` | string | Categoría (Periféricos, Monitores…). |
| `precio` | int | Precio **en céntimos** (`7990` = 79,90 €). Se usan enteros para evitar errores de decimales. |
| `stock` | int | Unidades disponibles. |

Productos actuales:

| id | Nombre | Categoría | Precio | Stock |
|---|---|---|---|---|
| 1 | Teclado | Periféricos | 79,90 € | 7 |
| 2 | Ratón | Periféricos | 39,90 € | 3 |
| 3 | Monitor | Monitores | 190,90 € | 0 |

---

### `php/funciones.php` — funciones reutilizables

Aquí están las funciones que usan las demás páginas, agrupadas en 4 bloques.

**1. Formato y estado del stock**

| Función | Qué recibe | Qué devuelve | Para qué sirve |
|---|---|---|---|
| `formatearPrecio(int $centimos)` | Precio en céntimos | `string` | Convierte `7990` en `"79,90 €"`. |
| `obtenerEstadoStock(int $stock)` | Unidades en stock | `string` | Texto del estado: `Agotado` (0), `Últimas unidades` (1–5) o `Disponible`. |
| `obtenerClaseEstado(int $stock)` | Unidades en stock | `string` | Clase CSS según el stock: `agotado`, `aviso` o `disponible`. |

**2. Seguridad y lectura de datos**

| Función | Qué recibe | Qué devuelve | Para qué sirve |
|---|---|---|---|
| `escapar(string $texto)` | Texto | `string` | Protege el texto antes de mostrarlo en HTML (evita XSS). |
| `leerCadena(array $origen, string $clave)` | `$_GET`/`$_POST` y una clave | `string` | Lee un dato de forma segura: si no existe o no es texto, devuelve `""`. |

**3. Búsqueda**

| Función | Qué recibe | Qué devuelve | Para qué sirve |
|---|---|---|---|
| `buscarProductoPorId(array $productos, int $id)` | Lista y un id | `array` o `null` | Devuelve el producto con ese id; si no existe, `null`. |
| `normalizarTexto(string $texto)` | Texto | `string` | Quita espacios sobrantes y pasa a minúsculas, para comparar sin distinguir mayúsculas. |
| `buscarProductos(array $productos, string $busqueda)` | Lista y texto | `array` | Devuelve los productos cuyo nombre contiene el texto. Búsqueda vacía → sin resultados. |

**4. Cálculo de la compra**

| Función | Qué recibe | Qué devuelve | Para qué sirve |
|---|---|---|---|
| `calcularCompra(int $precio, int $unidades)` | Precio de 1 unidad (céntimos) y unidades | `array` | Calcula los importes de la compra, todos en céntimos. |

`calcularCompra()` devuelve un array asociativo y sigue estos pasos:

```
subtotal  = precio × unidades
descuento = 10 % del subtotal, solo si unidades >= 5
base      = subtotal − descuento
iva       = 21 % de la base
total     = base + iva
```

Ejemplo: 5 teclados (7990 céntimos cada uno) → subtotal `39950`, descuento `3995`, base `35955`, IVA `7551`, total `43506` (435,06 €).

---

### `html/index.php` — página principal

Muestra el **catálogo** con todos los productos y permite ordenarlos.

**Variables principales:**

- `$orden`: criterio de ordenación leído de la URL con `leerCadena($_GET, "orden")`. Si viene vacío, se usa `"id"`.
- `$productosOrdenados`: copia de `$productos` ordenada según `$orden`.

**Cómo ordena:** usa `usort()` con el operador `<=>`:

| URL | Orden |
|---|---|
| `index.php?orden=id` | Por id (por defecto) |
| `index.php?orden=nombre` | Por nombre |
| `index.php?orden=precio` | Por precio |
| cualquier otro valor | Se vuelve a ordenar por id |

**Qué pinta:** cabecera con menú (Inicio, Buscar, Comprar), enlaces para cambiar el orden y, con un `foreach`, una tarjeta por producto con nombre, categoría, precio, estado de stock y un enlace a `producto.php?id=…`.

---

### `html/producto.php` — ficha de un producto

Página que muestra un producto concreto a partir de su id: `producto.php?id=1`.

**Variables principales:**

- `$idBruto`: el `id` tal cual llega por la URL (`$_GET["id"]`).
- `$id`: ese valor validado como entero con `filter_var(..., FILTER_VALIDATE_INT)`.
- `$producto`: el producto encontrado, o `null`.
- `$error`: mensaje de error si algo falla (`""` si todo va bien).

**Qué hace:**

1. Carga `datos.php` y `funciones.php`.
2. Si el id no es un entero válido (o es menor que 1) → código **400** y *"El id de producto no es válido"*.
3. Si es válido, busca el producto con `buscarProductoPorId()`. Si no existe → código **404** y *"El producto no existe"*.
4. Muestra la ficha: nombre, categoría, precio, stock, estado y botón *Comprar*.

> ⚠️ **Pendiente:** el bloque HTML todavía muestra datos fijos (Teclado mecánico, 79,90 €…) en lugar de usar `$producto` y `$error`.

---

### `html/buscar.php` — buscador

Formulario con un campo `q` que se envía por **GET** a sí mismo (`buscar.php?q=teclado`).

- Carga `datos.php` y `funciones.php` y crea `$resultados = []`.
- Ahora mismo muestra un resultado de ejemplo fijo.

> ⚠️ **Pendiente:** leer `q` con `leerCadena($_GET, "q")`, llamar a `buscarProductos()` y mostrar los resultados reales con un `foreach`.

---

### `html/compra.php` — formulario de compra

Formulario enviado por **POST** a sí mismo.

| Campo | Tipo | Descripción |
|---|---|---|
| `nombre` | texto | Nombre del cliente. No puede estar vacío. |
| `email` | email | Correo del cliente. Se valida con `FILTER_VALIDATE_EMAIL`. |
| `producto` | select | Producto a comprar. Sus opciones salen de `$productos`. |
| `unidades` | número | Cantidad (entero mayor que 0). |

**Flujo:**

```
GET  → se muestra el formulario vacío
POST → leer datos → validar → ¿hay errores?
                                 ├─ sí → se guardan en $errores
                                 └─ no → calcularCompra() → $resultadoCompra
```

**Validaciones**, en este orden: nombre, email, producto (id válido y existente), unidades y stock suficiente. Cada fallo añade un mensaje al array `$errores`.

**Variables que deja preparadas para el HTML:**

| Variable | Qué contiene |
|---|---|
| `$errores` | Lista de mensajes de error (`[]` = todo bien). |
| `$compraRealizada` | `true` si la compra se ha calculado. |
| `$resultadoCompra` | `subtotal`, `descuento`, `base`, `iva` y `total`, en céntimos. |
| `$productoSeleccionado` | El producto elegido, o `null`. |
| `$nombre`, `$email`, `$unidades`, `$productoId` | Lo que escribió el usuario, para rellenar de nuevo el formulario. |

> ⚠️ **Pendiente:** mostrar la lista de `$errores` y, si `$compraRealizada` es `true`, el resumen con `$resultadoCompra` (importes con `formatearPrecio()`).

---

### `html/css/estilos.css` — estilos

Hoja de estilos común a todas las páginas. Clases principales:

| Clase | Uso |
|---|---|
| `.contenedor` | Centra y limita el ancho del contenido. |
| `.cabecera`, `.navegacion` | Cabecera y menú de enlaces. |
| `.panel` | Caja con fondo para secciones. |
| `.grid-productos`, `.producto` | Cuadrícula y tarjetas de productos. |
| `.precio` | Estilo del precio. |
| `.estado`, `.disponible`, `.aviso`, `.agotado` | Etiqueta de stock (verde / naranja / rojo). |
| `.boton`, `.acciones` | Botones y su contenedor. |
| `.formulario`, `.campo` | Formularios. |
| `.resumen` | Tabla de resumen. |
| `.pie` | Pie de página. |

---

## Conceptos de PHP que se practican

- `require_once` y `__DIR__` para reutilizar archivos con rutas fiables.
- Constantes (`const`) y variables (`$`).
- Arrays asociativos y arrays de arrays.
- Funciones con tipos (`int`, `string`, `array`, `?array`).
- `foreach`, `if / elseif / else`, `usort`.
- Lectura de `$_GET` y `$_POST` y validación con `filter_var`.
- Operador `??` y comparación estricta `===`.
- Códigos HTTP (`http_response_code`).
- Escapar la salida con `htmlspecialchars` (XSS).
- Etiqueta corta `<?= ... ?>` para imprimir en HTML.
- Acumular errores en un array y decidir qué mostrar según el resultado.
