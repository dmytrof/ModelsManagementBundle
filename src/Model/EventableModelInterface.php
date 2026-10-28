<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Model;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

interface EventableModelInterface
{
    /**
     * Sets event dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): EventableModelInterface;

    /**
     * Returns event dispatcher
     */
    public function getEventDispatcher(): EventDispatcherInterface;
}