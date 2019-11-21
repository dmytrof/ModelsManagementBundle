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

use Dmytrof\ModelsManagementBundle\Exception\TargetException;
use Dmytrof\ModelsManagementBundle\Model\Target;
use Dmytrof\ModelsManagementBundle\Tests\Data\SomeModel;
use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class TargetTest extends TestCase
{
    /**
     * @var Target
     */
    protected $target;

    /**
     * Creates target
     * @return Target
     */
    public function createTarget(): Target
    {
        $registry = $this->createMock(ManagerRegistry::class);
        return new Target($registry, ...func_get_args());
    }

    public function setUp(): void
    {
        $this->target = $this->createTarget();
    }

    public function testEmptyTargetCreation(): void
    {
        $target = $this->createTarget();

        $this->assertInstanceOf(ManagerRegistry::class, $target->getRegistry());
        $this->assertEquals(0, $target->getId());
        $this->assertNull($target->getClassName());
        $this->assertNull($target->getModel());
        $this->assertEquals(['className' => null, 'id' => 0], $target->toArray());

        $this->expectException(TargetException::class);
        $target->getTargetRepository();
    }

    public function testNewModelTargetCreation(): void
    {
        $target = $this->createTarget(new SomeModel());

        $this->assertInstanceOf(ManagerRegistry::class, $target->getRegistry());
        $this->assertEquals(0, $target->getId());
        $this->assertEquals(SomeModel::class, $target->getClassName());
        $this->assertEquals(new SomeModel(), $target->getModel());
        $this->assertEquals(['className' => SomeModel::class, 'id' => 0], $target->toArray());

        $this->expectException(TargetException::class);
        $target->getTargetRepository();
    }
}