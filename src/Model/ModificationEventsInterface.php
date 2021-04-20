<?php

namespace Dmytrof\ModelsManagementBundle\Model;

use Dmytrof\ModelsManagementBundle\Event\ModificationEvent;

interface ModificationEventsInterface
{
    /**
     * Returns modification events
     * @return array|ModificationEvent[]
     */
    public function getModificationEvents(): array;

    /**
     * Adds modification events
     * @param ModificationEvent $event
     * @return $this
     */
    public function addModificationEvent(ModificationEvent $event): self;

    /**
     * Clears modification events
     * @return $this
     */
    public function cleanupModificationEvents(): self;
}