<?php

declare(strict_types=1);

namespace InFw\DomainUtils;

use Exception;

/**
 * Class StaticEmitter
 *
 * @method static Emitter addListener(string $event, callable $listener, int $priority = 0)
 * @method static Emitter deferred(DomainEvent $event)
 * @method static Emitter publish
 * @method static Emitter flush
 */
class StaticEmitter
{
    private static $implementation;

    public function __construct(string $emitterFqdn)
    {
        static::$implementation = $emitterFqdn;
    }

    public static function __callStatic(string $name, array $arguments = [])
    {
        $emitter = call_user_func([static::$implementation, 'create']);

        if (method_exists($emitter, $name)) {
            return call_user_func([$emitter, $name], ...$arguments);
        }

        throw new Exception('Static method not exists.');
    }

    public static function create(string $emitterFqdn): self
    {
        return new self($emitterFqdn);
    }
}
