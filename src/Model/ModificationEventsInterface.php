<?php

namespace Dmytrof\ModelsManagementBundle\Model;

use Dmytrof\ModelsManagementBundle\Event\ModificationEventInterface;

interface ModificationEventsInterface
{
    /**
     * Returns modification events
     * @return array|ModificationEventInterface[]
     */
    public function getModificationEvents(): array;

    /**
     * Adds modification events
     * @param ModificationEventInterface $event
     * @return $this
     */
    public function addModificationEvent(ModificationEventInterface $event): self;

    /**
     * Clears modification events
     * @return $this
     */
    public function cleanupModificationEvents(): self;
}