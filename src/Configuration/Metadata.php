<?php

declare(strict_types=1);

namespace Facile\DoctrineDDM\Configuration;

use Facile\DoctrineDDM\Exception;

/**
 * Class Metadata.
 */
class Metadata
{
    /**
     * @var array<string, EntityMetadata>
     */
    protected array $entityMetadatas = [];

    /**
     * @return array<string, EntityMetadata>
     */
    public function getEntityMetadatas(): array
    {
        return $this->entityMetadatas;
    }

    /**
     * @param EntityMetadata[] $entityMetadatas
     *
     * @throws \Facile\DoctrineDDM\Exception\InvalidArgumentException
     */
    public function setEntityMetadatas(array $entityMetadatas): void
    {
        $this->entityMetadatas = [];

        foreach ($entityMetadatas as $entityMetadata) {
            $this->addEntityMetadata($entityMetadata);
        }
    }

    /**
     * @throws \Facile\DoctrineDDM\Exception\InvalidArgumentException
     */
    public function addEntityMetadata(EntityMetadata $entityMetadata): void
    {
        if (array_key_exists($entityMetadata->getEntityClass(), $this->entityMetadatas)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Entity "%s" is already defined',
                $entityMetadata->getEntityClass(),
            ));
        }
        $this->entityMetadatas[$entityMetadata->getEntityClass()] = $entityMetadata;
    }

    public function getEntityMetadata(string $entityClass): ?EntityMetadata
    {
        return $this->entityMetadatas[$entityClass] ?? null;
    }
}
