<?php

declare(strict_types=1);

namespace Facile\DoctrineDDM\Factory;

use Facile\DoctrineDDM\Configuration;
use Facile\DoctrineDDM\Exception\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

class MetadataConfigFactory
{
    /**
     * @throws ContainerExceptionInterface if any other error occurs
     * @throws InvalidArgumentException
     */
    public function __invoke(ContainerInterface $container): Configuration\Metadata
    {
        $config = $container->get('config');

        return $this->createMetadata($config[self::class] ?? []);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createMetadata(array $configuration): Configuration\Metadata
    {
        $entities = [];
        foreach ($configuration as $entityClass => $config) {
            $entities[] = $this->createEntityMetadata($entityClass, $config);
        }

        $metadata = new Configuration\Metadata();
        $metadata->setEntityMetadatas($entities);

        return $metadata;
    }

    protected function createEntityMetadata(string $entity, array $configuration): Configuration\EntityMetadata
    {
        return new Configuration\EntityMetadata($entity, $configuration['discriminator_map'] ?? []);
    }
}
