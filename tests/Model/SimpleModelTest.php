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

use Dmytrof\ModelsManagementBundle\Model\SimpleModelInterface;
use Dmytrof\ModelsManagementBundle\Tests\Data\SomeModel;
use PHPUnit\Framework\TestCase;

class SimpleModelTest extends TestCase
{
    public function testNewSomeModel(): void
    {
        $model = new SomeModel();

        $this->assertInstanceOf(SimpleModelInterface::class, $model);
        $this->assertTrue($model->isModelNew());
        $this->assertFalse(!$model->isModelNew());
        $this->assertNull($model->getId());
        $this->assertEquals('SomeModel', $model->getModelCode());
        $this->assertEquals('ID: ', $model->getModelTitle());
        $this->assertEquals('NEW SomeModel', (string) $model);
    }

    public function testExistingSomeModel(): void
    {
        $model = (new SomeModel())->setId(25);

        $this->assertInstanceOf(SimpleModelInterface::class, $model);
        $this->assertFalse($model->isModelNew());
        $this->assertTrue(!$model->isModelNew());
        $this->assertSame(25, $model->getId());
        $this->assertEquals('SomeModel', $model->getModelCode());
        $this->assertEquals('ID: 25', $model->getModelTitle());
        $this->assertEquals('ID: 25', (string) $model);
    }
}