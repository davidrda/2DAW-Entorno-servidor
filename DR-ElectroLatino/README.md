# 🛒 MiniTienda PHP (DR-ElectroLatino)

Tienda online mínima hecha en **PHP puro** (sin framework ni base de datos) para practicar **sesiones** y **cookies** (DWES, Unidad 4).

- 🧺 El **carrito** se guarda en la **sesión** (en el servidor).
- 🎨 El **tema** y el **nombre del visitante** se guardan en **cookies** (en el navegador).
- 🔐 Incluye medidas de seguridad básicas: cookies `HttpOnly`/`SameSite`, validación de entradas, escapado de HTML y estructura para tokens CSRF.

---

## 🚀 Cómo ejecutarlo

Desde la carpeta del proyecto:

```bash
php -S localhost:8000
```

Abre <http://localhost:8000>. Requiere PHP 8.1 o superior (usa el tipo de retorno `never`).

---

## 📁 Estructura

```
DR-ElectroLatino/
├── index.php          Catálogo de productos
├── carrito.php        Vista del carrito
├── agregar.php        Acción: añadir producto
├── actualizar.php     Acción: cambiar cantidad      (pendiente)
├── eliminar.php       Acción: quitar producto       (pendiente)
├── vaciar.php         Acción: vaciar carrito        (pendiente)
├── preferencias.php   Cookies de tema y nombre
├── sesion.php         Laboratorio para inspeccionar la sesión
├── cerrar.php         Acción: cerrar sesión         (pendiente)
├── css/
│   └── estilos.css    Estilos (con tema claro/oscuro)
└── includes/
    ├── config.php     Configuración y arranque de la sesión
    ├── funciones.php  Funciones reutilizables
    └── productos.php  Catálogo de productos
```

---

## 🔄 Flujo general

```
Navegador                          Servidor (PHP)
─────────                          ──────────────
index.php  ── POST producto_id ──▶ agregar.php
                                     │ valida el id
                                     │ guarda en $_SESSION['carrito']
           ◀── 303 Redirect ────────┘
carrito.php ── GET ──────────────▶ lee $_SESSION['carrito'] y pinta la tabla
```

Todas las acciones que **cambian algo** (añadir, borrar, guardar…) usan **POST + redirección 303** (patrón *POST/Redirect/GET*). Así, al recargar la página no se repite la acción.

---

## 📄 Archivos y funciones

### ⚙️ `includes/config.php` — configuración y sesión

Se incluye **al principio de todas las páginas**, antes de cualquier HTML (`session_start()` y las cookies envían cabeceras HTTP, que no se pueden enviar si ya hay salida).

No define funciones. Hace, en orden:

| Paso | Qué hace | Para qué |
|------|----------|----------|
| `declare(strict_types=1)` | Tipado estricto en este archivo | Un tipo incorrecto lanza `TypeError` en vez de convertirse en silencio |
| `session.use_strict_mode` | Rechaza IDs de sesión inventados | Evita *session fixation* |
| `session.use_only_cookies` | El ID solo viaja en cookie | Evita `?PHPSESSID=...` en la URL |
| `$https` | `true` si la petición va por HTTPS | Se reutiliza para marcar las cookies como `secure` |
| `session_set_cookie_params()` | `httponly`, `samesite=Lax`, `secure` según `$https`, `lifetime=0` | JavaScript no lee la cookie y no se envía desde otros sitios |
| `session_start()` | Arranca/recupera la sesión | Da acceso a `$_SESSION` |
| `$_SESSION['carrito']` | Se inicializa como `[]` si no existe | Formato: `[id_producto => cantidad]` |
| `$_SESSION['csrf_token']` | Token aleatorio de 64 caracteres (`random_bytes`) | Base para la protección CSRF |
| `require_once funciones.php` | Carga las funciones comunes | |

---

### 🧰 `includes/funciones.php` — funciones reutilizables

| Función | Qué hace | Estado |
|---------|----------|--------|
| `e(string $texto): string` | Escapa HTML con `htmlspecialchars` (comillas incluidas, UTF-8). **Úsala siempre que imprimas datos del usuario** para evitar XSS | ✅ |
| `euros(float $cantidad): string` | Formatea un importe: `25.5` → `25,50 €` | ✅ |
| `unidadesCarrito(): int` | Suma las cantidades del carrito (no los tipos de producto distintos). Se muestra en el menú | ✅ |
| `totalCarrito(array $productos): float` | Debe devolver la suma de `precio × cantidad` de cada producto del carrito | ⏳ Devuelve `0.0` |
| `exigirPostValido(): void` | Si la petición no es `POST`, responde **405** con cabecera `Allow: POST` y termina | ✅ |
| `campoCsrf(): string` | Devuelve el `<input type="hidden" name="csrf_token">` de los formularios | ⏳ Usa el valor fijo `"prueba"` |
| `volver(string $pagina): never` | Redirige con **303 See Other** y termina (`never`: la función no vuelve) | ✅ |
| `temaActual(): string` | Devuelve `'claro'` u `'oscuro'` para el atributo `data-tema` | ⏳ Siempre devuelve `'claro'` |
| `cabecera(string $titulo): void` | Imprime el inicio del HTML: `<head>`, menú de navegación (con contador del carrito) y abre `<main>` | ✅ |
| `pie(): void` | Cierra `<main>` e imprime el pie y el cierre del documento | ✅ |

> 💡 `cabecera()` y `pie()` van siempre en pareja: una abre el HTML común y la otra lo cierra.

---

### 📦 `includes/productos.php` — catálogo

Define `$productos`, un array donde la **clave es el ID** del producto y el valor es otro array con `nombre`, `precio` e `icono`:

