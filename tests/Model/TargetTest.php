<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Model;

use Dmytrof\ModelsManagementBundle\Event\LoadTargetModel;
use Dmytrof\ModelsManagementBundle\Exception\InvalidTargetException;
use Dmytrof\ModelsManagementBundle\Exception\TargetException;
use Dmytrof\ModelsManagementBundle\Model\SimpleModelInterface;
use Dmytrof\ModelsManagementBundle\Model\Target;
use Dmytrof\ModelsManagementBundle\Model\Traits\SimpleModelTrait;
use Dmytrof\ModelsManagementBundle\Tests\Data\SomeModel;
use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TargetTest extends TestCase
{
    /**
     * Creates target
     * @return Target
     */
    public function createTarget(): Target
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        return new Target($eventDispatcher, ...func_get_args());
    }

    public function testEmptyTargetCreation(): void
    {
        $target = $this->createTarget();

        $this->assertInstanceOf(EventDispatcherInterface::class, $target->getEventDispatcher());
        $this->assertEquals(0, $target->getId());
        $this->assertNull($target->getClassName());
        $this->assertNull($target->getModel());
        $this->assertNull($target->getModelId());
        $this->assertEquals(['className' => null, 'id' => 0], $target->toArray());

        $this->expectException(InvalidTargetException::class);
        $target->getModel(true);
    }

    public function testNewModelTargetCreation(): void
    {
        $target = $this->createTarget(new SomeModel());

        $this->assertInstanceOf(EventDispatcherInterface::class, $target->getEventDispatcher());
        $this->assertEquals(0, $target->getId());
        $this->assertEquals(SomeModel::class, $target->getClassName());
        $this->assertEquals(new SomeModel(), $target->getModel());
        $this->assertNull($target->getModelId());
        $this->assertEquals(['className' => SomeModel::class, 'id' => 0], $target->toArray());
    }

    public function testModelTargetCreation(): void
    {
        $model = new SomeModel();
        $id = 123;
        $model->setId($id);
        $target = $this->createTarget($model);

        $this->assertInstanceOf(EventDispatcherInterface::class, $target->getEventDispatcher());
        $this->assertEquals($id, $target->getId());
        $this->assertEquals(SomeModel::class, $target->getClassName());
        $this->assertEquals($model, $target->getModel());
        $this->assertEquals(123, $target->getModelId());
        $this->assertEquals(['className' => SomeModel::class, 'id' => $id], $target->toArray());
    }

    public function testTargetRefreshing(): void
    {
        $model = new SomeModel();
        $target = $this->createTarget($model);

        $this->assertEquals(0, $target->getId());
        $this->assertEquals(SomeModel::class, $target->getClassName());
        $this->assertEquals(new SomeModel(), $target->getModel());
        $this->assertNull($target->getModelId());
        $this->assertEquals(['className' => SomeModel::class, 'id' => 0], $target->toArray());

        $model->setId(333);
        $this->assertInstanceOf(Target::class, $target->refresh());
        $this->assertEquals($model, $target->getModel());
        $this->assertEquals(333, $target->getModelId());
        $this->assertEquals(['className' => SomeModel::class, 'id' => 333], $target->toArray());
    }

    public function testGetModel(): void
    {
        $model = new SomeModel();
        $model->setId(44);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $target = new Target($eventDispatcher, null);
        $this->assertEquals(0, $target->getId());
        $this->assertNull($target->getClassName());

        $this->assertInstanceOf(Target::class, $target->fromArray([
            'id' => $model->getId(),
            'className' => SomeModel::class,
        ]));

        $this->assertEquals($model->getId(), $target->getId());
        $this->assertNull($target->getModelId());
        $this->assertNull($target->getModel());
        $this->assertEquals(SomeModel::class, $target->getClassName());

        $eventDispatcher
            ->method('dispatch')
            ->will($this->returnCallback(function (LoadTargetModel $event) use ($model) {
                $event->setModel($model);
                return $event;
            }));

        $this->assertEquals($model->getId(), $target->getId());
        $this->assertEquals($model->getId(), $target->getModelId());
        $this->assertInstanceOf(SomeModel::class, $target->getModel());
        $this->assertEquals($model, $target->getModel());
    }
}