<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Model\Traits;

use Dmytrof\ModelsManagementBundle\Exception\RuntimeException;
use Dmytrof\ModelsManagementBundle\Model\EventableModelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

trait EventableModelTrait
{
    protected ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * Sets event dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): EventableModelInterface
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     * Returns event dispatcher
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        if (!$this->eventDispatcher instanceof EventDispatcherInterface) {
            throw new RuntimeException('Event dispatcher is not set');
        }
        return $this->eventDispatcher;
    }
}