| ID | Producto | Precio |
|----|----------|--------|
| 1 | Ratón inalámbrico | 15,00 € |
| 2 | Teclado mecánico | 45,00 € |
| 3 | Alfombrilla | 8,00 € |
| 4 | Auriculares | 32,00 € |

El carrito solo guarda el **ID y la cantidad**; los datos del producto se consultan siempre aquí.

---

### 🏠 `index.php` — catálogo

Recorre `$productos` con `foreach` y pinta una tarjeta por producto. Cada tarjeta tiene un formulario `POST` a `agregar.php` con el `producto_id` en un campo oculto. Todos los textos pasan por `e()` y el ID se convierte con `(int)`.

---

### ➕ `agregar.php` — añadir al carrito

1. `exigirPostValido()`: solo acepta POST.
2. `filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT)`: comprueba que sea un entero.
3. Si no es válido o no existe en el catálogo → **400** y termina.
4. Suma 1 a la cantidad actual, con un **máximo de 99** (`min()`).
5. Redirige a `carrito.php`.

---

### 🧺 `carrito.php` — vista del carrito

- Si `$_SESSION['carrito']` está vacío: muestra «Tu carrito está vacío» y un enlace al catálogo.
- Si tiene productos: contiene las **9 actividades pendientes** (marcadas con comentarios `ACTIVIDAD n`):

| # | Actividad |
|---|-----------|
| 1 | Tabla con 5 columnas (Producto, Precio, Cantidad, Subtotal, Acción) |
| 2 | `foreach` sobre el carrito con sintaxis alternativa |
| 3 | Saltar (`continue`) los IDs que no existan en el catálogo |
| 4 | Mostrar nombre, precio y cantidad |
| 5 | Calcular el subtotal (`precio × cantidad`) |
| 6 | Formulario para actualizar la cantidad (0–99) → `actualizar.php` |
| 7 | Formulario para eliminar un producto → `eliminar.php` |
| 8 | Mostrar el total con `totalCarrito()` |
| 9 | Formulario para vaciar el carrito → `vaciar.php` |

---

### 🔧 Acciones pendientes: `actualizar.php`, `eliminar.php`, `vaciar.php`

Las tres hacen lo mismo por ahora: exigen POST y muestran un aviso de «Funcionalidad pendiente». Cuando se completen:

| Archivo | Debe hacer |
|---------|-----------|
| `actualizar.php` | Cambiar la cantidad (0–99) de un producto |
| `eliminar.php` | Quitar un producto según su `producto_id` |
| `vaciar.php` | Dejar `$_SESSION['carrito']` en `[]` |

---

### 🍪 `preferencias.php` — cookies de preferencias

**Con POST** (guardar o borrar) y luego redirige a sí misma:

| Acción | Qué ocurre |
|--------|-----------|
| `guardar` | Guarda la cookie `tema` y, si hay nombre, la cookie `visitante` |
| `borrar` | Borra ambas cookies reenviándolas con fecha de caducidad pasada |

Validaciones importantes:

- **Tema:** lista blanca (`claro` / `oscuro`); cualquier otro valor se convierte en `claro`.
- **Nombre:** `trim`, elimina caracteres de control con `preg_replace`, máximo 40 caracteres. Si queda vacío, se borra la cookie.
- **Cookies:** duran 30 días con `httponly`, `samesite=Lax` y `secure` según `$https`.

**Con GET** muestra el formulario. El nombre se lee de `$_COOKIE['visitante']` validando tipo y longitud, porque **una cookie la controla el usuario**.

> ⏳ El `<select>` y la cabecera dependen de `temaActual()`, que todavía devuelve siempre `'claro'`; por eso el tema aún no cambia al guardarlo.

---

### 🔍 `sesion.php` — laboratorio de sesión

Muestra el nombre de la cookie de sesión, el ID, el contenido del carrito y las unidades. Tiene dos formularios:

- **Regenerar identificador:** `session_regenerate_id(true)` cambia el ID y borra el archivo de sesión antiguo (los datos del carrito se conservan). Es lo que se hace tras un login para evitar *session fixation*.
- **Cerrar sesión:** envía a `cerrar.php`.

---

### 🚪 `cerrar.php` — cerrar sesión

Por ahora solo exige POST y redirige a `index.php`. **Pendiente:** vaciar `$_SESSION`, caducar la cookie de sesión y llamar a `session_destroy()`.

---

### 🎨 `css/estilos.css` — estilos

| Grupo | Clases |
|-------|--------|
| Estructura | `.contenedor`, `.cabecera`, `.barra`, `.marca`, `.pie`, `.panel`, `.estrecho` |
| Catálogo | `.productos`, `.tarjeta`, `.ilustracion`, `.precio` |
| Texto | `.intro`, `.etiqueta`, `.nota`, `.romper`, `.bienvenida` |
| Botones | `.boton`, `.peligro` (rojo), `.secundario` |
| Formularios y tablas | `.en-linea`, `.separado`, `.total`, `table`, `th`, `td`, `select`, `label` |
| Tema oscuro | Reglas `html[data-tema="oscuro"] ...` que sobrescriben los colores |

El tema se cambia con el atributo `data-tema` de `<html>`, que pone `cabecera()` usando `temaActual()`.

---

## ✅ Estado del proyecto

| Pendiente | Dónde |
|-----------|-------|
| Tabla del carrito (actividades 1–9) | `carrito.php` |
| Cálculo del total | `totalCarrito()` en `funciones.php` |
| Token CSRF real (generar, enviar y **validar**) | `campoCsrf()` y `exigirPostValido()` |
| Leer el tema desde la cookie | `temaActual()` |
| Actualizar, eliminar y vaciar | `actualizar.php`, `eliminar.php`, `vaciar.php` |
| Destruir la sesión | `cerrar.php` |
