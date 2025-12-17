<?php

namespace DataPlay\Services\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeMigrationCommand extends Command
{
    protected $signature = 'dataplay:make:sync-migration {model} {--force}';
    protected $description = 'Genera una tabla de hashes específica desde una plantilla .stub';

    public function handle()
    {
        $tableName = $this->tableName();
        $fileName = date('Y_m_d_000000') . "_create_{$tableName}_table.php";
        $destinationPath = database_path("migrations/{$fileName}");

        if (! $this->option('force') && $this->migrationExists($tableName)) {
            $this->error("Ya existe una migración para la tabla '{$tableName}'.");
            $this->info('Usa --force si realmente deseas crear otra.');

            return;
        }

        [$stubPackagePath, $stubFilename] = static::paths();
        $publishedStub = base_path("stubs/$stubFilename");
        $stubPath = file_exists($publishedStub) ? $publishedStub : $stubPackagePath;

        if (! File::exists($stubPath)) {
            $this->error("Plantilla no encontrada en: {$stubPath}");

            return;
        }

        $connection = config('dataplay.sync.connection');
        $stubContent = File::get($stubPath);
        $content = str_replace(
            ['{{connection}}', '{{tableName}}'],
            [$connection, $tableName],
            $stubContent
        );

        File::put($destinationPath, $content);

        $this->info("Migración creada con éxito: {$fileName}");
    }

    public static function paths(): array
    {
        return [
            __DIR__ . '/../../stubs/create_sync_hashes_table_migration.stub',
            'create_sync_hashes_table_migration.stub',
        ];
    }

    protected function migrationExists(string $tableName): bool
    {
        $files = File::files(database_path('migrations'));
        foreach ($files as $file) {
            if (str_contains($file->getFilename(), "create_{$tableName}_table")) {
                return true;
            }
        }

        return false;
    }

    protected function tableName(): string
    {
        $name = Str::plural(Str::snake($this->argument('model')));

        return "sync_{$name}";
    }
}
