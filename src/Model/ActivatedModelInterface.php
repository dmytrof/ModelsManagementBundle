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

interface ActivatedModelInterface
{
    /**
     * Return the name of active property
     * @return string
     */
    public static function getActiveProperty(): string;

    /**
     * Sets model active
     * @param bool $active
     * @return ActivatedModelInterface
     */
    public function setActive(bool $active = true): self;

    /**
     * Toggles model active
     * @return ActivatedModelInterface
     */
    public function toggleActive(): self;

    /**
     * Returns model active property value
     * @return bool
     */
    public function getActive(): bool;

    /**
     * Checks if model is active
     * @return boolean
     */
    public function isActive(): bool;

    /**
     * Activates model
     * @return ActivatedModelInterface
     */
    public function activate(): ActivatedModelInterface;

    /**
     * Deactivates model
     * @return ActivatedModelInterface
     */
    public function deactivate(): ActivatedModelInterface;
}