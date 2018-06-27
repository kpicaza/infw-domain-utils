<?php

declare(strict_types=1);

namespace InFw\DomainUtils;

interface Store
{
    public function save(AggregateRoot $aggregate): void;

    public function get(AggregateRootId $aggregateId): AggregateRoot;
}
