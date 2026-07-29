<?php

declare(strict_types=1);

namespace Facile\DoctrineDDM\Configuration;

/**
 * Class EntityMetadata.
 */
class EntityMetadata
{
    protected string $entityClass;

    /**
     * @var array<string, class-string>
     */
    protected array $discriminatorMap = [];

    /**
     * @param array<string, class-string> $discriminatorMap
     */
    public function __construct(string $entityClass, array $discriminatorMap = [])
    {
        $this->entityClass = $entityClass;
        $this->discriminatorMap = $discriminatorMap;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return array<string, class-string>
     */
    public function getDiscriminatorMap(): array
    {
        return $this->discriminatorMap;
    }

    /**
     * @param array<string, class-string> $discriminatorMap
     */
    public function setDiscriminatorMap(array $discriminatorMap): void
    {
        $this->discriminatorMap = $discriminatorMap;
    }
}
