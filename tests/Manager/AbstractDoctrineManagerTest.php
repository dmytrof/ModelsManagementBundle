<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Manager;

use Dmytrof\ModelsManagementBundle\EventSubscriber\ModelDoctrineSubscriber;
use Dmytrof\ModelsManagementBundle\Exception\{ModelValidationException, NotFoundException, NotDeletableModelException};
use Dmytrof\ModelsManagementBundle\Manager\AbstractDoctrineManager;
use Dmytrof\ModelsManagementBundle\Model\SimpleModelInterface;
use Doctrine\Common\EventManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\{EntityManagerInterface, Mapping\ClassMetadata};
use Dmytrof\ModelsManagementBundle\Tests\Data\{SomeModel, SomeModelDoctrineManager, SomeModelRepository};
use Symfony\Component\Form\{FormFactoryBuilder, FormFactoryInterface};
use Symfony\Component\Validator\{ConstraintViolation,
    ConstraintViolationList,
    Validator\ValidatorInterface};
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AbstractDoctrineManagerTest extends TestCase
{
    /**
     * @var AbstractDoctrineManager
     */
    protected $manager;

    public function setUp(): void
    {
        parent::setUp();

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventManager = $this->createMock(EventManager::class);
        $eventManager->method('dispatchEvent')->willReturn(true);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getEventManager')->willReturn($eventManager);
        $entityManager->method('persist');
        $entityManager->method('flush');
        $entityManager->method('find')->willReturnCallback(function ($className, $id, $lockMode, $lockVersion) {
            return ($id == 1) ? (new SomeModel())->setId(1) : null;
        });
        $entityManager->method('remove')->willReturnCallback(function (SimpleModelInterface $model) use ($entityManager, $eventDispatcher) {
            $subscriber = new ModelDoctrineSubscriber($eventDispatcher);
            $subscriber->preRemove(new LifecycleEventArgs($model, $entityManager));
        });

        $repo = new SomeModelRepository($entityManager, new ClassMetadata(SomeModel::class));


        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($entityManager);
        $registry->method('getRepository')->willReturn($repo);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturnCallback(function (SomeModel $object) {
            if (!$object->getFoo()) {
                return new ConstraintViolationList([(new ConstraintViolation('Foo is blank', 'Foo is blank', [], null, 'foo', true))]);
            }
            return new ConstraintViolationList();
        });
        $formFactory = (new FormFactoryBuilder())->getFormFactory();

        $this->manager = new SomeModelDoctrineManager($registry, $validator, $formFactory);
    }

    public function testEntityManager(): void
    {
        $this->assertInstanceOf(ManagerRegistry::class, $this->manager->getRegistry());
        $this->assertInstanceOf(EntityManagerInterface::class, $this->manager->getManager());
    }

    public function testValidatorAndFormFactory(): void
    {
        $this->assertInstanceOf(ValidatorInterface::class, $this->manager->getValidator());
        $this->assertInstanceOf(FormFactoryInterface::class, $this->manager->getFormFactory());
    }

    public function testGetModelClass(): void
    {
        $this->assertEquals(SomeModel::class, $this->manager->getModelClass());
    }

    public function testNew(): void
    {
        $this->assertEquals(new SomeModel(), $this->manager->new());
        $this->assertInstanceOf(SimpleModelInterface::class, $this->manager->new());
    }

    public function testGet(): void
    {
        $this->assertInstanceOf(SimpleModelInterface::class, $model = $this->manager->get(1));
        $this->assertInstanceOf(SomeModel::class, $model);
        $this->assertEquals(1, $model->getId());

        $this->assertNull($this->manager->get(2));
    }

    public function testGetItem(): void
    {
        $this->assertInstanceOf(SimpleModelInterface::class, $model = $this->manager->getItem(1));
        $this->assertInstanceOf(SomeModel::class, $model);
        $this->assertEquals(1, $model->getId());

        $this->expectException(NotFoundException::class);
        $this->manager->getItem(2);
    }

    public function testSave(): void
    {
        $item = new SomeModel();

        $this->assertSame($this->manager, $this->manager->save($item));

        $this->assertSame($this->manager, $this->manager->save((clone $item)->setFoo('qwe'), ['validate' => true, 'flush' => true]));

        $this->expectException(ModelValidationException::class);
        $this->manager->save($item, ['validate' => true]);
    }

    public function testRemove(): void
    {
        $item = new SomeModel();

        $this->assertSame($this->manager, $this->manager->remove((clone $item)->setId(2), ['flush' => true]));

        $this->expectException(NotDeletableModelException::class);
        $this->assertSame($this->manager, $this->manager->remove($item));
    }
}