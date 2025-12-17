<?php

namespace DataPlay\Services;

use DataPlay\Services\Commands\MakeMigrationCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class DataPlayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('dataplay.services.querylog', fn () => new QueryLog);
    }

    public function boot(): void
    {
        // commands
        if ($this->app->runningInConsole()) {
            $this->commands($this->getCommands());
        }

        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/dataplay.php',
            'dataplay'
        );

        // optional. publish assets
        [, $syncTableStubFilename] = MakeMigrationCommand::paths();
        $this->publishes([
            __DIR__ . "/../../stubs/{$syncTableStubFilename}" => base_path("stubs/$syncTableStubFilename"),
        ], 'dataplay-stubs');

        $this->publishes([
            __DIR__ . '/../config/dataplay.php' => config_path('dataplay.php'),
        ], 'dataplay-config');
    }

    /** @return array<string> */
    protected function getCommands(): array
    {
        $commands = [];

        $path = __DIR__ . '/Commands';
        $namespace = 'DataPlay\\Services\\Commands';
        $files = File::files($path);

        foreach ($files as $file) {
            $filename = $file->getFilenameWithoutExtension();

            $commands[] = "{$namespace}\\{$filename}";
        }

        return $commands;
    }
}
