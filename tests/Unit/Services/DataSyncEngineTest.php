<?php

namespace Tests\Unit\Services;

use DataPlay\Services\DataSyncEngine;
use DataPlay\Services\Exceptions\DataPlaySyncEngineException;

dataset('engine_data', [
    [
        'engine' => DataSyncEngine::new(),
        'data' => [
            'id' => 1,
            'name' => 'Alice',
            'email' => 'alice@email.com',
        ],
    ],
]);

test('payload returns only the specified keys from data', function(DataSyncEngine $engine, array $data): void {
    $engine->setData($data)->setUniqueKeys(['id']);
    $payload = $engine->payload(['name', 'id']);

    expect($payload)->toEqual([
        'id' => 1,
        'name' => 'Alice',
    ]);
})->with('engine_data');

test('payloadHash returns md5 of the joined payload values', function(DataSyncEngine $engine, array $data): void {
    $engine->setData($data)->setUniqueKeys(['id'])->setDataKeys(['name', 'email']);
    $expected = md5(implode('-', ['Alice', 'alice@email.com']));

    expect($engine->payloadHash(['name', 'email']))
        ->toEqual($expected);
})->with('engine_data');

test('keyHash and dataHash produce expected hashes', function(DataSyncEngine $engine, array $data): void {
    $engine->setData($data)->setUniqueKeys(['id'])->setDataKeys(['name']);

    expect($engine->keyHash())->toEqual(md5($data['id']))
        ->and($engine->dataHash())->toEqual(md5($data['name']));
})->with('engine_data');

test('payload throws when unique keys are not set', function(DataSyncEngine $engine, array $data): void {
    $engine->setUniqueKeys([]);
    $engine->setData(['id' => $data['id']]);

    expect(fn () => $engine->payload(['id']))
        ->toThrow(DataPlaySyncEngineException::class);
})->with('engine_data');

test('payload throws when data is not set', function(DataSyncEngine $engine, array $data): void {
    $engine->setData([]);

    expect(fn () => $engine->payload(['id']))
        ->toThrow(DataPlaySyncEngineException::class);
})->with('engine_data');
