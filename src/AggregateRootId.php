<?php

declare(strict_types=1);

namespace InFw\DomainUtils;

abstract class AggregateRootId implements ValueObject
{
    protected $id;

    protected function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equalTo(ValueObject $aggregateId): bool
    {
        return get_class($this) === get_class($aggregateId) && $this->id === (string)$aggregateId;
    }
}
