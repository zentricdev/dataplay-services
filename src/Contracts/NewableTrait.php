<?php

namespace DataPlay\Services\Contracts;

trait NewableTrait
{
    public static function new(...$args): static
    {
        /** @phpstan-ignore-next-line */
        return new static(...$args);
    }
}
