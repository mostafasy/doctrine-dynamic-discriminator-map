<?php

declare(strict_types=1);

namespace Facile\DoctrineDDMTest\Unit\Configuration;

use Facile\DoctrineDDM\Configuration\EntityMetadata;
use PHPUnit\Framework\TestCase;

class EntityMetadataTest extends TestCase
{
    public function testConstructorSetsValues(): void
    {
        $map = ['employee' => 'App\\Entity\\Employee'];
        $metadata = new EntityMetadata('App\\Entity\\Person', $map);

        self::assertSame('App\\Entity\\Person', $metadata->getEntityClass());
        self::assertSame($map, $metadata->getDiscriminatorMap());
    }

    public function testConstructorWithEmptyDiscriminatorMap(): void
    {
        $metadata = new EntityMetadata('App\\Entity\\Person');

        self::assertSame('App\\Entity\\Person', $metadata->getEntityClass());
        self::assertSame([], $metadata->getDiscriminatorMap());
    }

    public function testSetEntityClass(): void
    {
        $metadata = new EntityMetadata('App\\Entity\\Person');
        $metadata->setEntityClass('App\\Entity\\User');

        self::assertSame('App\\Entity\\User', $metadata->getEntityClass());
    }

    public function testSetDiscriminatorMap(): void
    {
        $metadata = new EntityMetadata('App\\Entity\\Person');
        $newMap = ['admin' => 'App\\Entity\\Admin'];
        $metadata->setDiscriminatorMap($newMap);

        self::assertSame($newMap, $metadata->getDiscriminatorMap());
    }
}
