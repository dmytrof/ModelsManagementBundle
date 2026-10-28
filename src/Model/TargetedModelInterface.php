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

interface TargetedModelInterface extends EventableModelInterface
{
    /**
     * Sets target
     */
    public function setTarget(Target|SimpleModelInterface $target): TargetedModelInterface;

    /**
     * Returns target
     */
    public function getTarget(): Target;

    /**
     * Checks target
     */
    public function hasTarget(): bool;

    /**
     * Refreshes target
     */
    public function refreshTarget(): TargetedModelInterface;
}