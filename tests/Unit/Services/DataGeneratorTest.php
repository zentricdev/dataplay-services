<?php

namespace Tests\Unit\Services;

use DataPlay\Services\DataGenerator;
use Illuminate\Support\LazyCollection;

it('can generate data as defined in schema', function(): void {
    $limit = random_int(1, 10);
    $data = DataGenerator::new()
        ->schema([
            'id' => fn ($args) => $args->pos,
            'email' => 'user{pos}@example.com',
        ])
        ->limit($limit)
        ->generate();

    expect($data)
        ->toBeInstanceOf(LazyCollection::class)
        ->and($data->count())->toBe($limit)
        ->and($data->first())->toBeArray();
});
