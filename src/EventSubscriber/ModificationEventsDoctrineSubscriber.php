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
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ModificationEventsDoctrineSubscriber implements EventSubscriber
{
    use UpdatedEntitiesTrait;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * ModelDoctrineSubscriber constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    /**
     * @param mixed $entity
     */
    protected function addUpdatedEntityWithModificationEvents($entity): void
    {
        if ($entity instanceof ModificationEventsInterface) {
           $this->addUpdatedEntity($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->addUpdatedEntityWithModificationEvents($args->getObject());
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->addUpdatedEntityWithModificationEvents($args->getObject());
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->addUpdatedEntityWithModificationEvents($args->getObject());
    }

    /**
     * @param PostFlushEventArgs $args
     */
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
        $this->makeFlushIfNeeded($args->getEntityManager());
    }
}