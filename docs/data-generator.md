[Home](/README.md) | [Data Generator](/docs/data-generator.md) |  [Data Sync Engine](/docs/data-sync-engine.md) | [Query Log](/docs/query-log.md)



# Data Generator

`DataGenerator` is a pure PHP service designed to generate large volumes of _fake_ data in the form of a configurable `LazyCollection`, with the main advantage of being **database and ORM (Eloquent) agnostic**.

Its purpose is to decouple data generation logic from Laravel's seeders.

---

## Advantages Over Laravel Factories and Seeders

While **Factories** and **Seeders** are the ideal solution for a Laravel application's database, `DataGenerator` offers specific benefits where its simplicity and low coupling are key:

| Feature            | DataGenerator                                                  | Factories / Seeders (Laravel)                                      |
| :----------------- | :--------------------------------------------------------------- | :----------------------------------------------------------------- |
| **Dependency**     | Pure PHP (`LazyCollection` dependency if used)                   | Strong dependency on **Eloquent** and the database.                |
| **Output**         | **LazyCollection\|array** (ready to be exported to JSON/CSV).    | **Model** object from Eloquent (requires conversion to _array_).   |
| **Memory Usage**   | Very efficient for massive _arrays_.                             | Generates Model objects, which can have a slight overhead.         |
| **Purpose**        | Generation of **agnostic data** for _testing_ or exportation.    | Generation of data for the application's **local database**.       |
| **Portability**    | **High.** Can be used in any PHP project.                        | **Low.** Requires the structure of the Laravel _framework_.        |

---

## Recommended Use Cases

The use of `DataGenerator` is advantageous in situations where the database is not the final destination of the generated data or when a lightweight and portable solution is required.

### 1. Agnostic Unit Tests

- **Description:** Generate _fake_ data structures to test services, _transformers_, or business logic that do not interact with Eloquent or the database.
- **Advantage:** Eliminates the need to load model _mocks_ or the entire database infrastructure in simple unit tests.

### 2. Massive File Generation and Export

- **Description:** Create large volumes of data (_datasets_) to be exported to file formats such as JSON, CSV, or XML for _mocking_, migration, or external performance testing purposes.
- **Usage:** The `LazyCollection` ensures that memory consumption remains low when writing the file, even with millions of records.

### 3. API Mocking and Response Simulation

- **Description:** Simulate responses from external APIs or microservices for _frontend_ development or system integration testing.
- **Usage:** The `schema` is defined according to the structure of the external API's response, and the result is serialized to JSON.

### 4. Multi-Framework Libraries and Packages

- **Description:** Include a data generation tool for the internal tests of a PHP package or library that must be compatible with various frameworks (Laravel, Symfony, pure PHP).
- **Advantage:** Avoids forcing an Eloquent dependency on the base package.

---

## Basic Usage of `DataGenerator`

The generator uses the `schema()` method to define the output structure and `limit()` for the number of items.

### Configuration Structure

The schema is an array where each key is the attribute name, which is associated with a `render` that can be a `string` or a `callable` that defines how the column's value will be generated.

### Implementation Example

```php
use DataPlay\Services\DataGenerator;

$data = DataGenerator::new()
    // 1. Define the column structure
    ->schema([
        // String replacement using placeholders {key}, {pos}, and {index}
        'item_id' => 'DP-ITEM-{pos}',
        // Use of a callable (closure) for advanced logic
        'name' => fn() => fake()->name(),
        'is_active' => fn($args) => $args->index % 5 === 0,
        'date' => fn() => now()->subDays(rand(1, 30))->toDateString(),
    ])
    // 2. Define the number of items to generate
    ->limit(10)
    // 3. Generate the LazyCollection of arrays
    ->generate();

// To use the data (without inserting it into the DB):
$firstElement = $data->first();
// ['item_id' => 'DP-ITEM-1', 'name' => 'John Doe', 'is_active' => true, 'date' => '...']
```
