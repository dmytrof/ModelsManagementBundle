<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Event\Traits;

use Dmytrof\ModelsManagementBundle\Event\ModificationEventInterface;

trait ModificationEventTrait
{
    /**
     * @var bool
     */
    protected $needsFlush = false;

    /**
     * Checks if flush needed
     * @see ModificationEventInterface::isNeedsFlush()
     */
    public function isNeedsFlush(): bool
    {
        return $this->needsFlush;
    }

    /**
     * Sets needs flush
     * @see ModificationEventInterface::setNeedsFlush()
     */
    public function setNeedsFlush(bool $needsFlush = true): ModificationEventInterface
    {
        $this->needsFlush = $needsFlush;
        return $this;
    }
}