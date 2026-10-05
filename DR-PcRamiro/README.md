# DWES Store

Pequeña tienda online de periféricos hecha con **PHP, HTML y CSS** para la asignatura de *Desarrollo Web en Entorno Servidor* (2º DAW).

No usa base de datos: los productos están guardados en un array de PHP. Sirve para aprender lo básico de PHP: variables, arrays, funciones, `foreach`, formularios y peticiones `GET`/`POST`.

---

## Estructura del proyecto

```
DR-PcRamiro/
├── css/
│   └── estilos.css        → estilos de todas las páginas
├── html/
│   ├── index.php          → página principal (catálogo)
│   ├── buscar.php         → buscador de productos
│   └── compra.php         → formulario de compra
└── php/
    ├── datos.php          → productos y constantes
    ├── funciones.php      → funciones reutilizables
    └── producto.php       → ficha de un producto
```

> **Nota:** las páginas de `html/` cargan `estilos.css`, `datos.php` y `funciones.php` con rutas relativas (`href="estilos.css"`, `require_once "datos.php"`). Para que funcionen hay que tener esos archivos accesibles desde la misma carpeta que la página (o ajustar las rutas).

---

## Cómo ejecutarlo

1. Tener PHP instalado.
2. Desde la carpeta del proyecto:
   ```bash
   php -S localhost:8000
   ```
3. Abrir `http://localhost:8000/html/index.php` en el navegador.

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

Aquí están las funciones que usan las demás páginas.

| Función | Qué recibe | Qué devuelve | Para qué sirve |
|---|---|---|---|
| `formatearPrecio(int $centimos)` | Precio en céntimos | `string` | Convierte `7990` en un texto de precio con formato (euros). |
| `obtenerEstadoStock(int $stock)` | Unidades en stock | `string` | Devuelve el texto del estado: agotado (`0`), pocas unidades (`≤ 5`) o disponible. |
| `obtenerClaseEstado(int $stock)` | Unidades en stock | `string` | Devuelve la clase CSS según el stock (para pintar el estado de un color u otro). |
| `escapar(string $texto)` | Texto | `string` | Protege el texto antes de mostrarlo en HTML (evita inyectar código, XSS). |
| `buscarProductoPorId(array $productos, int $id)` | Lista y un id | `array` o `null` | Recorre los productos y devuelve el que tenga ese id; si no existe, `null`. |
| `normalizarTexto(string $texto)` | Texto | `string` | Quita espacios sobrantes y pasa a minúsculas, para buscar sin distinguir mayúsculas. |
| `buscarProductos(array $productos, string $busqueda)` | Lista y texto | `array` | Devuelve los productos cuyo nombre contiene el texto buscado. Si la búsqueda está vacía, no devuelve resultados. |
| `leerCadena(array $origen, string $clave)` | `$_GET`/`$_POST` y una clave | `string` | Lee un dato de forma segura: si no existe o no es texto, devuelve `""`. |

---

### `php/producto.php` — ficha de un producto

Página que muestra un producto concreto a partir de su id: `producto.php?id=1`.

**Variables principales:**

- `$idBruto`: el `id` tal cual llega por la URL (`$_GET["id"]`).
- `$id`: ese valor validado como entero con `filter_var(..., FILTER_VALIDATE_INT)`.
- `$producto`: el producto encontrado, o `null`.
- `$error`: mensaje de error si algo falla.

**Qué hace:**

1. Carga `datos.php` y `funciones.php`.
2. Si el id no es un entero válido (o es menor que 1) → código **400** y mensaje *"El id de producto no es válido"*.
3. Si es válido, busca el producto con `buscarProductoPorId()`. Si no existe → código **404** y *"El producto no existe"*.
4. Muestra la ficha: nombre, categoría, precio, stock, estado y botón *Comprar*.

> ⚠️ **Pendiente:** el bloque HTML todavía muestra datos fijos (Teclado mecánico, 79,90 €…) en lugar de usar `$producto` y `$error`. Falta conectarlo para que muestre el producto real.

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

**Qué pinta:** cabecera con menú (Inicio, Buscar, Comprar), enlaces para cambiar el orden y, con un `foreach`, una tarjeta por producto con nombre, categoría, precio (`formatearPrecio`), estado de stock (`obtenerClaseEstado`) y un enlace a `producto.php?id=…`.

---

### `html/buscar.php` — buscador

Formulario con un campo `q` que se envía por **GET** a sí mismo (`buscar.php?q=teclado`).

- Carga `datos.php` y `funciones.php` y crea `$resultados = []`.
- Ahora mismo muestra un resultado de ejemplo fijo.

> ⚠️ **Pendiente:** leer `q` con `leerCadena($_GET, "q")`, llamar a `buscarProductos()` y mostrar los resultados reales. Además, los `require_once` apuntan a `datos.php` y `funciones.php`, que están en `php/`, no en `html/`.

---

### `html/compra.php` — formulario de compra

Formulario enviado por **POST** con estos campos:

| Campo | Tipo | Descripción |
|---|---|---|
| `nombre` | texto | Nombre del cliente. |
| `email` | email | Correo del cliente. |
| `producto` | select | Producto a comprar (ids 1–4). |
| `unidades` | número | Cantidad (mínimo 1). |

> ⚠️ **Pendiente:** es solo HTML; todavía no hay código PHP que valide ni procese la compra. Aquí se usarían `IVA_GENERAL`, `UNIDADES_DESCUENTO` y `DESCUENTO_CANTIDAD`. El selector incluye 4 productos (con *Monitor* y *Auriculares*) que no coinciden del todo con `$productos`.

---

### `css/estilos.css` — estilos

Hoja de estilos común a todas las páginas. Clases principales:

| Clase | Uso |
|---|---|
| `.contenedor` | Centra y limita el ancho del contenido. |
| `.cabecera`, `.navegacion` | Cabecera y menú de enlaces. |
| `.panel` | Caja con fondo para secciones. |
| `.grid-productos`, `.producto` | Cuadrícula y tarjetas de productos. |
| `.precio` | Estilo del precio. |
| `.estado`, `.disponible`, `.aviso`, `.agotado` | Etiquetas de stock (verde / aviso / rojo). |
| `.boton`, `.acciones` | Botones y su contenedor. |
| `.formulario`, `.campo` | Formularios. |
| `.resumen` | Tabla de resumen. |
| `.pie` | Pie de página. |

---

## Conceptos de PHP que se practican

- `require_once` para reutilizar archivos.
- Constantes (`const`) y variables (`$`).
- Arrays asociativos y arrays de arrays.
- Funciones con tipos (`int`, `string`, `array`, `?array`).
- `foreach`, `if / elseif / else`, `usort`.
- Lectura de `$_GET` y validación con `filter_var`.
- Códigos HTTP (`http_response_code`).
- Etiqueta corta `<?= ... ?>` para imprimir en HTML.
