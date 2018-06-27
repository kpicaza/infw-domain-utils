<?php

declare(strict_types=1);

namespace InFw\DomainUtils\Infrastructure\Tactician;

use InFw\DomainUtils\StaticEmitter;
use League\Tactician\Middleware;

class DomainEventEmitterMiddleware implements Middleware
{
    private $emitterType;
    private $eventMap;

    public function __construct(array $config)
    {
        $this->emitterType = $config['driver'];
        $this->eventMap = $config['event_map'];
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        StaticEmitter::create($this->emitterType);

        foreach ($this->eventMap[0] as $event => $listeners) {
            if (!is_array($listeners)) {
                continue;
            }

            foreach ($listeners as $priority => $listener) {
                StaticEmitter::addListener($event, $listener, $priority);
            }
        }

        $result = $next($command);

        StaticEmitter::publish();
        StaticEmitter::flush();

        return $result;
    }
}
