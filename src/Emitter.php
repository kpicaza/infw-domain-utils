<?php

declare(strict_types=1);

namespace InFw\DomainUtils;

interface Emitter
{
    public static function create();

    public function addListener($eventName, $listener);

    public function deferred(DomainEvent $event): void;

    public function publish(): void;

    public function flush(): void;
}
