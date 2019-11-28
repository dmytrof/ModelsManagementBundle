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

use Dmytrof\ModelsManagementBundle\Model\ActivatedModelInterface;

trait ActivatedModelTrait
{
    /**
     * @var boolean
     */
    protected $active;

    /**
     * Return the name of active property
     * @return string
     */
    public static function getActiveProperty(): string
    {
        return 'active';
    }

    /**
     * Sets model active
     * @param bool $active
     * @return ActivatedModelInterface
     */
    public function setActive(bool $active = true): ActivatedModelInterface
    {
        $this->{static::getActiveProperty()} = $active;
        return $this;
    }

    /**
     * Toggles model active
     * @return ActivatedModelInterface
     */
    public function toggleActive(): ActivatedModelInterface
    {
        $this->{static::getActiveProperty()} = !$this->{static::getActiveProperty()};
        return $this;
    }

    /**
     * Returns model active property value
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->{static::getActiveProperty()};
    }

    /**
     * Checks if model is active
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->getActive();
    }

    /**
     * Activates model
     * @return ActivatedModelInterface
     */
    public function activate(): ActivatedModelInterface
    {
        return $this->setActive(true);
    }

    /**
     * Deactivates model
     * @return ActivatedModelInterface
     */
    public function deactivate(): ActivatedModelInterface
    {
        return $this->setActive(false);
    }
}