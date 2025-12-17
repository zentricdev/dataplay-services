<?php

namespace DataPlay\Services;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SqlFormatter;

class QueryLog
{
    public const string ENABLED = 'dataplay.services.querylog.enabled';
    public const string PATH = 'dataplay.services.querylog.path';

    protected string $filepath;
    protected string $sql;

    public function __construct()
    {
        DB::listen(function(QueryExecuted $query) {
            if ($this->isEnabled()) {
                $this->sql = $query->toRawSql();
                $line = Str::repeat('-', 80);

                if (\strlen($this->sql) > 80) {
                    $this->sql = SqlFormatter::format($this->sql, false);
                }

                $message = "{$this->sql} ({$query->time}s)";

                File::append($this->filepath, "$message\n$line\n");
            }
        });
    }

    public function sql(): string
    {
        return $this->sql;
    }

    public function enable(): void
    {
        config()->set(static::ENABLED, true);

        $path = $this->path();
        $this->filepath = "$path/dataplay-queries.log";
        File::ensureDirectoryExists($path);
        File::delete($this->filepath);
    }

    public function disable(): void
    {
        config()->set(static::ENABLED, false);
    }

    public function isEnabled(): bool
    {
        return config(static::ENABLED);
    }

    public function filepath(): string
    {
        return $this->filepath;
    }

    public function path(): string
    {
        return config(static::PATH);
    }
}
