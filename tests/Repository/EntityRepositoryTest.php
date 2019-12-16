<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Repository;

use Dmytrof\ModelsManagementBundle\Model\DoctrinePaginator;
use Dmytrof\ModelsManagementBundle\Model\SimpleModelInterface;
use Dmytrof\ModelsManagementBundle\Repository\EntityRepositoryInterface;
use Doctrine\ORM\QueryBuilder;
use Dmytrof\ModelsManagementBundle\Tests\Data\{SomeModel, SomeModelRepository};
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class EntityRepositoryTest extends TestCase
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $repository;

    public function setUp(): void
    {
        $eventManager = $this->createMock(EventManager::class);
        $eventManager->method('dispatchEvent')->willReturn(true);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getEventManager')->willReturn($eventManager);
        $entityManager->method('createQueryBuilder')->willReturn(new QueryBuilder($entityManager));

        $this->repository = new SomeModelRepository($entityManager, new ClassMetadata(SomeModel::class));
    }

    public function testGetAlias(): void
    {
        $this->assertNotEmpty($alias = $this->repository->getAlias());
        $this->assertEquals($alias, $this->repository->getAlias());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $builder = new QueryBuilder($entityManager);
        $builder
            ->select('some_data')
            ->from(SomeModel::class, 'sm')
        ;
        $this->assertEquals('sm', $this->repository->getAlias($builder));
    }

    public function testCreateNew(): void
    {
        $this->assertEquals(new SomeModel(), $this->repository->createNew());
        $this->assertInstanceOf(SimpleModelInterface::class, $this->repository->createNew());
    }

    public function testGetQueryBuilder(): void
    {
        $this->assertInstanceOf(QueryBuilder::class, $builder = $this->repository->getQueryBuilder(['alias' => 'qwe']));
        $this->assertSame('qwe', $builder->getAllAliases()[0]);
    }
}