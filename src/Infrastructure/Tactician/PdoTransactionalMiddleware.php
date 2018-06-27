<?php

declare(strict_types=1);

namespace InFw\DomainUtils\Infrastructure\Tactician;

use League\Tactician\Middleware;
use PDO;
use Throwable;

class PdoTransactionalMiddleware implements Middleware
{
    protected $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        $this->connection->beginTransaction();

        try {
            $result = $next($command);
        } catch (Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }

        $this->connection->commit();

        return $result;
    }
}
