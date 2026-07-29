<?php

declare(strict_types=1);

namespace Facile\DoctrineDDMTest\Unit;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Facile\DoctrineDDM\Configuration\EntityMetadata;
use Facile\DoctrineDDM\Configuration\Metadata;
use Facile\DoctrineDDM\MetadataListener;
use Facile\DoctrineDDMTest\Assets\Entity\Employee;
use Facile\DoctrineDDMTest\Assets\Entity\Manager;
use Facile\DoctrineDDMTest\Assets\Entity\Person;
use PHPUnit\Framework\TestCase;


class MetadataListenerTest extends TestCase
{
    private MetadataListener $listener;

    private Metadata $config;

    protected function setUp(): void
    {
        $this->config = new Metadata();
        $this->listener = new MetadataListener($this->config);
    }

    public function testGetSubscribedEvents(): void
    {
        $events = $this->listener->getSubscribedEvents();

        self::assertIsArray($events);
        self::assertContains('loadClassMetadata', $events);
    }

    public function testLoadClassMetadataWithNoConfiguration(): void
    {
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getName')->willReturn('App\\\\Entity\\\\Unknown');

        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->method('getClassMetadata')->willReturn($metadata);

        // Should not throw, should do nothing
        $this->listener->loadClassMetadata($event);

        // If we get here without exception, test passes
        self::assertTrue(true);
    }

    public function testLoadClassMetadataMergesDiscriminatorMap(): void
    {
        // Setup configuration with dynamic entries
        $entityMetadata = new EntityMetadata(Person::class, [
            'employee' => Employee::class,
            'manager' => Manager::class,
        ]);
        $this->config->addEntityMetadata($entityMetadata);

        // Create real ClassMetadata for Person (so we can test attribute reading)
        $metadata = new ClassMetadata(Person::class);

        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->method('getClassMetadata')->willReturn($metadata);

        $this->listener->loadClassMetadata($event);

        // Verify the discriminator map was merged
        $discriminatorMap = $metadata->discriminatorMap;

        self::assertArrayHasKey('person', $discriminatorMap);
        self::assertSame(Person::class, $discriminatorMap['person']);
        self::assertArrayHasKey('employee', $discriminatorMap);
        self::assertSame(Employee::class, $discriminatorMap['employee']);
        self::assertArrayHasKey('manager', $discriminatorMap);
        self::assertSame(Manager::class, $discriminatorMap['manager']);
    }

    public function testLoadClassMetadataWithExistingDiscriminatorMapAttribute(): void
    {
        // Setup configuration with additional entries
        $entityMetadata = new EntityMetadata(Person::class, [
            'manager' => Manager::class,
        ]);
        $this->config->addEntityMetadata($entityMetadata);

        // Create real ClassMetadata
        $metadata = new ClassMetadata(Person::class);

        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->method('getClassMetadata')->willReturn($metadata);

        $this->listener->loadClassMetadata($event);

        $discriminatorMap = $metadata->discriminatorMap;

        // Should contain both the attribute-defined 'person' and config-defined 'manager'
        self::assertArrayHasKey('person', $discriminatorMap);
        self::assertArrayHasKey('manager', $discriminatorMap);
        self::assertSame(Manager::class, $discriminatorMap['manager']);
    }

    public function testLoadClassMetadataWithNullReflectionClass(): void
    {
        $entityMetadata = new EntityMetadata(Person::class, [
            'employee' => Employee::class,
        ]);
        $this->config->addEntityMetadata($entityMetadata);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getName')->willReturn(Person::class);
        $metadata->method('getReflectionClass')->willReturn(null);

        // Mock setDiscriminatorMap to verify it's called
        $metadata->expects(self::once())
            ->method('setDiscriminatorMap')
            ->with([
                'person' => Person::class,
                'employee' => Employee::class,
            ]);

        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->method('getClassMetadata')->willReturn($metadata);

        $this->listener->loadClassMetadata($event);
    }

    public function testLoadClassMetadataThrowsRuntimeExceptionOnMappingException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot set discriminator map for entity');

        $entityMetadata = new EntityMetadata(Person::class, [
            'employee' => Employee::class,
        ]);
        $this->config->addEntityMetadata($entityMetadata);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->method('getName')->willReturn(Person::class);
        $metadata->method('getReflectionClass')->willReturn(null);
        $metadata->method('setDiscriminatorMap')
            ->willThrowException(new \Doctrine\ORM\Mapping\MappingException('Metadata is locked'));

        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->method('getClassMetadata')->willReturn($metadata);

        $this->listener->loadClassMetadata($event);
    }

    public function testLoadClassMetadataConfigOverridesAttribute(): void
    {
        // Config provides different mapping for same key
        $entityMetadata = new EntityMetadata(Person::class, [
            'person' => Employee::class, // Override 'person' to point to Employee
        ]);
        $this->config->addEntityMetadata($entityMetadata);

        $metadata = new ClassMetadata(Person::class);

        $event = $this->createMock(LoadClassMetadataEventArgs::class);
        $event->method('getClassMetadata')->willReturn($metadata);

        $this->listener->loadClassMetadata($event);

        // Config should override attribute (array_merge behavior)
        $discriminatorMap = $metadata->discriminatorMap;
        self::assertSame(Employee::class, $discriminatorMap['person']);
    }
}
