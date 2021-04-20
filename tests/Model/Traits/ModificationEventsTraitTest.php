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

use Dmytrof\ModelsManagementBundle\Event\ModificationEvent;
use Dmytrof\ModelsManagementBundle\Model\{ModificationEventsInterface, Traits\ModificationEventsTrait};
use PHPUnit\Framework\TestCase;

class ModificationEventsTraitTest extends TestCase
{
    public function testModificationEvents()
    {
        $modelWithModificationEvents = new class implements ModificationEventsInterface {
            use ModificationEventsTrait;

            public function setSomething($value)
            {
                $logEvent = new class ($value) extends ModificationEvent {

                    private $value;

                    public function __construct($value)
                    {
                        $this->value = $value;
                    }

                    /**
                     * @return mixed
                     */
                    public function getValue()
                    {
                        return $this->value;
                    }
                };
                $this->addModificationEvent($logEvent);
            }
        };

        $this->assertEquals([], $modelWithModificationEvents->getModificationEvents());
        $modelWithModificationEvents->setSomething(123);
        $this->assertCount(1, $modelWithModificationEvents->getModificationEvents());
        $this->assertInstanceOf(ModificationEvent::class, $modelWithModificationEvents->getModificationEvents()[0]);
        $this->assertEquals(123, $modelWithModificationEvents->getModificationEvents()[0]->getValue());

        $modelWithModificationEvents->setSomething('qwer');
        $this->assertCount(2, $modelWithModificationEvents->getModificationEvents());
        $this->assertInstanceOf(ModificationEvent::class, $modelWithModificationEvents->getModificationEvents()[1]);
        $this->assertEquals('qwer', $modelWithModificationEvents->getModificationEvents()[1]->getValue());

        $this->assertInstanceOf(get_class($modelWithModificationEvents), $modelWithModificationEvents->cleanupModificationEvents());

        $this->assertEquals([], $modelWithModificationEvents->getModificationEvents());
    }
}