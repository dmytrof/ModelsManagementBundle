<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Model\Traits;

use Dmytrof\ModelsManagementBundle\Model\Traits\TimestampableModelTrait;
use PHPUnit\Framework\TestCase;

class TimestampableCloneTestBase
{
    public bool $parentCloned = false;

    public function __clone()
    {
        $this->parentCloned = true;
    }
}

class TimestampableCloneTestModel extends TimestampableCloneTestBase
{
    use TimestampableModelTrait;
}

class TimestampableCloneTestStandalone
{
    use TimestampableModelTrait;
}

class TimestampableCloneTest extends TestCase
{
    public function testCloneResetsTimestampsAndCallsParentClone(): void
    {
        $model = (new TimestampableCloneTestModel())
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $clone = clone $model;

        $this->assertNull($clone->getCreatedAt());
        $this->assertNull($clone->getUpdatedAt());
        $this->assertTrue($clone->parentCloned, 'parent::__clone() is called');
        $this->assertNotNull($model->getCreatedAt());
    }

    public function testCloneWithoutParent(): void
    {
        $clone = clone (new TimestampableCloneTestStandalone())->setCreatedAt(new \DateTime());

        $this->assertNull($clone->getCreatedAt());
    }
}
