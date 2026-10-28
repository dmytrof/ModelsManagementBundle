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

interface ActiveModelInterface
{
    /**
     * Return the name of active property
     */
    public static function getActiveProperty(): string;

    /**
     * Sets model active
     */
    public function setActive(bool $active = true): self;

    /**
     * Toggles model active
     */
    public function toggleActive(): self;

    /**
     * Returns model active property value
     */
    public function getActive(): bool;

    /**
     * Checks if model is active
     */
    public function isActive(): bool;

    /**
     * Activates model
     */
    public function activate(): ActiveModelInterface;

    /**
     * Deactivates model
     */
    public function deactivate(): ActiveModelInterface;
}