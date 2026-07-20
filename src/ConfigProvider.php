<?php

declare(strict_types=1);

namespace Facile\DoctrineDDM;

use Facile\DoctrineDDM\Factory\MetadataConfigFactory;
use Facile\DoctrineDDM\Factory\MetadataListenerFactory;
use Facile\DoctrineDDM\Configuration\Metadata;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Return default service mappings for Laminas/Mezzio DI container.
     */
    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                // register the configuration factory
                Metadata::class => MetadataConfigFactory::class,
                // register the metadata listener factory
                MetadataListener::class => MetadataListenerFactory::class,
            ],
        ];
    }
}
