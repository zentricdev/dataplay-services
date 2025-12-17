<?php

namespace DataPlay\Services;

use DataPlay\Services\Contracts\NewableTrait;
use DataPlay\Services\Exceptions\DataPlaySyncEngineException;

class DataSyncEngine
{
    use NewableTrait;

    /** @var array<string, mixed> */
    protected ?array $data = [];

    /** @var array<string> */
    protected array $uniqueKeys = [];

    /** @var array<string> */
    protected array $dataKeys = [];

    /** @param array<string, mixed> $data */
    public function setData(array $data): static
    {
        $this->data = $data;

        if (empty($this->dataKeys)) {
            $this->dataKeys = array_keys($data);
        }

        return $this;
    }

    /** @param array<string> $dataKeys */
    public function setDataKeys(array $dataKeys): static
    {
        $this->dataKeys = $dataKeys;

        return $this;
    }

    /** @param array<string> $uniqueKeys */
    public function setUniqueKeys(array $uniqueKeys): static
    {
        $this->uniqueKeys = $uniqueKeys;

        return $this;
    }

    public function keyHash(): string
    {
        return $this->payloadHash($this->uniqueKeys);
    }

    public function dataHash(): string
    {
        return $this->payloadHash($this->dataKeys);
    }

    /**
     * @param  array<string>  $keys
     * @return array<string, mixed>
     */
    public function payload(array $keys): array
    {
        throw_if(
            empty($this->uniqueKeys),
            new DataPlaySyncEngineException('Cannot get payload without unique keys.')
        );

        throw_if(
            empty($this->data),
            new DataPlaySyncEngineException('Cannot get payload  without data.')
        );

        return array_intersect_key($this->data, array_flip($keys));
    }

    /** @param array<string> $keys */
    public function payloadHash(array $keys): string
    {
        return md5(implode('-', $this->payload($keys)));
    }
}
