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

use Dmytrof\ModelsManagementBundle\Model\{Target, TargetedModelInterface};
use Dmytrof\ModelsManagementBundle\Tests\Data\{SomeModel, TargetedModel};
use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class TargetedModelTest extends TestCase
{
    /**
     * Creates targeted model
     * @return TargetedModelInterface
     */
    public function createTargetedModel(): TargetedModelInterface
    {
        $registry = $this->createMock(ManagerRegistry::class);
        return (new TargetedModel())->setRegistry($registry);
    }

    public function testNewTargetedModel(): void
    {
        $targetedModel = $this->createTargetedModel();

        $this->assertInstanceOf(ManagerRegistry::class, $targetedModel->getRegistry());
        $this->assertInstanceOf(Target::class, $targetedModel->getTarget());
        $this->assertFalse($targetedModel->hasTarget());
        $this->assertEquals(new Target($targetedModel->getRegistry()), $targetedModel->getTarget());
    }

    public function testTarget(): void
    {
        $targetedModel = $this->createTargetedModel();

        $targetModel = new SomeModel();
        $targetedModel->setTarget($targetModel);

        $this->assertFalse($targetedModel->hasTarget());
        $this->assertEquals(new Target($targetedModel->getRegistry(), $targetModel), $targetedModel->getTarget());
        $this->assertEquals(['className' => SomeModel::class, 'id' => 0], $targetedModel->getTarget()->toArray());

        $targetModel->setId(3);

        $this->assertInstanceOf(TargetedModelInterface::class, $targetedModel->refreshTarget());
        $this->assertTrue($targetedModel->hasTarget());
        $this->assertEquals(['className' => SomeModel::class, 'id' => 3], $targetedModel->getTarget()->toArray());
    }
}