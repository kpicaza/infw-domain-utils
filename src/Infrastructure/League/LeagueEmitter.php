<?php

declare(strict_types=1);

namespace InFw\DomainUtils\Infrastructure\League;

use InFw\DomainUtils\DomainEvent;
use InFw\DomainUtils\Emitter;
use League\Event\Emitter as BaseEmitter;

class LeagueEmitter extends BaseEmitter implements Emitter
{
    private static $instance;
    private $events;

    private function __construct()
    {
        $this->events = [];
    }

    public static function create(): self
    {
        if (null === static::$instance) {
            static::$instance = new self;
        }

        return static::$instance;
    }

    public function deferred(DomainEvent $event): void
    {
        $this->events[] = new LeagueEvent($event);
    }

    public function publish(): void
    {
        $this->emitBatch($this->events);
    }

    public function flush(): void
    {
        $this->events = [];
    }

}
