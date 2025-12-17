[Home](/README.md) | [Data Generator](/docs/data-generator.md) |  [Data Sync Engine](/docs/data-sync-engine.md) | [Query Log](/docs/query-log.md)

# Data Sync Engine

`DataSyncEngine` is a utility service designed to facilitate data synchronization processes. It generates hashes from data payloads, which can be used to efficiently determine if a record needs to be created, updated, or skipped.

This is particularly useful when synchronizing data from an external source (like an API, a CSV file, etc.) into a local database, as it provides a reliable way to check for changes without comparing every single field.

## Main Features

- **State Agnostic:** It operates on pure PHP arrays, having no dependency on Eloquent or any other ORM.
- **Flexible Key-based Hashing:** Allows you to define two distinct sets of keys:
    - **Unique Keys:** Used to generate a `keyHash()`, which uniquely identifies a record. This hash can be stored in a `sync_hashes` table to quickly find existing records.
    - **Data Keys:** Used to generate a `dataHash()`, which represents the state of the record's data. If the `dataHash` of an incoming record differs from the stored one, it means the record needs to be updated.
- **Simple and Efficient:** By comparing simple MD5 hashes, it avoids complex object comparisons and reduces the logic required in your synchronization scripts.

## Use Case: Data Import from External Source

Imagine you are importing a large dataset from a CSV file into your application's database daily. The `DataSyncEngine` simplifies this process:

1.  For each row in the CSV, you create an instance of the `DataSyncEngine`.
2.  You define the columns that uniquely identify a row (e.g., `external_id`) as `uniqueKeys`.
3.  You define all the columns that might change as `dataKeys`.
4.  The engine generates a `keyHash` (to find the record) and a `dataHash` (to check for changes).
5.  You can then look up the `keyHash` in a dedicated synchronization table.
    - If it **doesn't exist**, you create the new record in your main table and store both hashes.
    - If it **exists**, you compare the incoming `dataHash` with the stored one.
        - If they **match**, you do nothing, as the data is unchanged.
        - If they **don't match**, you update the record in your main table and update the `dataHash` in the synchronization table.

## Basic Usage

```php
use DataPlay\Services\DataSyncEngine;

// Incoming data from an external source
$incomingData = [
    'user_id' => 'USR-123',
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'last_login' => '2023-10-27 10:00:00',
    'created_at' => '2023-01-01 00:00:00' // This column should be ignored
];

$syncEngine = DataSyncEngine::new()
    ->setData($incomingData)
    // Keys that identify the record
    ->setUniqueKeys(['user_id'])
    // Keys that should be monitored for changes
    ->setDataKeys(['name', 'email', 'last_login']);

// Generate the hashes
$keyHash = $syncEngine->keyHash();
// md5('USR-123')

$dataHash = $syncEngine->dataHash();
// md5('John Doe-john.doe@example.com-2023-10-27 10:00:00')

// Now you can use these hashes in your sync logic...
```

This approach standardizes and simplifies the logic for keeping your local data synchronized with an external source.