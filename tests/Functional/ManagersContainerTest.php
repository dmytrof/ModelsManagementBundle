<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Functional;

use Dmytrof\ModelsManagementBundle\Exception\ManagerException;
use Dmytrof\ModelsManagementBundle\Manager\ManagerInterface;
use Dmytrof\ModelsManagementBundle\Service\ManagersContainer;
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Entity\{Article, Comment};
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Manager\{ArticleManager, CommentManager};
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * ManagersContainer wired by the bundle in a real kernel: application managers are collected
 * through the "dmytrof.model.manager" tag added by autoconfiguration of ManagerInterface.
 */
class ManagersContainerTest extends KernelTestCase
{
    private ManagersContainer $managers;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->managers = static::getContainer()->get(ManagersContainer::class);
    }

    public function testServiceIsPublic(): void
    {
        // Applications fetch the container directly (e.g. $container->get(ManagersContainer::class))
        $this->assertTrue(self::$kernel->getContainer()->has(ManagersContainer::class));
        $this->assertSame($this->managers, self::$kernel->getContainer()->get(ManagersContainer::class));
    }

    public function testApplicationManagersAreCollected(): void
    {
        $this->assertEqualsCanonicalizing([Article::class, Comment::class], $this->managers->getModelClasses());
        $this->assertCount(2, $this->managers);

        foreach ($this->managers as $modelClass => $manager) {
            $this->assertInstanceOf(ManagerInterface::class, $manager);
            $this->assertSame($modelClass, $manager->getModelClass());
        }
    }

    public function testManagersAreTheSameServiceInstances(): void
    {
        $container = static::getContainer();

        $this->assertSame($container->get(ArticleManager::class), $this->managers->getManagerForModelClass(Article::class));
        $this->assertSame($container->get(CommentManager::class), $this->managers->getManagerForModelClass(Comment::class));
    }

    public function testHas(): void
    {
        $this->assertTrue($this->managers->has(Article::class));
        $this->assertFalse($this->managers->has(\stdClass::class));
        $this->assertFalse($this->managers->has(null));
    }

    public function testUnsupportedModelClass(): void
    {
        $this->expectException(ManagerException::class);
        $this->expectExceptionMessage('Unsupported model class stdClass');

        $this->managers->getManagerForModelClass(\stdClass::class);
    }

    public function testManagerFromContainerPersistsModels(): void
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        (new SchemaTool($em))->createSchema($em->getMetadataFactory()->getAllMetadata());

        $manager = $this->managers->getManagerForModelClass(Article::class);
        $article = $manager->new()->setTitle('Saved via ManagersContainer');
        $manager->save($article);
        $em->clear();

        $this->assertSame('Saved via ManagersContainer', $manager->getItem($article->getId())->getTitle());
    }
}
