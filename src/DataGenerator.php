<?php

namespace DataPlay\Services;

use DataPlay\Services\Contracts\NewableTrait;
use DataPlay\Services\Exceptions\DataGeneratorException;
use Generator;
use Illuminate\Support\LazyCollection;

class DataGenerator
{
    use NewableTrait;

    /** @param array<string, string|callable(mixed...): mixed> $schema */
    public function __construct(
        protected array $schema = [],
        protected int $limit = 1,
    ) {}

    /** @param array<string, string|callable(mixed...): mixed> $schema */
    public function schema(array $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    /** @return \Illuminate\Support\LazyCollection<int, mixed> */
    public function generate(?int $limit = null): LazyCollection
    {
        throw_if(
            empty($this->schema),
            new DataGeneratorException('Cannot generate data without schema.')
        );

        if ($limit) {
            $this->limit = $limit;
        }

        return LazyCollection::make(function(): Generator {
            $i = 0;

            while ($i < $this->limit) {
                yield $this->generateItem($i);

                $i++;
            }
        });
    }

    /** @return array<string, string|callable(mixed...): mixed> */
    protected function generateItem(int $index): array
    {
        $pos = $index + 1;

        $item = [];

        foreach ($this->schema as $attribute => $render) {
            $args = (object) [
                'key' => $attribute,
                'index' => $index,
                'pos' => $pos,
            ];

            if (is_callable($render)) {
                $item[$attribute] = $render($args);

                continue;
            }

            $item[$attribute] = str_replace(
                ['{index}', '{pos}', '{key}'],
                [(string) $index, (string) $pos, (string) $attribute],
                $render
            );
        }

        return $item;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }
}
