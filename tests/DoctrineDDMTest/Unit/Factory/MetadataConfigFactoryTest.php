<?php

declare(strict_types=1);

namespace Facile\DoctrineDDMTest\Unit\Factory;

use Facile\DoctrineDDM\Configuration\EntityMetadata;
use Facile\DoctrineDDM\Configuration\Metadata;
use Facile\DoctrineDDM\Factory\MetadataConfigFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MetadataConfigFactoryTest extends TestCase
{
    public function testCreateMetadataFromContainer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->with('config')
            ->willReturn([
                MetadataConfigFactory::class => [
                    'App\\Entity\\Person' => [
                        'discriminator_map' => [
                            'employee' => 'App\\Entity\\Employee',
                        ],
                    ],
                ],
            ]);

        $factory = new MetadataConfigFactory();
        $metadata = $factory($container);

        self::assertInstanceOf(Metadata::class, $metadata);
        self::assertCount(1, $metadata->getEntityMetadatas());

        $entityMetadata = $metadata->getEntityMetadata('App\\Entity\\Person');
        self::assertInstanceOf(EntityMetadata::class, $entityMetadata);
        self::assertSame(['employee' => 'App\\Entity\\Employee'], $entityMetadata->getDiscriminatorMap());
    }

    public function testCreateMetadataWithEmptyConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->with('config')
            ->willReturn([]);

        $factory = new MetadataConfigFactory();
        $metadata = $factory($container);

        self::assertInstanceOf(Metadata::class, $metadata);
        self::assertSame([], $metadata->getEntityMetadatas());
    }

    public function testCreateMetadataDirectly(): void
    {
        $factory = new MetadataConfigFactory();
        $metadata = $factory->createMetadata([
            'App\\Entity\\Person' => [
                'discriminator_map' => [
                    'employee' => 'App\\Entity\\Employee',
                    'manager' => 'App\\Entity\\Manager',
                ],
            ],
        ]);

        self::assertCount(1, $metadata->getEntityMetadatas());

        $entityMetadata = $metadata->getEntityMetadata('App\\Entity\\Person');
        self::assertSame([
            'employee' => 'App\\Entity\\Employee',
            'manager' => 'App\\Entity\\Manager',
        ], $entityMetadata->getDiscriminatorMap());
    }
}
