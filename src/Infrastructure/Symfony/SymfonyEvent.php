<?php

declare(strict_types=1);

namespace InFw\DomainUtils\Infrastructure\League;

use InFw\DomainUtils\DomainEvent;
use Symfony\Component\EventDispatcher\Event;

class SymfonyEvent extends Event
{
    private $event;

    public function __construct(DomainEvent $event)
    {
        $this->event = $event;
    }

    public function event(): DomainEvent
    {
        return $this->event;
    }
}
