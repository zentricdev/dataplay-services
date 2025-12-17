<?php

namespace Tests\Feature\Services;

use DataPlay\Services\Facades\QueryLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

test('query log feature', function() {
    QueryLog::enable();

    expect(QueryLog::isEnabled())->toBeTrue();
    expect(File::exists(QueryLog::filepath()))->toBeFalse();

    DB::table('sync')->insert([
        'key_hash' => 'test_key',
        'data_hash' => 'test_data',
        'data' => json_encode(['foo' => 'bar']),
    ]);

    $logPath = QueryLog::filepath();
    expect(File::exists($logPath))->toBeTrue();

    $logContent = File::get($logPath);
    expect($logContent)->toContain('insert into "sync"');

    QueryLog::disable();
    expect(QueryLog::isEnabled())->toBeFalse();

    $logSize = File::size($logPath);

    DB::table('sync')
        ->where('key_hash', 'test_key')
        ->update(['data_hash' => 'new_hash']);

    expect(File::size($logPath))->toBe($logSize);
});
