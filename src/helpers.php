<?php

if (! function_exists('resolve')) {
    function resolve(string $className): object
    {
        return new $className;
    }
}
