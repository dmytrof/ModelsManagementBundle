<?php

namespace Dmytrof\ModelsManagementBundle\Tests\DependencyInjection;

use Dmytrof\ModelsManagementBundle\DependencyInjection\DmytrofModelsManagementExtension;
use Gedmo\Blameable\BlameableListener;
use Gedmo\Mapping\Driver\AttributeReader;
use Gedmo\Timestampable\TimestampableListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\{ContainerBuilder, Reference};

class DmytrofModelsManagementExtensionTest extends TestCase
{
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        (new DmytrofModelsManagementExtension())->load([], $this->container);
    }

    public static function gedmoListeners(): iterable
    {
        yield [TimestampableListener::class];
        yield [BlameableListener::class];
    }

    #[DataProvider('gedmoListeners')]
    public function testGedmoListenersReadAttributes(string $listener): void
    {
        $calls = $this->container->getDefinition($listener)->getMethodCalls();
        $this->assertCount(1, $calls);
        [$method, [$reader]] = $calls[0];

        $this->assertSame('setAnnotationReader', $method);
        $this->assertInstanceOf(Reference::class, $reader);
        $this->assertNotSame('annotation_reader', (string) $reader, 'doctrine/annotations must not be required');
        $this->assertSame(AttributeReader::class, $this->container->getDefinition((string) $reader)->getClass());
    }

    public function testListenerCanBeInstantiatedWithoutAnnotationReader(): void
    {
        $this->container->getDefinition(TimestampableListener::class)->setPublic(true);
        $this->container->compile();

        $this->assertInstanceOf(TimestampableListener::class, $this->container->get(TimestampableListener::class));
    }
}
