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

use Dmytrof\ModelsManagementBundle\Model\ActiveModelInterface;

trait ActiveModelTrait
{
    protected bool $active = false;

    /**
     * Return the name of active property
     */
    public static function getActiveProperty(): string
    {
        return 'active';
    }

    /**
     * Sets model active
     */
    public function setActive(?bool $active = true): ActiveModelInterface
    {
        $this->{static::getActiveProperty()} = (bool) $active;
        return $this;
    }

    /**
     * Toggles model active
     */
    public function toggleActive(): ActiveModelInterface
    {
        $this->{static::getActiveProperty()} = !$this->{static::getActiveProperty()};
        return $this;
    }

    /**
     * Returns model active property value
     */
    public function getActive(): bool
    {
        return (bool) $this->{static::getActiveProperty()};
    }

    /**
     * Checks if model is active
     */
    public function isActive(): bool
    {
        return $this->getActive();
    }

    /**
     * Activates model
     */
    public function activate(): ActiveModelInterface
    {
        return $this->setActive(true);
    }

    /**
     * Deactivates model
     */
    public function deactivate(): ActiveModelInterface
    {
        return $this->setActive(false);
    }
}