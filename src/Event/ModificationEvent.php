<?php

namespace Dmytrof\ModelsManagementBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

abstract class ModificationEvent extends Event
{
    /**
     * @var bool
     */
    protected $needsFlush = false;

    /**
     * Checks if flush needed
     * @return bool
     */
    public function isNeedsFlush(): bool
    {
        return $this->needsFlush;
    }

    /**
     * Sets needs flush
     * @param bool $needsFlush
     * @return $this
     */
    public function setNeedsFlush(bool $needsFlush): self
    {
        $this->needsFlush = $needsFlush;
        return $this;
    }
}