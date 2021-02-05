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

interface TargetedModelInterface extends EventableModelInterface
{
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