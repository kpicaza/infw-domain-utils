<?php

declare(strict_types=1);

namespace InFw\DomainUtils;

abstract class AggregateRoot
{
    protected $aggregateId;
    protected $events;

    public static function reconstitute(array $events): self
    {
        $self = new static;

        array_walk($events, function (DomainEvent $event) use ($self) {
            $self->apply($event);
        });

        return $self;
    }

    protected function recordThat(DomainEvent $event): void
    {
        /** @var Emitter $emitter */
        $this->apply($event);
        $this->events[] = $event;
        StaticEmitter::deferred($event);
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    abstract public function apply(DomainEvent $event): void;
}
