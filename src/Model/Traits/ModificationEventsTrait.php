<?php

namespace Dmytrof\ModelsManagementBundle\Model\Traits;

use Dmytrof\ModelsManagementBundle\Event\ModificationEventInterface;
use Dmytrof\ModelsManagementBundle\Model\ModificationEventsInterface;

trait ModificationEventsTrait
{
    protected array $modificationEvents = [];

    /**
     * Returns modification events
     */
    public function getModificationEvents(): array
    {
        return $this->modificationEvents;
    }

    /**
     * Adds modification events
     */
    public function addModificationEvent(ModificationEventInterface $event): ModificationEventsInterface
    {
        $this->modificationEvents[] = $event;
        return $this;
    }

    /**
     * Clears modification events
     */
    public function cleanupModificationEvents(): ModificationEventsInterface
    {
        $this->modificationEvents = [];
        return $this;
    }
}