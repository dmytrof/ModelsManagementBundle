<?php

namespace Dmytrof\ModelsManagementBundle\Model\Traits;

use Dmytrof\ModelsManagementBundle\Event\ModificationEventInterface;
use Dmytrof\ModelsManagementBundle\Model\ModificationEventsInterface;

trait ModificationEventsTrait
{
    /**
     * @var array
     */
    protected $modificationEvents = [];

    /**
     * Returns modification events
     * @return array
     */
    public function getModificationEvents(): array
    {
        return $this->modificationEvents;
    }

    /**
     * Adds modification events
     * @param ModificationEventInterface $event
     * @return ModificationEventsInterface
     */
    public function addModificationEvent(ModificationEventInterface $event): ModificationEventsInterface
    {
        array_push($this->modificationEvents, $event);
        return $this;
    }

    /**
     * Clears modification events
     * @return ModificationEventsInterface
     */
    public function cleanupModificationEvents(): ModificationEventsInterface
    {
        $this->modificationEvents = [];
        return $this;
    }
}