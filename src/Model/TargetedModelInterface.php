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

use Doctrine\Common\Persistence\ManagerRegistry;
use Dmytrof\ModelsManagementBundle\{Model\Target, Model\SimpleModelInterface, Exception\InvalidTargetException};

interface TargetedModelInterface
{
    /**
     * Sets registry
     * @param ManagerRegistry $registry
     * @return TargetedModelInterface
     */
    public function setRegistry(ManagerRegistry $registry): TargetedModelInterface;

    /**
     * Returns registry
     * @return null|ManagerRegistry
     */
    public function getRegistry(): ?ManagerRegistry;

    /**
     * Sets target
     * @param Target|SimpleModelInterface $target
     * @return TargetedModelInterface
     */
    public function setTarget($target): self;

    /**
     * Returns target
     * @throws InvalidTargetException
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
    public function refreshTarget(): self;
}