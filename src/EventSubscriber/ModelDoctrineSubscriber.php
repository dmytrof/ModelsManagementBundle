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
use Dmytrof\ModelsManagementBundle\Exception\NotDeletableModelException;
use Dmytrof\ModelsManagementBundle\Model\{ConditionalDeletionInterface,
    EventableModelInterface,
    TargetedModelInterface};
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ModelDoctrineSubscriber implements EventSubscriber
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
            Events::postLoad,
            Events::prePersist,
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
            Events::postFlush,
        ];
    }

    /**
     * Sets registry to target
     * @param LifecycleEventArgs $args
     */
    protected function setEventDispatcherToEventableModel(LifecycleEventArgs $args): void
    {
        if ($args->getObject() instanceof EventableModelInterface) {
            $args->getObject()->setEventDispatcher($this->eventDispatcher);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $this->setEventDispatcherToEventableModel($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setEventDispatcherToEventableModel($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof TargetedModelInterface) {
            $this->addUpdatedEntity($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof TargetedModelInterface) {
            $this->addUpdatedEntity($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof ConditionalDeletionInterface && !$entity->canBeDeleted()) {
            throw new NotDeletableModelException();
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->hasUpdatedEntities()) {
            foreach ($this->getUpdatedEntities() as $entity) {
                if ($entity instanceof TargetedModelInterface && !$entity->hasTarget()) {
                    $entity->refreshTarget();
                    $this->setNeedsFlush();
                }
            }
            $this->cleanupUpdatedEntities();
        }
        $this->makeFlushIfNeeded($args->getEntityManager());
    }
}