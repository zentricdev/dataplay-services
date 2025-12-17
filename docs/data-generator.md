# DataGenerator: Generación de Datos Agnosticos

`DataGenerator` es un servicio PHP puro diseñado para generar grandes volúmenes de datos _fake_ en forma de `LazyCollection` y configurable, con la principal ventaja de ser **agnóstico a la base de datos y al ORM (Eloquent)**.

Su propósito es desacoplar la lógica de generación de datos de los seeders de Laravel.

---

## Ventajas Sobre Factories y Seeders de Laravel

Mientras que las **Factories** y **Seeders** son la solución ideal para la base de datos de una aplicación Laravel, el `DataGenerator` ofrece beneficios específicos donde su simplicidad y bajo acoplamiento son clave:

| Característica     | DataGenerator                                                    | Factories / Seeders (Laravel)                                         |
| :----------------- | :--------------------------------------------------------------- | :-------------------------------------------------------------------- |
| **Dependencia**    | PHP Puro (Dependencia de `LazyCollection` si se usa)             | Fuerte dependencia de **Eloquent** y la base de datos.                |
| **Output**         | **LazyCollection\|array** (listo para exportar a JSON/CSV).                 | Objeto **Modelo** de Eloquent (requiere conversión a _array_).        |
| **Uso en Memoria** | Muy eficiente para _arrays_ masivos.                 | Genera objetos de Modelo, lo que puede tener una sobrecarga ligera.   |
| **Propósito**      | Generación de **datos agnósticos** para _testing_ o exportación. | Generación de datos para la **base de datos local** de la aplicación. |
| **Portabilidad**   | **Alta.** Puede usarse en cualquier proyecto PHP.                | **Baja.** Requiere la estructura del _framework_ Laravel.             |

---

## Casos de Uso Recomendados

El uso de `DataGenerator` es ventajoso en situaciones donde la base de datos no es el destino final de los datos generados o cuando se requiere una solución liviana y portable.

### 1. Pruebas Unitarias Agnósticas

- **Descripción:** Generar estructuras de datos _fake_ para probar servicios, _transformers_ o lógica de negocio que no interactúan con Eloquent o la base de datos.
- **Ventaja:** Elimina la necesidad de cargar _mocks_ de modelos o la infraestructura completa de la base de datos en pruebas unitarias simples.

### 2. Generación y Exportación Masiva de Archivos

- **Descripción:** Crear grandes volúmenes de datos (_datasets_) para ser exportados a formatos de archivo como JSON, CSV, o XML para fines de _mocking_, migración o pruebas de rendimiento externas.
- **Uso:** La `LazyCollection` asegura que el consumo de memoria se mantenga bajo al escribir el archivo, incluso con millones de registros.

### 3. API Mocking y Simulación de Respuestas

- **Descripción:** Simular respuestas de APIs externas o de microservicios para el desarrollo _frontend_ o para pruebas de integración de sistemas.
- **Uso:** Se define el `schema` según la estructura de la respuesta de la API externa y se serializa el resultado a JSON.

### 4. Librerías y Paquetes Multi-Framework

- **Descripción:** Incluir una herramienta de generación de datos para las pruebas internas de un paquete o librería PHP que debe ser compatible con varios frameworks (Laravel, Symfony, PHP puro).
- **Ventaja:** Se evita forzar una dependencia de Eloquent en el paquete base.

---

## Uso Básico del `DataGenerator`

El generador utiliza el método `schema()` para definir la estructura de salida y `limit()` para el número de elementos.

### Estructura de la Configuración

El schema es un array donde cada key es el nombre de atributo al que se le asocia un `render` que puede ser un `string` o `callable` que define cómo se generará el valor de la columna.


### Ejemplo de Implementación

```php
use DataPlay\Services\DataGenerator;

$data = DataGenerator::new()
    // 1. Define la estructura de las columnas
    ->schema([
        // Remplazo de string usando placeholders {key}, {pos} y {index}
        'item_id' => 'DP-ITEM-{pos}',
        // Uso de un callables (closure) para lógica avanzada
        'is_active' => fn($args) => $args->index % 5 === 0,
        'date' => fn() => now()->subDays(rand(1, 30))->toDateString(),
    ])
    // 2. Define el número de elementos a generar
    ->limit(10)
    // 3. Genera la LazyCollection de arrays
    ->generate();

// Para usar los datos (sin inserción en BD):
$primerElemento = $data->first();
// ['item_id' => 'DP-ITEM-1', 'is_active' => true, 'date' => '...']
```
