<?php

declare(strict_types=1);

namespace InFw\DomainUtils\Infrastructure\Symfony;

use InFw\DomainUtils\DomainEvent;
use InFw\DomainUtils\Emitter;
use InFw\DomainUtils\Infrastructure\League\SymfonyEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SymfonyEmitter extends EventDispatcher implements Emitter
{
    /**
     * @var EventDispatcherInterface
     */
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
        $this->events[] = new SymfonyEvent($event);
    }

    public function publish(): void
    {
        foreach ($this->events as $event) {
            $this->dispatch($event->getName(), $event);
        }
    }

    public function flush(): void
    {
        $this->events = [];
    }

}
