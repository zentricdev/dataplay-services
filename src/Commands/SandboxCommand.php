<?php

namespace DataPlay\Services\Commands;

use DataPlay\Services\Contracts\AbstractSyncTransformer;
use DataPlay\Services\DTOs\UserDTO;
use DataPlay\Services\Services\DataGenerator;
use Illuminate\Console\Command;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class SandboxCommand extends Command
{
    protected $signature = 'sandbox';

    public function handle()
    {
        intro(__METHOD__);
        $rawData = $this->generator()->generate();
        $values = $rawData->first();
        $dto = UserDTO::fromArray($values);

        dump(
            $dto->toArray(),
            $dto->toJson(),
            $dto->toSyncData(),
        );
    }

    protected function testTerminalOuputs(): void
    {
        $response = spin(
            callback: function() {
                sleep(1);

                return 'Finished';
            },
            message: 'Fetching response...'
        );

        intro('Esto es un intro');
        info('Esto es un info');
        alert('Esto es un alert');
        warning('Esto es un warning');
        note('Esto es un note');
        error('Esto es un error');
        outro("Esto es un outro - $response");
    }

    protected function testGenerator(): void
    {
        $rawData = $this->generator()->generate();

        $syncData = $this->generator()
            ->addTransformer($this->transformer())
            ->generate();

        dump($rawData->toArray());
        dump($syncData->toArray());
    }

    protected function generator(): DataGenerator
    {
        $schema = [
            'id' => ['render' => fn ($args) => $args->pos],
            'email' => ['render' => 'user{pos}@email.com'],
            'name' => ['render' => fn ($args) => "User {$args->pos}"], // . Str::padLeft($args->pos, 3, '0')],
            'password' => ['render' => fn ($args) => md5("{$args->key}{$args->index}")],
        ];

        return DataGenerator::new()
            ->schema($schema)
            ->limit(1);
    }

    protected function transformer(): AbstractSyncTransformer
    {
        return new class extends AbstractSyncTransformer
        {
            public function uniqueKeys(): array
            {
                return ['id'];
            }

            public function dataKeys(): array
            {
                return ['email', 'name', 'password'];
            }
        };
    }
}
