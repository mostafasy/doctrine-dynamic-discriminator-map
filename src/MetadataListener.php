<?php

declare(strict_types=1);

namespace Facile\DoctrineDDM;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Facile\DoctrineDDM\Configuration\Metadata as Configuration;

class MetadataListener implements EventSubscriber
{
    protected Configuration $metadataConfig;

    public function __construct(Configuration $metadataConfig)
    {
        $this->metadataConfig = $metadataConfig;
    }

    /**
     * Returns an array of events this listener wants to listen to.
     * @return list<string>
     */
    public function getSubscribedEvents(): array
    {
        return ['loadClassMetadata'];
    }

    /**
     * @throws \RuntimeException If discriminator map cannot be set
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        /** @var ClassMetadata $metadata */
        $metadata = $event->getClassMetadata();

        $entityMetadata = $this->metadataConfig->getEntityMetadata($metadata->getName());

        if (! $entityMetadata) {
            return;
        }

        $class = $metadata->getReflectionClass();
        if ($class === null) {
            $class = new \ReflectionClass($metadata->getName());
        }
        $discriminatorMap = [];
        $attributes = $class->getAttributes(DiscriminatorMap::class);
        if (!empty($attributes)) {
            /** @var DiscriminatorMap $attributeInstance */
            $attributeInstance = $attributes[0]->newInstance();
            $discriminatorMap = $attributeInstance->value;
        }
        $discriminatorMap = array_merge($discriminatorMap, $entityMetadata->getDiscriminatorMap());
        try {
            $metadata->setDiscriminatorMap($discriminatorMap);
        } catch (\Doctrine\ORM\Mapping\MappingException $e) {
            throw new \RuntimeException(
                sprintf(
                    'Cannot set discriminator map for entity "%s": %s. ' .
                    'Ensure the listener is registered before metadata is finalized.',
                    $metadata->getName(),
                    $e->getMessage()
                ),
                0,
                $e
            );
        }    }
}
