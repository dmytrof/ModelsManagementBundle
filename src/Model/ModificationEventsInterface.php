<?php

namespace Dmytrof\ModelsManagementBundle\Model;

use Dmytrof\ModelsManagementBundle\Event\ModificationEventInterface;

interface ModificationEventsInterface
{
    /**
     * Returns modification events
     * @return array<array-key, ModificationEventInterface>
     */
    public function getModificationEvents(): array;

    /**
     * Adds modification events
     */
    public function addModificationEvent(ModificationEventInterface $event): self;

    /**
     * Clears modification events
     */
    public function cleanupModificationEvents(): self;
}