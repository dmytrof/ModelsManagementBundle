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

use Dmytrof\ModelsManagementBundle\Model\EventableModelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

trait EventableModelTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Sets event dispatcher
     * @param EventDispatcherInterface $eventDispatcher
     * @return EventableModelInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): EventableModelInterface
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     * Returns event dispatcher
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
}