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

use Dmytrof\ModelsManagementBundle\Exception\InvalidFlagException;

trait FlagsTrait
{
    /**
     * @var array
     */
    protected $flags = [];

    /**
     * Sets flag
     * @param int|string $flag
     * @param bool $value
     * @return $this
     */
    public function setFlag($flag, bool $value = true): self
    {
        $this->checkFlagType($flag);
        $this->flags[$flag] = $value;
        return $this;
    }

    /**
     * Checks flag
     * @param int|string $flag
     * @return bool
     */
    public function hasFlag($flag): bool
    {
        $this->checkFlagType($flag);
        return $this->flags[$flag] ?? false;
    }

    /**
     * Pops flag
     * @param int|string $flag
     * @return bool
     */
    public function popFlag($flag): bool
    {
        $flagValue = $this->hasFlag($flag);
        $this->unsetFlag($flag);
        return $flagValue;
    }

    /**
     * Unsets flag
     * @param int|string $flag
     * @return $this
     */
    public function unsetFlag($flag): self
    {
        $this->checkFlagType($flag);
        unset($this->flags[$flag]);
        return $this;
    }

    /**
     * Unsets flag
     * @param int|string $flag
     * @return $this
     */
    public function removeFlag($flag): self
    {
        return $this->unsetFlag($flag);
    }

    /**
     * Returns flags
     * @return array
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * Checks type of flag
     * @param $flag
     * @return bool
     */
    protected function checkFlagType($flag): bool
    {
        if (!is_int($flag) && !is_string($flag)) {
            throw new InvalidFlagException(sprintf('Unsupported type \'%s\' of flag. Strings and integers are supported only!', gettype($flag)));
        }
        return true;
    }
}