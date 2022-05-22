<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver as PgSQLDriver;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

final class FixPostgreSQLDefaultSchemaListener implements EventSubscriber
{
    private const FIXED_SCHEME = 'public';

    public function getSubscribedEvents(): array
    {
        return [
            ToolEvents::postGenerateSchema,
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $args): void
    {
        $driver = $args->getEntityManager()->getConnection()->getDriver();
        if (!$driver instanceof PgSQLDriver) {
            return;
        }

        $schema = $args->getSchema();

        if (!$schema->hasNamespace(self::FIXED_SCHEME)) {
            $schema->createNamespace(self::FIXED_SCHEME);
        }
    }
}