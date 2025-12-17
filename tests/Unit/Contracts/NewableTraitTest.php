<?php

namespace Tests\Unit\Contracts;

use DataPlay\Services\Contracts\NewableTrait;

it('can create a new instance of the class', function(): void {
    $class = new class
    {
        use NewableTrait;
    };

    expect($class::new())->toBeInstanceOf(get_class($class));
});

it('can create a new instance with arguments', function(): void {
    $class = new class('foo', 'bar')
    {
        use NewableTrait;

        public function __construct(public string $arg1, public string $arg2)
        {
            //
        }
    };

    $instance = $class::new('foo', 'bar');

    expect($instance)->toBeInstanceOf(get_class($class))
        ->and($instance->arg1)->toEqual('foo')
        ->and($instance->arg2)->toEqual('bar');
});
