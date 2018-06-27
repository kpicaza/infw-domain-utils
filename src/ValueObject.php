<?php

declare(strict_types=1);

namespace InFw\DomainUtils;

interface ValueObject
{
    public function __toString(): string;

    public function equalTo(ValueObject $aggregateId): bool;
}
