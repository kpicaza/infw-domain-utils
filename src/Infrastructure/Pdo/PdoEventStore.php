<?php

declare(strict_types=1);

namespace InFw\DomainUtils\Infrastructure\Pdo;

use InFw\DomainUtils\AggregateRoot;
use InFw\DomainUtils\AggregateRootId;
use InFw\DomainUtils\DomainEvent;
use InFw\DomainUtils\Store;
use PDO;

abstract class PdoEventStore implements Store
{
    const TABLE = 'event_store';

    protected $connection;
    protected $aggregateType;

    public function __construct(PDO $connection, string $aggregateType)
    {
        $this->connection = $connection;
        $this->aggregateType = $aggregateType;
    }

    public function save(AggregateRoot $aggregate): void
    {
        /** @var DomainEvent $event */
        foreach ($aggregate->getEvents() as $event) {
            $this->insertEvent($aggregate, $event);
        }
    }

    public function get(AggregateRootId $aggregateId): AggregateRoot
    {
        $aggregateType = $this->aggregateType;
        $events = $this->getEvents((string)$aggregateId);

        return call_user_func(
            [$aggregateType, 'reconstitute'],
            array_map(function (array $eventData) {
                $eventType = $eventData['name'];

                return call_user_func([$eventType, 'fromArray'], $eventData);
            }, $events)
        );
    }

    private function lastEventVersion(string $aggregateId, string $eventType): int
    {
        $q = $this->connection->prepare(
            "SELECT version 
                      FROM `event_store` 
                      WHERE `aggregate_id` = :aggregateId 
                      AND `aggregate_type` = :aggregateType
                      AND `name` = :eventType
                      ORDER BY `version` DESC"
        );
        $q->bindValue(':aggregateId', $aggregateId);
        $q->bindValue(':aggregateType', $this->formatClassName($this->aggregateType));
        $q->bindValue(':eventType', $this->formatClassName($eventType));

        $q->execute();

        $result = $q->fetchColumn();

        return false === $result ? 0 : $result++;
    }

    private function getEvents(string $aggregateId): array
    {
        $q = $this->connection->prepare(
            "SELECT DISTINCT * 
                      FROM `event_store` 
                      WHERE `aggregate_id` = :aggregateId 
                      GROUP BY `name` 
                      ORDER BY `ocurred_on` ASC, `version` DESC"
        );

        $q->bindValue(':aggregateId', $aggregateId);
        $q->execute();

        return $q->fetchAll();
    }

    private function insertEvent(AggregateRoot $aggregate, DomainEvent $event): void
    {
        $table = self::TABLE;

        $q = $this->connection->prepare("INSERT INTO `$table` (
                  `name`, `aggregate_id`, `aggregate_type`, `payload`, `occurred_on`, `version`
                ) VALUES (
                  :name, :aggregateId, :aggregateType, :payload, :occurredOn, :version
                )");

        $eventType = get_class($event);

        $q->bindValue(':name', $this->formatClassName($eventType));
        $q->bindValue(':aggregateId', $aggregate->aggregateId());
        $q->bindValue(':aggregateType', $this->formatClassName($this->aggregateType));
        $q->bindValue(':payload', json_encode($event->payload(), 1));
        $q->bindValue(':occurredOn', $event->occurredOn()->format('Y-m-d H:i:s'));
        $q->bindValue(':version', $this->lastEventVersion(
            $aggregate->aggregateId(),
            $eventType
        ));

        $q->execute();
    }

    private function formatClassName(string $className): string
    {
        return str_replace('\\', '\\\\',$className);
    }
}
