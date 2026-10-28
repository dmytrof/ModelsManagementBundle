<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\EventSubscriber;

use Dmytrof\ModelsManagementBundle\EventSubscriber\Traits\UpdatedEntitiesTrait;
use Dmytrof\ModelsManagementBundle\Model\ModificationEventsInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ModificationEventsDoctrineSubscriber implements EventSubscriber
{
    use UpdatedEntitiesTrait;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    protected function addUpdatedEntityWithModificationEvents(object $entity): void
    {
        if ($entity instanceof ModificationEventsInterface) {
           $this->addUpdatedEntity($entity);
        }
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        dd(__METHOD__);
        $this->addUpdatedEntityWithModificationEvents($args->getObject());
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->addUpdatedEntityWithModificationEvents($args->getObject());
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->addUpdatedEntityWithModificationEvents($args->getObject());
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->hasUpdatedEntities()) {
            foreach ($this->getUpdatedEntities() as $entity) {
                if ($entity instanceof ModificationEventsInterface) {
                    foreach ($entity->getModificationEvents() as $event) {
                        $this->eventDispatcher->dispatch($event);
                        if ($event->isNeedsFlush()) {
                            $this->setNeedsFlush(true);
                        }
                    }
                    $entity->cleanupModificationEvents();
                }
            }
            $this->cleanupUpdatedEntities();
        }
        $this->makeFlushIfNeeded($args->getObjectManager());
    }
}
