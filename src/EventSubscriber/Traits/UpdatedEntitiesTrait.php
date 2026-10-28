<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\EventSubscriber\Traits;

use Doctrine\Persistence\ObjectManager;

trait UpdatedEntitiesTrait
{
    protected array $updatedEntities = [];

    protected bool $needsFlush = false;

    /**
     * Returns updated entities
     */
    protected function getUpdatedEntities(): array
    {
        return $this->updatedEntities;
    }

    /**
     * Checks updated entities exists
     */
    protected function hasUpdatedEntities(): bool
    {
        return (bool) count($this->updatedEntities);
    }

    /**
     * Adds updated entity
     */
    protected function addUpdatedEntity(object $entity): self
    {
        if (!in_array($entity, $this->updatedEntities)) {
            $this->updatedEntities[] = $entity;
        }

        return $this;
    }

    /**
     * Cleans up updated entities
     */
    protected function cleanupUpdatedEntities(): self
    {
        $this->updatedEntities = [];
        return $this;
    }

    /**
     * Sets need flush
     */
    protected function setNeedsFlush(bool $needsFlush = true): self
    {
        $this->needsFlush = $needsFlush;
        return $this;
    }

    /**
     * Makes flush if needed
     */
    protected function makeFlushIfNeeded(ObjectManager $objectManager): self
    {
        if ($this->needsFlush) {
            $this->needsFlush = false;
            $objectManager->flush();
        }

        return $this;
    }
}