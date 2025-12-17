<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    // protected function getEnvironmentSetUp($app)
    // {
    //     // Base de datos SQLite en memoria
    //     // $app['config']->set('database.default', 'testing');
    //     // $app['config']->set('database.connections.testing', [
    //     //     'driver' => 'sqlite',
    //     //     'database' => ':memory:',
    //     //     'prefix' => '',
    //     // ]);
    // }

    // Este método es crucial para que TestBench sepa qué Service Provider cargar
    protected function getPackageProviders($app)
    {
        return [
            \DataPlay\Services\DataPlayServiceProvider::class,
        ];
    }
}
