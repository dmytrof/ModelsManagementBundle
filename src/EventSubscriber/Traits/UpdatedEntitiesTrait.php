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
    /**
     * @var array
     */
    protected $updatedEntities = [];

    /**
     * @var bool
     */
    protected $needsFlush = false;

    /**
     * Returns updated entities
     * @return array
     */
    protected function getUpdatedEntities(): array
    {
        return $this->updatedEntities;
    }

    /**
     * Checks updated entities exists
     * @return bool
     */
    protected function hasUpdatedEntities(): bool
    {
        return (bool) count($this->updatedEntities);
    }

    /**
     * Adds updated entity
     * @param $entity
     * @return $this
     */
    protected function addUpdatedEntity($entity): self
    {
        if (!in_array($entity, $this->updatedEntities)) {
            array_push($this->updatedEntities, $entity);
        }

        return $this;
    }

    /**
     * Cleans up updated entities
     * @return $this
     */
    protected function cleanupUpdatedEntities(): self
    {
        $this->updatedEntities = [];
        return $this;
    }

    /**
     * Sets need flush
     * @param bool $needsFlush
     * @return $this
     */
    protected function setNeedsFlush(bool $needsFlush = true): self
    {
        $this->needsFlush = $needsFlush;
        return $this;
    }

    /**
     * Makes flush if needed
     * @param ObjectManager $objectManager
     * @return $this
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