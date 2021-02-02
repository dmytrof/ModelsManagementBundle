<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Model\Traits;

use Dmytrof\ModelsManagementBundle\Exception\InvalidFlagException;
use Dmytrof\ModelsManagementBundle\Model\Traits\FlagsTrait;
use PHPUnit\Framework\TestCase;

class FlagsTraitTest extends TestCase
{
    public function testSupportedFlagsTypes()
    {
        $modelWithFlags = new class {
            use FlagsTrait;

            public const SOME_FLAG1 = 1;
            public const SOME_FLAG2 = 'FLAG_2';
        };

        $this->assertFalse($modelWithFlags->hasFlag($modelWithFlags::SOME_FLAG1));
        $this->assertFalse($modelWithFlags->hasFlag($modelWithFlags::SOME_FLAG2));

        $this->assertFalse($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG1));
        $this->assertFalse($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG2));

        $this->assertInstanceOf(get_class($modelWithFlags), $modelWithFlags->setFlag($modelWithFlags::SOME_FLAG1));

        $this->assertEquals([
            $modelWithFlags::SOME_FLAG1 => true,
        ], $modelWithFlags->getFlags());

        $this->assertTrue($modelWithFlags->hasFlag($modelWithFlags::SOME_FLAG1));
        $this->assertFalse($modelWithFlags->hasFlag($modelWithFlags::SOME_FLAG2));

        $this->assertTrue($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG1));
        $this->assertFalse($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG2));

        $this->assertEquals([], $modelWithFlags->getFlags());

        $this->assertFalse($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG1));
        $this->assertFalse($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG2));

        $this->assertInstanceOf(get_class($modelWithFlags), $modelWithFlags->setFlag($modelWithFlags::SOME_FLAG2));

        $this->assertEquals([
            $modelWithFlags::SOME_FLAG2 => true,
        ], $modelWithFlags->getFlags());

        $this->assertFalse($modelWithFlags->hasFlag($modelWithFlags::SOME_FLAG1));
        $this->assertTrue($modelWithFlags->hasFlag($modelWithFlags::SOME_FLAG2));

        $this->assertInstanceOf(get_class($modelWithFlags), $modelWithFlags->unsetFlag($modelWithFlags::SOME_FLAG2));

        $this->assertEquals([], $modelWithFlags->getFlags());

        $this->assertFalse($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG1));
        $this->assertFalse($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG2));

        $this->assertInstanceOf(get_class($modelWithFlags), $modelWithFlags->setFlag($modelWithFlags::SOME_FLAG1, false));
        $this->assertEquals([
            $modelWithFlags::SOME_FLAG1 => false,
        ], $modelWithFlags->getFlags());
        $this->assertFalse($modelWithFlags->popFlag($modelWithFlags::SOME_FLAG1));
    }

    public function testArrayFlagsTypes()
    {
        $modelWithFlags = new class {
            use FlagsTrait;

            public const UNSUPPORTED_FLAG = ['ARRAY_FLAG'];
        };

        $this->expectException(InvalidFlagException::class);
        $modelWithFlags->setFlag($modelWithFlags::UNSUPPORTED_FLAG);

    }

    public function testBooleanFlagTypes()
    {
        $modelWithFlags = new class {
            use FlagsTrait;
        };

        $this->expectException(InvalidFlagException::class);
        $modelWithFlags->hasFlag(true);
    }

    public function testBooleanFalseFlagTypes()
    {
        $modelWithFlags = new class {
            use FlagsTrait;
        };

        $this->expectException(InvalidFlagException::class);
        $modelWithFlags->popFlag(false);
    }

    public function testNullFlagTypes()
    {
        $modelWithFlags = new class {
            use FlagsTrait;
        };

        $this->expectException(InvalidFlagException::class);
        $modelWithFlags->unsetFlag(null);
    }

    public function testObjectFlagTypes()
    {
        $modelWithFlags = new class {
            use FlagsTrait;
        };

        $this->expectException(InvalidFlagException::class);
        $modelWithFlags->removeFlag((object) []);
    }

    public function testFloatFlagTypes()
    {
        $modelWithFlags = new class {
            use FlagsTrait;
        };

        $this->expectException(InvalidFlagException::class);
        $modelWithFlags->hasFlag(3.14);
    }
}