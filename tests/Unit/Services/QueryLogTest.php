<?php

namespace Tests\Unit\Services;

use DataPlay\Services\Facades\QueryLog;
use Illuminate\Support\Facades\DB;

it('enables updates config setting to true and reset queries.log', function(): void {
    QueryLog::enable();

    $this->assertTrue(QueryLog::isEnabled());
    $this->assertFileDoesNotExist(QueryLog::filepath());
});

it('disables updates config setting to false', function(): void {
    QueryLog::disable();

    $this->assertFalse(QueryLog::isEnabled());
});

it('enables sql formatter for lines longer than 80 characters length', function(): void {
    QueryLog::enable();

    DB::insert(
        'insert into sync
            (key_hash, data_hash, status, is_active, data, created_at, updated_at)
            values (?, ?, ?, ?, ?, ?, ?)
        ',
        [
            'e4da3b7fbbce2345d7772b0674a318d5',
            '79a6c501d5a86b32fede633f8aca7e9e',
            'new',
            1,
            '{"text":"' . \str_repeat('x', 100) . '",}',
            now()->toDateTimeString(),
            now()->toDateTimeString(),
        ]
    );

    $sql = QueryLog::sql();

    $this->assertStringContainsString("\n", $sql);

});
