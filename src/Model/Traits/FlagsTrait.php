<?php

declare(strict_types=1);

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Model\Traits;

trait FlagsTrait
{
    protected array $flags = [];

    public function setFlag(int|string $flag, bool $value = true): self
    {
        $this->flags[$flag] = $value;
        return $this;
    }

    public function hasFlag(int|string $flag): bool
    {
        return $this->flags[$flag] ?? false;
    }

    public function popFlag(int|string $flag): bool
    {
        $flagValue = $this->hasFlag($flag);
        $this->unsetFlag($flag);
        return $flagValue;
    }

    public function unsetFlag(int|string $flag): self
    {
        unset($this->flags[$flag]);
        return $this;
    }

    public function removeFlag(int|string $flag): self
    {
        return $this->unsetFlag($flag);
    }

    public function getFlags(): array
    {
        return $this->flags;
    }
}