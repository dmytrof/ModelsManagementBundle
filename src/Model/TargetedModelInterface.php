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

use Dmytrof\ModelsManagementBundle\{Model\Target, Model\SimpleModelInterface, Exception\InvalidTargetException};
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

interface TargetedModelInterface
{
    /**
     * Sets event dispatcher
     * @param EventDispatcherInterface $eventDispatcher
     * @return TargetedModelInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): TargetedModelInterface;

    /**
     * Returns event dispatcher
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Sets target
     * @param Target|SimpleModelInterface $target
     * @return TargetedModelInterface
     */
    public function setTarget($target): TargetedModelInterface;

    /**
     * Returns target
     * @return Target
     */
    public function getTarget(): Target;

    /**
     * Checks target
     * @return bool
     */
    public function hasTarget(): bool;

    /**
     * Refreshes target
     * @return TargetedModelInterface
     */
    public function refreshTarget(): TargetedModelInterface;
}