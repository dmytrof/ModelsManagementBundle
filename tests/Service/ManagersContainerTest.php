<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Service;

use Dmytrof\ModelsManagementBundle\Exception\ManagerException;
use Dmytrof\ModelsManagementBundle\Manager\ManagerInterface;
use Dmytrof\ModelsManagementBundle\Service\ManagersContainer;
use Dmytrof\ModelsManagementBundle\Tests\Data\SomeModel;
use PHPUnit\Framework\TestCase;

class ManagersContainerTest extends TestCase
{
    /**
     * @return ManagersContainer
     */
    public function testManagersContainer(): ManagersContainer
    {
        $container = new ManagersContainer([]);
        $this->assertCount(0, $container);

        $manager1 = $this->createMock(ManagerInterface::class);
        $manager1->method('getModelClass')->willReturn(SomeModel::class);

        $manager2 = $this->createMock(ManagerInterface::class);
        $manager2->method('getModelClass')->willReturn('SomeModelClass');

        $container = new ManagersContainer([$manager1, $manager2]);
        $this->assertCount(2, $container);
        $this->assertInstanceOf(\Iterator::class, $container->getIterator());

        $this->assertEquals([SomeModel::class, 'SomeModelClass'], $container->getModelClasses());

        $this->assertTrue($container->has(SomeModel::class));
        $this->assertTrue($container->has('SomeModelClass'));
        $this->assertFalse($container->has('qwe'));

        $this->assertInstanceOf(ManagerInterface::class, $container->getManagerForModelClass(SomeModel::class));

        $this->expectException(ManagerException::class);
        $container->getManagerForModelClass('qwe');

        return $container;
    }
}