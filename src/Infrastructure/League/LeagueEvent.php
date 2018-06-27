<?php

declare(strict_types=1);

namespace InFw\DomainUtils\Infrastructure\League;

use InFw\DomainUtils\DomainEvent;
use League\Event\Event;

class LeagueEvent extends Event
{
    private $event;

    public function __construct(DomainEvent $event)
    {
        parent::__construct(get_class($event));
        $this->event = $event;
    }

    public function event(): DomainEvent
    {
        return $this->event;
    }
}
