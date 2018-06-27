<?php

declare(strict_types=1);

namespace InFw\DomainUtils;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;

abstract class DomainEvent implements JsonSerializable
{
    protected $aggregateId;
    protected $payload;
    protected $occurredOn;
    protected $name;

    public static function occur(string $aggregateId, array $payload = []): self
    {
        $self = new static;
        $self->name = static::class;
        $self->aggregateId = $aggregateId;
        $self->payload = $payload;
        $self->occurredOn = DateTimeImmutable::createFromMutable(new DateTime);

        return $self;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function occurredOn(): DateTimeInterface
    {
        return $this->occurredOn;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'aggregate_id' => $this->aggregateId,
                'occurred_on' => $this->occurredOn,
            ],
            $this->payload
        );
    }
}
