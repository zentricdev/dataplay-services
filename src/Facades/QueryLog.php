<?php

namespace DataPlay\Services\Facades;

use Illuminate\Support\Facades\Facade;

class QueryLog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dataplay.services.querylog';
    }
}
