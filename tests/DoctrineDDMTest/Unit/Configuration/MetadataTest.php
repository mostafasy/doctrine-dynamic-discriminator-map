<?php

declare(strict_types=1);

namespace Facile\DoctrineDDMTest\Unit\Configuration;

use Facile\DoctrineDDM\Configuration\EntityMetadata;
use Facile\DoctrineDDM\Configuration\Metadata;
use Facile\DoctrineDDM\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MetadataTest extends TestCase
{
    public function testGetEntityMetadatasReturnsEmptyArrayByDefault(): void
    {
        $config = new Metadata();

        self::assertSame([], $config->getEntityMetadatas());
    }

    public function testAddEntityMetadata(): void
    {
        $config = new Metadata();
        $entityMetadata = new EntityMetadata('App\\Entity\\Person', ['employee' => 'App\\Entity\\Employee']);

        $config->addEntityMetadata($entityMetadata);

        self::assertCount(1, $config->getEntityMetadatas());
        self::assertSame($entityMetadata, $config->getEntityMetadata('App\\Entity\\Person'));
    }

    public function testAddDuplicateEntityMetadataThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity "App\\Entity\\Person" is already defined');

        $config = new Metadata();
        $entityMetadata = new EntityMetadata('App\\Entity\\Person');

        $config->addEntityMetadata($entityMetadata);
        $config->addEntityMetadata($entityMetadata);
    }

    public function testSetEntityMetadatas(): void
    {
        $config = new Metadata();
        $entityMetadata1 = new EntityMetadata('App\\Entity\\Person');
        $entityMetadata2 = new EntityMetadata('App\\Entity\\User');

        $config->setEntityMetadatas([$entityMetadata1, $entityMetadata2]);

        self::assertCount(2, $config->getEntityMetadatas());
        self::assertSame($entityMetadata1, $config->getEntityMetadata('App\\Entity\\Person'));
        self::assertSame($entityMetadata2, $config->getEntityMetadata('App\\Entity\\User'));
    }

    public function testGetEntityMetadataReturnsNullForUnknownEntity(): void
    {
        $config = new Metadata();

        self::assertNull($config->getEntityMetadata('App\\Entity\\Unknown'));
    }

    public function testSetEntityMetadatasResetsExistingData(): void
    {
        $config = new Metadata();
        $oldMetadata = new EntityMetadata('App\\Entity\\Old');
        $config->addEntityMetadata($oldMetadata);

        $newMetadata = new EntityMetadata('App\\Entity\\New');
        $config->setEntityMetadatas([$newMetadata]);

        self::assertCount(1, $config->getEntityMetadatas());
        self::assertNull($config->getEntityMetadata('App\\Entity\\Old'));
        self::assertSame($newMetadata, $config->getEntityMetadata('App\\Entity\\New'));
    }
}
