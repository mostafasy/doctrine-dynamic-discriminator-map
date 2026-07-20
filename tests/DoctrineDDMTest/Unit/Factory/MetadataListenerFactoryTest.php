<?php

declare(strict_types=1);

namespace Facile\DoctrineDDMTest\Unit\Factory;

use Facile\DoctrineDDM\Configuration\Metadata;
use Facile\DoctrineDDM\Factory\MetadataListenerFactory;
use Facile\DoctrineDDM\MetadataListener;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MetadataListenerFactoryTest extends TestCase
{
    public function testCreateService(): void
    {
        $metadataConfig = new Metadata();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->with(Metadata::class)
            ->willReturn($metadataConfig);

        $factory = new MetadataListenerFactory();
        $listener = $factory($container);

        self::assertInstanceOf(MetadataListener::class, $listener);
    }
}
